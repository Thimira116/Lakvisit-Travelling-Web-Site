<?php
session_start();
// Include your database connection file
include 'db_connection.php'; // Make sure this file defines $conn

if (isset($_GET['delete_user_id'])) {
    $id = intval($_GET['delete_user_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}

// See All User Accounts
$all_users_result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - Manage Posts</title>
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
      <h1 class="h4 mb-0 text-center">Admin Panel</h1>
    </div>
  </div>
</nav>

<!-- Admin Panel Content -->
<div class="container mt-5">

  
    <!-- Pending Approval for Regular Posts -->
    <a href="admin_pending_post.php"><h6 class="mb-4">Pending Posts</h2></a>

    <!-- Pending Approval for Service Ads -->
    <a href="admin_approved_post.php"><h6 class="mb-4">Approved Posts</h2></a>
  
  <!--All User Accounts-->
  <h2 class="mb-4"><u>User Accounts</u></h2>
  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Username</th><th>Email</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($user = $all_users_result->fetch_assoc()): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= $user['username'] ?></td>
          <td><?= $user['email'] ?></td>
          <td><a class="btn btn-danger btn-sm" href="?delete_user_id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

    
    
</div>




<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
