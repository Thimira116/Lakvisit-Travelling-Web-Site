<?php
session_start();

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = htmlspecialchars($_POST['title']);
  $description = htmlspecialchars($_POST['description']);
  $category = htmlspecialchars($_POST['category']);
  $province = htmlspecialchars($_POST['province']);
  $district = htmlspecialchars($_POST['district']);
  $location = htmlspecialchars($_POST['location']);

  $conn = new mysqli('localhost', 'root', '', 'Lakvisit');
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $username = $_SESSION['username'];
  $stmt = $conn->prepare("INSERT INTO destination_ads (username, title, description, category, province, district, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssss", $username, $title, $description, $category, $province, $district, $location);
  $stmt->execute();

  $ad_id = $stmt->insert_id;

  $upload_dir = 'uploads/';
  if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }

  foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
    if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
      $filename = basename($_FILES['images']['name'][$index]);
      $target_file = $upload_dir . time() . '_' . $filename;
      move_uploaded_file($tmp_name, $target_file);

      $img_stmt = $conn->prepare("INSERT INTO destination_images (ad_id, image_path) VALUES (?, ?)");
      $img_stmt->bind_param("is", $ad_id, $target_file);
      $img_stmt->execute();
      $img_stmt->close();
    }
  }

  $stmt->close();
  $conn->close();

  header("Location: destination.php?success=1");
  exit;
}
?>
