<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #eef2f7, #f8f9fa);
      font-family: 'Segoe UI', sans-serif;
    }
    .profile-card {
      max-width: 600px;
      margin: auto;
      background: white;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background-color: #0d6efd;
      color: white;
      font-size: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .info-label {
      font-weight: 500;
      color: #6c757d;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-person-circle"></i> Admin Profile</h3>
    <a href="admin_dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>

  <div class="profile-card">
    <div class="text-center mb-4">
      <div class="avatar mx-auto mb-2"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
      <h5 class="mb-0"><?= htmlspecialchars($user['username']) ?></h5>
      <span class="badge bg-primary text-capitalize"><?= htmlspecialchars($user['role']) ?></span>
    </div>
    <hr>
    <div class="mb-3">
      <p class="info-label mb-1">Status</p>
      <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
        <?= ucfirst($user['status']) ?>
      </span>
    </div>
    <div class="mb-3">
      <p class="info-label mb-1">Registered Date</p>
      <p><?= date('F j, Y', strtotime($user['created_at'])) ?></p>
    </div>
    <div class="mb-3">
      <p class="info-label mb-1">Last Login</p>
      <p><?= $user['last_login'] ?? 'N/A' ?></p>
    </div>
    <div class="text-end">
      <a href="change_password.php" class="btn btn-warning"><i class="bi bi-shield-lock"></i> Change Password</a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
