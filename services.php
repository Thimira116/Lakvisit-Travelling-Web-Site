<?php
// Secure session setup (only effective if site is HTTPS)
session_set_cookie_params([
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();

// Include your database connection file
include 'db_connection.php'; // Make sure this file defines $conn

// Get user ID if logged in
$user_id = 0;
if (!empty($_SESSION['username'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
}


$user_id = 0;
if (!empty($_SESSION['username'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
}

// Handle POST Requests (Submit or Delete) before rendering HTML

// Insert ad if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_service_ad'])) {
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'];
    $province = $_POST['province'];
    $district = $_POST['district'];
    $location = $_POST['location'];
    $username = $_SESSION['username'];

    // 1. Insert ad details
    $stmt = $conn->prepare("INSERT INTO services_ads (title, description, category, province, district, location, username, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssssss", $title, $description, $category, $province, $district, $location, $username);
    $stmt->execute();
    $ad_id = $stmt->insert_id;
    $stmt->close();

    // 2. Process multiple images upload
    if (!empty($_FILES['images']['name'][0])) {
        $uploadDir = 'uploads/services/'; // Make sure this folder exists & is writable
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['images']['name'][$key]);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowedTypes)) {
                $newFileName = uniqid('img_', true) . '.' . $fileExt;
                $targetFilePath = $uploadDir . $newFileName;
                if (move_uploaded_file($tmp_name, $targetFilePath)) {
                    $img_stmt = $conn->prepare("INSERT INTO services_images (ad_id, image_path) VALUES (?, ?)");
                    $img_stmt->bind_param("is", $ad_id, $targetFilePath);
                    $img_stmt->execute();
                    $img_stmt->close();
                }
            }
        }
    }

    echo "<script>window.location.href='services.php';</script>";
    exit;
}

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    $deleteId = intval($_POST['delete_post_id']);
    $currentUser = $_SESSION['username'];

    // Make sure the user owns the post before deleting
    $checkQuery = $conn->prepare("SELECT id FROM services_ads WHERE id = ? AND username = ?");
    $checkQuery->bind_param("is", $deleteId, $currentUser);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult && $checkResult->num_rows > 0) {
        // Use prepared statements for safe deletion
        // Delete images first
        $deleteImagesStmt = $conn->prepare("DELETE FROM service_images WHERE ad_id = ?");
        $deleteImagesStmt->bind_param("i", $deleteId);
        $deleteImagesStmt->execute();
        $deleteImagesStmt->close();
        
        // Then delete the post
        $deletePostStmt = $conn->prepare("DELETE FROM services_ads WHERE id = ?");
        $deletePostStmt->bind_param("i", $deleteId);
        $deletePostStmt->execute();
        $deletePostStmt->close();

        echo "<script>window.location.href='services.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Services | Lakvisit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
    a {
      text-decoration: none !important;
    }
    .navbar {
      background-color: #ffffff;
    }
    .navbar-brand img {
      height: 40px;
    }
    .nav-link {
      color: var(--primary-color) !important;
      font-weight: 500;
    }
    .nav-link:hover,
    .nav-link.active {
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
    .username-box {
      border: 2px solid var(--primary-color);
      border-radius: 10px;
      padding: 5px 12px;
      background-color: #f1fdf6;
      color: var(--primary-color);
      font-weight: 600;
    }

    @media (max-width: 991.98px) {
      .nav-item.username-wrapper {
        margin-bottom: 10px;
      }
    }
    /* Added CSS for uniform carousel images */
    .carousel-item img {
      height: 250px;       /* fixed height */
      width: 100%;         /* fill container width */
      object-fit: cover;   /* crop image nicely */
      border-radius: 0.375rem; /* same rounding as .rounded */
      border: 1px solid #dee2e6; /* same border as .border */
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="Lakvisit.png" alt="Lakvisit Logo"/>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="destination.php">Destinations</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="services.php">Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.php">Help & Support</a>
        </li>
        <?php if (!empty($_SESSION['username'])): ?>
              <li class="nav-item">
                  <a class="nav-link" href="bookmark.php" title="Bookmarks">
                      Bookmarks
                  </a>
              </li>
          <?php endif; ?>
          
        <?php if (empty($_SESSION['username'])): ?>
          <li class="nav-item">
            <a class="btn btn-outline-success me-2" href="register.php">Register</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-success" href="login.php">Login</a>
          </li>
        <?php else: ?>
          <li class="nav-item username-wrapper d-flex align-items-center me-2">
              <a href="profile.php"><span class="nav-link username-box"><?= htmlspecialchars($_SESSION['username']) ?></span></a>
          </li>
          <li class="nav-item">
            <form method="post" action="logout.php" class="m-0">
              <button type="submit" class="btn btn-danger">Logout</button>
            </form>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-3 d-flex justify-content-between align-items-center flex-wrap gap-3">

  <form class="d-flex flex-wrap gap-2 align-items-center" method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <div class="col-12 col-md-auto">
      <select class="form-select form-select-sm" name="filter_category" aria-label="Filter by category">
        <option value="">All Categories</option>
        <?php
        $category_options = ["Accommodation", "Transportation", "Tours and Activities", "Food and Dining", "Travel Planning and Support", "Events and MICE Services", "Wellness and Spa", "Shopping and Retail"];
        $selected_category = $_GET['filter_category'] ?? '';
        foreach ($category_options as $cat_option) {
          $selected = ($selected_category == $cat_option) ? "selected" : "";
          echo "<option value=\"$cat_option\" $selected>$cat_option</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-12 col-md-auto">
      <select class="form-select form-select-sm" name="filter_province" aria-label="Filter by province">
        <option value="">All Provinces</option>
        <?php
        $province_options = ["Western", "Central", "Southern", "Northern", "Eastern", "North Western", "North Central", "Uva", "Sabaragamuwa"];
        $selected_province = $_GET['filter_province'] ?? '';
        foreach ($province_options as $prov_option) {
          $selected = ($selected_province == $prov_option) ? "selected" : "";
          echo "<option value=\"$prov_option\" $selected>$prov_option</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-12 col-md-auto">
      <select class="form-select form-select-sm" name="filter_district" aria-label="Filter by district">
        <option value="">All Districts</option>
        <?php
        $district_options = ["Colombo", "Gampaha", "Kalutara", "Kandy", "Matale", "Nuwara Eliya", "Galle", "Matara", "Hambantota", "Jaffna", "Kilinochchi", "Mannar", "Vavuniya", "Mullaitivu", "Trincomalee", "Batticaloa", "Ampara", "Kurunegala", "Puttalam", "Anuradhapura", "Polonnaruwa", "Badulla", "Monaragala", "Ratnapura", "Kegalle"];
        $selected_district = $_GET['filter_district'] ?? '';
        foreach ($district_options as $dist_option) {
          $selected = ($selected_district == $dist_option) ? "selected" : "";
          echo "<option value=\"$dist_option\" $selected>$dist_option</option>";
        }
        ?>
      </select>
    </div>

    <div class="d-none d-md-block col-md-auto d-flex gap-2">
      <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
      <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn btn-outline-primary btn-sm">Reset</a>
    </div>

    <div class="w-100 d-block d-md-none">
      <button type="submit" class="btn btn-outline-primary btn-sm w-100 mt-2">Filter</button>
      <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn btn-outline-primary btn-sm w-100 mt-2">Reset</a>
    </div>
  </form>

  <?php if (!empty($_SESSION['username'])): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postServiceModal">Add Service</button>
  <?php endif; ?>
</div>

<div class="modal fade" id="postServiceModal" tabindex="-1" aria-labelledby="postServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="postServiceModalLabel">Post a Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="title" class="form-label">Service</label>
            <input type="text" name="title" id="title" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
          </div>
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
              <option value="">Select Category</option>
              <option>Accommodation</option>
              <option>Transportation</option>
              <option>Tours and Activities</option>
              <option>Food and Dining</option>
              <option>Travel Planning and Support</option>
              <option>Events and MICE Services</option>
              <option>Wellness and Spa</option>
              <option>Shopping and Retail</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="province" class="form-label">Province</label>
            <select class="form-select" id="province" name="province" required>
              <option value="">Select Province</option>
              <option>Western</option>
              <option>Central</option>
              <option>Southern</option>
              <option>Northern</option>
              <option>Eastern</option>
              <option>North Western</option>
              <option>North Central</option>
              <option>Uva</option>
              <option>Sabaragamuwa</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="district" class="form-label">District</label>
            <select class="form-select" id="district" name="district" required>
              <option value="">Select District</option>
              <option>Colombo</option>
              <option>Gampaha</option>
              <option>Kalutara</option>
              <option>Kandy</option>
              <option>Matale</option>
              <option>Nuwara Eliya</option>
              <option>Galle</option>
              <option>Matara</option>
              <option>Hambantota</option>
              <option>Jaffna</option>
              <option>Kilinochchi</option>
              <option>Mannar</option>
              <option>Vavuniya</option>
              <option>Mullaitivu</option>
              <option>Trincomalee</option>
              <option>Batticaloa</option>
              <option>Ampara</option>
              <option>Kurunegala</option>
              <option>Puttalam</option>
              <option>Anuradhapura</option>
              <option>Polonnaruwa</option>
              <option>Badulla</option>
              <option>Monaragala</option>
              <option>Ratnapura</option>
              <option>Kegalle</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="location" class="form-label">Location (City, Street etc.)</label>
            <input type="text" class="form-control" id="location" name="location" required>
          </div>
          <div class="mb-3">
            <label for="images" class="form-label">Upload Photos</label>
            <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple required>
          </div>
          <div>
            <p class="text-primary">Your post will appear once approved...</p>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="submit" name="submit_service_ad" class="btn btn-primary">Submit</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container mt-5">
  <div class="row">
    <?php
    // THIS IS THE CORRECTED AND PROPERLY PLACED FILTERING LOGIC
    $sql = "SELECT * FROM services_ads WHERE status='approved'";
    $params = [];
    $types = "";

    $filter_category = $_GET['filter_category'] ?? '';
    $filter_province = $_GET['filter_province'] ?? '';
    $filter_district = $_GET['filter_district'] ?? '';

    if (!empty($filter_category)) {
      $sql .= " AND category = ?";
      $params[] = $filter_category;
      $types .= "s";
    }
    if (!empty($filter_province)) {
      $sql .= " AND province = ?";
      $params[] = $filter_province;
      $types .= "s";
    }
    if (!empty($filter_district)) {
      $sql .= " AND district = ?";
      $params[] = $filter_district;
      $types .= "s";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
      $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0):
      while ($ad = $result->fetch_assoc()):
    ?>
      <div class="col-md-3 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= html_entity_decode($ad['title']) ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($ad['province']) ?> - <?= htmlspecialchars($ad['district']) ?></h6>
            <p class="card-text mt-auto"><small class="text-muted">Location: <?= htmlspecialchars($ad['location']) ?></small></p>
            
            <?php
            $ad_id = $ad['id'];
            $image_result = $conn->query("SELECT image_path FROM services_images WHERE ad_id = $ad_id");
            if ($image_result && $image_result->num_rows > 0):
            ?>
              <div id="carouselAd<?= $ad_id ?>" class="carousel slide mb-3" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <?php
                  $activeSet = false;
                  while ($img = $image_result->fetch_assoc()):
                  ?>
                    <div class="carousel-item <?= !$activeSet ? 'active' : '' ?>">
                      <img src="<?= htmlspecialchars($img['image_path']) ?>" class="d-block w-100 rounded border" alt="Ad Image" style="height: 180px; object-fit: cover;">
                    </div>
                  <?php
                    $activeSet = true;
                  endwhile;
                  ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselAd<?= $ad_id ?>" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselAd<?= $ad_id ?>" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
            <?php endif; ?>

            <a href="services_post.php?id=<?= $ad['id'] ?>" class="btn btn-primary">View Details</a>

            <?php if (!empty($_SESSION['username'])): ?>
              <?php
                  $isBookmarked = false;
                  if ($user_id) {
                      $checkBookmark = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND post_id = ? AND post_type = 'service'");
                      $checkBookmark->bind_param("ii", $user_id, $ad['id']);
                      $checkBookmark->execute();
                      $checkBookmark->store_result();
                      $isBookmarked = $checkBookmark->num_rows > 0;
                      $checkBookmark->close();
                  }
              ?>
              <button class="btn btn-outline-danger bookmark-btn mt-2"
                      data-id="<?= $ad['id'] ?>"
                      data-type="service">
                  <i class="bi <?= $isBookmarked ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                  <?= $isBookmarked ? ' Bookmarked' : ' Add to Bookmark' ?>
              </button>
            <?php endif; ?>


          </div>
        </div>
      </div>
    <?php endwhile; ?>
    </div>
    <?php
    else:
      echo "<p>No services found.</p>";
    endif;
    $stmt->close();
    ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
      $('.bookmark-btn').click(function() {
          var btn = $(this);
          var postId = btn.data('id');
          var postType = btn.data('type');

          $.post('bookmark_action.php', { post_id: postId, post_type: postType }, function(response) {
              if (response.status === 'added') {
                  btn.html('<i class="bi bi-heart-fill text-danger"></i> Bookmarked');
              } else if (response.status === 'removed') {
                  btn.html('<i class="bi bi-heart"></i> Add to Bookmark');
              } else if (response.status === 'error') {
                  alert(response.message);
              }
          }, 'json');
      });
  });
</script>


</body>
</html>