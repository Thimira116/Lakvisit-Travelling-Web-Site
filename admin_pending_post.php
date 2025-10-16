<?php
session_start();

// Include your database connection file
include 'db_connection.php'; // Make sure this file defines $conn

// Approve Post (regular ads)
if (isset($_GET['approve_id'])) {
    $id = intval($_GET['approve_id']);
    $stmt = $conn->prepare("UPDATE destination_ads SET approved = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_pending_post.php");
    exit;
}

// Delete Post (regular ads)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM destination_ads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_pending_post.php");
    exit;
}

// Approve Service Ad
if (isset($_GET['approve_service_id'])) {
    $id = intval($_GET['approve_service_id']);
    $stmt = $conn->prepare("UPDATE services_ads SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_pending_post.php");
    exit;
}

// Delete Service Ad
if (isset($_GET['delete_service_id'])) {
    $id = intval($_GET['delete_service_id']);
    $stmt = $conn->prepare("DELETE FROM services_ads WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Also delete associated images

    // Assuming images for service ads are stored in 'ad_images' with a column to distinguish type
    $img_stmt = $conn->prepare("DELETE FROM destination_images WHERE ad_id = ?");
    $img_stmt->bind_param("i", $id);
    $img_stmt->execute();
    $img_stmt->close();

    header("Location: admin_pending_post.php");
    exit;
}

if (isset($_GET['delete_user_id'])) {
    $id = intval($_GET['delete_user_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_pending_post.php");
    exit;
}

// Function to get all photos for a given regular ad id
function getPhotos($conn, $ad_id) {
    $photos = [];
    $stmt = $conn->prepare("SELECT image_path FROM destination_images WHERE ad_id = ?");
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $photos[] = $row['image_path'];
    }
    $stmt->close();
    return $photos;
}

// Function to get all photos for a given service ad id
function getServicePhotos($conn, $ad_id) {
    $photos = [];
    $stmt = $conn->prepare("SELECT image_path FROM services_images WHERE ad_id = ?");
    $stmt->bind_param("i", $ad_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $photos[] = $row['image_path'];
    }
    $stmt->close();
    return $photos;
}

// Get Pending Posts (regular ads)
$posts_result = $conn->query("SELECT * FROM destination_ads WHERE approved = 0");

// Get Pending Service Ads
$service_ads_result = $conn->query("SELECT * FROM services_ads WHERE status = 'pending'");

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - Pending Posts</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .photo-thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      margin-right: 5px;
      border-radius: 4px;
      border: 1px solid #ddd;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .photo-thumb:hover {
      transform: scale(1.2);
      border-color: #007bff;
    }

    /* Responsive gap between action buttons */
    @media (max-width: 768px) {
      td > .btn {
        display: block;
        width: 100%;
        margin-bottom: 5px;
      }
      td > .btn + .btn {
        margin-top: 5px;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container d-flex align-items-center justify-content-between">
    <a class="navbar-brand" href="index.php">
      <img src="Lakvisit.png" alt="Lakvisit Logo" style="height: 40px;">
    </a>
    <div class="mx-auto">
      <h1 class="h4 mb-0 text-center">Pending Posts</h1>
    </div>
  </div>
</nav>

<!-- Admin Panel Content -->

<div class="container mt-5">

<!-- Pending Approval for Regular Posts -->
    <a href="admin.php"><h6 class="mb-4">Admin Panel</h2></a>

<!-- Pending Approval for Service Ads -->
    <a href="admin_approved_post.php"><h6 class="mb-4">Approved Posts</h2></a>

  <!-- Pending Approval for Regular Posts -->

  <h2 class="mb-4">Destinations Posts</h2>
  <div class="table-responsive mb-5">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>User Name</th>
          <th>Place Name</th>
          <th>Description</th>
          <th>Category</th>
          <th>Province</th>
          <th>District</th>
          <th>Location</th>
          <th>Photos</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($post = $posts_result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($post['id']) ?></td>
          <td><?= htmlspecialchars($post['username']) ?></td>
          <td><?= html_entity_decode($post['title']) ?></td>
          <td><?= nl2br(html_entity_decode($post['description'])) ?></td>
          <td><?= htmlspecialchars($post['category']) ?></td>
          <td><?= htmlspecialchars($post['province']) ?></td>
          <td><?= htmlspecialchars($post['district']) ?></td>
          <td><?= htmlspecialchars($post['location']) ?></td>
          <td>
            <?php
              $photos = getPhotos($conn, $post['id']);
              if (count($photos) > 0):
                foreach ($photos as $photo):
            ?>
                <a href="<?= htmlspecialchars($photo) ?>" target="_blank" title="View Full Image">
                  <img src="<?= htmlspecialchars($photo) ?>" alt="Ad Photo" class="photo-thumb" />
                </a>
            <?php
                endforeach;
              else:
                echo "<span>No Photos</span>";
              endif;
            ?>
          </td>
          <td>
            <a class="btn btn-success btn-sm" href="admin_pending_post.php?approve_id=<?= $post['id'] ?>">Approve</a>
            <a class="btn btn-danger btn-sm" href="admin_pending_post.php?delete_id=<?= $post['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Pending Approval for Service Ads -->

  <h2 class="mb-4">Services Post</h2>
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>User Name</th>
          <th>Service Title</th>
          <th>Description</th>
          <th>Category</th>
          <th>Province</th>
          <th>District</th>
          <th>Location</th>
          <th>Photos</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($service_ad = $service_ads_result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($service_ad['id']) ?></td>
          <td><?= htmlspecialchars($service_ad['username']) ?></td>
          <td><?= html_entity_decode($service_ad['title']) ?></td>
          <td><?= nl2br(html_entity_decode($service_ad['description'])) ?></td>
          <td><?= htmlspecialchars($service_ad['category']) ?></td>
          <td><?= htmlspecialchars($service_ad['province']) ?></td>
          <td><?= htmlspecialchars($service_ad['district']) ?></td>
          <td><?= htmlspecialchars($service_ad['location']) ?></td>
          <td>
            <?php
              $photos = getServicePhotos($conn, $service_ad['id']);
              if (count($photos) > 0):
                foreach ($photos as $photo):
            ?>
                <a href="<?= htmlspecialchars($photo) ?>" target="_blank" title="View Full Image">
                  <img src="<?= htmlspecialchars($photo) ?>" alt="Service Ad Photo" class="photo-thumb" />
                </a>
            <?php
                endforeach;
              else:
                echo "<span>No Photos</span>";
              endif;
            ?>
          </td>
          <td>
            <a class="btn btn-success btn-sm" href="admin_pending_post.php?approve_service_id=<?= $service_ad['id'] ?>">Approve</a>
            <a class="btn btn-danger btn-sm" href="admin_pending_post.php?delete_service_id=<?= $service_ad['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>