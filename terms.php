<?php
// Secure session setup (only effective if site is HTTPS)
session_set_cookie_params([
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();
include 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Terms and Conditions - Lakvisit</title>
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

    .page-header {
      text-align: center;
      margin: 40px 0;
    }

    .page-header h1 {
      font-weight: 700;
      color: var(--primary-color);
    }

    .terms-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 40px;
      margin-bottom: 50px;
    }

    .terms-card h3 {
      margin-top: 30px;
      font-weight: 500;
      color: var(--primary-color);
    }

    .terms-card p, .terms-card ul {
      margin-top: 10px;
      font-size: 15px;
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
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="destination.php">Destinations</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="services.php">Services</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="contact.php">Help & Support</a>
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

  <!-- Page Header -->
  <div class="page-header">
    <h1>Terms and Conditions</h1>
    <p class="text-muted">Effective Date: 11/10/2025</p>
    <hr>
  </div>

  <!-- Terms Card -->
  <div class="container">
    <div class="terms-card">
      <p>Welcome to <strong>Lakvisit</strong>. By accessing or using our website, you agree to comply with and be bound by the following Terms and Conditions. If you do not agree, please do not use our website.</p>

      <h3>1. Acceptance of Terms</h3>
      <ul>
        <li>By using this website, you agree to these Terms and Conditions and our Privacy Policy. These terms apply to all users, including guests, registered users, advertisers, and contributors.</li>
      </ul>

      <h3>2. User Responsibilities</h3>
      <ul>
        <li>You must be at least 16 years old to use this website.</li>
        <li>You agree to use the website responsibly and lawfully.</li>
        <li>Users must not post, upload, or share illegal, offensive, or harmful content.</li>
        <li>You may not interfere with the functionality of the website, including hacking or using automated scripts.</li>
        <li>You are responsible for any activity under your account.</li>
      </ul>

      <h3>3. User Content</h3>
      <ul>
        <li>Users may post destination or service ads, reviews, or comments.</li>
        <li>You retain ownership of your content, but by posting, you grant <strong>Lakvisit</strong> a license to display, modify, or distribute it.</li>
        <li>Content must be accurate, lawful, and respectful of other users.</li>
      </ul>

      <h3>4. Intellectual Property</h3>
      <ul>
        <li>All content on this website, including text, images, graphics, logos, and software, is owned by <strong>Lakvisit</strong> or its licensors. You may not copy, distribute, or reproduce content without prior written permission.</li>
      </ul>

      <h3>5. Advertisements and Listings</h3>
      <ul>
        <li>Users posting services or destinations must provide accurate information.</li>
        <li><strong>Lakvisit</strong> reserves the right to approve, reject, or remove any content that violates these terms.</li>
        <li>We are not responsible for the accuracy, legality, or quality of user-submitted ads or services.</li>
      </ul>

      

      <h3>6. Disclaimers</h3>
      <ul>
        <li>We do not guarantee that all information on the website is accurate or complete. Use of the website is at your own risk.</li>
      </ul>

      <h3>7. Limitation of Liability</h3>
      <ul>
        <li><strong>Lakvisit</strong> is not liable for damages or losses arising from your use of the website or reliance on user-submitted content. We are not responsible for disputes between users, including travel bookings, services, or accommodations.</li>
      </ul>

      <h3>8. Privacy</h3>
      <ul>
        <li>Your use of the website is governed by our <a href="privacy.php" target="_blank">Privacy Policy</a>.</li>
      </ul>

      <h3>9. Governing Law</h3>
      <ul>
        <li>These terms are governed by the laws of <strong>Sri Lanka</strong>. Disputes will be resolved in the courts of <strong>Sri Lanka</strong>.</li>
      </ul>

      <h3>10. Changes to Terms</h3>
      <ul>
        <li>We may update these Terms at any time without notice. Continued use of the website constitutes acceptance of updated Terms and Conditions.</li>
      </ul>

    </div>
  </div>

  <!-- Footer -->
  <footer class="py-4">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Lakvisit. All rights reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
