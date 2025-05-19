<?php
include('db_connection.php');
session_start(); // Ensure session is active to access managerID

include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_venue') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $venueID = (int)$_POST['venueID'];
            $venueName = $_POST['venueName'] ?? '';
            $barangayAddress = $_POST['barangayAddress'] ?? '';
            $cityAddress = $_POST['cityAddress'] ?? '';
            $landmarks = $_POST['landmarks'] ?? '';
            $routes = $_POST['routes'] ?? '';
            $venueDesc = $_POST['venueDesc'] ?? '';
            $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
            $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
            $availabilityDays = (int)($_POST['availabilityDays'] ?? 0);
            $priceRangeID = (int)($_POST['priceRangeID'] ?? 0);
            $amAvail = isset($_POST['amAvail']) ? 1 : 0;
            $nnAvail = isset($_POST['nnAvail']) ? 1 : 0;
            $pmAvail = isset($_POST['pmAvail']) ? 1 : 0;
            $contactEmail = mysqli_real_escape_string($conn, $_POST['contactEmail']);
            $contactNum = mysqli_real_escape_string($conn, $_POST['contactNum']);

            // Checkboxes
            $intimate = isset($_POST['intimate']) ? 1 : 0;
            $business = isset($_POST['business']) ? 1 : 0;
            $casual = isset($_POST['casual']) ? 1 : 0;
            $fun = isset($_POST['fun']) ? 1 : 0;
            $eventPlanner = isset($_POST['eventPlanner']) ? 1 : 0;
            $equipRentals = isset($_POST['equipRentals']) ? 1 : 0;
            $decoServices = isset($_POST['decoServices']) ? 1 : 0;
            $onsiteStaff = isset($_POST['onsiteStaff']) ? 1 : 0;
            $techSupport = isset($_POST['techSupport']) ? 1 : 0;
            $pwdFriendly = isset($_POST['pwdFriendly']) ? 1 : 0;
            $parking = isset($_POST['parking']) ? 1 : 0;
            $cateringServices = isset($_POST['cateringServices']) ? 1 : 0;
            $securityStaff = isset($_POST['securityStaff']) ? 1 : 0;
            $wifiAccess = isset($_POST['wifiAccess']) ? 1 : 0;
            $payCash = isset($_POST['payCash']) ? 1 : 0;
            $payElectronic = isset($_POST['payElectronic']) ? 1 : 0;
            $payBank = isset($_POST['payBank']) ? 1 : 0;

            // Fetch current image paths
            $current = $conn->prepare("SELECT imgs, img2, img3 FROM venueData WHERE venueID = ?");
            $current->bind_param("i", $venueID);
            $current->execute();
            $result = $current->get_result();
            $existing = $result->fetch_assoc();
            $current->close();

            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            function uploadImage($fieldName, $existingPath) {
                global $uploadDir;
                if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$fieldName]['tmp_name'];
                    $fileName = basename($_FILES[$fieldName]['name']);
                    $targetFile = $uploadDir . time() . '_' . $fileName;
                    if (move_uploaded_file($tmpName, $targetFile)) {
                        return $targetFile;
                    }
                }
                return $existingPath;
            }

            $imgs = uploadImage('img1', $existing['imgs']);
            $img2 = uploadImage('img2', $existing['img2']);
            $img3 = uploadImage('img3', $existing['img3']);

            $stmt = $conn->prepare("
                UPDATE venueData SET
                venueName=?, barangayAddress=?, cityAddress=?, contactEmail=?, contactNum=?,
                landmarks=?, routes=?, venueDesc=?, priceRangeID=?, 
                intimate=?, business=?, casual=?, fun=?,
                eventPlanner=?, equipRentals=?, decoServices=?, onsiteStaff=?, techSupport=?,
                pwdFriendly=?, parking=?, cateringServices=?, securityStaff=?, wifiAccess=?,
                latitude=?, longitude=?, availabilityDays=?, amAvail=?, nnAvail=?, pmAvail=?,
                paycash=?, payelectronic=?, paybank=?,
                imgs=?, img2=?, img3=?
                WHERE venueID=?
            ");

            $stmt->bind_param(
                "ssssssssiiiiiiiiiiiiiiissiiiiiiisssi",
                $venueName, $barangayAddress, $cityAddress, $contactEmail, $contactNum,
                $landmarks, $routes, $venueDesc, $priceRangeID,
                $intimate, $business, $casual, $fun,
                $eventPlanner, $equipRentals, $decoServices, $onsiteStaff, $techSupport,
                $pwdFriendly, $parking, $cateringServices, $securityStaff, $wifiAccess,
                $latitude, $longitude, $availabilityDays, $amAvail, $nnAvail, $pmAvail,
                $payCash, $payElectronic, $payBank,
                $imgs, $img2, $img3,
                $venueID
            );

            if ($stmt->execute()) {
                header("Location: DashboardAdmin.php");
                exit;
            } else {
                echo "<p class='text-red-600 text-center mt-4'>Error updating venue: " . $stmt->error . "</p>";
            }

            $stmt->close();
            $conn->close();
        } else {
            echo "<p class='text-red-600 text-center mt-4'>Invalid request method.</p>";
        }
    }
// DIVISION HERE
    if ($action === 'add_facility') {
    $venueID = intval($_POST['venueID'] ?? 0);

    // === 1. UPDATE existing facilities ===
    if (!empty($_POST['facilityID'])) {
        $facilityIDs = $_POST['facilityID'];
        $maxCapacities = $_POST['maxCapacity'];
        $prices = $_POST['price'];

        $stmtUpdate = $conn->prepare("UPDATE venuefacilities SET maxCapacity = ?, price = ? WHERE facilityID = ? AND venueID = ?");

        for ($i = 0; $i < count($facilityIDs); $i++) {
            $fid = intval($facilityIDs[$i]);
            $maxCap = intval($maxCapacities[$i]);
            $price = floatval($prices[$i]);

            if ($fid > 0 && $maxCap >= 0 && $price >= 0) {
                $stmtUpdate->bind_param("idii", $maxCap, $price, $fid, $venueID);
                $stmtUpdate->execute();
            }
        }
        $stmtUpdate->close();
    }

    // === 2. INSERT new facilities ===
    if (!empty($_POST['newFacilityType'])) {
        $newFacilityTypes = $_POST['newFacilityType'];
        $newMaxCapacities = $_POST['newMaxCapacity'];
        $newPrices = $_POST['newPrice'];

        $stmtInsert = $conn->prepare("INSERT INTO venuefacilities (venueID, facilityType, maxCapacity, price) VALUES (?, ?, ?, ?)");

        for ($i = 0; $i < count($newFacilityTypes); $i++) {
            $ftype = intval($newFacilityTypes[$i]);
            $maxCap = intval($newMaxCapacities[$i]);
            $price = floatval($newPrices[$i]);

            if ($venueID > 0 && $ftype > 0 && $maxCap >= 0 && $price >= 0) {
                $stmtInsert->bind_param("iiid", $venueID, $ftype, $maxCap, $price);
                if (!$stmtInsert->execute()) {
                    error_log("Facility insert failed: " . $stmtInsert->error);
                }
            }
        }
        $stmtInsert->close();
    }


    // Redirect after success
    header("Location: DashboardAdmin.php?message=FacilitiesUpdated");
    exit;
}

}

?>
