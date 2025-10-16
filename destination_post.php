<?php

// Prevent caching so that "back" button reloads fresh content
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
$conn = new mysqli("localhost", "root", "", "Lakvisit");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get main ad details
$stmt = $conn->prepare("SELECT * FROM destination_ads WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    echo "<p>Ad not found.</p>";
    exit;
}

// Get ad images from ad_images table
$images = $conn->query("SELECT * FROM destination_images WHERE ad_id = $id");

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['feedback'])) {
    $username = $_SESSION['username'] ?? 'Anonymous';
    $comment = $conn->real_escape_string($_POST['comment']);
    
    // Insert feedback
    $conn->query("INSERT INTO destination_feedback (ad_id, username, comment) VALUES ($id, '$username', '$comment')");
    
    // ✅ Redirect to avoid duplicate submission on refresh
    header("Location: ".$_SERVER['PHP_SELF']."?id=".$id."#feedback");
    exit();
}


// Get feedbacks
$feedbacks = $conn->query("SELECT * FROM destination_feedback WHERE ad_id = $id ORDER BY created_at DESC");

// Get related ads in the same district (excluding current ad)
$related_stmt = $conn->prepare("SELECT * FROM destination_ads WHERE district = ? AND id != ? LIMIT 6");
$related_stmt->bind_param("si", $post['district'], $id);
$related_stmt->execute();
$related_ads = $related_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($post['title']) ?> - Lakvisit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet"/>
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
    footer {
      background-color: #000;
    }
    footer p {
      color: #fff;
    }
    @media (max-width: 991.98px) {
      .nav-item.username-wrapper {
        margin-bottom: 10px;
      }
      .navbar-nav > li.nav-item:last-child {
        margin-top: 10px;
      }
    }
    .carousel-item img {
      height: 250px;
      width: 100%;
      object-fit: cover;
      border-radius: 0.375rem;
      border: 1px solid #dee2e6;
    }

    /*Responsive Carousel Styling*/
 
    #adCarousel .carousel-item img {
      object-fit: cover;
      width: 100%;
      height: 400px; /* Default desktop */
      border-radius: 12px;
    }
    @media (min-width: 1200px) {
      #adCarousel .carousel-item img {
        height: 500px; /* Large desktop */
      }
    }
    @media (max-width: 991.98px) {
      #adCarousel .carousel-item img {
        height: 350px; /* Tablet */
      }
    }
    @media (max-width: 575.98px) {
      #adCarousel .carousel-item img {
        height: 250px; /* Mobile */
      }
    }

    
  </style>
</head>
<body>

<!-- Navbar -->
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
        <li class="nav-item"><a class="nav-link">Home</a></li>
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

<!-- Main Container -->
<div class="container my-4">

  <h2 class="mb-3"><?= htmlspecialchars($post['title']) ?></h2>
  

  <!-- Ad Images -->
<?php if ($images->num_rows > 0): ?>
  <div id="adCarousel" class="carousel slide mb-3" data-bs-ride="carousel">

    <!-- Indicators -->
    <div class="carousel-indicators">
      <?php for ($i = 0; $i < $images->num_rows; $i++): ?>
        <button type="button" data-bs-target="#adCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>"></button>
      <?php endfor; ?>
    </div>

    <!-- Carousel Items -->
    <div class="carousel-inner">
      <?php 
      $active = "active";
      $images->data_seek(0); // reset pointer
      while ($img = $images->fetch_assoc()): ?>
        <div class="carousel-item <?= $active ?>">
          <img src="<?= htmlspecialchars($img['image_path']) ?>" 
               class="d-block w-100 rounded border" 
               alt="Ad Image">
        </div>
      <?php 
      $active = ""; 
      endwhile; ?>
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#adCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#adCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>

  <!-- Carousel Auto Slide -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const myCarousel = document.querySelector('#adCarousel');
      if (myCarousel) {
        new bootstrap.Carousel(myCarousel, {
          interval: 3000,   // Auto slide every 3 seconds
          ride: 'carousel', // Start auto play immediately
          pause: 'hover'    // Pause when hovered
        });
      }
    });
  </script>

<?php else: ?>
  <p><em>No images available.</em></p>
<?php endif; ?>


  <!-- Ad Details -->
  <p><strong>Category:</strong> <?= htmlspecialchars($post['category']) ?></p>
  <p><strong>Location:</strong> <?= htmlspecialchars($post['location']) ?>, <?= htmlspecialchars($post['district']) ?> District, <?= htmlspecialchars($post['province']) ?> Province</p>
  <p><?= nl2br(html_entity_decode($post['description'])) ?></p>

  <hr>

  <!-- Feedback Form -->
  <h4>Leave Feedback</h4>
  <form method="POST">
    <div class="mb-3">
      <textarea name="comment" class="form-control" required placeholder="Your feedback..." rows="3"></textarea>
    </div>
    <button type="submit" name="feedback" class="btn btn-primary">Submit</button>
  </form>

  <!-- Feedback List -->
  <div class="mt-4" id="feedback">
    <h5>Feedback</h5>
    <?php if ($feedbacks->num_rows > 0): ?>
      <?php while ($row = $feedbacks->fetch_assoc()): ?>
        <div class="feedback-box mb-2">
          <strong><?= htmlspecialchars($row['username']) ?></strong><br>
          <p><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
          <small class="text-muted"><?= $row['created_at'] ?></small>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No feedback yet.</p>
    <?php endif; ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">

  window.addEventListener('pageshow', function(event) {
    // If page was restored from bfcache, reload it
    if (event.persisted) {
        window.location.reload();
    }
});

</script>
</body>
</html>
