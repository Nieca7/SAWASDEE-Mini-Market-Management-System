<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $userId = $user['id'];
        $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$userId]);

        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
            exit();
        } elseif ($user['role'] === 'employee') {
            header("Location: employee_dashboard.php");
            exit();
        } else {
            $error = "Invalid role.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      display: flex;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #edf2f7;
    }
    .login-wrapper {
      display: flex;
      flex: 1;
    }
    .login-left {
      background: linear-gradient(135deg, #3f72af, #112d4e);
      color: white;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      padding: 2rem;
      text-align: center;
      animation: fadeInLeft 1s;
    }
    .login-right {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      animation: fadeInRight 1s;
    }
    .login-form {
      width: 100%;
      max-width: 400px;
      background: #f9f9f9;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .logo {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 1rem;
    }
    @keyframes fadeInLeft {
      from { opacity: 0; transform: translateX(-50px); }
      to { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeInRight {
      from { opacity: 0; transform: translateX(50px); }
      to { opacity: 1; transform: translateX(0); }
    }
  </style>
</head>
<body>
<div class="login-wrapper">
  <div class="login-left">
    <div class="logo">🛒 Sawasdee POS</div>
    <p class="lead">Smart. Simple. Secure.</p>
  </div>
  <div class="login-right">
    <div class="login-form">
      <h3 class="mb-4 text-center">Login to Continue</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
