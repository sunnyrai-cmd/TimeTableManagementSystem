<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../CSS/all.css">
    <!-- Then DataTables -->
    <!-- <link rel="stylesheet" href="DataTables/datatables.min.css"> -->
    <!-- <link href="DataTables/datatables.min.css" rel="stylesheet"> -->
    <script src="DataTables/datatables.min.js"></script>
</head>
<body>
    

<?php
include("connection.php");
$sql = "SELECT * FROM subjectform";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table id='myTable'>
    <thead>
        <tr>
            <th>Course</th>
            <th>Semester</th>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Duration</th>
            <th>Faculty Id</th>
            <th>Faculty Name</th>
            <th>Lecture Required</th>
            <th>Edit/Delete</th>
        </tr>
    </thead><tbody>";

    while ($subjRow = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . $subjRow["Course"] . "</td>
            <td>" . $subjRow["Semester"] . "</td>
            <td>" . $subjRow["SubjectCode"] . "</td>
            <td>" . $subjRow["SubjectName"] . "</td>
            <td>" . $subjRow["Duration"] . "</td>
            <td>" . $subjRow["FacultyId"] . "</td>
            <td>" . $subjRow["FacultyName"] . "</td>
            <td>" . $subjRow["LectureRequired"] . "</td>
            <td>
                <a href='subject.php?ID=" . $subjRow['SubjectCode'] . "'>Edit</a> 
                <a href='SubjectDelete.php?ID=" . $subjRow['SubjectCode'] . "' onclick=\"return confirm('Are you sure you want to delete this subject?')\">Delete</a>
            </td>
        </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "0 results";
}
$conn->close();
?>
 <script>
        $(document).ready(function () {
    // DataTables
    $('#myTable').DataTable({
        ordering: true,
        paging: true,
        searching: true
    });
});
    </script>
</body>
</html>