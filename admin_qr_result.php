<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$code = $_GET['code'] ?? '';
$product = null;

if ($code) {
  $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? OR code = ?");
  $stmt->execute([$code, $code]);
  $product = $stmt->fetch();

  if (!$product) {
    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, code) VALUES (?, 'Uncategorized', 0.00, 0, ?)");
    $stmt->execute([$code, $code]);
    $product_id = $pdo->lastInsertId();
    header("Location: edit_product.php?id=$product_id&new=1");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QR Product Result - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
  <?php if (isset($_GET['new']) && $_GET['new'] == 1): ?>
    <div class="alert alert-success">✅ New product created from QR scan. Please fill in the details below.</div>
  <?php endif; ?>

  <h3 class="mb-4">🔍 Product Scan Result</h3>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
      <p class="card-text">
        <strong>Category:</strong> <?= htmlspecialchars($product['category']) ?><br>
        <strong>Price:</strong> RM <?= number_format($product['price'], 2) ?><br>
        <strong>Stock:</strong> <?= $product['stock'] ?><br>
      </p>
      <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary">Edit Product</a>
      <a href="admin_qr_scanner.php" class="btn btn-secondary">Back to Scanner</a>
    </div>
  </div>
</div>
</body>
</html>
