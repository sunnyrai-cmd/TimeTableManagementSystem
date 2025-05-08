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
<style>
    /* Reset some default browser styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f4f7f9;
    color: #2c3e50;
    padding: 20px;
}

/* Main Content */
.main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

/* Header */
header {
    margin-bottom: 30px;
    text-align: center;
}

header h1 {
    font-size: 2.5rem;
    color: #34495e;
    margin-bottom: 10px;
}

.download-btn {
    background-color: #3498db;
    color: #fff;
    padding: 10px 25px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s;
    float: right;
    margin-top: -50px;
}

.download-btn:hover {
    background-color: #2980b9;
}

/* Table Container */
.table-container {
    margin-top: 20px;
}

/* Each Timetable Section */
.timetable-section {
    margin-bottom: 50px;
}

.timetable-section h2 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: #2c3e50;
}

/* Final Timetable Tables */
.final-timetable {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.final-timetable th,
.final-timetable td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 0.95rem;
}

.final-timetable th {
    background-color: #2c3e50;
    color: #fff;
}

.final-timetable tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

.final-timetable tbody tr:hover {
    background-color: #e6f7ff;
}

td small {
    display: block;
    font-size: 0.8rem;
    color: #7f8c8d;
    margin-top: 5px;
}

/* Section separator */
.timetable-section:not(:last-child)::after {
    content: "";
    display: block;
    height: 2px;
    background: #ecf0f1;
    margin: 40px 0;
}

/* Responsive */
@media (max-width: 768px) {
    .final-timetable th, .final-timetable td {
        font-size: 0.8rem;
        padding: 8px;
    }

    header h1 {
        font-size: 2rem;
    }

    .download-btn {
        float: none;
        display: block;
        margin: 20px auto 0;
    }
}

</style>
<main class="main-content">
            <header>
                <h1>Timetables</h1>
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