<?php
    session_start();
    if (!isset($_SESSION["email"])) {
        header("Location: ../Login/login.php");
        exit();
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="calendar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <a href="https://www.its.edu.in/" target="_blank">
                    <img src="../images/ITS.png" alt="ITS LOGO" title="its.edu.in">
                </a>
                <h2>ITS</h2>
            </div>
            <div class="item">
                <nav>
                    <a href="../index.php">Home</a>
                    <a href="../CoursePHP/course.php">Courses & Subjects</a>
                    <a href="../FacultyPHP/faculty.php">Faculty Assignment</a>
                    <a href="../RoomPHP/room.php">Rooms & Labs</a>
                    <a href="../SubjectPHP/Subject.php">Subject Management</a>
                    <a href="../TimeTablePHP/timetable.php">Generate Timetable</a>
                    <a href="../TimeTablePHP/GeneratedTimeTable.php">View Timetables</a>
                    <a href="calendar.php" class="active">Academic Calendar</a>
                </nav>
            </div>
            <button id="logout-btn" class="logout" onclick="window.location.href='../logout.php';">Logout</button>
        </div>
        

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>Time Table Management System / Calendar</h1>
                <div class="profile">
                    <?php
                    if (isset($_SESSION["username"])) {
                        echo "<h3>" . htmlspecialchars($_SESSION["username"]) . "</h3>";
                        include_once("connection.php");

                        if (isset($_SESSION["email"])) {
                            $email = $_SESSION["email"];

                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            $query = "SELECT file.file_path 
                                      FROM file 
                                      INNER JOIN registrationform 
                                      ON file.file_id = registrationform.profileid 
                                      WHERE registrationform.email = ?";
                                      
                            $stmt = $conn->prepare($query);

                            if ($stmt === false) {
                                die("Error preparing statement: " . $conn->error);
                            }

                            $stmt->bind_param("s", $email);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result && $result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $filePath = $row["file_path"];

                                // Fix relative file path
                                $fullPath = "../uploads/" . basename($filePath);

                                if (!empty($filePath) && file_exists($fullPath)) {
                                    echo '<img src="' . htmlspecialchars($fullPath) . '" alt="User Photo">';
                                } else {
                                    echo '<img src="images/default-user.png" alt="Default User Photo">';
                                }
                            }

                            $stmt->close();
                        }
                    }
                    ?>
                </div>
            </header>

            <!-- Calendar Section -->
            <section class="calendar">
                <div class="calendar-header">
                    <button id="prev-month">&lt;</button>
                    <h2 id="month-year"></h2>
                    <button id="next-month">&gt;</button>
                </div>
                <div class="calendar-body">
                    <table id="calendar-table">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="calendar.js"></script>
</body>
</html>
