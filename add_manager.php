<?php
    include('db_connection.php');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
    
        $sql = "INSERT INTO managerData (firstname, lastname)
                VALUES ('$firstName', '$lastName')";
    
        if ($conn->query($sql)) {
            echo "<script>
                alert('New manager added successfully.');
                window.location.href = 'view_manager.php';
            </script>";
        } else {
            echo "<script>alert('Error adding manager.');</script>";
        }
    }    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Manager | Venue Database</title>
</head>
<body>
    <h2>Create New Manager</h2>
    <form action="add_manager.php" method="POST">
        <label>First Name: <input type="text" name="firstName" required></label><br>
        <label>Last Name: <input type="text" name="lastName" required></label><br>

        <button type="submit">Add Manager</button>
    </form>
    <a href="view_manager.php">Back to Manager List</a>
</body>
</html>
