<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];
  $status = $_POST['status'] ?? 'active';

  $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status, created_at) VALUES (?, ?, ?, ?, NOW())");
  $stmt->execute([$username, $password, $role, $status]);
  header("Location: admin_manage_users.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add New User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(to right, #eaf0f6, #ffffff); font-family: 'Segoe UI', sans-serif; }
    .form-wrapper {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .btn-success {
      background: linear-gradient(135deg, #28a745, #218838);
      border: none;
    }
    .btn-success:hover {
      background: linear-gradient(135deg, #218838, #1e7e34);
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="form-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><i class="bi bi-person-plus"></i> Add New User</h4>
      <a href="admin_manage_users.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="admin">Admin</option>
          <option value="employee">Employee</option>
        
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active" selected>Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Create User</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
