<?php
    session_start();
    if (!isset($_SESSION["email"])) {
        header("Location: ../Login/login.php");
        exit();
    }
    ?>
<?php
include("connection.php");

$row = [
    'Course' => '',
    'Semester' => '',
    'SubjectCode' => '',
    'SubjectName' => '',
    'Duration' => '',
    'FacultyId' => '',
    'FacultyName' => '',
    'LectureRequired' => ''
];

$isEdit = false;

if (isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $sql = "SELECT * FROM subjectform WHERE SubjectCode = '$id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $isEdit = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Form</title>
    <link rel="stylesheet" href="../CSS/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="main-container">
        <div class="sidebar">
            <div class="logo">
                <a href="https://www.its.edu.in/" target="_blank">
                    <img src="../images/ITS.png" alt="ITS LOGO">
                </a>
                <h2>ITS</h2>
            </div>
            <nav class="item">
                <a href="../index.php">Home</a>
                <a href="../CoursePHP/course.php">Courses & Subjects</a>
                <a href="../FacultyPHP/faculty.php">Faculty Assignment</a>
                <a href="../RoomPHP/room.php">Rooms & Labs</a>
                <a href="Subject.php" class="active">Subject Management</a>
                <a href="../TimeTablePHP/timetable.php">Generate Timetable</a>
                <a href="../TimeTablePHP/GeneratedTimeTable.php">View Timetables</a>
                <a href="../Calendar/calendar.php">Academic Calendar</a>
            </nav>
            <button id="logout-btn" class="logout" onclick="window.location.href='../logout.php'">Logout</button>
        </div>

        <main class="main-content">
            <header>
                <h1>Time Table Management System / Subject</h1>
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
                                $profileRow = $result->fetch_assoc();
                                $filePath = $profileRow["file_path"];

                                // Fix relative file path
                                $fullPath = "../uploads/" . basename($filePath);

                                if (!empty($filePath) && file_exists($fullPath)) {
                                    echo '<img src="' . htmlspecialchars($fullPath) . '" alt="User Photo">';
                                } else {
                                    echo '<img src="../images/default-user.png" alt="Default User Photo">';
                                }
                            }

                            $stmt->close();
                        }
                    }
                    ?>
                </div>
            </header>

            <section class="lectures">
                <h2><?= $isEdit ? 'Edit Subject' : 'Add Subject' ?></h2>

                <div class="form-container" style="height: 78vh; overflow: auto;">
                    <form action="<?= $isEdit ? 'UpdateSubject.php' : 'InsertSubject.php' ?>" method="post" id="lectureForm">
                        <?php
                        $course_sql = "SELECT DISTINCT Course FROM courseform";
                        $course_result = $conn->query($course_sql);
                        ?>

                        <label for='Course'>Select Course:</label>
                        <select id='course' name='Course' required>
                            <option value=''>Select Course</option>
                            <?php while ($cr = $course_result->fetch_assoc()): ?>
                                <option value="<?= $cr['Course'] ?>" <?= ($cr['Course'] === $row['Course']) ? 'selected' : '' ?>>
                                    <?= $cr['Course'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <label for='Semester'>Select Semester:</label>
                        <select id='semester' name='Semester' required>
                            <option value=''>Select Semester</option>
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?= $i ?>" <?= ((string)$i === (string)$row['Semester']) ? 'selected' : '' ?>>
                            <?= $i ?>
                            </option>
                            <?php endfor; ?>
                        </select>

                        <?php if ($isEdit): ?>
                            <input type="hidden" name="SubjectCode" value="<?= $row['SubjectCode'] ?>">
                            <label>Subject Code: <strong><?= $row['SubjectCode'] ?></strong></label>
                        <?php else: ?>
                            <label for='SubjectCode'>Select Subject Code:</label>
                            <select id='subjectCode' name='SubjectCode' required>
                                <option value=''>Select Subject Code</option>
                                <!-- dynamically filled via JS -->
                            </select>
                        <?php endif; ?>

                        <label for='SubjectName'>Subject Name:</label>
                        <input type="text" id="subjectName" name="SubjectName" value="<?= $row['SubjectName'] ?>" readonly required>

                        <label for="Duration">Enter Duration (Minutes):</label>
                        <input type="number" id="Duration" name="Duration" placeholder="Duration" value="<?= $row['Duration'] ?>" required>

                        <label for="FacultyId">Faculty ID:</label>
                        <input type="text" id="facultyid" name="FacultyId" placeholder="Faculty Id" value="<?= $row['FacultyId'] ?>" readonly required>

                        <label for="FacultyName">Faculty Name:</label>
                        <input type="text" id="tutor" name="FacultyName" placeholder="Faculty Name" value="<?= $row['FacultyName'] ?>" readonly required>

                        <label for="LectureRequired">Lectures Required:</label>
                        <input type="number" id="LectureRequired" name="LectureRequired" placeholder="Lectures Required" value="<?= $row['LectureRequired'] ?>" required>

                        <div class="buttons">
                            <button type="submit" onclick="window.location.href='../logout.php';"><?= $isEdit ? 'Update Subject' : 'Add Subject' ?></button>
                        </div>
                    </form>
                </div>

                <div class="subject-table">
                    <h2>Subject List</h2>
                    <?php include("SubjectTable.php"); ?>
                </div>
            </section>
        </main>
    </div>
    <script src="SubjectCodeName.js"></script>
</body>
</html>
