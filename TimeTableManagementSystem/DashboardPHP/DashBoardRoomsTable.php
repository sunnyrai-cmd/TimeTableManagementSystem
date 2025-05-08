<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms List</title>
    <link rel="stylesheet" href="../CSS/HomeSubjectsTable.css">
</head>
<body>
    <h1>Rooms List</h1>
    <div class="container"></div>
    <?php
include("connection.php");
$sql = "SELECT * FROM roomform";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<table>
    <thead>
    <tr>
    <th>Room Id</th>
    <th>Room Name</th>
    <th>Location</th>
    <th>Room Type</th>
    <th>Room Capacity</th>
    <th>Room Status</th>
    </tr>
    </thead><tbody>";
    
    while ($roomRow = $result->fetch_assoc()) {
        echo "<tr>
        <td>" . $roomRow["RoomId"] . "</td>
            <td>" . $roomRow["RoomName"] . "</td>
            <td>" . $roomRow["Location"] . "</td>
            <td>" . $roomRow["RoomType"] . "</td>
            <td>" . $roomRow["Capacity"] . "</td>
            <td>" . $roomRow["Availability"] . "</td>
            </tr>";
        }
        
        echo "</tbody></table>";
    } else {
        echo "0 results";
    }
    $conn->close();
    
    ?>
</body>
</html>