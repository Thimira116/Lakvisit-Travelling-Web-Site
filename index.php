<?php
// Secure session setup (only effective if site is HTTPS)
session_set_cookie_params([
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();
  $servername = "localhost";
  $db_username = "root";
  $db_password = "";
  $dbname = "Lakvisit";
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

    .hero {
      background: url('sigiriya.jpg') no-repeat center center;
      background-size: cover;
      height: 600px;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      transition: background-image 1s ease-in-out;
    }

    .hero img {
      display: none;
    }

    .hero h1 {
      font-size: 4rem;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
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
    }

    footer p {
      color: #fff;
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
            <a class="nav-link active">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="destination.php">Destinations</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="services.php">Services</a>
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

  <section class="hero" id="hero">
    <div>
      <img src="sigiriya.jpg" alt="">
      <img src="galle.jpg" alt="">
      <img src="kandy.jpg" alt="">
      <img src="beach.jpg" alt="">
      <img src="elephant.jpg" alt="">
      <img src="ella.jpg" alt="">
      <img src="panther.jpg" alt="">
      <img src="mountain.jpg" alt="">
      <h1>Explore around the Sri Lanka with Us</h1>
      <p class="lead">Your adventure begins here</p>
      
    </div>
  </section>

  <section class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Why Travel With Us?</h2>
      <div class="row text-center">
        <div class="col-md-4">
          <h5>Customized Tours</h5>
          <p>We design tailor-made travel experiences just for you.</p>
        </div>
        <div class="col-md-4">
          <h5>Affordable Packages</h5>
          <p>Enjoy top destinations at unbeatable prices.</p>
        </div>
        <div class="col-md-4">
          <h5>24/7 Support</h5>
          <p>Our team is here to help you anytime, anywhere.</p>
        </div>
      </div>
    </div>
  </section>

  <hr>

  <section class="py-5 bg-light">
    <div class="container">
      <div class="row text-center">
        <?php
          

          $conn = new mysqli($servername, $db_username, $db_password, $dbname);
          if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            echo "<div class='col-12'><h5 class='text-danger'>Something went wrong. Please try again later.</h5></div>";
          } else {
            $userStmt = $conn->prepare("SELECT COUNT(*) AS total FROM users");
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            $userCount = $userResult->fetch_assoc()['total'];

            $destinationStmt = $conn->prepare("SELECT COUNT(*) AS total FROM destination_ads WHERE approved = 1");
            $destinationStmt->execute();
            $destinationResult = $destinationStmt->get_result();
            $destinationCount = $destinationResult->fetch_assoc()['total'];

            $serviceStmt = $conn->prepare("SELECT COUNT(*) AS total FROM services_ads WHERE status = 'approved'");
            $serviceStmt->execute();
            $serviceResult = $serviceStmt->get_result();
            $serviceCount = $serviceResult->fetch_assoc()['total'];
        ?>
            <div class="col-md-4 mb-4 mb-md-0">
              <h2 class="mb-3">Users</h2>
              <h1 class="display-7 text-success"><?= htmlspecialchars($userCount) ?></h1>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
              <h2 class="mb-3">Destinations</h2>
              <h1 class="display-7 text-success"><?= htmlspecialchars($destinationCount) ?></h1>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
              <h2 class="mb-3">Services</h2>
              <h1 class="display-7 text-success"><?= htmlspecialchars($serviceCount) ?></h1>
            </div>
        <?php
          }
          $conn->close();
        ?>
      </div>
    </div>
  </section>

  <footer class="py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Lakvisit. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const heroSection = document.getElementById('hero');
    const images = Array.from(heroSection.querySelectorAll('img'));
    let currentIndex = 0;

    function rotateHeroBackground() {
      const image = images[currentIndex];
      if (image) {
        heroSection.style.backgroundImage = `url('${image.src}')`;
      }
      currentIndex = (currentIndex + 1) % images.length;
    }

    rotateHeroBackground();
    setInterval(rotateHeroBackground, 5000);
  </script>
</body>
</html>
