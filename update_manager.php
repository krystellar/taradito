<?php
    include('db_connection.php');

    if (isset($_GET['managerID'])) {
        $managerID = $_GET['managerID'];
        $result = $conn->query("SELECT * FROM managerData WHERE managerID = '$managerID'");
        $row = $result->fetch_assoc();

        if (!$row) {
            echo "<script>alert('Manager not found.'); window.location.href = 'view_manager.php';</script>";
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $sql = "UPDATE managerData 
                SET firstname = '$firstName', lastname = '$lastName'
                WHERE managerID = '$managerID'";

        if ($conn->query($sql)) {
            echo "<script>alert('Manager updated successfully.'); window.location.href = 'view_manager.php';</script>";
        } else {
            echo "<script>alert('Error updating manager.');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Manager | Venue Database</title>
</head>
<body>
    <h2>Update Manager</h2>

    <form action="update_manager.php?managerID=<?php echo $managerID; ?>" method="POST">
        <label>First Name: <input type="text" name="firstName" value="<?php echo $row['firstName']; ?>" required></label><br>
        <label>Last Name: <input type="text" name="lastName" value="<?php echo $row['lastName']; ?>" required></label><br>

        <button type="submit">Update Manager</button>
    </form>

    <br>
    <a href="view_manager.php">Back to Manager List</a>
</body>
</html>
