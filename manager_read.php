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
                <li><a href="add_manager.php">Add Manager</a></li>
                <li><a href="view_manager.php">View Manager List</a></li>
            </ul>
        </nav>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Venue ID</th>
                        <th>Venue Name</th>
                        <th>Barangay Address</th>
                        <th>City Address</th>
                        <th>Max Capacity</th>
                        <th>Intimate</th>
                        <th>Business</th>
                        <th>Fun</th>
                        <th>Casual</th>
                        <th>Availability Days</th>
                        <th>AM Availability</th>
                        <th>NN Availability</th>
                        <th>PM Availability</th>
                        <th>Event Planner</th>
                        <th>Equipment Rentals</th>
                        <th>Decoration Services</th>
                        <th>Onsite Staff</th>
                        <th>Tech Support</th>
                        <th>PWD Friendly</th>
                        <th>Price Range</th>
                        <th>Contact Email</th>
                        <th>Contact Number</th>
                        <th>Manager Name</th>
                        <th>Facebook Link</th>
                        <th>Instagram Link</th>
                        <th>Website Link</th>
                        <th>Walk-in Booking</th>
                        <th>Online Booking</th>
                        <th>Phone Booking</th>
                        <th>Pay Cash</th>
                        <th>Pay Bank</th>
                        <th>Pay Electronic</th>
                        <th>Google Maps</th>
                        <th>Public Transport</th>
                        <th>Routes</th>
                        <th>Landmarks</th>
                        <th>Images</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    include('db_connection.php');
                    $sql = "SELECT v.*, 
                        p.priceRangeText, 
                        m.firstName, 
                        m.lastName 
                        FROM venueData v
                        LEFT JOIN priceRange p ON v.priceRangeID = p.priceRangeID
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
                            echo "<td>" . $row['intimate'] . "</td>";
                            echo "<td>" . $row['business'] . "</td>";
                            echo "<td>" . $row['fun'] . "</td>";
                            echo "<td>" . $row['casual'] . "</td>";
                            echo "<td>" . $row['availabilityDays'] . "</td>";
                            echo "<td>" . $row['amAvail'] . "</td>";
                            echo "<td>" . $row['nnAvail'] . "</td>";
                            echo "<td>" . $row['pmAvail'] . "</td>";
                            echo "<td>" . $row['eventPlanner'] . "</td>";
                            echo "<td>" . $row['equipRentals'] . "</td>";
                            echo "<td>" . $row['decoServices'] . "</td>";
                            echo "<td>" . $row['onsiteStaff'] . "</td>";
                            echo "<td>" . $row['techSupport'] . "</td>";
                            echo "<td>" . $row['pwdFriendly'] . "</td>";
                            echo "<td>" . $row['priceRangeID'] . "</td>";
                            echo "<td>" . $row['contactEmail'] . "</td>";
                            echo "<td>" . $row['contactNum'] . "</td>";
                            echo "<td>" . $row['lastName'] . ', ' . $row['firstName'] . "</td>";
                            echo "<td>" . $row['facebookLink'] . "</td>";
                            echo "<td>" . $row['instagramLink'] . "</td>";
                            echo "<td>" . $row['websiteLink'] . "</td>";
                            echo "<td>" . $row['walkInBook'] . "</td>";
                            echo "<td>" . $row['onlineBook'] . "</td>";
                            echo "<td>" . $row['phoneBook'] . "</td>";
                            echo "<td>" . $row['payCash'] . "</td>";
                            echo "<td>" . $row['payBank'] . "</td>";
                            echo "<td>" . $row['payElectronic'] . "</td>";
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
                            <form action='manager_delete.php' method='POST' onsubmit=\"return confirm('Are you sure you want to delete {$row['venueName']}?');\">
                                <input type='hidden' name='venueID' value='{$row['venueID']}'>
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