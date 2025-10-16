<?php
session_start();
date_default_timezone_set('Asia/Colombo'); // ✅ Set timezone

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

$message = '';
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];

    // ✅ Check token
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() LIMIT 1");
    $stmt->execute(['token' => $token]);
    $reset = $stmt->fetch();

    if ($reset) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // ✅ Update password
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $hashedPassword, 'id' => $reset['user_id']]);

        // ✅ Delete used token
        $pdo->prepare("DELETE FROM password_resets WHERE token = :token")->execute(['token' => $token]);

        $message = "✅ Password updated successfully! <a href='login.php'>Login here</a>";
    } else {
        // Debugging info (remove in production)
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token LIMIT 1");
        $stmt->execute(['token' => $token]);
        $checkToken = $stmt->fetch();

        if (!$checkToken) {
            $message = "❌ Invalid token (not found in DB).";
        } else {
            $message = "⏳ Token found but expired. Expired at: " . $checkToken['expires_at'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Reset Password | Lakvisit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="form-container p-4 bg-white shadow rounded">
    <h3>Reset Password</h3>
    <?php if ($message): ?>
      <div class="alert alert-info"><?= $message ?></div>
    <?php else: ?>
      <form method="post">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <div class="mb-3">
          <label for="password" class="form-label">New Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
