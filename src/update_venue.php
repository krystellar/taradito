<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venueID = (int)$_POST['venueID'];
    $venueName = $_POST['venueName'] ?? '';
    $barangayAddress = $_POST['barangayAddress'] ?? '';
    $cityAddress = $_POST['cityAddress'] ?? '';
    $landmarks = $_POST['landmarks'] ?? '';
    $routes = $_POST['routes'] ?? '';
    $venueDesc = $_POST['venueDesc'] ?? '';

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

    // Upload or retain existing images
    $imgs = uploadImage('img1', $existing['imgs']);
    $img2 = uploadImage('img2', $existing['img2']);
    $img3 = uploadImage('img3', $existing['img3']);

    // Update DB
    $stmt = $conn->prepare("
        UPDATE venueData SET
            venueName=?, barangayAddress=?, cityAddress=?, landmarks=?, routes=?, venueDesc=?,
            intimate=?, business=?, casual=?, fun=?,
            eventPlanner=?, equipRentals=?, decoServices=?, onsiteStaff=?, techSupport=?,
            pwdFriendly=?, parking=?, cateringServices=?, securityStaff=?, wifiAccess=?,
            imgs=?, img2=?, img3=?
        WHERE venueID=?
    ");

    $stmt->bind_param(
        "ssssssiiiiiiiiiiiiissssi",
        $venueName, $barangayAddress, $cityAddress, $landmarks, $routes, $venueDesc,
        $intimate, $business, $casual, $fun,
        $eventPlanner, $equipRentals, $decoServices, $onsiteStaff, $techSupport,
        $pwdFriendly, $parking, $cateringServices, $securityStaff, $wifiAccess,
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
?>
