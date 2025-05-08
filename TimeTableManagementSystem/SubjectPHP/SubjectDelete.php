<?php
include("connection.php");
if (isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $sql = "DELETE FROM subjectform WHERE SubjectCode = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Record deleted successfully');</script>";
        echo "<script>window.location.href='Subject.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
        echo "<script>window.location.href='Subject.php';</script>";
    }
} else {
    echo "<script>alert('Error: Subject Code not provided.');</script>";
    echo "<script>window.location.href='Subject.php';</script>";
}
$conn->close();
?>