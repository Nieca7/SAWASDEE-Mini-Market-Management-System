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
  <title>Suppliers - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .supplier-card {
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      transition: 0.3s;
    }
    .supplier-card:hover {
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .supplier-icon {
      width: 60px;
      height: 60px;
      background: #dee2e6;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: #6c757d;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-building"></i> Supplier Management</h2>
    <div>
      <a href="upload_invoice.php" class="btn btn-outline-secondary"><i class="bi bi-upload"></i> Upload Invoice</a>
      <a href="add_supplier.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Supplier</a>
    </div>
  </div>

  <div class="row g-4">
    <?php foreach ($suppliers as $supplier):
      $docs = $pdo->prepare("SELECT * FROM supplier_documents WHERE supplier_id = ? ORDER BY uploaded_at DESC");
      $docs->execute([$supplier['id']]);
      $documents = $docs->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="col-md-6 col-lg-4">
      <div class="card supplier-card p-3">
        <div class="d-flex align-items-center mb-3">
          <div class="supplier-icon me-3">
            <i class="bi bi-person-badge"></i>
          </div>
          <div>
            <h5 class="mb-0"><?= htmlspecialchars($supplier['name']) ?></h5>
            <small class="text-muted"><?= htmlspecialchars($supplier['company']) ?></small>
          </div>
        </div>
        <ul class="list-unstyled mb-3">
          <li><strong>Phone:</strong> <?= htmlspecialchars($supplier['phone']) ?></li>
          <li><strong>Email:</strong> <?= htmlspecialchars($supplier['email']) ?></li>
          <li><strong>Address:</strong> <?= htmlspecialchars($supplier['address']) ?></li>
        </ul>
        <div class="mb-3">
          <strong>Invoices:</strong>
          <ul class="list-unstyled small">
            <?php if (count($documents) > 0): ?>
              <?php foreach ($documents as $doc): ?>
                <li><a href="uploads/invoices/<?= htmlspecialchars($doc['filename']) ?>" target="_blank"><i class="bi bi-file-earmark-text me-1"></i><?= htmlspecialchars($doc['filename']) ?></a></li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="text-muted">No documents</li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="d-flex justify-content-end gap-2">
          <a href="edit_supplier.php?id=<?= $supplier['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> Edit</a>
          <a href="delete_supplier.php?id=<?= $supplier['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this supplier?');"><i class="bi bi-trash"></i> Delete</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
