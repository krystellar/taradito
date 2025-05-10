<?php
session_start();
include('db_connection.php');
if (!isset($_SESSION['userID'])) {
    header("Location: Login_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $userID = $_SESSION['userID'];
    $venueID = $_POST['venueID'];

    if (!isset($rating) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        $_SESSION['error'] = 'Please provide a valid rating between 1 and 5.';
        header("Location: listing.php?id=$venueID");
        exit;
    }

    $sql = "INSERT INTO userratings (venueID, userID, rating, review, createDate, updateDate) 
            VALUES (?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $venueID, $userID, $rating, $review);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Review added successfully!';
    } else {
        $_SESSION['error'] = 'An error occurred while adding your review.';
    }
    $stmt->close();
    $conn->close();

    header("Location: listing.php?id=$venueID");
    exit;
}
?>
