<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();

    if (!password_verify($current, $user['password'])) {
        $message = "❌ Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $message = "❌ New passwords do not match.";
    } else {
        $newHashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$newHashed, $_SESSION['username']]);
        $message = "✅ Password updated successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password - Sawasdee POS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #dfe9f3, #ffffff);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      width: 100%;
      max-width: 500px;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .form-control:focus {
      box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
    }
    .toggle-password {
      cursor: pointer;
    }
  </style>
</head>
<body>
<div class="card p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Change Password</h4>
    <a href="admin_profile.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert <?= str_contains($message, '✅') ? 'alert-success' : 'alert-danger' ?>">
      <?= $message ?>
    </div>
  <?php endif; ?>

  <form method="POST" novalidate>
    <div class="mb-3 position-relative">
      <label class="form-label">Current Password</label>
      <input type="password" name="current_password" class="form-control" required>
    </div>

    <div class="mb-3 position-relative">
      <label class="form-label">New Password</label>
      <div class="input-group">
        <input type="password" name="new_password" class="form-control password-field" required>
        <span class="input-group-text toggle-password"><i class="bi bi-eye-slash"></i></span>
      </div>
    </div>

    <div class="mb-3 position-relative">
      <label class="form-label">Confirm New Password</label>
      <div class="input-group">
        <input type="password" name="confirm_password" class="form-control password-field" required>
        <span class="input-group-text toggle-password"><i class="bi bi-eye-slash"></i></span>
      </div>
    </div>

    <div class="d-grid mt-3">
      <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Password</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', () => {
      const input = icon.closest('.input-group').querySelector('input');
      const iconElement = icon.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        iconElement.classList.remove('bi-eye-slash');
        iconElement.classList.add('bi-eye');
      } else {
        input.type = 'password';
        iconElement.classList.remove('bi-eye');
        iconElement.classList.add('bi-eye-slash');
      }
    });
  });
</script>
</body>
</html>
