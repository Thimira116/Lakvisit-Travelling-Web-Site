<?php
session_start();

// Database connection info - update these with your actual DB details
$host = 'localhost';
$db   = 'Lakvisit';
$user = 'root';
$pass = '';    
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Replace this with your actual user validation logic, e.g. DB query
function validate_user($usernameOrEmail, $password) {
    global $pdo;

    // Prepare query to find user by username or email
    $stmt = $pdo->prepare("SELECT username, password FROM users WHERE username = :input OR email = :input LIMIT 1");
    $stmt->execute(['input' => $usernameOrEmail]);
    $user = $stmt->fetch();

    if ($user) {
        // Verify password using password_verify (assuming hashed passwords in DB)
        if (password_verify($password, $user['password'])) {
            return $user['username'];
        }
    }
    return false;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = $_POST['usernameEmail'] ?? '';
    $password = $_POST['password'] ?? '';

    $username = validate_user($usernameOrEmail, $password);

    if ($username !== false) {
        $_SESSION['username'] = $username;
        // Redirect to homepage after login success
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username/email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Lakvisit</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Google Fonts -->
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

    .form-container {
      max-width: 400px;
      margin: 80px auto;
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      color: var(--primary-color);
      margin-bottom: 25px;
      text-align: center;
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
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
          <!-- Removed username display to avoid showing if user is not logged in -->
          <!-- Show only Register button -->
          <li class="nav-item">
            <a class="btn btn-outline-success me-2" href="register.php">Register</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Login Form Section -->
  <div class="container form-container">
    <h2>Login to Your Account</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post" action="">
      <div class="mb-3">
        <label for="usernameEmail" class="form-label">Username or Email</label>
        <input type="text" name="usernameEmail" class="form-control" id="usernameEmail" placeholder="Enter username or email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Login</button>
      </div>
    </form>
    <p class="text-center mt-3">
      <a href="forgot_password.php">Forgot Password?</a>
    </p>

    <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
