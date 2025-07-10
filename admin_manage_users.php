<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Management - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .card-user {
      border-radius: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      transition: 0.3s;
    }
    .card-user:hover {
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .user-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #0d6efd, #0a58ca);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    .section-header {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
  </style>
</head>
<body>

<div class="container py-5">

  <!-- ALERT SECTION -->
  <?php if (isset($_SESSION['alert'])): ?>
    <div class="alert alert-<?= $_SESSION['alert']['type'] ?> alert-dismissible fade show" role="alert">
      <?= $_SESSION['alert']['message'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['alert']); ?>
  <?php endif; ?>

  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="section-header">
      <h2 class="mb-0">👥 User Management</h2>
      <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
    <a href="add_user_form.php" class="btn btn-success">
      <i class="bi bi-person-plus"></i> Add User
    </a>
  </div>

  <!-- USER CARDS -->
  <div class="row g-4">
    <?php foreach ($users as $user): ?>
      <?php
        $status = $user['status'] ?? 'inactive';
        $initials = strtoupper(substr($user['username'], 0, 2));
      ?>
      <div class="col-md-4">
        <div class="card card-user p-3 h-100">
          <div class="d-flex align-items-center">
            <div class="user-avatar me-3"><?= $initials ?></div>
            <div>
              <h5 class="mb-0"><?= htmlspecialchars($user['username']) ?></h5>
              <span class="badge bg-info text-dark text-capitalize"><?= htmlspecialchars($user['role']) ?></span>
              <div class="mt-1">
                <span class="badge <?= $status === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                  <?= ucfirst($status) ?>
                </span>
              </div>
            </div>
          </div>
          <div class="mt-3">
            <p class="mb-1"><strong>Last Login:</strong> <?= $user['last_login'] ?? '-' ?></p>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">
              <i class="bi bi-pencil-square"></i>
            </a>
            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?');">
              <i class="bi bi-trash"></i>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
