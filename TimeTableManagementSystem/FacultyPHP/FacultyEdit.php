<?php
include("connection.php");   

if (isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Popup</title>
    <link rel="stylesheet" href="../CSS/FacultyEdit.css">
    <link rel="stylesheet" href="CSS/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap"rel="stylesheet">
</head>
<body>
<div class="hide">
    <?php include("../faculty.php"); ?>
    </div>


    <div class="Editcontainer">
    <div class="cancel">
    <button id="cancel-btn" >Cancel</button>
    <div>
        <h1>Update Faculty</h1>
    </div>
        <br />

        <form action="FacultyUpdate.php" method="post">
            <input type="hidden" name="id" value="<?php echo $row['FacultyId']; ?>">
            <label for="FacultyName">Enter Faculty Name:</label>
            <input type="text" name="FacultyName" id="FacultyName" value="<?php echo $row['FacultyName']; ?>"><br />
            <label for="SubjectCode">Enter Subject Code:</label>
            <input type="text" name="SubjectCode" id="SubjectCode" value="<?php echo $row['SubjectCode']; ?>"><br />
            <label for="Designation">Designation:</label>
            <select name="Designation" required>
                <option value="<?php echo $row['Designation']; ?>"><?php echo $row['Designation']; ?></option>
                <option value="Professor">Professor</option>
                <option value="Associate Professor">Associate Professor</option>
                <option value="Assistant Professor">Assistant Professor</option>
                <option value="Lecturer">Lecturer</option>
            </select><br />
            <label for="Department">Department:</label>
            <select name="Department" required>
                <option value="<?php echo $row['Department']; ?>"><?php echo $row['Department']; ?></option>
                <option value="CSE">CSE</option>
                <option value="IT">IT</option>
                <option value="ECE">ECE</option>
                <option value="ME">ME</option>
            </select><br />
            <label for="FacultyLoad">Load:</label>
            <input type="text" name="FacultyLoad" id="FacultyLoad" value="<?php echo $row['FacultyLoad']; ?>"><br />
            <div class="update">
            <button type="submit" >Update</button>
            </div>
        </form>
        <script>
            document.getElementById("cancel-btn").onclick = function() {
                window.location.href = "../faculty.php";
            };
        </script>
    </div>
</body>
</html>
