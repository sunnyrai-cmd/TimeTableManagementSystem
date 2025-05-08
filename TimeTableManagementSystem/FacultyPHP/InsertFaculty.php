<?php include("connection.php"); 
?>
<?php
$facultyid=$_POST["FacultyId"];
$facultname=$_POST["FacultyName"];
$subjectcode=$_POST["SubjectCode"];
$designation= $_POST["Designation"];
$departmemt= $_POST["Department"];
$load= $_POST["load"];



$sql = "INSERT INTO `facultyform` (`FacultyId`, `FacultyName`, `SubjectCode`, `Designation`, `Department`, `FacultyLoad`) 
VALUES ('$facultyid', '$facultname', '$subjectcode', '$designation', '$departmemt', '$load')";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('record inserted successfully');</script>";
    echo "<script>window.location.href='faculty.php';</script>";
} else {
    $error = addslashes($conn->error); 
    echo "<script>alert('Error: $error'); window.location.href='faculty.php';</script>";
}
$conn->close();
?>