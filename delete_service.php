<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$ad_id = intval($_GET['id']);
$current_user = $_SESSION['username'];

// Include your database connection file
include 'db_connection.php'; // Make sure this file defines $conn

// Check if post belongs to current user
$stmt = $conn->prepare("SELECT * FROM services_ads WHERE id = ? AND username = ?");
$stmt->bind_param("is", $ad_id, $current_user);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    die("Unauthorized action or post not found.");
}
$stmt->close();

// Delete related images
$img_stmt = $conn->prepare("SELECT image_path FROM services_images WHERE ad_id = ?");
$img_stmt->bind_param("i", $ad_id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();
while ($img = $img_result->fetch_assoc()) {
    if (file_exists($img['image_path'])) {
        unlink($img['image_path']); // Delete file from server
    }
}
$img_stmt->close();

// Delete from services_images table
$stmt = $conn->prepare("DELETE FROM services_images WHERE ad_id = ?");
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$stmt->close();

// Delete from services_ads table
$stmt = $conn->prepare("DELETE FROM services_ads WHERE id = ?");
$stmt->bind_param("i", $ad_id);
$stmt->execute();
$stmt->close();

$conn->close();
header("Location: profile.php?deleted=service");
exit();
?>
