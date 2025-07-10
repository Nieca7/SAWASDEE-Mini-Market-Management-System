<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(120deg, #f1f4f8, #dbe5f1);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .supplier-card {
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.07);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background-color: #fff;
    }
    .supplier-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .supplier-icon {
      width: 60px;
      height: 60px;
      background: #e9ecef;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.7rem;
      color: #495057;
    }
    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }
    .section-title {
      font-size: 1.75rem;
      font-weight: 600;
    }
    .gradient-btn {
      background: linear-gradient(135deg, #5cb85c, #4cae4c);
      border: none;
    }
    .gradient-btn:hover {
      background: linear-gradient(135deg, #4cae4c, #3c9c3c);
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
  <div class="d-flex align-items-center gap-3">
    <a href="admin_dashboard.php" class="btn btn-outline-dark rounded-circle" title="Back to Dashboard">
      <i class="bi bi-arrow-left"></i>
    </a>
    <h2 class="section-title mb-0"><i class="bi bi-building"></i> Supplier Management</h2>
  </div>
  <a href="add_supplier.php" class="btn gradient-btn text-white"><i class="bi bi-plus-circle"></i> Add Supplier</a>
</div>


  <div class="row g-4">
    <?php foreach ($suppliers as $supplier): ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card supplier-card p-3 h-100">
        <div class="d-flex align-items-center mb-3">
          <div class="supplier-icon me-3">
            <i class="bi bi-person-badge"></i>
          </div>
          <div>
            <h5 class="mb-0 text-primary-emphasis"><?= htmlspecialchars($supplier['name']) ?></h5>
            <small class="text-muted"><?= htmlspecialchars($supplier['company']) ?></small>
          </div>
        </div>
        <ul class="list-unstyled small mb-3">
          <li><i class="bi bi-telephone me-1"></i><strong> Phone:</strong> <?= htmlspecialchars($supplier['phone']) ?></li>
          <li><i class="bi bi-envelope me-1"></i><strong> Email:</strong> <?= htmlspecialchars($supplier['email']) ?></li>
          <li><i class="bi bi-geo-alt me-1"></i><strong> Address:</strong> <?= htmlspecialchars($supplier['address']) ?></li>
        </ul>
        <div class="d-flex justify-content-end gap-2 mt-auto">
          <a href="edit_supplier.php?id=<?= $supplier['id'] ?>" class="btn btn-sm btn-outline-primary btn-action">
            <i class="bi bi-pencil-square"></i> Edit
          </a>
          <a href="delete_supplier.php?id=<?= $supplier['id'] ?>" class="btn btn-sm btn-outline-danger btn-action" onclick="return confirm('Delete this supplier?');">
            <i class="bi bi-trash"></i> Delete
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