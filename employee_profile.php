<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employee Profile - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e3f2fd, #ffffff);
      font-family: 'Segoe UI', sans-serif;
    }
    .profile-container {
      max-width: 600px;
      margin: 80px auto;
      background-color: #fff;
      border-radius: 1rem;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      padding: 2rem;
    }
    .profile-header {
      font-weight: 600;
      color: #0d6efd;
    }
    .profile-icon {
      font-size: 3rem;
      color: #0d6efd;
    }
    .info-label {
      font-weight: 500;
      color: #6c757d;
    }
  </style>
</head>
<body>
<div class="profile-container">
  <div class="text-center mb-4">
    <i class="bi bi-person-circle profile-icon"></i>
    <h4 class="profile-header mt-2">Employee Profile</h4>
  </div>
  <div class="mb-3">
    <label class="info-label">Username</label>
    <div class="form-control bg-light"><?= htmlspecialchars($user['username']) ?></div>
  </div>
  <div class="mb-3">
    <label class="info-label">Role</label>
    <div class="form-control bg-light text-capitalize"><?= $user['role'] ?></div>
  </div>
  <div class="mb-4">
    <label class="info-label">Status</label>
    <div class="form-control bg-light text-capitalize"><?= $user['status'] ?? 'active' ?></div>
  </div>
  <div class="d-flex justify-content-between">
    <a href="change_password_employee.php" class="btn btn-outline-primary">
      <i class="bi bi-key"></i> Change Password
    </a>
    <a href="employee_dashboard.php" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
