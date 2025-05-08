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
    <title>Timetable Management</title>
    <link rel="stylesheet" href="../CSS/timetable.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="timetable.php" class="active">Generate Timetable</a>
                <a href="GeneratedTimeTable.php">View Timetables</a>
                <a href="../Calendar/calendar.php">Academic Calendar</a>
            </nav>
            <button id="logout-btn" class="logout" onclick="window.location.href='../logout.php'">Logout</button>
        </div>

        <main class="main-content">
            <header>
                <h1>Time Table Management System</h1>
                <div class="profile">
                    <h3><?php echo $_SESSION["email"]; ?></h3>
                    <img src="../images/profile.png" alt="Profile">
                </div>
            </header>

            <section class="form-section">
                <h2><i class="fas fa-calendar-alt"></i> Generate Timetable</h2>
                <form action="GenerateTimeTable.php" id="timetable-form" method="post">
                    <div class="form-row">
                        <!-- Course, Semester, Section dropdowns -->
                        <div class="form-group">
                            <label for="course"><i class="fas fa-graduation-cap"></i> Course:</label>
                            <select id="course" name="course" required>
                                <option value="">Select Course</option>
                                <?php
                                include("connection.php");
                                $courseQuery = "SELECT DISTINCT Course FROM courseform";
                                $courseResult = $conn->query($courseQuery);
                                while ($row = $courseResult->fetch_assoc()) {
                                    echo "<option value='{$row['Course']}'>{$row['Course']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="semester"><i class="fas fa-layer-group"></i> Semester:</label>
                            <select id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <?php
                                for ($i = 1; $i <= 8; $i++) {
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="section"><i class="fas fa-users"></i> Section:</label>
                            <select id="section" name="section" required>
                                <option value="">Select Section</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="subjects"><i class="fas fa-book"></i> Subjects Per Day:</label>
                            <input type="number" id="subjects" name="subjects" min="1" max="10" required>
                        </div>

                        <div class="form-group">
                            <label for="start-time"><i class="fas fa-clock"></i> Starting Time:</label>
                            <input type="time" id="start-time" name="start-time" value="09:00" required>
                        </div>

                        <div class="form-group">
                            <label for="subject-duration"><i class="fas fa-stopwatch"></i> Subject Duration:</label>
                            <select id="subject-duration" name="subject-duration" required>
                                <option value="45">45 minutes</option>
                                <option value="50">50 minutes</option>
                                <option value="55">55 minutes</option>
                                <option value="60" selected>60 minutes</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="break-duration"><i class="fas fa-coffee"></i> Break Duration:</label>
                            <input type="number" id="break-duration" name="break-duration" min="5" max="30" value="10" required>
                        </div>

                        <div class="form-group">
                            <label for="lectures-before-lunch"><i class="fas fa-utensils"></i> Lectures Before Lunch:</label>
                            <input type="number" id="lectures-before-lunch" name="lectures-before-lunch" min="1" max="10" value="3" required>
                        </div>

                        <div class="form-group">
                            <label for="lunch-duration"><i class="fas fa-clock"></i> Lunch Duration:</label>
                            <input type="number" id="lunch-duration" name="lunch-duration" min="30" max="90" value="45" required>
                        </div>
                    </div>

                    <div class="form-group days-group">
                        <label><i class="fas fa-calendar-day"></i> Select Days:</label>
                        <div class="days-container">
                            <label class="day-option">
                                <input type="checkbox" name="days[]" value="Monday" checked hidden>
                                <span class="day-btn">Mon</span>
                            </label>
                            <label class="day-option">
                                <input type="checkbox" name="days[]" value="Tuesday" checked hidden>
                                <span class="day-btn">Tue</span>
                            </label>
                            <label class="day-option">
                                <input type="checkbox" name="days[]" value="Wednesday" checked hidden>
                                <span class="day-btn">Wed</span>
                            </label>
                            <label class="day-option">
                                <input type="checkbox" name="days[]" value="Thursday" checked hidden>
                                <span class="day-btn">Thu</span>
                            </label>
                            <label class="day-option">
                                <input type="checkbox" name="days[]" value="Friday" checked hidden>
                                <span class="day-btn">Fri</span>
                            </label>
                            <label class="day-option">
                                <input type="checkbox" name="days[]" value="Saturday" hidden>
                                <span class="day-btn">Sat</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="generate-btn">
                        <i class="fas fa-magic"></i> Generate Timetable
                    </button>
                </form>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Day selection toggle
            document.querySelectorAll('.day-option').forEach(option => {
                option.addEventListener('click', function () {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                    this.querySelector('.day-btn').classList.toggle('active', checkbox.checked);
                });
            });

            // Initialize active state for days
            document.querySelectorAll('.day-option input[type="checkbox"]').forEach(checkbox => {
                if (checkbox.checked) {
                    checkbox.parentElement.querySelector('.day-btn').classList.add('active');
                }
            });

            // Form validation
            document.getElementById('timetable-form').addEventListener('submit', function (e) {
                const selectedDays = document.querySelectorAll('input[name="days[]"]:checked');
                if (selectedDays.length === 0) {
                    alert('Please select at least one day');
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>