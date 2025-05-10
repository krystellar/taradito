<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['userID'])) {
    header("Location: Login_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venueID = $_POST['venueID'];
    $ratingID = $_POST['ratingID'];
    $userID = $_SESSION['userID'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    if (!isset($ratingID) || !isset($rating) || !isset($review)) {
        $_SESSION['error'] = 'Missing required fields.';
        header("Location: listing.php?id=" . urlencode($venueID)); 
        exit;
    }

    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        $_SESSION['error'] = 'Please provide a valid rating between 1 and 5.';
        header("Location: listing.php?id=$venueID");
        exit;
    }

    $sql = "UPDATE userratings SET rating = ?, review = ?, updateDate = NOW() WHERE ratingID = ? AND userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $rating, $review, $ratingID, $userID);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Review updated successfully!';
    } else {
        $_SESSION['error'] = 'An error occurred while updating your review: ' . $stmt->error;
    }
    $stmt->close();
    $conn->close();

    header("Location: listing.php?id=" . urlencode($venueID));
    exit;
}
?>
