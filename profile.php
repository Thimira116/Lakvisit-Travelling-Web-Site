<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

// Include your database connection file
include 'db_connection.php'; // Make sure this file defines $conn

$current_user = $_SESSION['username'];

// Fetch user data
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Fetch user's destination posts
$dest_sql = "SELECT * FROM destination_ads WHERE username = ?";
$dest_stmt = $conn->prepare($dest_sql);
$dest_stmt->bind_param("s", $current_user);
$dest_stmt->execute();
$dest_posts = $dest_stmt->get_result();

// Fetch user's service posts
$service_sql = "SELECT * FROM services_ads WHERE username = ?";
$service_stmt = $conn->prepare($service_sql);
$service_stmt->bind_param("s", $current_user);
$service_stmt->execute();
$service_posts = $service_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Profile | Lakvisit</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" />
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
  .navbar {
    background-color: #ffffff;
  }
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
  .username-box {
    border: 2px solid var(--primary-color);
    border-radius: 10px;
    padding: 5px 12px;
    background-color: #f1fdf6;
    color: var(--primary-color);
    font-weight: 600;
  }
  .profile-card {
    background-color: #fff;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 40px;
  }
  .card-post {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    background-color: white;
    margin-bottom: 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
  }
  .section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-top: 40px;
    margin-bottom: 20px;
  }
  .post-img {
    width: 100%;
    height: auto;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
  }
  .modal-header{
    background-color: #00B763;
  }

    a {
      text-decoration: none !important;
    }
    .navbar-brand img {
      height: 40px;
    }

@media (max-width: 768px) {
  .profile-card { padding: 20px; }
  .card-post { padding: 10px; }
  .section-title { font-size: 1.1rem; margin-top: 30px; margin-bottom: 15px; }
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="Lakvisit.png" alt="Lakvisit Logo" class="img-fluid"/>
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
        <?php if (!empty($_SESSION['username'])): ?>
              <li class="nav-item">
                  <a class="nav-link" href="bookmark.php" title="Bookmarks">
                      Bookmarks
                  </a>
              </li>
          <?php endif; ?>
        
        <li class="nav-item username-wrapper d-flex align-items-center me-2 active">
              <a href="profile.php"><span class="nav-link username-box active"><?= htmlspecialchars($_SESSION['username']) ?></span></a>
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


<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-lg-10 col-md-12">
      <!-- User Profile -->
      <div class="profile-card text-center">
        <h3><?= htmlspecialchars($user_data['username'] ?? $user_data['username']) ?></h3>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($user_data['fullname'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email'] ?? 'N/A') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($user_data['phone'] ?? 'N/A') ?></p>
      </div>


      <!-- User Destination Posts -->
<div class="section-title">Your Destination Posts</div>
<div class="row">
  <?php while($ad = $dest_posts->fetch_assoc()): ?>
    <div class="col-md-3 mb-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= html_entity_decode($ad['title']) ?></h5>
          <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($ad['province'] ?? '') ?> - <?= htmlspecialchars($ad['district'] ?? '') ?></h6>
          <p class="card-text mt-auto"><small class="text-muted">Location: <?= htmlspecialchars($ad['location'] ?? '') ?></small></p>

          <?php
            $ad_id = $ad['id'];
            $images = $conn->query("SELECT image_path FROM destination_images WHERE ad_id = $ad_id");
            if ($images && $images->num_rows > 0):
          ?>
            <div id="carouselDest<?= $ad_id ?>" class="carousel slide mb-3" data-bs-ride="carousel">
              <div class="carousel-inner">
                <?php
                  $activeSet = false;
                  while ($img = $images->fetch_assoc()):
                ?>
                  <div class="carousel-item <?= !$activeSet ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($img['image_path']) ?>" class="d-block w-100 rounded border" alt="Destination Image" style="height: 180px; object-fit: cover;">
                  </div>
                <?php
                  $activeSet = true;
                  endwhile;
                ?>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselDest<?= $ad_id ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselDest<?= $ad_id ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
          <?php endif; ?>

          <a href="destination_post.php?id=<?= $ad['id'] ?>" class="btn btn-primary mb-1">View Details</a>
          <a href="delete_destination.php?id=<?= $ad['id'] ?>" class="btn btn-danger delete-btn">Delete Post</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- User Service Posts -->
<div class="section-title">Your Service Posts</div>
<div class="row">
  <?php while($ad = $service_posts->fetch_assoc()): ?>
    <div class="col-md-3 mb-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= html_entity_decode($ad['title']) ?></h5>
          <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($ad['province'] ?? '') ?> - <?= htmlspecialchars($ad['district'] ?? '') ?></h6>
          <p class="card-text mt-auto"><small class="text-muted">Location: <?= htmlspecialchars($ad['location'] ?? '') ?></small></p>

          <?php
            $ad_id = $ad['id'];
            $images = $conn->query("SELECT image_path FROM services_images WHERE ad_id = $ad_id");
            if ($images && $images->num_rows > 0):
          ?>
            <div id="carouselServ<?= $ad_id ?>" class="carousel slide mb-3" data-bs-ride="carousel">
              <div class="carousel-inner">
                <?php
                  $activeSet = false;
                  while ($img = $images->fetch_assoc()):
                ?>
                  <div class="carousel-item <?= !$activeSet ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($img['image_path']) ?>" class="d-block w-100 rounded border" alt="Service Image" style="height: 180px; object-fit: cover;">
                  </div>
                <?php
                  $activeSet = true;
                  endwhile;
                ?>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselServ<?= $ad_id ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselServ<?= $ad_id ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
          <?php endif; ?>

          <a href="service_post.php?id=<?= $ad['id'] ?>" class="btn btn-primary mb-1">View Details</a>
          <a href="delete_service.php?id=<?= $ad['id'] ?>" class="btn btn-danger delete-btn">Delete Post</a>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this post? This action cannot be undone.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function(event) {
      event.preventDefault(); // Prevent default link action
      const deleteUrl = this.getAttribute('href');
      confirmDeleteBtn.setAttribute('href', deleteUrl);
      deleteModal.show();
    });
  });
</script>

</body>
</html>

<?php
// Close connection at the very end
$conn->close();
?>
