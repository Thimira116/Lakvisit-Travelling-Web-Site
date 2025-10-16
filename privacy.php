<?php
// Secure session setup (only effective if site is HTTPS)
session_set_cookie_params([
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Privacy Policy - Lakvisit</title>
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
    <h1>Privacy Policy</h1>
    <p class="text-muted">Effective Date: 11/10/2025</p>
    <hr>
  </div>

  <!-- Privacy Card -->
  <div class="container">
    <div class="terms-card">
      <p>This Privacy Policy describes how <strong>Lakvisit</strong> collects, uses, and protects your personal information when you access and use our website.</p>

      <h3>1. Information We Collect</h3>
      <ul>
        <li><strong>Personal Information:</strong> Name, email address, and phone number during registration.</li>
        <li><strong>Usage Data:</strong> IP address, browser type, device information, and pages visited on our website.</li>
        <li><strong>Content Data:</strong> Information you provide when posting ads, reviews, comments, or services.</li>
      </ul>

      <h3>2. How We Use Your Information</h3>
      <ul>
        <li>To provide and improve our travel services.</li>
        <li>To manage your account and enable user-to-user interaction.</li>
        <li>To send notifications, updates, or promotional offers.</li>
        <li>To monitor website usage and prevent fraudulent activities.</li>
        <li>To comply with legal obligations.</li>
      </ul>

      <h3>3. Sharing of Information</h3>
      <ul>
        <li>We do not sell your personal data to third parties.</li>
        <li>We may share your information with service providers who help operate our website.</li>
        <li>Information may be disclosed if required by law or to protect the rights and safety of our users.</li>
      </ul>

      <h3>4. Cookies and Tracking</h3>
      <ul>
        <li>We use cookies and similar technologies to enhance your browsing experience, remember preferences, and analyze traffic. You can disable cookies in your browser settings, but some features may not function properly.</li>
      </ul>

      <h3>5. Data Security</h3>
      <ul>
        <li>We implement reasonable technical and organizational measures to protect your personal data. However, no method of transmission over the Internet is completely secure.</li>
      </ul>

      <h3>6. Your Rights</h3>
      <ul>
        <li>Access, update, or delete your personal data by contacting us.</li>
        <li>Opt out of marketing communications anytime.</li>
        <li>Request a copy of the personal information we hold about you.</li>
      </ul>

      <h3>7. Third-Party Links</h3>
      <ul>
        <li>Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of those websites.</li>
      </ul>

      <h3>8. Children’s Privacy</h3>
      <ul>
        <li>Our services are not intended for users under 16 years old. We do not knowingly collect personal data from children.</li>
      </ul>

      <h3>9. Changes to This Privacy Policy</h3>
      <ul>
        <li>We may update these Privacy Policies at any time without notice. Continued use of the website constitutes acceptance of updated Privcy Policy.</li>
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
