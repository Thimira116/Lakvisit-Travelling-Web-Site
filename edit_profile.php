<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Avidimu";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$current_user = $_SESSION['username'];
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $fullname = $_POST['fullname'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $new_username = $_POST['username'];
  $new_password = $_POST['password'];

  // Hash the new password only if it's not empty
  if (!empty($new_password)) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_sql = "UPDATE users SET fullname = ?, email = ?, phone = ?, username = ?, password = ? WHERE username = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssss", $fullname, $email, $phone, $new_username, $hashed_password, $current_user);
  } else {
    $update_sql = "UPDATE users SET fullname = ?, email = ?, phone = ?, username = ? WHERE username = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssss", $fullname, $email, $phone, $new_username, $current_user);
  }

  if ($stmt->execute()) {
    $_SESSION['username'] = $new_username;
    $_SESSION['message'] = "Profile updated successfully.";
    header("Location: profile.php");
    exit();
  } else {
    $error = "Failed to update profile.";
  }
  $stmt->close();
}

// Fetch current user data
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
      background-color: #f8f9fa;
    }
    .card {
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
          <img src="Lakvisit.png" alt="Avidimu Logo"/>
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
            <li class="nav-item"><a class="nav-link" href="contact.php">Help & Support</a></li>
            <?php if (!empty($_SESSION['username'])): ?>
              <li class="nav-item">
                  <a class="nav-link" href="bookmark.php" title="Bookmarks">
                      Bookmarks
                  </a>
              </li>
            <?php endif; ?>

            <!--<?php if (empty($_SESSION['username'])): ?>
              <li class="nav-item"><a class="btn btn-outline-success me-2" href="register.php">Register</a></li>
              <li class="nav-item"><a class="btn btn-success" href="login.php">Login</a></li>
            <?php else: ?>-->
              <li class="nav-item username-wrapper d-flex align-items-center me-2 active">
                <a href="profile.php"><span class="nav-link username-box active"><?= htmlspecialchars($_SESSION['username']) ?></span></a>
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

    <div class="container" style="margin-top: 60px; max-width: 600px;">
    <div class="card">
      <h3 class="mb-4">Edit Profile</h3>
      <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <form method="post">
        <div class="mb-3">
          <label for="fullname" class="form-label">Full Name</label>
          <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user_data['fullname']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone</label>
          <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">New Password (leave blank to keep current password)</label>
          <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-success">Update Profile</button>
        <a href="Profile.php" class="btn btn-danger">Cancel</a>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>