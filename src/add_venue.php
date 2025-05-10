<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venueName = mysqli_real_escape_string($conn, $_POST['venueName']);
    $barangayAddress = mysqli_real_escape_string($conn, $_POST['barangayAddress']);
    $cityAddress = mysqli_real_escape_string($conn, $_POST['cityAddress']);
    $venueDesc = mysqli_real_escape_string($conn, $_POST['venueDesc']);
    
    $landmarks = mysqli_real_escape_string($conn, $_POST['landmarks']);
    $routes = mysqli_real_escape_string($conn, $_POST['routes']);

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
        INSERT INTO venueData (
            venueName,
            barangayAddress,
            cityAddress,
            venueDesc,
            landmarks,
            routes,
            intimate,
            business,
            casual,
            fun,
            eventPlanner,
            equipRentals,
            decoServices,
            onsiteStaff,
            techSupport,
            pwdFriendly,
            parking,
            cateringServices,
            securityStaff,
            wifiAccess,
            imgs,
            img2,
            img3,
            img4,
            img5
        ) VALUES (
            '$venueName',
            '$barangayAddress',
            '$cityAddress',
            '$venueDesc',
            '$landmarks',
            '$routes',
            $intimate,
            $business,
            $casual,
            $fun,
            $eventPlanner,
            $equipRentals,
            $decoServices,
            $onsiteStaff,
            $techSupport,
            $pwdFriendly,
            $parking,
            $cateringServices,
            $securityStaff,
            $wifiAccess,
            '$imgs',
            '$img2',
            '$img3',
            '$img4',
            '$img5'
        );
    ";

    if ($conn->query($sql)) {
        echo "<p class='text-green-600 text-center mt-4'>Venue added successfully.</p>";
        header("Location: DashboardAdmin.php");
        exit;
    } else {
        echo "<p class='text-red-600 text-center mt-4'>Error adding venue: " . $conn->error . "</p>";
    }

    $conn->close();
} else {
    echo "<p class='text-red-600 text-center mt-4'>Invalid request method.</p>";
}
?>
