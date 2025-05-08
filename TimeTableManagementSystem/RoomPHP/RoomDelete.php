<?php
include("connection.php");
if(isset($_GET['ID'])) {
    $id = $_GET['ID'];
    $sql = "DELETE FROM roomform WHERE RoomId = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Record deleted successfully');</script>";
        echo "<script>window.location.href='room.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
        echo "<script>window.location.href='room.php';</script>";
    }
} else {
    echo "<script>alert('Error: Room ID not provided.');</script>";
    echo "<script>window.location.href='room.php';</script>";
}