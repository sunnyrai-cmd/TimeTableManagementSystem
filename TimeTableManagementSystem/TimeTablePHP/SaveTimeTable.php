<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_timetable'])) {
    // Get form data
    $course = $_POST["course"] ?? '';
    $semester = $_POST["semester"] ?? '';
    $section = $_POST["section"] ?? '';
    $subjectDuration = $_POST["subject-duration"] ?? 60;
    $lunchDuration = $_POST["lunch-duration"] ?? 45;
    $selectedDays = $_POST['days'] ?? [];
    $subjectAssignments = $_POST['subject'] ?? [];
    $roomAssignments = $_POST['room'] ?? [];

    // Clear old timetable entries
    $deleteQuery = "DELETE FROM timetable WHERE course = ? AND Semester = ? AND Section = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("sss", $course, $semester, $section);
    $stmt->execute();
    $stmt->close();

    // Process each day
    foreach ($selectedDays as $day) {
        if (!isset($subjectAssignments[$day])) continue;

        foreach ($subjectAssignments[$day] as $time => $subjectCode) {
            if (empty($subjectCode)) continue;

            $roomId = $roomAssignments[$day][$time] ?? '';
            if (empty($roomId)) continue;

            // Get subject details
            $subjectQuery = "SELECT SubjectName FROM subjectform WHERE SubjectCode = ?";
            $stmt = $conn->prepare($subjectQuery);
            $stmt->bind_param("s", $subjectCode);
            $stmt->execute();
            $subjectResult = $stmt->get_result();
            $subject = $subjectResult->fetch_assoc();
            $stmt->close();

            // Get room details
            $roomQuery = "SELECT RoomName FROM roomform WHERE RoomId = ?";
            $stmt = $conn->prepare($roomQuery);
            $stmt->bind_param("s", $roomId);
            $stmt->execute();
            $roomResult = $stmt->get_result();
            $room = $roomResult->fetch_assoc();
            $stmt->close();

            // Get faculty details
            $facultyQuery = "SELECT FacultyId, FacultyName FROM facultyform WHERE SubjectCode = ? LIMIT 1";
            $stmt = $conn->prepare($facultyQuery);
            $stmt->bind_param("s", $subjectCode);
            $stmt->execute();
            $facultyResult = $stmt->get_result();
            $faculty = $facultyResult->fetch_assoc() ?? ['FacultyId' => 0, 'FacultyName' => ''];
            $stmt->close();

            // Calculate end time
            $endTime = date('H:i', strtotime("+$subjectDuration minutes", strtotime($time)));

            // Insert into timetable
            $insertQuery = "INSERT INTO timetable 
                (course, Semester, Section, SubjectCode, SubjectName, RoomId, RoomName, start_time, end_time, day_of_week, FacultyId, FacultyName) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param(
                "ssssssssssis",
                $course,
                $semester,
                $section,
                $subjectCode,
                $subject['SubjectName'],
                $roomId,
                $room['RoomName'],
                $time,
                $endTime,
                $day,
                $faculty['FacultyId'],
                $faculty['FacultyName']
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redirect to the timetable page
    header("Location: GeneratedTimeTable.php?success=1");
    exit();
} else {
    // If accessed directly or no form data
    header("Location: timetable.php");
    exit();
}
?>