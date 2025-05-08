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
    $sql = "SELECT * FROM facultyform WHERE FacultyId = '$id'";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Faculty</title>
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
                <a href="../index.php">Home</a>
                <a href="../CoursePHP/course.php" >Courses & Subjects</a>
                <a href="../FacultyPHP/faculty.php" class="active">Faculty Assignment</a>
                <a href="../RoomPHP/room.php">Rooms & Labs</a>
                <a href="../SubjectPHP/Subject.php">Subject Management</a>
                <a href="../TimeTablePHP/timetable.php">Generate Timetable</a>
                <a href="../TimeTablePHP/GeneratedTimeTable.php">View TimeTables</a>
                <a href="../Calendar/calendar.php">Academic Calendar</a>
            </nav>
            <button id="logout-btn" class="logout">Logout</button>
        </div>


        <main class="main-content">
            <header>
                <h1>Time Table Management System / Faculty</h1>
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
                                $FacultyRow = $result->fetch_assoc();
                                $filePath = $FacultyRow["file_path"];

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

            <section class="faculty">
                <h2>Add Faculty</h2>
                <div class="form-container">
                    <form id="facultyForm"
                        action="<?php echo isset($row['FacultyId']) ? 'UpdateFaculty.php' : 'insertFaculty.php'; ?>"
                        method="post">
                        <label for="FacultyId">Enter Faculty Id : </label>
                        <input type="text" placeholder="Faculty Id" id="FacultyId" name="FacultyId"
                            value="<?php echo isset($row['FacultyId']) ? $row['FacultyId'] : ''; ?>" <?php echo isset($row['FacultyId']) ? 'readonly' : ''; ?> required>


                        <label for="FacultyName">Enter Faculty Name : </label>
                        <input type="text" placeholder="Faculty Name" id="FacultyName" name="FacultyName"
                            value="<?php echo isset($row['FacultyName']) ? $row['FacultyName'] : ''; ?>" required>

                        <label for="SubjectCode">Enter Subject Code : </label>
                        <input type="text" placeholder="Subject Code" id="SubjectCode" name="SubjectCode"
                            value="<?php echo isset($row['SubjectCode']) ? $row['SubjectCode'] : ''; ?>" required>

                        <label for="Designation">Enter Designation : </label>
                        <select name="Designation" required>
                            <option value="Professor" <?php echo (isset($row['Designation']) && $row['Designation'] == 'Professor') ? 'selected' : ''; ?>>Professor</option>
                            <option value="Associate Professor" <?php echo (isset($row['Designation']) && $row['Designation'] == 'Associate Professor') ? 'selected' : ''; ?>>Associate Professor
                            </option>
                            <option value="Assistant Professor" <?php echo (isset($row['Designation']) && $row['Designation'] == 'Assistant Professor') ? 'selected' : ''; ?>>Assistant Professor
                            </option>
                            <option value="Department Chairperson" <?php echo (isset($row['Designation']) && $row['Designation'] == 'Department Chairperson') ? 'selected' : ''; ?>>Department
                                Chairperson</option>
                            <option value="Lecturer" <?php echo (isset($row['Designation']) && $row['Designation'] == 'Lecturer') ? 'selected' : ''; ?>>Lecturer</option>
                            <option value="Director" <?php echo (isset($row['Designation']) && $row['Designation'] == 'Director') ? 'selected' : ''; ?>>Director</option>
                        </select>

                        <label for="Department">Enter Department : </label>
                        <select name="Department" required>
                            <option value="MCA" <?php echo (isset($row['Department']) && $row['Department'] == 'MCA') ? 'selected' : ''; ?>>MCA</option>
                            <option value="MBA" <?php echo (isset($row['Department']) && $row['Department'] == 'MBA') ? 'selected' : ''; ?>>MBA</option>
                            <option value="PGDM" <?php echo (isset($row['Department']) && $row['Department'] == 'PGDM') ? 'selected' : ''; ?>>PGDM</option>
                            <option value="BBA" <?php echo (isset($row['Department']) && $row['Department'] == 'BBA') ? 'selected' : ''; ?>>BBA</option>
                            <option value="BCA" <?php echo (isset($row['Department']) && $row['Department'] == 'BCA') ? 'selected' : ''; ?>>BCA</option>
                        </select>

                        <label for="load">Enter Load : </label>
                        <select name="load" required>
                            <option value="1" <?php echo (isset($row['load']) && $row['load'] == '1') ? 'selected' : ''; ?>>1</option>
                            <option value="2" <?php echo (isset($row['load']) && $row['load'] == '2') ? 'selected' : ''; ?>>2</option>
                            <option value="3" <?php echo (isset($row['load']) && $row['load'] == '3') ? 'selected' : ''; ?>>3</option>
                        </select>


                        <div class="buttons">
                            <button
                                type="submit" onclick="window.location.href='../logout.php';"><?php echo isset($row['FacultyId']) ? 'Update' : 'Add Faculty'; ?></button>

                        </div>
                    </form>
                </div>

                <div class="faculty-table">
                    <?php include("facultyTable.php"); ?>
                </div>
    </div>
    </section>
    </main>
    </div>
</body>

</html>