<?php
    include('db_connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $venueID = $_POST['venueID'];
        $venueName = $_POST['venueName'];
        $barangayAddress = $_POST['barangayAddress'];
        $cityAddress = $_POST['cityAddress']; 
        $maxCapacity = $_POST['maxCapacity'];
        $intimate = isset($_POST['intimate']) ? 1 : 0;
        $business = isset($_POST['business']) ? 1 : 0;
        $fun = isset($_POST['fun']) ? 1 : 0;
        $casual = isset($_POST['casual']) ? 1 : 0;
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

        $eventPlanner = isset($_POST['eventPlanner']) ? 1 : 0;
        $equipRentals = isset($_POST['equipRentals']) ? 1 : 0;
        $decoServices = isset($_POST['decoServices']) ? 1:0; 
        $onsiteStaff = isset($_POST['onsiteStaff']) ? 1: 0;
        $techSupport = isset($_POST['techSupport']) ? 1 : 0;
        $pwdFriendly = isset($_POST['pwdFriendly']) ? 1 : 0;
        // price range will have a predefined set of values 
        $priceRangeID = $_POST['priceRangeID'];
        $validRanges = ['1', '2', '3', '4', '5'];
        if (!in_array($priceRangeID, $validRanges)) {
            $priceRangeID = 'NULL';
        }
        $contactEmail = $_POST['contactEmail'];
        $contactNum = $_POST['contactNum']; 
        $managerID = $_POST['managerID'];
        $facebookLink = $_POST['facebookLink'];
        $instagramLink = $_POST['instagramLink'];
        $websiteLink = $_POST['websiteLink'];
        $walkInBook = isset($_POST['walkInBook']) ? 1 : 0;
        $onlineBook = isset($_POST['onlineBook']) ? 1 : 0;
        $phoneBook = isset($_POST['phoneBook']) ? 1 : 0;
        $payCash = isset($_POST['payCash']) ? 1 : 0;
        $payBank = isset($_POST['payBank']) ? 1 : 0;
        $payElectronic = isset($_POST['payElectronic']) ? 1 : 0;
        $gmaps = $_POST['gmaps'];
        $publicTranspo = $_POST['publicTranspo'];
        $routes = $_POST['routes'];
        $landmarks = $_POST['landmarks'] ?? '';
        $imgs = $_POST['imgs'] ?? '';
        $managerID = $_POST['managerID'];

        $sql = "UPDATE venueData SET
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
            decoServices = " . ($decoServices === '' ? 'NULL' : "'$decoServices'") . ",
            onsiteStaff = " . ($onsiteStaff === '' ? 'NULL' : "'$onsiteStaff'") . ",
            techSupport = " . ($techSupport === '' ? 'NULL' : "'$techSupport'") . ",
            pwdFriendly = " . ($pwdFriendly === '' ? 'NULL' : "'$pwdFriendly'") . ",
            priceRangeID = " . ($priceRangeID === '' ? 'NULL' : "'$priceRangeID'") . ",
            contactEmail = " . ($contactEmail === '' ? 'NULL' : "'$contactEmail'") . ",
            contactNum = " . ($contactNum === '' ? 'NULL' : "'$contactNum'") . ",
            managerID = " . ($managerID === '' ? 'NULL' : "'$managerID'") . ",
            facebookLink = " . ($facebookLink === '' ? 'NULL' : "'$facebookLink'") . ",
            instagramLink = " . ($instagramLink === '' ? 'NULL' : "'$instagramLink'") . ",
            websiteLink = " . ($websiteLink === '' ? 'NULL' : "'$websiteLink'") . ",
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
                window.location.href = 'manager_read.php';
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
