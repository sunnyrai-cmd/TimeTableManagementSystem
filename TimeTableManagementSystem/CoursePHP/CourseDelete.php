<?php
include("connection.php");
if(isset($_GET['ID'])){
    $id=$_GET['ID'];
    $sql="DELETE FROM courseform WHERE CourseCode='$id'";
    if($conn->query($sql)==true){
        echo "<script>alert('Record deleted successfully');</script>";
        echo "<script>window.location.href='course.php';</script>";
    }else{
        echo "<script>alert('Error: " . $conn->error . "');</script>";
        echo "<script>window.location.href='course.php';</script>";
    }
}else{
    echo "<script>alert('Error: Course ID not provided.');</script>";
    echo "<script>window.location.href='course.php';</script>";
}
$conn->close();
?>