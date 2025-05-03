<?php
    include('db_connection.php');
    $venueID = $_POST['venueID'] ?? $_GET['venueID'] ?? null;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // venueId autoincremented
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
            $priceRangeID = Null;
        }
        $contactEmail = $_POST['contactEmail'] ?? NULL;
        $contactNum = $_POST['contactNum'] ?? NULL; 
        $managerID = $_POST['managerID'];
        $facebookLink = $_POST['facebookLink'] ?? NULL;
        $instagramLink = $_POST['instagramLink'] ?? NULL;
        $websiteLink = $_POST['websiteLink'] ?? NULL;
        $walkInBook = isset($_POST['walkInBook']) ? 1 : 0;
        $onlineBook = isset($_POST['onlineBook']) ? 1 : 0;
        $phoneBook = isset($_POST['phoneBook']) ? 1 : 0;
        $payCash = isset($_POST['payCash']) ? 1 : 0;
        $payBank = isset($_POST['payBank']) ? 1 : 0;
        $payElectronic = isset($_POST['payElectronic']) ? 1 : 0;
        $gmaps = $_POST['gmaps'];
        $publicTranspo = $_POST['publicTranspo'];
        $routes = $_POST['routes'];
        $landmarks = $_POST['landmarks'] ?? NULL;
        $imgs = $_POST['imgs'] ?? NULL;

        $sql = "INSERT INTO venueData (
            venueName, barangayAddress, cityAddress, maxCapacity, intimate, business, fun, casual, 
            availabilityDays, amAvail, nnAvail, pmAvail, eventPlanner, equipRentals, 
            decoServices, onsiteStaff, techSupport, pwdFriendly, priceRangeID, contactEmail, contactNum, managerID,
            facebookLink, instagramLink, websiteLink, walkInBook, onlineBook,
            phoneBook, payCash, payBank, payElectronic, gmaps, publicTranspo, routes, landmarks, imgs
        ) VALUES (
            '$venueName', '$barangayAddress', '$cityAddress', '$maxCapacity', '$intimate', '$business', '$fun', '$casual',
            '$availabilityDays', '$amAvail', '$nnAvail', '$pmAvail', '$eventPlanner', '$equipRentals',
            '$decoServices', '$onsiteStaff', '$techSupport', '$pwdFriendly', '$priceRangeID', '$contactEmail', '$contactNum', '$managerID',
            '$facebookLink', '$instagramLink', '$websiteLink', '$walkInBook', '$onlineBook',
            '$phoneBook', '$payCash', '$payBank', '$payElectronic', '$gmaps', '$publicTranspo',
            '$routes', '$landmarks', '$imgs'
        )";
        
        try {
            $conn->query($sql);
            $newVenueID = $conn->insert_id;
            echo "<script>
                alert('New venue record added successfully! Venue ID: $newVenueID');
                window.location.href = 'manager_read.php';
            </script>";
        } catch (mysqli_sql_exception $e) {
            error_log($e->getMessage());
            echo "<script>
                alert('An unexpected error occurred. Please try again later.');
                window.history.back();
            </script>";
        }
        
    }
?>
