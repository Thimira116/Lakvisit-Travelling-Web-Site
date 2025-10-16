<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us | Lakvisit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

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

<!--Navbar-->

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
        <li class="nav-item"><a class="nav-link" href="destination.php">Destinations</a></li>
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link active" href="contact.php">Help & Support</a></li>
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

<!-- Contact Us -->

<section class="py-5">
  <div class="container">
    <h2 class="text-center mb-5">Contact Us</h2>
    <div class="row">

    <hr>

      <!-- Contact Info -->

      <div class="col-md-5 mb-4">
        <h5>Contact Information</h5>
        <p><strong>Phone:</strong> +94 76 407 0611</p>
        <p><strong>Email:</strong> lakvisitinfo@gmail.com</p>
        <!--<p><strong>Address:</strong> No. 123, Main Street, Colombo, Sri Lanka</p>-->

        <hr>
        
        <h5 class="mt-4">Follow Us</h5>
        <p>
          
          <a href="#" class="text-decoration-none me-3">
            <i class="bi bi-facebook fs-1" style="color:#00B763;"></i>
          </a>
          <a href="#" class="text-decoration-none me-3">
            <i class="bi bi-whatsapp fs-1" style="color:#00B763;"></i>
          </a>
          <a href="#" class="text-decoration-none">
            <i class="bi bi-instagram fs-1" style="color:#00B763;"></i>
          </a>

        </p>

        <hr>
        <div>
        <a href="terms.php"><h5>Terms & Conditions</h5></a>
        <br>
        <a href="privacy.php"><h5>Privacy Policies</h5></a>
      </div>

      </div>


  <!-- FAQ and User Guide Section -->
  <div class="col-md-7">

    <!-- Section Title -->
    <h3 class="mb-4">F&Q</h3>

    <!-- FAQ Accordion -->
    <div class="accordion" id="faqAccordion">
      
        <!-- FAQ 1: Create Account -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeading1">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
              How do I create an account and log in?
            </button>
          </h2>
          <div id="faq1" class="accordion-collapse collapse" aria-labelledby="faqHeading1" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Click the "Register" button on the top menu, fill in your details, and submit. After registration, use your username or email and password to log in via the "Login" button.
            </div>
          </div>
        </div>

        <!-- FAQ 2: Add Destination/Service -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeading3">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
              How do I add a destination or service post?
            </button>
          </h2>
          <div id="faq3" class="accordion-collapse collapse" aria-labelledby="faqHeading3" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Log in and click the "Add Service" or "Add Destination" button. Fill out the form with details and upload images. Submit the post; it will appear once approved by the admin.
            </div>
          </div>
        </div>

        <!-- FAQ 3: Edit or Delete Post -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeading4">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
              How do I delete my post?
            </button>
          </h2>
          <div id="faq4" class="accordion-collapse collapse" aria-labelledby="faqHeading4" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Go to your profile page, find the post you want to delete. Click "Delete" to remove the post entirely. Only your own posts can be deleted.
            </div>
          </div>
        </div>

        <!-- FAQ 4: Contact Support -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeading5">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
              How can I contact support if I face issues?
            </button>
          </h2>
          <div id="faq5" class="accordion-collapse collapse" aria-labelledby="faqHeading5" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Use the live chat at the bottom corner of the page or email us at <b>support@lakvisit.com</b>. Our team will assist you as soon as possible.
            </div>
          </div>
        </div>

        <!-- FAQ 5: Search Services -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="faqHeading6">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" aria-expanded="false" aria-controls="faq6">
              How do I search for services by category or location?
            </button>
          </h2>
          <div id="faq6" class="accordion-collapse collapse" aria-labelledby="faqHeading6" data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Use the filters at the top of the Services page to select a category, province, and district. Click "Filter" to see relevant services.
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>  
</section>

  <br><br>

  <footer class="py-4 mt-5">
    <div class="container text-center">
      <p class="mb-0">&copy; 2025 Lakvisit. All rights reserved.</p>
    </div>
  </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
