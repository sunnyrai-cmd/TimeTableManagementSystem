<?php
    session_start();
    if (!isset($_SESSION["email"])) {
        header("Location: ../Login/login.php");
        exit();
    }
    ?>
<?php
include("connection.php");

if (isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $sql = "SELECT * FROM roomform WHERE RoomId = '$id'";
    $result = $conn->query($sql);

    if (!$result) {
        echo "Error: " . $conn->error;
    } else {
        $row = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rooms/Lab</title>
    <link rel="stylesheet" href="../CSS/all.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap"
        rel="stylesheet" />
</head>

<body>
    <div class="main-container">
        <div class="sidebar">
            <div class="logo">
                <a href="https://www.its.edu.in/" target="_blank">
                    <img src="../images/ITS.png" alt="ITS LOGO" title="its.edu.in" />
                </a>
                <h2>ITS</h2>
            </div>
            <nav>
                <a href="../index.php">Home</a>
                <a href="../CoursePHP/course.php">Courses & Subjects</a>
                <a href="../FacultyPHP/faculty.php">Faculty Assignment</a>
                <a href="room.php" class="active">Rooms & Labs</a>
                <a href="../SubjectPHP/Subject.php">Subject Management</a>
                <a href="../TimeTablePHP/timetable.php">Generate Timetable</a>
                <a href="../TimeTablePHP/GeneratedTimeTable.php">View Timetables</a>
                <a href="../Calendar/calendar.php">Academic Calendar</a>
            </nav>
            <button id="logout-btn" class="logout" onclick="window.location.href='../logout.php'">Logout</button>
        </div>

        <main class="main-content">
            <header>
                <h1>Time Table Management System / Rooms & labs</h1>
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
                                $RoomRow = $result->fetch_assoc();
                                $filePath = $RoomRow["file_path"];

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
            <section class="room">
                <h2>Add Room</h2>
                <div class="form-container">
                    <form action="<?php echo isset($row['RoomId']) ? 'UpdateRoom.php' : 'InsertRoom.php'; ?>" method="post" id="RoomForm">
                        <label for="RoomId">Enter Room Id : </label>
                        <input type="text" placeholder="Room Id" id="RoomId" name="RoomId"  value="<?php echo isset($row['RoomId']) ? $row['RoomId'] : ''; ?>" <?php echo isset($row['RoomId']) ? 'readonly' : ''; ?> required />
                        <label for="RoomName">Enter Room Name : </label>
                        <input type="text" placeholder="Room Name" id="RoomName" name="RoomName"   value="<?php echo isset($row['RoomName']) ? $row['RoomName'] : ''; ?>" required />
                        <label for="Location">Enter Location : </label>
                        <input type="text" placeholder="Location" id="Location" name="Location"   value="<?php echo isset($row['Location']) ? $row['Location'] : ''; ?>"required />
                        <label for="RoomType">Enter Room Type : </label>
                        <select name="RoomType" required>
                            <option value="ClassRoom"  <?php echo (isset($row['RoomType']) && $row['RoomType'] == 'ClassRoom') ? 'selected' : ''; ?>>Class Room</option>
                            <option value="Lab" <?php echo (isset($row['RoomType']) && $row['RoomType'] == 'Lab') ? 'selected' : ''; ?>>Lab</option>
                            <option value=" SeminarHall" <?php echo (isset($row['RoomType']) && $row['RoomType'] == 'SeminarHall') ? 'selected' : ''; ?>>Seminar Hall</option>
                            <option value="LectureHall" <?php echo (isset($row['RoomType']) && $row['RoomType'] == 'LectureHall') ? 'selected' : ''; ?>>Lecture Hall</option>
                        </select>
                        <label for="Capacity">Enter Capacity : </label>
                        <input type="number" placeholder="Capacity" id="Capacity" name="Capacity"   value="<?php echo isset($row['Capacity']) ? $row['Capacity'] : ''; ?>" required />
                        <label for="Availability">Enter Availability : </label>
                        <select name="Availability" required>
                            <option value="Available" <?php echo (isset($row['Availability']) && $row['Availability'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                            <option value="Occupied" <?php echo (isset($row['Availability']) && $row['Availability'] == 'Occupied') ? 'selected' : ''; ?>>Occupied</option>
                        </select>
                        <div class="buttons">
                            <button type="submit" onclick="window.location.href='../logout.php';"><?php echo isset($row['RoomId']) ? 'Update' : 'Add Room'; ?></button>
                        </div>
                    </form>
                </div>
                <div class="room-table">
                    <h2>Room List</h2>
                    <?php include("RoomTable.php"); ?>
                </div>
            </section>
        </main>
    </div>
</body>

</html>