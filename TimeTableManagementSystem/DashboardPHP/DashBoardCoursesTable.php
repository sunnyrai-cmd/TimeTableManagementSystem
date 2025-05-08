<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course List</title>
    <link rel="stylesheet" href="../CSS/HomeSubjectsTable.css">
</head>
<body>
    <h1>Course Lists</h1>
    <div class="container"></div>
    <?php
include("connection.php");
$sql = "SELECT * FROM courseform";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table>
    <thead>
    <tr>
    <th>Course</th>
    <th>Course Code</th>
    <th>Duration</th>
    <th>Semester</th>
    <th>Subject Code</th>
    <th>Subject Name</th>
    </tr>
    </thead><tbody>";
    
    while ($courseRow = $result->fetch_assoc()) {
        echo "<tr>
        <td>" . $courseRow["Course"] . "</td>
            <td>" . $courseRow["CourseCode"] . "</td>
            <td>" . $courseRow["CourseDuration"] . "</td>
            <td>" . $courseRow["semester"] . "</td>
            <td>" . $courseRow["SubjectCode"] . "</td>
            <td>" . $courseRow["SubjectName"] . "</td>
            </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "0 results";
    }
    $conn->close();
    
    ?>
    </div>
</body>
</html>