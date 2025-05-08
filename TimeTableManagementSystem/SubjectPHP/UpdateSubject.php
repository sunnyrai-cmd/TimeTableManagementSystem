<?php
include("connection.php");

if(isset($_POST["SubjectCode"])){
    $subjectcode = $_POST["SubjectCode"];
    $course = $_POST["Course"];
    $semester = $_POST["Semester"];
    $subjectname = $_POST["SubjectName"];
    $duration = $_POST["Duration"];
    $facultyid = $_POST["FacultyId"];
    $facultyname = $_POST["FacultyName"];
    $lecturerequired = $_POST["LectureRequired"];


    $sql = "UPDATE `subjectform` SET `Course`='$course',`Semester`='$semester',`SubjectName`='$subjectname',`Duration`='$duration',`FacultyId`='$facultyid',`FacultyName`='$facultyname',`LectureRequired`='$lecturerequired' WHERE `SubjectCode`='$subjectcode'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Update Successfully');</script>";
        echo "<script>window.location.href='Subject.php';</script>";
    } else {
        echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
        echo "<script>window.location.href='Subject.php';</script>";
    }
} else {
    echo "<script>alert('Error: Subject Code not provided.');</script>";
    echo "<script>window.location.href='Subject.php';</script>";
}
?>
