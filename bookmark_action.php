<?php
session_start();
include 'db_connection.php'; // Make sure this defines $conn = new mysqli(...);

// Make sure user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login required']);
    exit;
}

// Get user ID from username
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

// Get post data
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : '';

if (!$post_id || !$post_type) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// Check if bookmark already exists
$check = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND post_id = ? AND post_type = ?");
$check->bind_param("iis", $user_id, $post_id, $post_type);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Remove bookmark
    $check->close();
    $delete = $conn->prepare("DELETE FROM bookmarks WHERE user_id = ? AND post_id = ? AND post_type = ?");
    $delete->bind_param("iis", $user_id, $post_id, $post_type);
    $delete->execute();
    $delete->close();
    echo json_encode(['status' => 'removed']);
} else {
    // Add bookmark
    $check->close();
    $insert = $conn->prepare("INSERT INTO bookmarks (user_id, post_id, post_type) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $user_id, $post_id, $post_type);
    $insert->execute();
    $insert->close();
    echo json_encode(['status' => 'added']);
}

$conn->close();
?>
