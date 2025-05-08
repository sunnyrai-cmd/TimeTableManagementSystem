<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty List</title>
    <link rel="stylesheet" href="../CSS/HomeSubjectsTable.css">
</head>
<body>
    <h1>Faculty Lists</h1>
    <div class="container"></div>
    <?php
include("connection.php");
$sql = "SELECT * FROM facultyform";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table>
    <thead>
    <tr>
    <th>Faculty Id</th>
    <th>Faculty Name</th>
    <th>Subject Code</th>
    <th>Designation</th>
    <th>Department</th>
    <th>Faculty Load</th>
    </tr>
    </thead><tbody>";
    
    while ($facultyRow = $result->fetch_assoc()) {
        echo "<tr>
        <td>" . $facultyRow["FacultyId"] . "</td>
            <td>" . $facultyRow["FacultyName"] . "</td>
            <td>" . $facultyRow["SubjectCode"] . "</td>
            <td>" . $facultyRow["Designation"] . "</td>
            <td>" . $facultyRow["Department"] . "</td>
            <td>" . $facultyRow["FacultyLoad"] . "</td>
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