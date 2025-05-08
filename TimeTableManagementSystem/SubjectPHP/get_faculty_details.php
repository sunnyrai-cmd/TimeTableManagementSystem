<?php
include("connection.php");

if (isset($_POST['subjectCode'])) {
    $subjectCode = $_POST['subjectCode'];
    $response = [];

    $stmt = $conn->prepare("SELECT FacultyId, FacultyName FROM facultyform WHERE SubjectCode = ?");
    $stmt->bind_param("s", $subjectCode);
    $stmt->execute();
    $stmt->bind_result($facultyId, $facultyName);
    $stmt->fetch();
    $stmt->close();

    $response['facultyId'] = $facultyId ?? '';
    $response['facultyName'] = $facultyName ?? '';

    echo json_encode($response);
}
?>
