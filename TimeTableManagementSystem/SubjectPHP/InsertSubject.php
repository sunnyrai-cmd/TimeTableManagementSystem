<?php include("connection.php"); 
?>
<?php
$Course=$_POST["Course"];
$Semester=$_POST["Semester"];
$SubjectCode=$_POST["SubjectCode"];
$SubjectName=$_POST["SubjectName"];
$Duration= $_POST["Duration"];
$FacultyId=$_POST["FacultyId"];
$FacultyName= $_POST["FacultyName"];
$LectureRequired= $_POST["LectureRequired"];

$sql = "INSERT INTO `subjectform` (`Course`, `Semester`, `SubjectCode`, `SubjectName`, `Duration`, `FacultyId`, `FacultyName`,LectureRequired) 
VALUES ('$Course','$Semester', '$SubjectCode', '$SubjectName', '$Duration', '$FacultyId', '$FacultyName','$LectureRequired')";
if ($Course == "" ||$Semester==""|| $SubjectCode == "" || $SubjectName == "" || $Duration == "" || $FacultyId == "" || $FacultyName == "" || $LectureRequired == "") {
    echo "<script>alert('Please fill all fields');</script>";
    echo "<script>window.location.href='Subject.php';</script>";
    exit;
}
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('record inserted successfully');</script>";
    echo "<script>window.location.href='Subject.php';</script>";
} else {
    $error = addslashes($conn->error); 
    echo "<script>alert('Error: $error'); window.location.href='faculty.php';</script>";
}
$conn->close();
?>