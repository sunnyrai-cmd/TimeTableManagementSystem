<?php include("connection.php"); 
?>
<?php


$RoomId=$_POST["RoomId"];   
$RoomName=$_POST["RoomName"];
$RoomType=$_POST["RoomType"];
$Location=$_POST["Location"];
$Capacity=$_POST["Capacity"];
$Availability=$_POST["Availability"];
$sql = "INSERT INTO `roomform` (`RoomId`, `RoomName`, `RoomType`, `Location`, `Capacity`, `Availability`)
VALUES ('$RoomId', '$RoomName', '$RoomType', '$Location', '$Capacity', '$Availability')";
if ($RoomId == "" || $RoomName == "" || $RoomType == "" || $Location == "" || $Capacity == "" || $Availability == "") {
    echo "<script>alert('Please fill all fields');</script>";
    echo "<script>window.location.href='room.php';</script>";
    exit;
}
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('record inserted successfully');</script>";
    echo "<script>window.location.href='room.php';</script>";
} else {
    $error = addslashes($conn->error); 
    echo "<script>alert('Error: $error'); window.location.href='room.php';</script>";
}


