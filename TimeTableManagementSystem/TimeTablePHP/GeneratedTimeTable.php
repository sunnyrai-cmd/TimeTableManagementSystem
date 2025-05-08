<?php
session_start();
include("connection.php");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug: Check if we're connected
echo "<!-- Database connection established -->";

// Fetch all generated timetables
$query = "SELECT DISTINCT course, Semester, Section FROM timetable ORDER BY course, Semester, Section";
$result = $conn->query($query);

// Debug: Check query results
if (!$result) {
    echo "<!-- Query error: " . $conn->error . " -->";
    $timetables = [];
} else {
    $timetables = $result->fetch_all(MYSQLI_ASSOC);
    echo "<!-- Found " . count($timetables) . " timetables -->";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Timetables</title>
    <link rel="stylesheet" href="../CSS/Generatedtimetable.css">
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <div class="logo">
                <a href="https://www.its.edu.in/" target="_blank">
                    <img src="../images/ITS.png" alt="ITS LOGO" title="its.edu.in">
                </a>
                <h2>ITS</h2>
            </div>
            <nav>
                <a href="../index.php">Home</a>
                <a href="../CoursePHP/course.php">Courses & Subjects</a>
                <a href="../FacultyPHP/faculty.php">Faculty Assignment</a>
                <a href="../RoomPHP/room.php">Rooms & Labs</a>
                <a href="../SubjectPHP/Subject.php">Subject Management</a>
                <a href="timetable.php">Generate Timetable</a>
                <a href="GeneratedTimeTable.php" class="active">View Timetables</a>
                <a href="../Calendar/calendar.php">Academic Calendar</a>
            </nav>
            <button id="logout-btn" class="logout" onclick="window.location.href='../logout.php'">Logout</button>
        </div>
        <main class="main-content">
            <header>
                <h1>Generated Timetables</h1>
                <button class="download-btn" onclick="window.location.href='?download=pdf'">Download All as PDF</button>
                <div style="clear: both;"></div>
            </header>
            <section class="table-container">
                <?php if (empty($timetables)): ?>
                    <p>No timetables have been generated yet.</p>
                <?php else: ?>
                    <?php foreach ($timetables as $timetable): ?>
                        <div class="timetable-section">
                            <h2><?= htmlspecialchars($timetable['course']) ?> - Semester
                                <?= htmlspecialchars($timetable['Semester']) ?> - Section
                                <?= htmlspecialchars($timetable['Section']) ?></h2>

                            <?php
                            // Get all time slots for this timetable grouped by day
                            $slotsQuery = "SELECT * FROM timetable 
                                           WHERE course = ? AND Semester = ? AND Section = ?
                                           ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), 
                                           start_time";
                            $stmt = $conn->prepare($slotsQuery);
                            $stmt->bind_param("sss", $timetable['course'], $timetable['Semester'], $timetable['Section']);
                            $stmt->execute();
                            $slotsResult = $stmt->get_result();
                            $slotsByDay = [];

                            while ($row = $slotsResult->fetch_assoc()) {
                                $slotsByDay[$row['day_of_week']][] = $row;
                            }

                            // Get all unique time slots for column headers
                            $timesQuery = "SELECT DISTINCT start_time, end_time FROM timetable 
                                         WHERE course = ? AND Semester = ? AND Section = ?
                                         ORDER BY start_time";
                            $stmt = $conn->prepare($timesQuery);
                            $stmt->bind_param("sss", $timetable['course'], $timetable['Semester'], $timetable['Section']);
                            $stmt->execute();
                            $timesResult = $stmt->get_result();
                            $timeSlots = $timesResult->fetch_all(MYSQLI_ASSOC);
                            ?>

                            <table border="1" class="final-timetable">
                                <thead>
                                    <tr>
                                        <th>Day/Time</th>
                                        <?php foreach ($timeSlots as $slot): ?>
                                            <th><?= htmlspecialchars($slot['start_time']) ?>-<?= htmlspecialchars($slot['end_time']) ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $daysOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    foreach ($daysOrder as $day):
                                        if (isset($slotsByDay[$day])):
                                            ?>
                                            <tr>
                                                <th><?= htmlspecialchars($day) ?></th>
                                                <?php
                                                // Create an array with all time slots for this day
                                                $daySlots = [];
                                                foreach ($slotsByDay[$day] as $slot) {
                                                    $timeKey = $slot['start_time'] . '-' . $slot['end_time'];
                                                    $daySlots[$timeKey] = $slot;
                                                }

                                                // Output cells for each time slot
                                                foreach ($timeSlots as $timeSlot) {
                                                    $timeKey = $timeSlot['start_time'] . '-' . $timeSlot['end_time'];
                                                    if (isset($daySlots[$timeKey])) {
                                                        $slot = $daySlots[$timeKey];
                                                        echo '<td>';
                                                        echo htmlspecialchars($slot['SubjectName']);
                                                        // if (!empty($slot['FacultyName'])) {
                                                        //     echo '<br><small>' . htmlspecialchars($slot['FacultyName']) . '</small>';
                                                        // }
                                                        // if (!empty($slot['RoomName'])) {
                                                        //     echo '<br><small>' . htmlspecialchars($slot['RoomName']) . '</small>';
                                                        // }
                                                        echo '</td>';
                                                    } else {
                                                        echo '<td></td>';
                                                    }
                                                }
                                                ?>
                                            </tr>
                                        <?php endif; endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Subject-Faculty reference table -->
                            <table border="1" class="final-timetable" style="margin-top: 20px;">
                                <thead>
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Faculty</th>
                                        <th>Room No/Lab No</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Get unique subjects for this timetable
                                    $subjectsQuery = "SELECT DISTINCT SubjectCode, SubjectName, FacultyName, RoomName 
                                                     FROM timetable 
                                                     WHERE course = ? AND Semester = ? AND Section = ?
                                                     ORDER BY SubjectName";
                                    $stmt = $conn->prepare($subjectsQuery);
                                    $stmt->bind_param("sss", $timetable['course'], $timetable['Semester'], $timetable['Section']);
                                    $stmt->execute();
                                    $subjectsResult = $stmt->get_result();

                                    while ($subject = $subjectsResult->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($subject['SubjectCode']) ?></td>
                                            <td><?= htmlspecialchars($subject['SubjectName']) ?></td>
                                            <td><?= htmlspecialchars($subject['FacultyName']) ?></td>
                                            <td><?= htmlspecialchars($subject['RoomName']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>