<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestID = (int)$_POST['requestID'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        mysqli_begin_transaction($conn);

        try {
            $fetchRequest = "SELECT * FROM venuerequests WHERE requestID = $requestID";
            $requestResult = mysqli_query($conn, $fetchRequest);
            $requestData = mysqli_fetch_assoc($requestResult);

            if (!$requestData) throw new Exception("Request not found.");

            $managerID = (int)$requestData['managerID'];

            // Escape string values
            $venueName = mysqli_real_escape_string($conn, $requestData['venueName']);
            $barangayAddress = mysqli_real_escape_string($conn, $requestData['barangayAddress']);
            $cityAddress = mysqli_real_escape_string($conn, $requestData['cityAddress']);
            $venueDesc = mysqli_real_escape_string($conn, $requestData['venueDesc']);
            $contactEmail = mysqli_real_escape_string($conn, $requestData['contactEmail']);
            $latitude = mysqli_real_escape_string($conn, $requestData['latitude']);
            $longitude = mysqli_real_escape_string($conn, $requestData['longitude']);
            $publicTranspo = mysqli_real_escape_string($conn, $requestData['publicTranspo']);
            $routes = mysqli_real_escape_string($conn, $requestData['routes']);
            $landmarks = mysqli_real_escape_string($conn, $requestData['landmarks']);
            $imgs = mysqli_real_escape_string($conn, $requestData['imgs']);
            $img2 = mysqli_real_escape_string($conn, $requestData['img2']);
            $img3 = mysqli_real_escape_string($conn, $requestData['img3']);

            $priceRangeID = (int)$requestData['priceRangeID'];
            $contactNum = (int)$requestData['contactNum'];
            $availabilityDays = (int)$requestData['availabilityDays'];

            // Build insert query
            $insertVenue = "INSERT INTO venuedata (
                venueName, barangayAddress, cityAddress,
                intimate, business, fun, casual, venueDesc, availabilityDays,
                amAvail, nnAvail, pmAvail, eventPlanner, equipRentals, decoServices,
                onsiteStaff, techSupport, pwdFriendly, parking, cateringServices, securityStaff,
                wifiAccess, priceRangeID, contactEmail, contactNum,
                payCash, payBank, payElectronic,
                latitude, longitude, publicTranspo, routes, landmarks,
                imgs, img2, img3
            ) VALUES (
                '$venueName', '$barangayAddress', '$cityAddress',
                {$requestData['intimate']}, {$requestData['business']}, {$requestData['fun']}, {$requestData['casual']}, '$venueDesc', $availabilityDays,
                {$requestData['amAvail']}, {$requestData['nnAvail']}, {$requestData['pmAvail']}, {$requestData['eventPlanner']}, {$requestData['equipRentals']}, {$requestData['decoServices']},
                {$requestData['onsiteStaff']}, {$requestData['techSupport']}, {$requestData['pwdFriendly']}, {$requestData['parking']}, {$requestData['cateringServices']}, {$requestData['securityStaff']},
                {$requestData['wifiAccess']}, $priceRangeID, '$contactEmail', $contactNum,
                {$requestData['payCash']}, {$requestData['payBank']}, {$requestData['payElectronic']},
                '$latitude', '$longitude', '$publicTranspo', '$routes', '$landmarks',
                '$imgs', '$img2', '$img3'
            )";

            if (!mysqli_query($conn, $insertVenue)) throw new Exception("Failed to insert venue data: " . mysqli_error($conn));

            $newVenueID = mysqli_insert_id($conn);

            $insertManagerVenue = "INSERT INTO managervenue (managerID, venueID) VALUES ($managerID, $newVenueID)";
            if (!mysqli_query($conn, $insertManagerVenue)) throw new Exception("Failed to link manager to venue.");

            // Fetch all facility records linked to this request
            $facilityQuery = "SELECT * FROM requestfacilities WHERE requestID = $requestID";
            $facilityResult = mysqli_query($conn, $facilityQuery);

            if (!$facilityResult) throw new Exception("Failed to fetch request facilities: " . mysqli_error($conn));

            while ($facilityRow = mysqli_fetch_assoc($facilityResult)) {
                $facilityTypeID = (int)$facilityRow['facilityType'];
                $facilityCapacity = (int)$facilityRow['maxCapacity'];
                $facilityPrice = floatval($facilityRow['price']);

                $insertFacility = "INSERT INTO venuefacilities (
                    venueID, facilityType, maxCapacity, price
                ) VALUES (
                    $newVenueID, $facilityTypeID, $facilityCapacity, $facilityPrice
                )";

                if (!mysqli_query($conn, $insertFacility)) {
                    throw new Exception("Failed to insert facility: " . mysqli_error($conn));
                }
            }

            // After successful insertion, clean up the temporary request facilities
            $deleteRequestFacilities = "DELETE FROM requestFacilities WHERE requestID = $requestID";
            mysqli_query($conn, $deleteRequestFacilities);


            $deleteRequest = "DELETE FROM venuerequests WHERE requestID = $requestID";
            mysqli_query($conn, $deleteRequest);

            mysqli_commit($conn);
            header("Location: superadmin.php");
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Error: " . $e->getMessage();
        }

    } elseif ($action === 'reject') {
        $requestID = (int)$_POST['requestID'];
        $deleteSql = "DELETE FROM venuerequests WHERE requestID = $requestID";
        if (mysqli_query($conn, $deleteSql)) {
            header("Location: superadmin.php");
            exit();
        } else {
            echo "Error rejecting request: " . mysqli_error($conn);
        }
    }
}
?>
