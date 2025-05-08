<?php
    session_start();
    if (!isset($_SESSION["email"])) {
        header("Location: ../Login/login.php");
        exit();
    }
    ?>
<?php
include("connection.php");
$row = []; // initialize

if (isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $sql = "SELECT * FROM courseform WHERE CourseCode = '$id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
}
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Form</title>
      <link rel="stylesheet" href="../CSS/all.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap"
        rel="stylesheet">

</head>

<body>
    <div class="main-container">
        <div class="sidebar">
            <div class="logo">
                <a href="https://www.its.edu.in/" target="_blank">
                    <img src="../images/ITS.png" alt="ITS LOGO" title="its.edu.in">
                </a>
                <h2>ITS</h2>
            </div>
            <nav>
                <a href="../index.php" >Home</a>
                <a href="course.php" class="active" >Courses & Subjects</a>
                <a href="../FacultyPHP/faculty.php">Faculty Assignment</a>
                <a href="../RoomPHP/room.php">Rooms & Labs</a>
                <a href="../SubjectPHP/Subject.php">Subject Management</a>
                <a href="../TimeTablePHP/timetable.php">Generate Timetable</a>
                <a href="../TimeTablePHP/GeneratedTimeTable.php">View TimeTables</a>
                <a href="../Calendar/calendar.php">Academic Calendar</a>
            </nav>
            <button id="logout-btn" class="logout" onclick="window.location.href='../logout.php'">Logout</button>
        </div>

        <main class="main-content">
            <header>
                <h1>Time Table Management System / Course</h1>
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
                                $CourseRow = $result->fetch_assoc();
                                $filePath = $CourseRow["file_path"];

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

            <section class="course">
                <h2><?php echo isset($row['CourseCode']) ? 'Edit Course' : 'Add Course'; ?></h2>
                <div class="form-container" style="height: 78vh; overflow: auto;">
                    <form action="<?php echo isset($row['CourseCode']) ? 'UpdateCourse.php' : 'InsertCourse.php'; ?>"
                        method="post" id="courseForm">

                        <!-- Hidden Field for Updating -->
                        <?php if (isset($row['CourseCode'])): ?>
                            <input type="hidden" name="originalCourseCode" value="<?php echo $row['CourseCode']; ?>">
                        <?php endif; ?>
                        <label for="CourseCode">Enter Course Code:</label>
                        <input type="text" name="CourseCode" id="CourseCode"
                            value="<?php echo $row['CourseCode'] ?? ''; ?>" required placeholder="Enter Course Code">

                        <label for="Course">Enter Course:</label>
                        <input type="text" name="Course" id="Course" value="<?php echo $row['Course'] ?? ''; ?>"
                            required placeholder="Enter Course Name">


                        <label for="CourseDuration">Enter Course Duration:</label>
                        <input type="number" name="CourseDuration" id="CourseDuration"
                            value="<?php echo $row['CourseDuration'] ?? ''; ?>" required
                            placeholder="Enter Course Duration in Years">

                        <label for="semester">Enter The Semester:</label>
                        <select id="semester" name="semester" required>
                            <option value="" disabled <?php echo empty($row['semester']) ? 'selected' : ''; ?>>Select
                                Semester</option>
                            <?php
                            for ($i = 1; $i <= 8; $i++) {
                                $selected = (isset($row['semester']) && $row['semester'] == $i) ? 'selected' : '';
                                echo "<option value='$i' $selected>{$i} Semester</option>";
                            }
                            ?>
                        </select>

                        <label for="SubjectCode">Enter Subject Code:</label>
                        <input type="text" name="SubjectCode" id="SubjectCode"
                            value="<?php echo $row['SubjectCode'] ?? ''; ?>" required placeholder="Enter Subject Code">

                        <label for="SubjectName">Enter Subject Name:</label>
                        <input type="text" name="SubjectName" id="SubjectName"
                            value="<?php echo $row['SubjectName'] ?? ''; ?>" required placeholder="Enter Subject Name">

                        <label for="SubjectType">Enter Subject Type:</label>
                        <select id="SubjectType" name="SubjectType" required>
                            <option value="" disabled <?php echo empty($row['SubjectType']) ? 'selected' : ''; ?>>Select
                                Subject Type</option>
                            <?php
                            $types = ['Theory', 'Practical', 'Lab', 'Project'];
                            foreach ($types as $type) {
                                $selected = (isset($row['SubjectType']) && $row['SubjectType'] == $type) ? 'selected' : '';
                                echo "<option value='$type' $selected>$type</option>";
                            }
                            ?>
                        </select>

                        <div class="buttons">
                            <button type="submit" onclick="window.location.href='../logout.php';"
                                class="submit-btn"><?php echo isset($row['CourseCode']) ? 'Update Course' : 'Add Course'; ?></button>
                        </div>
                    </form>
                </div>

                <div class="course-table">
                    <h2>Course List</h2>
                    <?php include("CourseTable.php"); ?>
                </div>
            </section>

        </main>
    </div>
   
</body>

</html>