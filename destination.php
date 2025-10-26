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

$user_id = 0;
if (isset($_SESSION['username'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $stmt->bind_result($id);
    if ($stmt->fetch()) {
        $user_id = $id;
    }
    $stmt->close();
}

// Handle Delete Post request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post_id'])) {
    if (!empty($_SESSION['username'])) {
        $post_id = intval($_POST['delete_post_id']);
        $conn_del = new mysqli($servername, $db_username, $db_password, $dbname);
        if ($conn_del->connect_error) {
            die("Connection failed: " . $conn_del->connect_error);
        }
        // Make sure post belongs to the logged-in user before deleting
        $stmt = $conn_del->prepare("DELETE FROM destination_ads WHERE id = ? AND username = ?");
        $stmt->bind_param("is", $post_id, $_SESSION['username']);
        $stmt->execute();
        $stmt->close();
        $conn_del->close();
        // Redirect to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Destinations | Lakvisit</title>
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
      /* ADDITIONAL GAP BETWEEN LOGOUT AND POST AD BUTTON ON SMALL SCREENS */
      .navbar-nav > li.nav-item:last-child {
        margin-top: 10px;
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
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link active" href="destination.php">Destinations</a></li>
          <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Help & Support</a></li>
          <?php if (!empty($_SESSION['username'])): ?>
              <li class="nav-item">
                  <a class="nav-link" href="bookmark.php" title="Bookmarks">
                      Bookmarks
                  </a>
              </li>
          <?php endif; ?>

          <?php if (empty($_SESSION['username'])): ?>
            <li class="nav-item"><a class="btn btn-outline-success me-2" href="register.php">Register</a></li>
            <li class="nav-item"><a class="btn btn-success" href="login.php">Login</a></li>
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

<!-- Additional PHP setup for filters -->
<?php

$filter_category = $_GET['filter_category'] ?? '';
$selectedProvince = $_GET['filter_province'] ?? '';
$selectedDistrict = $_GET['filter_district'] ?? '';

// Get filters from query string
$filter_category = isset($_GET['filter_category']) ? $conn->real_escape_string($_GET['filter_category']) : '';
$filter_province = isset($_GET['filter_province']) ? $conn->real_escape_string($_GET['filter_province']) : '';
$filter_district = isset($_GET['filter_district']) ? $conn->real_escape_string($_GET['filter_district']) : '';

// Build SQL query with filters
$sql = "SELECT * FROM destination_ads WHERE approved = 1";

if (!empty($filter_category)) {
    $sql .= " AND category = '$filter_category'";
}
if (!empty($filter_province)) {
    $sql .= " AND province = '$filter_province'";
}
if (!empty($filter_district)) {
    $sql .= " AND district = '$filter_district'";
}

$result = $conn->query($sql);
?>

<div class="container mt-3 d-flex justify-content-between align-items-center flex-wrap gap-3">

  <form class="d-flex flex-wrap gap-2 align-items-center" method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
    <div class="col-12 col-md-auto">
      <select class="form-select form-select-sm" name="filter_category" aria-label="Filter by category">
        <option value="">All Categories</option>
        <?php
        $category = ["Cultural and Heritage Tours", "Beach Holidays and Water Sports", "Wildlife Safaris", "Hill Country Retreats", "Adventure Activities"];
        foreach ($category as $cat) {
          $selected = ($filter_category == $cat) ? "selected" : "";
          echo "<option $selected>$cat</option>";
        }
        ?>
      </select>
    </div>

    <div class="col-12 col-md-auto">
        <select class="form-select form-select-sm" name="filter_province" aria-label="Filter by province">
            <option value="">All Provinces</option>
            <?php
            $provinces = ["Western", "Central", "Southern", "Northern", "Eastern", "North Western", "North Central", "Uva", "Sabaragamuwa"];
            foreach ($provinces as $prov) {
                $selected = ($filter_province == $prov) ? "selected" : "";
                echo "<option $selected>$prov</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-12 col-md-auto">
        <select class="form-select form-select-sm" name="filter_district" aria-label="Filter by district">
            <option value="">All Districts</option>
            <?php
            $districts = ["Colombo", "Gampaha", "Kalutara", "Kandy", "Matale", "Nuwara Eliya", "Galle", "Matara", "Hambantota", "Jaffna", "Kilinochchi", "Mannar", "Vavuniya", "Mullaitivu", "Trincomalee", "Batticaloa", "Ampara", "Kurunagala", "Puttalam","Anuradhapura", "Polonnaruwa", "Badulla", "Monaragala", "Ratnapura", "Kegalle"];
            foreach ($districts as $dist) {
                $selected = ($filter_district == $dist) ? "selected" : "";
                echo "<option $selected>$dist</option>";
            }
            ?>
        </select>
    </div>

    <!-- Filter and Reset Buttons for larger screens (md and up) -->
    <div class="d-none d-md-block col-md-auto d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary btn-sm">Filter</button>
        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn btn-outline-primary btn-sm">Reset</a>
    </div>

    <!-- Filter and Reset Buttons for small screens (below md) -->
    <div class="w-100 d-block d-md-none">
        <button type="submit" class="btn btn-outline-primary btn-sm w-100 mt-2">Filter</button>
        <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="btn btn-outline-primary btn-sm w-100 mt-2">Reset</a>
    </div>
</form>
  <?php if (!empty($_SESSION['username'])): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postAdModal">Add Destination</button>
  <?php endif; ?>
</div>


  <div class="modal fade" id="postAdModal" tabindex="-1" aria-labelledby="postAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="post_ad.php" method="POST" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="postAdModalLabel">Add a Destination</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="adTitle" class="form-label">Place Name</label>
              <input type="text" class="form-control" id="adTitle" name="title" required>
            </div>
            <div class="mb-3">
              <label for="adDescription" class="form-label">Description</label>
              <textarea class="form-control" id="adDescription" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category" required>
              <option value="">Select Category</option>
              <option>Cultural and Heritage Tours</option>
              <option>Beach Holidays and Water Sports</option>
              <option>Wildlife Safaris</option>
              <option>Hill Country Retreats</option>
              <option>Adventure Activities</option>
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
            <button type="submit" class="btn btn-success">Submit</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  
<div class="container my-5">

  <?php
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Build filter query parts
  $filterConditions = ["approved = 1"];
  $params = [];
  $types = "";

  if (!empty($filter_category)) {
    $filterConditions[] = "category = ?";
    $params[] = $filter_category;
    $types .= "s";
  }
  if (!empty($selectedProvince)) {
    $filterConditions[] = "province = ?";
    $params[] = $selectedProvince;
    $types .= "s";
  }
  if (!empty($selectedDistrict)) {
    $filterConditions[] = "district = ?";
    $params[] = $selectedDistrict;
    $types .= "s";
  }

  $whereSql = "";
  if (count($filterConditions) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $filterConditions);
  }

  $stmt = $conn->prepare("SELECT * FROM destination_ads $whereSql");
  if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
  }

  if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
  }

  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0):
  ?>
    <div class="row">
    <?php
    //$user_id = $_SESSION['user_id'] ?? null;
    while ($ad = $result->fetch_assoc()): ?>
      <div class="col-md-3 mb-4">
        <div class="card h-100 shadow-sm position-relative">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= html_entity_decode($ad['title']) ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($ad['province']) ?> - <?= htmlspecialchars($ad['district']) ?></h6>
            <p class="card-text mt-auto"><small class="text-muted">Location: <?= htmlspecialchars($ad['location']) ?></small></p>

            <p class="card-text"><small class="text-muted">Views: <?= intval($ad['views']) ?></small></p>

            <?php
            $ad_id = $ad['id'];
            $images = $conn->query("SELECT image_path FROM destination_images WHERE ad_id = $ad_id");
            if ($images && $images->num_rows > 0):
            ?>
              <div id="carouselAd<?= $ad_id ?>" class="carousel slide mb-3" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <?php
                  $activeSet = false;
                  while ($img = $images->fetch_assoc()):
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

            <a href="destination_post.php?id=<?= $ad['id'] ?>" class="btn btn-primary">View Details</a>

            <?php if (!empty($_SESSION['username'])): ?>
              <?php
                $isBookmarked = false;
                if ($user_id) {
                  $checkBookmark = $conn->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND post_id = ? AND post_type = 'destination'");
                  $checkBookmark->bind_param("ii", $user_id, $ad['id']);
                  $checkBookmark->execute();
                  $checkBookmark->store_result();
                  $isBookmarked = $checkBookmark->num_rows > 0;
                  $checkBookmark->close();
                }
              ?>
              <button class="btn btn-outline-danger bookmark-btn mt-2"
                      data-id="<?= $ad['id'] ?>"
                      data-type="destination">
                <i class="bi <?= $isBookmarked ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
                <?= $isBookmarked ? 'Bookmarked' : 'Add to Bookmark' ?>
              </button>
            <?php endif; ?>



          </div>
        </div>
      </div>
    <?php endwhile; ?>
    </div>
  <?php
  else:
    echo "<p>No destinations found.</p>";
  endif;

  $stmt->close();
  $conn->close();
  ?>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


  <script>
    function showDeleteModal(postId) {
      document.getElementById('delete_post_id_modal').value = postId;
      var modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
      modal.show();
    }

    document.querySelectorAll('.bookmark-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const postId = this.dataset.id;
        const postType = this.dataset.type;
        const button = this;

        fetch('bookmark_action.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `post_id=${postId}&post_type=${postType}`
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'added') {
            button.innerHTML = '<i class="bi bi-heart-fill text-danger"></i> Bookmarked';
          } else if (data.status === 'removed') {
            button.innerHTML = '<i class="bi bi-heart"></i> Add to Bookmark';
          } else {
            alert(data.message || 'Something went wrong');
          }
        });
      });
    });
  </script>

</body>
</html>
