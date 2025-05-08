<?php
include("connection.php");

$originalCode = $_POST['originalCourseCode'];
$Course = $_POST['Course'];
$CourseCode = $_POST['CourseCode'];
$CourseDuration = $_POST['CourseDuration'];
$semester = $_POST['semester'];
$SubjectCode = $_POST['SubjectCode'];
$SubjectName = $_POST['SubjectName'];
$SubjectType = $_POST['SubjectType'];

$sql = "UPDATE courseform SET
            Course = '$Course',
            CourseCode = '$CourseCode',
            CourseDuration = '$CourseDuration',
            semester = '$semester',
            SubjectCode = '$SubjectCode',
            SubjectName = '$SubjectName',
            SubjectType = '$SubjectType'
        WHERE CourseCode = '$originalCode'";

if ($conn->query($sql) === TRUE) {
    header("Location: course.php");
    exit();
} else {
    echo "Error updating record: " . $conn->error;
}
$conn->close();
?>
