<?php
include("connection.php");

$full_name = $_POST["full_name"];
$email= $_POST["email"];
$password= $_POST["password"];
$dob= $_POST["dob"];
$gender= $_POST["gender"];
$phone_number= $_POST["ph_no"];
$role= $_POST["role"];
$file_id=NULL;
if(isset($_FILES["uploadprofile"])){
    $filename=$_FILES["uploadprofile"]["name"];
    $tempname=$_FILES["uploadprofile"]["tmp_name"];
    $folder = "../uploads/{$filename}";
    
    if(!move_uploaded_file($tempname, $folder)){
        echo "Failed to upload file.";
    } 
    if(isset($folder)){
        $profile_sql = $conn->prepare("INSERT INTO `file`(`file_path`) VALUES(?)");
        $profile_sql->bind_param("s", $folder);
        if ($profile_sql->execute()) {
            $file_id = $conn->insert_id; 
        } else {
            echo "File uploaded but failed to insert into database.";
        }
    }
    }

$register_sql= "INSERT INTO `registrationform`(`full_name`,`email`,`password`,`dob`,`gender`,`phone_number`,`role`,`profileid`)
VALUES('$full_name','$email','$password','$dob','$gender','$phone_number','$role','$file_id')";
if($conn->query($register_sql) === TRUE) {
    if ($file_id !== NULL) {
        echo '<script>alert("Record inserted successfully");</script>';
    } else {
        echo '<script>alert("Record NoT inserted ");</script>';
    }
    echo '<script>window.location.href = "../Login/login.php";</script>';
}
else if ($conn->error) {
    echo "Error: $register_sql<br>{$conn->error}";
} else {
    echo "Error: $register_sql<br>{$conn->error}";
}
$conn->close();
?>