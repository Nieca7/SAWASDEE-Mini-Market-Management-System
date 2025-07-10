<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';
$products = $pdo->query("SELECT * FROM products WHERE deleted = 0")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Inventory - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <style>
    body { background-color: #f1f3f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .badge-stock { font-size: 0.85rem; }
    .table-img { width: 50px; height: 50px; object-fit: cover; border-radius: 0.5rem; }
    .card { border-radius: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }
    .btn-back { font-size: 1.2rem; }
    .modal-header { background-color: #0d6efd; color: white; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
      <a href="admin_dashboard.php" class="text-dark me-2 btn-back"><i class="bi bi-arrow-left-circle"></i></a>
      <i class="bi bi-box-seam"></i> Product Inventory
    </h2>
    <a href="admin_recycle_bin.php" class="btn btn-outline-dark"><i class="bi bi-trash3"></i> Recycle Bin</a>
  </div>

  <div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">📋 Product List</h5>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="bi bi-plus-circle"></i> Add Product</button>
    </div>
    <div class="table-responsive">
      <table id="productsTable" class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Price (RM)</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product): ?>
          <tr>
            <td><img src="product_images/<?= htmlspecialchars($product['image'] ?? 'default.png') ?>" class="table-img"></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['category']) ?></td>
            <td><?= $product['stock'] ?></td>
            <td><?= number_format($product['price'], 2) ?></td>
            <td>
              <?php
                $stock = $product['stock'];
                if ($stock < 5) echo '<span class="badge bg-danger badge-stock">Low</span>';
                elseif ($stock <= 20) echo '<span class="badge bg-warning text-dark badge-stock">OK</span>';
                else echo '<span class="badge bg-success badge-stock">Plenty</span>';
              ?>
            </td>
            <td>
              <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i></a>
              <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="add_product.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Price (RM)</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Product Image</label>
            <input type="file" name="image" accept="image/*" class="form-control">
          </div>
          <button type="submit" class="btn btn-success w-100">Save Product</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    $('#productsTable').DataTable();
  });
</script>
</body>
</html>
