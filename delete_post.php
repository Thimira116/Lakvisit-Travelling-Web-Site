<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Include your database connection file
include 'db_connection.php'; // Make sure this file defines $conn

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';
$current_user = $_SESSION['username'];

if ($post_id && in_array($type, ['destination', 'service'])) {
    if ($type === 'destination') {
        $sql = "DELETE FROM destination_ads WHERE id = ? AND username = ?";
    } else {
        $sql = "DELETE FROM services_ads WHERE id = ? AND username = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $post_id, $current_user);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Post deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete the post.";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid request.";
}

$conn->close();
header("Location: Profile.php");
exit();
?>
