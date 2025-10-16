<?php
// Secure session setup
session_set_cookie_params([
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();
include 'db_connection.php'; // Ensure this defines $conn = new mysqli(...)

$user_id = 0;

// ✅ Fix: use username from session to find user ID
if (isset($_SESSION['username'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
}

// If user not found, set a default (optional)
if (!$user_id) {
    $user_id = 0;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Explore the Sri Lanka | Lakvisit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    :root {
      --primary-color: #00B763;
      --accent-color: #E53935;
      --light-bg: #F8F9FA;
      --dark-text: #212529;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-bg);
      color: var(--dark-text);
    }
    a { text-decoration: none !important; }
    .navbar { background-color: #ffffff; }
    .nav-link {
      color: var(--primary-color) !important;
      font-weight: 500;
    }
    .nav-link:hover, .nav-link.active {
      color: var(--accent-color) !important;
    }
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    .btn-primary:hover {
      background-color: var(--accent-color);
      border-color: var(--accent-color);
    }
    h2, h5 { 
      color: var(--primary-color); 
    }
    footer { 
      background-color: #000;
      color: #fff;
      position: relative;
      bottom: 0;
      width: 100%; 
    }
    footer p { 
      color: #fff;
    }
    html, body {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    body > footer {
      margin-top: auto;
    }


    .username-box {
      border: 2px solid var(--primary-color);
      border-radius: 10px;
      padding: 5px 12px;
      background-color: #f1fdf6;
      color: var(--primary-color);
      font-weight: 600;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="index.php">
        <img src="Lakvisit.png" alt="Lakvisit Logo" style="height:40px;"/>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="destination.php">Destinations</a></li>
          <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Help & Support</a></li>
          <li class="nav-item"><a class="nav-link active" href="bookmark.php">Bookmarks</a></li>
          <li class="nav-item username-wrapper d-flex align-items-center me-2">
            <a href="profile.php"><span class="nav-link username-box"><?= htmlspecialchars($_SESSION['username']) ?></span></a>
          </li>
          <li class="nav-item">
            <form method="post" action="logout.php" class="m-0">
              <button type="submit" class="btn btn-danger">Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <?php
  // Database connection
  $conn = new mysqli("localhost", "root", "", "Lakvisit");
  if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
  }

  // Fetch destination bookmarks
  $destQuery = "
    SELECT d.* FROM bookmarks b
    JOIN destination_ads d ON b.post_id = d.id
    WHERE b.user_id = ? AND b.post_type = 'destination'
  ";
  $stmt1 = $conn->prepare($destQuery);
  $stmt1->bind_param("i", $user_id);
  $stmt1->execute();
  $destinations = $stmt1->get_result();

  // Fetch service bookmarks
  $servQuery = "
    SELECT s.* FROM bookmarks b
    JOIN services_ads s ON b.post_id = s.id
    WHERE b.user_id = ? AND b.post_type = 'service'
  ";
  $stmt2 = $conn->prepare($servQuery);
  $stmt2->bind_param("i", $user_id);
  $stmt2->execute();
  $services = $stmt2->get_result();
  ?>

  <div class="container my-5">
    <h2 class="mb-4 text-center">My Bookmarks</h2>

    <!-- DESTINATIONS -->
    <h4 class="mt-5 mb-3"><i class="fa-solid fa-map-location-dot me-2 text-success"></i>Bookmarked Destinations</h4>
    <div class="row">
      <?php if ($destinations->num_rows > 0): ?>
        <?php while ($d = $destinations->fetch_assoc()): ?>
          <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
              <?php
                $imgRes = $conn->query("SELECT image_path FROM destination_images WHERE ad_id = " . intval($d['id']) . " LIMIT 1");
                $img = $imgRes->fetch_assoc()['image_path'] ?? 'no-image.jpg';
              ?>
              <img src="<?= htmlspecialchars($img) ?>" class="card-img-top rounded-top" alt="Destination Image" style="height:180px;object-fit:cover;">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-truncate"><?= htmlspecialchars($d['title']) ?></h5>
                <p class="text-muted small mb-2"><?= htmlspecialchars($d['province']) ?> - <?= htmlspecialchars($d['district']) ?></p>
                <a href="destination_post.php?id=<?= $d['id'] ?>" class="btn btn-primary mt-auto">View Details</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-muted">No bookmarked destinations yet.</p>
      <?php endif; ?>
    </div>

    <!-- SERVICES -->
    <h4 class="mt-5 mb-3"><i class="fa-solid fa-briefcase me-2 text-success"></i>Bookmarked Services</h4>
    <div class="row">
      <?php if ($services->num_rows > 0): ?>
        <?php while ($s = $services->fetch_assoc()): ?>
          <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
              <?php
                $imgRes = $conn->query("SELECT image_path FROM services_images WHERE ad_id = " . intval($s['id']) . " LIMIT 1");
                $img = $imgRes->fetch_assoc()['image_path'] ?? 'no-image.jpg';
              ?>
              <img src="<?= htmlspecialchars($img) ?>" class="card-img-top rounded-top" alt="Service Image" style="height:180px;object-fit:cover;">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-truncate"><?= htmlspecialchars($s['title']) ?></h5>
                <p class="text-muted small mb-2"><?= htmlspecialchars($s['province']) ?> - <?= htmlspecialchars($s['district']) ?></p>
                <a href="service_post.php?id=<?= $s['id'] ?>" class="btn btn-primary mt-auto">View Details</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-muted">No bookmarked services yet.</p>
      <?php endif; ?>
    </div>
  </div>

    <footer class="py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Lakvisit. All rights reserved.</p>
    </div>
  </footer>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
