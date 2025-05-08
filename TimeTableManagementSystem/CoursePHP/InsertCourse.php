<?php 
include("connection.php");
?>
<?php
$Course=$_POST["Course"];
$CourseCode=$_POST["CourseCode"];
$CourseDuration=$_POST["CourseDuration"];
$CourseType=$_POST["CourseType"];
$semester=$_POST["semester"];
$SubjectCode=$_POST["SubjectCode"];
$SubjectName=$_POST["SubjectName"];
$SubjectType=$_POST["SubjectType"];

$sql = "INSERT INTO `courseform` (`Course`, `CourseCode`, `CourseDuration`, `Semester`, `SubjectCode`, `SubjectName`, `SubjectType`)
VALUES ('$Course', '$CourseCode', '$CourseDuration', '$semester', '$SubjectCode', '$SubjectName', '$SubjectType')";
if($conn->query($sql)==true){
    echo "<script>alert('record inserted successfully');</script>";
    echo "<script>window.location.href='course.php';</script>";
}else{
    $error = addslashes($conn->error); 
    echo "<script>alert('Error: $error'); window.location.href='course.php';</script>";
}
$conn->close();

?>