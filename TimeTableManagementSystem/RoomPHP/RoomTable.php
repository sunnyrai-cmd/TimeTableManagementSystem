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

    
</body>
</html><?php
include("connection.php");
?>
<?php
$sql="SELECT * FROM roomform";
$result=mysqli_query($conn,$sql);
if($result->num_rows>0){
    echo "<table id='myTable'>
    <thead>
        <tr>
            <th>Room Id</th>
            <th>Room Name</th>
            <th>Location</th>
            <th>Room Type</th>
            <th>Capacity</th>
            <th>Availability</th>
            <th>Edit/Delete</th>
        </tr>
    </thead>";
    while($row=$result->fetch_assoc()){
      echo "<tr>
            <td>" . $row["RoomId"] . "</td>
            <td>" . $row["RoomName"] . "</td>
            <td>" . $row["Location"] . "</td>
            <td>" . $row["RoomType"] . "</td>
            <td>" . $row["Capacity"] . "</td>
            <td>" . $row["Availability"] . "</td>
            <td><a href='room.php?ID=" . $row['RoomId'] . "'>Edit</a> <a href='RoomDelete.php?ID=" . $row['RoomId'] . "'>Delete</a> </td>
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
</head>
</body>

