<?php
include 'db_connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Get the venueID passed from the form
        $venueID = $_POST['venueID'];

        // Get the image details
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageSize = $_FILES['image']['size'];
        $imageType = $_FILES['image']['type'];

        // Validate if the file is a valid image
        if (getimagesize($imageTmpPath) === false) {
            die("Uploaded file is not a valid image.");
        }

        // Set the upload directory and file name
        $uploadDir = '../uploads/'; // Assuming the uploads folder is outside the src folder
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $imageName = uniqid('img_') . '.' . $imageExtension; // Create a unique file name
        $uploadPath = $uploadDir . $imageName;

        // Move the uploaded file to the uploads folder
        if (move_uploaded_file($imageTmpPath, $uploadPath)) {
            // Save the relative image path in the database
            $imagePath = 'uploads/' . $imageName; // Store relative path

            // SQL query to update the venue with the new image
            $sql = "UPDATE venues SET image_path = ? WHERE venue_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $imagePath, $venueID);

            if ($stmt->execute()) {
                echo "Image uploaded and saved successfully.";
            } else {
                echo "Error updating database.";
            }
        } else {
            echo "Error uploading the image.";
        }
    } else {
        echo "No image selected or error in file upload.";
    }
}
?>
