<?php
    include('db_connection.php');
    $venueID = $_GET['venueID'] ?? null;
    $result = $conn->query("SELECT * FROM managerData");

    if (isset($_GET['deleteManagerID'])) {
        $managerIDToDelete = $_GET['deleteManagerID'];
        $conn->query("DELETE FROM managerData WHERE managerID = '$managerIDToDelete'");
        echo "<script>alert('Manager deleted successfully.'); window.location.href = 'view_manager.php';</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager List | Venue Database</title>
</head>
<body>
    <h2>Manager List</h2>
    <table>
        <thead>
            <tr>
                <th>Manager ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['managerID']; ?></td>
                    <td><?php echo $row['firstName']; ?></td>
                    <td><?php echo $row['lastName']; ?></td>
                    <td>
                        <a href="update_manager.php?managerID=<?php echo $row['managerID']; ?>">Edit</a> |
                        <a href="view_manager.php?deleteManagerID=<?php echo $row['managerID']; ?>" onclick="return confirm('Are you sure you want to delete this manager?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="add_manager.php">Add New Manager</a>
    <a href="manager_read.php">Back to Venue List</a>
</body>
</html>
