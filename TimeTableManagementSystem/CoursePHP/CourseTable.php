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
?>
<?php
$sql = "SELECT * FROM courseform";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table id='myTable'>
    <thead>
        <tr>
            <th>Course Code</th>
            <th>Course</th>
            <th>Course Duration</th>
            <th>Semester</th>
            <th>Subject Code</th>
            <th>Subject Name</th>
            <th>Subject Type</th>
            <th>Edit/Delete</th>
        </tr>
    </thead>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["CourseCode"] . "</td>
                <td>" . $row["Course"] . "</td>
                <td>" . $row["CourseDuration"] . "</td>
                <td>" . $row["semester"] . "</td>
                <td>" . $row["SubjectCode"] . "</td>
                <td>" . $row["SubjectName"] . "</td>
                <td>" . $row["SubjectType"] . "</td>
                <td>
                    <a href='course.php?ID=" . $row['CourseCode'] . "'>Edit</a>
                    <a href='CourseDelete.php?ID=" . $row['CourseCode'] . "'>Delete</a>
                </td>
              </tr>";
    }
    echo "</table>";
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