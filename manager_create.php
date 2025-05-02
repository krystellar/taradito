
<?php
    include('db_connection.php');
    /*AFTER MAKING THE VENUEID PRIMARY KEY AND AUTO INCREMENTED, USE:
    ALTER TABLE venueData 
    MODIFY venueID INT AUTO_INCREMENT PRIMARY KEY;
    */

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // venueId autoincremented
        $venueName = $_POST['venueName'];
        $barangayAddress = $_POST['barangayAddress'];
        $cityAddress = $_POST['cityAddress']; 
        $maxCapacity = $_POST['maxCapacity'];
        $intimate = $_POST['inti']; 
        $business = $_POST['busi'];
        $fun = $_POST['fun']; 
        $casual = $_POST['casu'];
        // availabilityDays will have a predefined set of values
        $validOptions = ['Everyday', 'Everyday (except holidays)', 'Weekdays only','Weekends only', 'Monday to Saturday'];
        $availabilityDays = $_POST['availabilityDays'];
        if (!in_array($availabilityDays, $validOptions)) {
            $availabilityDays = null;
        }
        // time availbility will be automatically set according to start time and end time
        $startTime = $_POST['startTime'];
        $endTime = $_POST['endTime'];
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $amStart = strtotime("06:00");
        $amEnd   = strtotime("11:59");
        $nnStart = strtotime("12:00");
        $nnEnd   = strtotime("17:59");
        $pmStart = strtotime("18:00");
        $pmEnd   = strtotime("23:59");
        function overlaps($rangeStart, $rangeEnd, $start, $end) {
            return $start <= $rangeEnd && $end >= $rangeStart;
        }
        $amAvail = overlaps($amStart, $amEnd, $start, $end) ? 1 : 0;
        $nnAvail = overlaps($nnStart, $nnEnd, $start, $end) ? 1 : 0;
        $pmAvail = overlaps($pmStart, $pmEnd, $start, $end) ? 1 : 0;

        $eventPlanner = $_POST['evntPlan']; 
        $equipRentals = $_POST['EqmRentals']; 
        $decorateServices = $_POST['DecServ']; 
        $onsiteStaff = $_POST['siteStaff'];
        $techSupport = $_POST['techsup']; 
        $pwdFriendly = $_POST['pwdFriendly'];
        // price range will have a predefined set of values 
        $priceRange = $_POST['priceRange'];
        $validRanges = ['1', '2', '3', '4', '5'];
        if (!in_array($priceRange, $validRanges)) {
            $priceRange = 'NULL';
        }
        $contactEmail = $_POST['contactEmail'];
        $contactNum = $_POST['contactNum']; 
        $managerLname = $_POST['venueManagerLname'];
        $managerFname = $_POST['venueManagerFname'];
        $fbLink = $_POST['facebookLink'];
        $igLink = $_POST['instagramLink'];
        $webLink = $_POST['websiteLink'];
        $walkInBook = $_POST['walkInBook'];
        $onlineBook = $_POST['onlineBook'];
        $phoneBook = $_POST['phoneBook'];
        $payCash = $_POST['payCash'];
        $payBank = $_POST['payBank'];
        $payElectronic = $_POST['payElectronic'];
        $gmaps = $_POST['gmaps'];
        $publicTranspo = $_POST['publicTranspo'];
        $routes = $_POST['routes'];
        $landmarks = $_POST['landmarks'];
        $imgs = $_POST['imgs'];

        $sql  = "INSERT INTO venueData (
            venueName, barangayAddress, cityAddress, maxCapacity, intimate, business, fun, casual, 
            availabilityDays, startTime, endTime, amAvail, nnAvail, pmAvail, eventPlanner, equipRentals, 
            decorateServices, onsiteStaff, techSupport, pwdFriendly, priceRange, contactEmail, contactNum,
            managerLname, managerFname, fbLink, igLink, webLink, walkInBook, onlineBook,
            phoneBook, payCash, payBank, payElectronic, gmaps, publicTranspo, routes, landmarks, imgs
        ) VALUES (
            '$venueName', '$barangayAddress', '$cityAddress', '$maxCapacity',
            '$intimate','$business','$fun','$casual','$availabilityDays','$startTime','$endTime',
            '$amAvail','$nnAvail','$pmAvail','$eventPlanner','$equipRentals','$decorateServices',
            '$onsiteStaff','$techSupport','$pwdFriendly','$priceRange','$contactEmail','$contactNum',
            '$managerLname','$managerFname','$fbLink','$igLink','$webLink','$walkInBook',
            '$onlineBook','$phoneBook','$payCash','$payBank','$payElectronic','$gmaps','$publicTranspo',
            '$routes', '$landmarks', '$imgs'
        )";        
    
        try {
            $conn->query($sql);
            $newVenueID = $conn->insert_id;
            echo "<script>
                alert('New venue record added successfully! Venue ID: $newVenueID');
                window.location.href = 'index.php';
            </script>";
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "<script>
                    alert('Error: Venue already exists.');
                    window.history.back();
                </script>";
            } else {
                echo "<script>
                    alert('An unexpected error occurred. Please try again later.');
                    window.history.back();
                </script>";
            }
        }
    
    }
?>
