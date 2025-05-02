<?php
    include('db_connection.php');
    $priceRangeDropDown = $conn->query("SELECT priceRange, range FROM priceRange");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Append | Countries Database</title>
    <link rel="stylesheet" href="form.css">
</head>
<body>
    <h2>Add a New Venue Record</h2>
    <form action="manager_create.php" method="POST">
        <h3>Basic Information</h3>
        <div class="form-section">
            <label>venueName: <input type="text" name="venueName" required></label>
            <label>Barangay Address: <input type="text" name="barangayAddress" required></label>
            <label>City Address: <input type="text" name="cityAddress" required></label>
            <label>Max Capacity: <input type="number" name="maxCapacity"></label>
            <label>Intimate: <input type="checkbox" name="intimate" value="0"></label>
            <label>Business: <input type="checkbox" name="business" value="0"></label>
            <label>Fun: <input type="checkbox" name="fun" value="0"></label>
            <label>Casual: <input type="checkbox" name="casual" value="0"></label>
            <label>Availability Days:
                <select name="availabilityDays" required>
                    <option value="">Select Availability</option>
                    <option value="Everyday">Everyday</option>
                    <option value="Everyday (except holidays)">Everyday (except holidays)</option>
                    <option value="Weekdays only">Weekdays only</option>
                    <option value="Weekends only">Weekends only</option>
                    <option value="Monday to Saturday">Monday to Saturday</option>
                </select>
            </label>
            <label>Start Time: <input type="time" name="startTime" required></label>
            <label>End Time: <input type="time" name="endTime" required></label>
            <label>Price Range:
                <select name="priceRange" required>
                    <option value="">Select Price Range</option>
                    <?php
                    while ($priceRange = $priceRangeDropDown->fetch_assoc()) {
                        echo "<option value='{$priceRange['priceRange']}'>{$priceRange['range']}</option>";
                    }
                    ?>
                </select>
            </label>
        </div>

        <h3>More info</h3>
        <div class="form-grid">
            <label>Event Planner: <input type="checkbox" name="evntPlan" value="0"></label>
            <label>Equipment Rentals: <input type="checkbox" name="EqmRentals" value="0"></label>
            <label>Decorate Services: <input type="checkbox" name="DecServ" value="0"></label>
            <label>Onsite Staff: <input type="checkbox" name="siteStaff" value="0"></label>
            <label>Tech Support: <input type="checkbox" name="techsup" value="0"></label>
            <label>PWD Friendly: <input type="checkbox" name="pwdFriendly" value="0"></label>
            <label>Contact Email: <input type="email" name="contactEmail"></label>
            <label>Contact Number: <input type="text" name="contactNum"></label>
            <label>Venue Manager Last Name: <input type="text" name="venueManagerLname"></label>
            <label>Venue Manager First Name: <input type="text" name="venueManagerFname"></label>
            <label>Facebook Link: <input type="url" name="facebookLink"></label>
            <label>Instagram Link: <input type="url" name="instagramLink"></label>
            <label>Website Link: <input type="url" name="websiteLink"></label>
            <label>Walk-in Booking: <input type="checkbox" name="walkInBook" value="0"></label>
            <label>Online Booking: <input type="checkbox" name="onlineBook" value="0"></label>
            <label>Phone Booking: <input type="checkbox" name="phoneBook" value="0"></label>
            <label>Cash Payment: <input type="checkbox" name="payCash" value="0"></label>
            <label>Bank Payment: <input type="checkbox" name="payBank" value="0"></label>
            <label>Electronic Payment: <input type="checkbox" name="payElectronic" value="0"></label>
            <label>Google Maps Link: <input type="url" name="gmaps"></label>
            <label>Public Transport: <input type="text" name="publicTranspo"></label>
            <label>Routes: <input type="text" name="routes"></label>
        </div>
        <button type="submit" class="submit-btn">Add Venue</button>
    </form>

</body>
</html>
