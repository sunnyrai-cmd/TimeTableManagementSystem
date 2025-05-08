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
    

<?php include("connection.php"); 
?>
<?php 
echo"<h2>Faculty List</h2>";
  $sql = "SELECT * FROM facultyform";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    echo "<table id='myTable'>
     <thead>
         <tr>
             <th>Faculty Id</th>
             <th>Faculty Name</th>
             <th>Subject Code</th>
             <th>Designation</th>
             <th>Department</th>
             <th>Load</th>
             <th>Edit/Delete</th>
             </tr>
             </thead>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["FacultyId"] . "</td>
                <td>" . $row["FacultyName"] . "</td>
                <td>" . $row["SubjectCode"] . "</td>
                <td>" . $row["Designation"] . "</td>
                <td>" . $row["Department"] . "</td>
                <td>" . $row["FacultyLoad"] . "</td>
                <td><a href='faculty.php?ID=" . $row['FacultyId'] . "'>Edit</a> <a href='FacultyDelete.php?ID=" . $row['FacultyId'] . "'>Delete</a> </td>
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