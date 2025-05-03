<?php
    include('db_connection.php');
    $sql = "SELECT * FROM venueData";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Venue Database</title>
</head>
<body>
<div class="container">
        <nav>
            <ul>
                <li><a href="manager_createForm.php">Add New Venue</a></li>
            </ul>
        </nav>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Venue Name</th>
                        <th>City Address</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    include('db_connection.php');
                    $sql = " SELECT v.*, 
                            p.range AS priceRangeText, 
                            m.managerFname, 
                            m.managerLname 
                        FROM venueData v
                        LEFT JOIN priceRange p ON v.priceRange = p.priceRange
                        LEFT JOIN managerData m ON v.managerID = m.managerID
                        ORDER BY v.venueID ASC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['venueID'] . "</td>";
                            echo "<td>" . $row['venueName'] . "</td>";
                            echo "<td>" . $row['barangayAddress'] . "</td>";
                            echo "<td>" . $row['cityAddress'] . "</td>";
                            echo "<td>" . $row['maxCapacity'] . "</td>";
                            echo "<td>" . $row['inti'] . "</td>";
                            echo "<td>" . $row['bsn'] . "</td>";
                            echo "<td>" . $row['fun'] . "</td>";
                            echo "<td>" . $row['casu'] . "</td>";
                            echo "<td>" . $row['availabilityDays'] . "</td>";
                            echo "<td>" . $row['amAvail'] . "</td>";
                            echo "<td>" . $row['nnAvail'] . "</td>";
                            echo "<td>" . $row['pmAvail'] . "</td>";
                            echo "<td>" . $row['evntPLan'] . "</td>";
                            echo "<td>" . $row['EqmRentals'] . "</td>";
                            echo "<td>" . $row['DecServ'] . "</td>";
                            echo "<td>" . $row['siteStaff'] . "</td>";
                            echo "<td>" . $row['techSup'] . "</td>";
                            echo "<td>" . $row['pwdFriendly'] . "</td>";
                            echo "<td>" . $row['priceRangeText'] . "</td>";
                            echo "<td>" . $row['contactEmail'] . "</td>";
                            echo "<td>" . $row['contactNum'] . "</td>";
                            echo "<td>" . $row['managerLname'] . ', ' . $row['managerFname'] . "</td>";
                            echo "<td>" . $row['facebookLink'] . "</td>";
                            echo "<td>" . $row['instagramLink'] . "</td>";
                            echo "<td>" . $row['websiteLink'] . "</td>";
                            echo "<td>" . $row['walkInBook'] . "</td>";
                            echo "<td>" . $row['onlineBook'] . "</td>";
                            echo "<td>" . $row['phoneBook'] . "</td>";
                            echo "<td>" . $row['paymentMethods'] . "</td>";
                            echo "<td>" . $row['gmaps'] . "</td>";
                            echo "<td>" . $row['publicTranspo'] . "</td>";
                            echo "<td>" . $row['routes'] . "</td>";
                            echo "<td>" . $row['landmarks'] . "</td>";
                            echo "<td>" . $row['imgs'] . "</td>";
                            echo "<td>
                            <form action='manager_updateForm.php' method='GET'>
                                <input type='hidden' name='venueID' value='{$row['venueID']}'>
                                <button type='submit'>Update</button>
                            </form>
                            <form action='maanger_delete.php' method='POST' onsubmit=\"return confirm('Are you sure you want to delete {$row['venueName']}?');\">
                                <input type='hidden' name='countryCode' value='{$row['venueID']}'>
                                <button type='submit'>Delete</button>
                            </form>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='36'>No records found</td></tr>";
                    }
                    $conn->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>