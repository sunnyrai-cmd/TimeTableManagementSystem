<?php
include("connection.php");
if (isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $sql = "DELETE FROM facultyform WHERE FacultyId = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Record deleted successfully');</script>";
        echo "<script>window.location.href='faculty.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
        echo "<script>window.location.href='faculty.php';</script>";
    }
} else {
    echo "<script>alert('Error: Faculty ID not provided.');</script>";
    echo "<script>window.location.href='faculty.php';</script>";
}
?>