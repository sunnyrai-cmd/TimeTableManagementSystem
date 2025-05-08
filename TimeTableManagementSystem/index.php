    <?php
    session_start();
    if (!isset($_SESSION["email"])) {
        header("Location: Login/login.php");
        exit();
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Table Management Dashboard</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap" rel="stylesheet">
</head>
<body>
	 <div class="container">
        <div class="sidebar">
            <div class="logo">
                <a href="https://www.its.edu.in/" target="_blank">
                <img src="images/ITS.png" alt="ITS LOGO" title ="its.edu.in">
                </a>
                <h2 color="white">ITS</h2>
            </div>
           
                  

            <div class="item">
                <div class="sidebar-menu">   
                    <nav>
                        <a href="index.php" class="active">Home</a>
                        <a href="CoursePHP/course.php" >Courses & Subjects</a>
                        <a href="FacultyPHP/faculty.php">Faculty Assignment</a>
                        <a href="RoomPHP/room.php"> Rooms & Labs</a>
                        <a href="SubjectPHP/Subject.php">Subject Management</a>
                        <a href="TimeTablePHP/timetable.php">Generate Timetable</a>
                        <a href="TimeTablePHP/GeneratedTimeTable.php">View TimeTables</a>
                        <a href="Calendar/calendar.php">Academic Calendar</a>
                    </nav>
                </div>
            </div>
            <button id="logout-btn" class="logout" onclick="window.location.href='logout.php';">Logout</button>
    </div>

    <div class="main-content">
        <header>
            <div class="user-info">
                <p>Time Table Management</p>
                <span>ITS</span>
            </div>
            <div class="search-bar">
                <input type="text" placeholder="Search">
            </div>
            <div class="profile-pic">
            <?php
                if (isset($_SESSION["username"])) {
            ?>
            <?php echo "<h3>Welcome " . $_SESSION["username"] . "</h3>"; ?>
            <?php
            include_once("CoursePHP/connection.php");

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
            $fullPath = "uploads/" . basename($filePath);

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

        <div class="dashboard-cards">
            <a href="DashboardPHP/DashBoardCoursesTable.php" target="_self">
                <div class="card">
                <img src="images/online-course.png" alt="Courses" width="100">
                <p>Courses</p>
                </div>
            </a>
            <a href="DashboardPHP/DashBoardSubjectsTable.php" target="_self">
                <div class="card">
                    <img src="images/speech.png" alt="Lectures" width="100">
                    <p>Subjects</p>
                </div>
            </a>
            <a href="DashboardPHP/DashBoardFacultyTable.php" target="_self">
                <div class="card">
                    <img src="images/teacher.png" alt="Faculty" width="100">
                    <p>Faculty</p>
                </div>
            </a>   
            <a href="DashboardPHP/DashBoardRoomsTable.php" target="_self">
            <div class="card">
                <img src="images/training.png" alt="Room No." width="100">
                <p>Rooms</p>
            </div>
            </a>
            <a href="#" target="_self">
            <div class="card">
                <img src="images/coding.png" alt="Labs" width="100">
                <p>Labs</p>
            </div>
            </a>
            <a href="DashboardPHP/DashBoardTimeTable.php" target="_self">
            <div class="card">
                <img src="images/schedule.png" alt="Timetable" width="100">
                <p>Timetable</p>
            </div>
            </a>
            <div class="card">
            	 <a href="Calendar/calendar.php" target="_self">
                <img src="images/calendar.png" alt="Calendar" width="100">
                </a>
                <p>Calendar</p>
            </div>
            <div class="card">
                <img src="images/settings.png" alt="Settings" width="100">
                <p>Settings</p>
            </div>
        </div>
    </div>
	</div>
</body>
</html>
