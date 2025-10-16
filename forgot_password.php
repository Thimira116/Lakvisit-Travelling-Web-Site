<?php
session_start();
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

date_default_timezone_set("Asia/Colombo"); // ✅ Set your timezone

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 day')); // ✅ valid for 24 hours

        // Delete old tokens for this user (optional cleanup)
        $pdo->prepare("DELETE FROM password_resets WHERE user_id = :user_id")
            ->execute(['user_id' => $user['id']]);

        // Save token
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, token, expires_at) 
            VALUES (:user_id, :token, :expires_at)
        ");
        $stmt->execute([
            'user_id'   => $user['id'],
            'token'     => $token,
            'expires_at'=> $expires
        ]);

        // Reset link
        $resetLink = "http://localhost/Lakvisit/reset_password.php?token=" . urlencode($token);

        // TODO: Replace with real email sending (PHPMailer or mail())
        $message = "✅ Password reset link (valid for 24h): <a href='$resetLink'>$resetLink</a>";
    } else {
        $message = "⚠️ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forgot Password | Lakvisit</title>
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

    h2, h5 {
      color: var(--primary-color);
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
<body class="bg-light">
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

<div class="container mt-5">
  <div class="form-container p-4 bg-white shadow rounded">
    <h3>Forgot Password</h3>
    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label for="email" class="form-label">Enter your registered email</label>
        <input type="email" name="email" id="email" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
    </form>
  </div>
</div>
</body>
</html>
