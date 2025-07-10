<?php
include 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $check->execute([$username]);
    if ($check->rowCount() > 0) {
        $error = "Username already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status, created_at) VALUES (?, ?, 'employee', 'active', NOW())");
        $stmt->execute([$username, $password]);
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register - Sawasdee POS</title>
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
    .register-wrapper {
      display: flex;
      flex: 1;
    }
    .register-left {
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
    .register-right {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      animation: fadeInRight 1s;
    }
    .register-form {
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
<div class="register-wrapper">
  <div class="register-left">
    <div class="logo">🛒 Sawasdee POS</div>
    <p class="lead">Smart. Simple. Secure.</p>
  </div>
  <div class="register-right">
    <div class="register-form">
      <h3 class="mb-4 text-center">Create Your Account</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="register.php">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
      </form>

      <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
