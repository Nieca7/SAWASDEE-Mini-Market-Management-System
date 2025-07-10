<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['restore_selected'])) {
        $ids = $_POST['restore'] ?? [];
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("UPDATE products SET deleted = 0 WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }
    }
    if (isset($_POST['delete_selected'])) {
        $ids = $_POST['restore'] ?? [];
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }
    }
    header("Location: admin_recycle_bin.php");
    exit();
}

$deletedProducts = $pdo->query("SELECT * FROM products WHERE deleted = 1 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recycle Bin - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f1f3f5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .table th, .table td {
      vertical-align: middle;
    }
    .action-buttons {
      gap: 0.5rem;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="bi bi-trash3"></i> Recycle Bin</h2>
    <a href="admin_products.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Products</a>
  </div>

  <div class="card p-4">
    <?php if (count($deletedProducts) > 0): ?>
      <form method="POST">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col"><input type="checkbox" id="selectAll"></th>
                <th scope="col">Product</th>
                <th scope="col">Category</th>
                <th scope="col">Stock</th>
                <th scope="col">Price (RM)</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($deletedProducts as $product): ?>
              <tr>
                <td><input type="checkbox" name="restore[]" value="<?= $product['id'] ?>"></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td><?= $product['stock'] ?></td>
                <td><?= number_format($product['price'], 2) ?></td>
                <td class="d-flex action-buttons">
                  <a href="restore_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-success">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
          <button type="submit" name="restore_selected" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Restore Selected
          </button>
          <button type="submit" name="delete_selected" class="btn btn-danger" onclick="return confirm('Are you sure you want to permanently delete selected products? This cannot be undone.');">
            <i class="bi bi-x-circle"></i> Delete Permanently
          </button>
        </div>
      </form>
    <?php else: ?>
      <div class="alert alert-info">No deleted products found.</div>
    <?php endif; ?>
  </div>
</div>
<script>
  document.getElementById('selectAll')?.addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('input[name="restore[]"]');
    for (const box of checkboxes) {
      box.checked = this.checked;
    }
  });
</script>
</body>
</html>
