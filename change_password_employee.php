<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($current, $user['password'])) {
        if ($new === $confirm) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $update->execute([$hashed, $_SESSION['username']]);
            $message = '<div class="alert alert-success">✅ Password updated successfully.</div>';
        } else {
            $message = '<div class="alert alert-warning">⚠️ New passwords do not match.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">❌ Current password is incorrect.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password - Employee</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f8f9fa, #dee2e6);
      font-family: 'Segoe UI', sans-serif;
    }
    .profile-card {
      max-width: 500px;
      margin: 80px auto;
      background-color: #fff;
      border-radius: 1rem;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      padding: 2rem;
    }
    .profile-card h4 {
      font-weight: 600;
      color: #0d6efd;
    }
  </style>
</head>
<body>
<div class="profile-card">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4><i class="bi bi-key-fill"></i> Change Password</h4>
    <a href="employee_profile.php" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back
    </a>
  </div>

  <?= $message ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Current Password</label>
      <input type="password" name="current_password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">New Password</label>
      <input type="password" name="new_password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Confirm New Password</label>
      <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">
      <i class="bi bi-save"></i> Update Password
    </button>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
