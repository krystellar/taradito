<?php
    include('db_connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $venueID = $_POST['venueID'];
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


        $sql = "UPDATE venueData SET
            venueID = '$venueID',
            venueName = '$venueName',
            barangayAddress = '$barangayAddress',
            cityAddress = '$cityAddress',
            maxCapacity = " . ($maxCapacity === '' ? 'NULL' : "'$maxCapacity'") . ",
            intimate = " . ($intimate === '' ? 'NULL' : "'$intimate'") . ",
            business = " . ($business === '' ? 'NULL' : "'$business'") . ",
            fun = " . ($fun === '' ? 'NULL' : "'$fun'") . ",
            casual = " . ($casual === '' ? 'NULL' : "'$casual'") . ",
            availabilityDays = " . ($availabilityDays === '' ? 'NULL' : "'$availabilityDays'") . ",
            amAvail = " . ($amAvail === '' ? 'NULL' : "'$amAvail'") . ",
            nnAvail = " . ($nnAvail === '' ? 'NULL' : "'$nnAvail'") . ",
            pmAvail = " . ($pmAvail === '' ? 'NULL' : "'$pmAvail'") . ",
            eventPlanner = " . ($eventPlanner === '' ? 'NULL' : "'$eventPlanner'") . ", 
            equipRentals = " . ($equipRentals === '' ? 'NULL' : "'$equipRentals'") . ",
            decorateServices = " . ($decorateServices === '' ? 'NULL' : "'$decorateServices'") . ",
            onsiteStaff = " . ($onsiteStaff === '' ? 'NULL' : "'$onsiteStaff'") . ",
            techSupport = " . ($techSupport === '' ? 'NULL' : "'$techSupport'") . ",
            pwdFriendly = " . ($pwdFriendly === '' ? 'NULL' : "'$pwdFriendly'") . ",
            priceRange = " . ($priceRange === '' ? 'NULL' : "'$priceRange'") . ",
            contactEmail = " . ($contactEmail === '' ? 'NULL' : "'$contactEmail'") . ",
            contactNum = " . ($contactNum === '' ? 'NULL' : "'$contactNum'") . ",
            venueManagerLname = " . ($managerLname === '' ? 'NULL' : "'$managerLname'") . ",
            venueManagerFname = " . ($managerFname === '' ? 'NULL' : "'$managerFname'") . ",
            facebookLink = " . ($fbLink === '' ? 'NULL' : "'$fbLink'") . ",
            instagramLink = " . ($igLink === '' ? 'NULL' : "'$igLink'") . ",
            websiteLink = " . ($webLink === '' ? 'NULL' : "'$webLink'") . ",
            walkInBook = " . ($walkInBook === '' ? 'NULL' : "'$walkInBook'") . ",
            onlineBook = " . ($onlineBook === '' ? 'NULL' : "'$onlineBook'") . ",
            phoneBook = " . ($phoneBook === '' ? 'NULL' : "'$phoneBook'") . ",
            payCash = " . ($payCash === '' ? 'NULL' : "'$payCash'") . ",
            payBank = " . ($payBank === '' ? 'NULL' : "'$payBank'") . ",
            payElectronic = " . ($payElectronic === '' ? 'NULL' : "'$payElectronic'") . ",
            gmaps = " . ($gmaps === '' ? 'NULL' : "'$gmaps'") . ",
            publicTranspo = " . ($publicTranspo === '' ? 'NULL' : "'$publicTranspo'") . ",
            routes = " . ($routes === '' ? 'NULL' : "'$routes'") . ",
            landmarks = " . ($landmarks === '' ? 'NULL' : "'$landmarks'") . ",
            imgs = " . ($imgs === '' ? 'NULL' : "'$imgs'") . "
            WHERE venueID = '$venueID'";

        try {
            $conn->query($sql);
            echo "<script>
                alert('Venue record updated successfully!');
                window.location.href = 'index.php';
            </script>";
        } catch (mysqli_sql_exception $e) {
            echo "<script>
                alert('An error occurred while updating. Please try again.');
                window.history.back();
                console.error('Error details: " . $e->getMessage() . "');
            </script>";
        }
    }
?>
