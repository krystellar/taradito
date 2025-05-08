<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venueID = (int)$_POST['venueID'];
    $venueName = mysqli_real_escape_string($conn, $_POST['venueName']);
    $barangayAddress = mysqli_real_escape_string($conn, $_POST['barangayAddress']);
    $cityAddress = mysqli_real_escape_string($conn, $_POST['cityAddress']);
    $venueDesc = mysqli_real_escape_string($conn, $_POST['venueDesc']);

    $intimate  = isset($_POST['intimate'])  ? 1 : 0;
    $business  = isset($_POST['business'])  ? 1 : 0;
    $casual    = isset($_POST['casual'])    ? 1 : 0;
    $fun       = isset($_POST['fun'])       ? 1 : 0;
    $eventPlanner = isset($_POST['eventPlanner']) ? 1 : 0;
    $equipRentals = isset($_POST['equipRentals']) ? 1 : 0;
    $decoServices = isset($_POST['decoServices']) ? 1 : 0;
    $onsiteStaff = isset($_POST['onsiteStaff']) ? 1 : 0;
    $techSupport = isset($_POST['techSupport']) ? 1 : 0;
    $pwdFriendly = isset($_POST['pwdFriendly']) ? 1 : 0;
    $parking    = isset($_POST['parking'])    ? 1 : 0;
    $cateringServices = isset($_POST['cateringServices']) ? 1 : 0;
    $securityStaff = isset($_POST['securityStaff']) ? 1 : 0;
    $wifiAccess = isset($_POST['wifiAccess']) ? 1 : 0;

    $imgs = mysqli_real_escape_string($conn, $_POST['imgs']);
    $img2 = mysqli_real_escape_string($conn, $_POST['img2']);
    $img3 = mysqli_real_escape_string($conn, $_POST['img3']);
    $img4 = mysqli_real_escape_string($conn, $_POST['img4']);
    $img5 = mysqli_real_escape_string($conn, $_POST['img5']);

    $sql = "
        UPDATE venueData SET
            venueName = '$venueName',
            barangayAddress = '$barangayAddress',
            cityAddress = '$cityAddress',
            venueDesc = '$venueDesc',
            intimate = $intimate,
            business = $business,
            casual = $casual,
            fun = $fun,
            eventPlanner = $eventPlanner,
            equipRentals = $equipRentals,
            decoServices = $decoServices,
            onsiteStaff = $onsiteStaff,
            techSupport = $techSupport,
            pwdFriendly = $pwdFriendly,
            parking = $parking,
            cateringServices = $cateringServices,
            securityStaff = $securityStaff,
            wifiAccess = $wifiAccess,
            imgs = '$imgs',
            img2 = '$img2',
            img3 = '$img3',
            img4 = '$img4',
            img5 = '$img5'
        WHERE venueID = $venueID;
    ";

    if ($conn->query($sql)) {
        echo "<p class='text-green-600 text-center mt-4'>Venue updated successfully.</p>";
        header("Location: DashboardAdmin.php");
        exit;
    } else {
        echo "<p class='text-red-600 text-center mt-4'>Error updating venue: " . $conn->error . "</p>";
    }

    $conn->close();
} else {
    echo "<p class='text-red-600 text-center mt-4'>Invalid request method.</p>";
}
?>
