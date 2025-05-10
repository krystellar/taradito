<?php
session_start();
require_once 'db_connection.php';

if (!isset($_POST['ratingID'])) {
    die("Missing review ID.");
}

$ratingID = $_POST['ratingID'];
$userID = $_SESSION['userID'];

// Get the venueID associated with the review
$stmt = $conn->prepare("SELECT venueID, userID FROM userratings WHERE ratingID = ?");
$stmt->bind_param("i", $ratingID);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();

if (!$review) {
    die("Review not found.");
}

if ($review['userID'] != $userID) {
    die("Unauthorized to delete this review.");
}

$venueID = $review['venueID'];

$stmt = $conn->prepare("DELETE FROM userratings WHERE ratingID = ? AND userID = ?");
$stmt->bind_param("ii", $ratingID, $userID);

if ($stmt->execute()) {
    header("Location: listing.php?id=" . $venueID);
    exit();
} else {
    echo "Failed to delete review: " . $stmt->error;
}
?>
