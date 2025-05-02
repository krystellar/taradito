<?php
    include('db_connection.php');

    if (isset($_GET['venueID'])) {
        $venueID = $_GET['venueID'];

        $result = $conn->query("SELECT * FROM venueData WHERE venueID = '$venueID'");
        $row = $result->fetch_assoc();
    }
?>

<!-- html skeleton form for updating venue data form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update | Venue Database</title>
</head>
<body>
    <h2>Update venue Record</h2>
    <form action="manager_update.php" method="POST">
        <input type="hidden" name="venueID" value="<?php echo $row['venueID']; ?>">

        <h3>Basic Information</h3>
        <div class="form-section">
            <label>Venue Name: <input type="text" name="venueName" value="<?php echo $row['venueName']; ?>" required></label>
            <label>Barangay Address: <input type="text" name="barangayAddress" value="<?php echo $row['barangayAddress']; ?>" required></label>
            <label>City Address: <input type="text" name="cityAddress" value="<?php echo $row['cityAddress']; ?>" required></label>
            <label>Max Capacity: <input type="number" name="maxCapacity" value="<?php echo $row['maxCapacity']; ?>"></label>
            <label>Intimate: <input type="checkbox" name="intimate" value="0" <?php echo $row['intimate'] ? 'checked' : ''; ?>></label>
            <label>Business: <input type="checkbox" name="business" value="0" <?php echo $row['business'] ? 'checked' : ''; ?>></label>
            <label>Fun: <input type="checkbox" name="fun" value="0" <?php echo $row['fun'] ? 'checked' : ''; ?>></label>
            <label>Casual: <input type="checkbox" name="casual" value="0" <?php echo $row['casual'] ? 'checked' : ''; ?>></label>
            <label>Availability Days:
                <select name="availabilityDays">
                    <option value="">--Select Availability--</option>
                    <option value="Everyday" <?= $row['availabilityDays'] === 'Everyday' ? 'selected' : '' ?>>Everyday</option>
                    <option value="Everyday (except holidays)" <?= $row['availabilityDays'] === 'Everyday (except holidays)' ? 'selected' : '' ?>>Everyday (except holidays)</option>
                    <option value="Weekdays only" <?= $row['availabilityDays'] === 'Weekdays only' ? 'selected' : '' ?>>Weekdays only</option>
                    <option value="Weekends only" <?= $row['availabilityDays'] === 'Weekends only' ? 'selected' : '' ?>>Weekends only</option>
                    <option value="Monday to Saturday" <?= $row['availabilityDays'] === 'Monday to Saturday' ? 'selected' : '' ?>>Monday to Saturday</option>
                </select>
            </label>
            <label>Start Time: <input type="time" name="startTime" required></label>
            <label>End Time: <input type="time" name="endTime" required></label>
            <label>Price Range:
                <select name="priceRange" required>
                    <option value="">--Select Price Range--</option>
                    <option value="1" <?= $row['priceRange'] == 1 ? 'selected' : '' ?>>Below Php 5000</option>
                    <option value="2" <?= $row['priceRange'] == 2 ? 'selected' : '' ?>>Php 5000 - 10000</option>
                    <option value="3" <?= $row['priceRange'] == 3 ? 'selected' : '' ?>>Php 10000 - 15000</option>
                    <option value="4" <?= $row['priceRange'] == 4 ? 'selected' : '' ?>>Php 15000 - 20000</option>
                    <option value="5" <?= $row['priceRange'] == 5 ? 'selected' : '' ?>>Above 20000</option>
                </select>
            </label>
            <label>Google Maps Link: <input type="url" name="gmaps" value="<?php echo $row['gmaps']; ?>"></label>
            <label>Public Transport: <input type="text" name="publicTranspo" value="<?php echo $row['publicTranspo']; ?>"></label>
            <label>Routes: <input type="text" name="routes" value="<?php echo $row['routes']; ?>"></label>
        </div>

        <h3>More about the Venue</h3>
        <div class="form-grid">
            <label>Event Planner: <input type="checkbox" name="eventPlanner" value="0" <?php echo $row['eventPlanner'] ? 'checked' : ''; ?>></label>
            <label>Equipment Rentals: <input type="checkbox" name="equipmentRentals" value="0" <?php echo $row['equipmentRentals'] ? 'checked' : ''; ?>></label>
            <label>Decorate Services: <input type="checkbox" name="decorateServices" value="0" <?php echo $row['decorateServices'] ? 'checked' : ''; ?>></label>
            <label>Onsite Staff: <input type="checkbox" name="onsiteStaff" value="0" <?php echo $row['onsiteStaff'] ? 'checked' : ''; ?>></label>
            <label>Tech Support: <input type="checkbox" name="techSupport" value="0" <?php echo $row['techSupport'] ? 'checked' : ''; ?>></label>
            <label>PWD Friendly: <input type="checkbox" name="pwdFriendly" value="0" <?php echo $row['pwdFriendly'] ? 'checked' : ''; ?>></label>
            <label>Contact Email: <input type="email" name="contactEmail" value="<?php echo $row['contactEmail']; ?>"></label>
            <label>Contact Number: <input type="text" name="contactNum" value="<?php echo $row['contactNum']; ?>"></label>
            <label>Manager Last Name: <input type="text" name="managerLname" value="<?php echo $row['managerLname']; ?>"></label>
            <label>Manager First Name: <input type="text" name="managerFname" value="<?php echo $row['managerFname']; ?>"></label>
            <label>Facebook Link: <input type="url" name="fbLink" value="<?php echo $row['fbLink']; ?>"></label>
            <label>Instagram Link: <input type="url" name="igLink" value="<?php echo $row['igLink']; ?>"></label>
            <label>Website Link: <input type="url" name="webLink" value="<?php echo $row['webLink']; ?>"></label>
            <label>Walk-in Booking: <input type="checkbox" name="walkInBook" value="0" <?php echo $row['walkInBook'] ? 'checked' : ''; ?>></label>
            <label>Online Booking: <input type="checkbox" name="onlineBook" value="0" <?php echo $row['onlineBook'] ? 'checked' : ''; ?>></label>
            <label>Phone Booking: <input type="checkbox" name="phoneBook" value="0" <?php echo $row['phoneBook'] ? 'checked' : ''; ?>></label>
            <label>Payment Cash: <input type="checkbox" name="payCash" value="0" <?php echo $row['payCash'] ? 'checked' : ''; ?>></label>
            <label>Payment Bank: <input type="checkbox" name="payBank" value="0" <?php echo $row['payBank'] ? 'checked' : ''; ?>></label>
            <label>Payment Electronic: <input type="checkbox" name="payElectronic" value="0" <?php echo $row['payElectronic'] ? 'checked' : ''; ?>></label>
        </div>
    <button type="submit" class="submit-btn">Save Changes</button>
</form>
</body>
</html>
