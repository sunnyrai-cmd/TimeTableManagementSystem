<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects Table</title>
    <link rel="stylesheet" href="../CSS/HomeSubjectsTable.css">
</head>
<body>
    <h1>Subjects Lists</h1>
    <div class="container"></div>
    <?php
include("connection.php");
$sql = "SELECT * FROM subjectform";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table>
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