<?php
include("connection.php");

if(isset($_POST["RoomId"])){
    $roomid=$_POST["RoomId"];
    $roomname=$_POST["RoomName"];
    $location=$_POST["Location"];
    $roomtype= $_POST["RoomType"];
    $capacity= $_POST["Capacity"];
    $availability= $_POST["Availability"];

    $sql = "UPDATE `roomform` SET `RoomName`='$roomname',`Location`='$location',`RoomType`='$roomtype',`Capacity`='$capacity',`Availability`='$availability' WHERE `RoomId`='$roomid'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Update Successfully');</script>";
        echo "<script>window.location.href='room.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
} else {
    echo "<script>alert('Error: Room ID not provided.');</script>";
    echo "<script>window.location.href='room.php';</script>";
}