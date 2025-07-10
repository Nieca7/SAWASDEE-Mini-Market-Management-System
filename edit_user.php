<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    echo "Invalid user ID.";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $query = "UPDATE users SET username=?, role=?, status=? WHERE id=?";
    $params = [$username, $role, $status, $id];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET username=?, password=?, role=?, status=? WHERE id=?";
        $params = [$username, $password, $role, $status, $id];
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    header("Location: admin_manage_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f1f4f8; font-family: 'Segoe UI', sans-serif; }
    .form-wrapper {
      max-width: 600px;
      margin: auto;
      background: white;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="form-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit User</h4>
      <a href="admin_manage_users.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="employee" <?= $user['role'] == 'employee' ? 'selected' : '' ?>>Employee</option>
          
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> Update User
        </button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
