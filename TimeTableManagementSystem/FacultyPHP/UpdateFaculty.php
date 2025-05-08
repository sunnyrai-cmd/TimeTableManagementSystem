<?php
include("connection.php");

if(isset($_POST["FacultyId"])){
    $facultyid=$_POST["FacultyId"];
    $facultname=$_POST["FacultyName"];
    $subjectcode=$_POST["SubjectCode"];
    $designation= $_POST["Designation"];
    $departmemt= $_POST["Department"];
    $load= $_POST["load"];

    $sql = "UPDATE `facultyform` SET `FacultyName`='$facultname',`SubjectCode`='$subjectcode',`Designation`='$designation',`Department`='$departmemt',`FacultyLoad`='$load' WHERE `FacultyId`='$facultyid'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Update Successfully');</script>";
        echo "<script>window.location.href='faculty.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
} else {
    echo "<script>alert('Error: Faculty ID not provided.');</script>";
    echo "<script>window.location.href='faculty.php';</script>";
}

?>