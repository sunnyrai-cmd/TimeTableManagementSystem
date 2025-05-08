<?php
include 'connection.php';

$course = $_POST['course'];
$semester = $_POST['semester'];

$sql = "SELECT DISTINCT SubjectCode FROM courseform WHERE Course='$course' AND Semester='$semester'";
$result = $conn->query($sql);

echo "<option value=''>Select Subject Code</option>";
while ($row = $result->fetch_assoc()) {
    echo "<option value='" . $row['SubjectCode'] . "'>" . $row['SubjectCode'] . "</option>";
}
?>
