<?php
include 'connection.php';

$subjectCode = $_POST['subjectCode'];

$sql = "SELECT SubjectName FROM courseform WHERE SubjectCode='$subjectCode' LIMIT 1";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo $row['SubjectName'];
?>
