<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$products = $pdo->query("SELECT * FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product_id'])) {
  $pid = $_POST['add_product_id'];
  $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;
  header("Location: cashier_pos.php");
  exit();
}

if (isset($_POST['remove_product_id'])) {
  unset($_SESSION['cart'][$_POST['remove_product_id']]);
  header("Location: cashier_pos.php");
  exit();
}

if (isset($_POST['update_qty'])) {
  foreach ($_POST['qty'] as $pid => $qty) {
    $_SESSION['cart'][$pid] = max(1, (int)$qty);
  }
  header("Location: cashier_pos.php");
  exit();
}

if (isset($_POST['checkout'])) {
  $cashier = $_SESSION['username'];
  $paymentMethod = $_POST['payment_method'] ?? 'Cash';
  foreach ($_SESSION['cart'] as $pid => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch();
    if ($product) {
      $amount = $product['price'] * $qty;
      $stmt = $pdo->prepare("INSERT INTO sales_reports (cashier, title, category, price, qty, amount, profit, payment_method, report_date)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
      $stmt->execute([
        $cashier,
        $product['name'],
        $product['category'],
        $product['price'],
        $qty,
        $amount,
        $amount * 0.2,
        $paymentMethod
      ]);
      $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$qty, $pid]);
    }
  }
  $_SESSION['cart'] = [];
  header("Location: cashier_pos.php?success=1");
  exit();
}

$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>POS System - Cashier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: linear-gradient(to right, #eef2f3, #8e9eab); min-height: 100vh; }
    .product-btn { width: 100%; margin-bottom: 15px; padding: 1rem; font-weight: bold; border-radius: 10px; transition: all 0.3s ease-in-out; }
    .product-btn:hover { background-color: #0d6efd; color: white; }
    .card-custom { border-radius: 15px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); }
    .form-select, .form-control { border-radius: 10px; }
    .cart-highlight { background-color: #fff; border-left: 5px solid #0d6efd; }
    .navbar-brand { font-weight: bold; letter-spacing: 1px; }
    #total-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: #0d6efd;
      color: white;
      font-weight: bold;
      padding: 10px 20px;
      text-align: center;
      font-size: 1.1rem;
      box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a href="employee_dashboard.php" class="btn btn-outline-light me-3"><i class="bi bi-arrow-left"></i></a>
    <span class="navbar-brand">Sawasdee POS - Cashier</span>
    <span class="text-white">Welcome, <?= $_SESSION['username'] ?> | <span id="liveTime"></span></span>
  </div>
</nav>
<div class="container py-4">
  <div class="row g-4">
    <!-- Products List -->
    <div class="col-md-7">
      <div class="card card-custom">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0">🛍 Product Catalog</h5>
          <input type="text" id="searchBox" class="form-control w-50" placeholder="Search Product...">
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="row" id="productList">
              <?php foreach ($products as $p): ?>
              <div class="col-md-6 product-item">
                <button name="add_product_id" value="<?= $p['id'] ?>" class="btn btn-outline-dark product-btn">
                  <?= htmlspecialchars($p['name']) ?><br>
                  <small>RM <?= number_format($p['price'], 2) ?></small>
                </button>
              </div>
              <?php endforeach; ?>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Cart Summary -->
    <div class="col-md-5">
      <div class="card card-custom cart-highlight">
        <div class="card-header bg-success text-white d-flex justify-content-between">
          <h5 class="mb-0">🛒 Your Cart</h5>
          <span class="badge bg-light text-dark">Items: <?= count($cart) ?></span>
        </div>
        <div class="card-body">
          <form method="POST">
            <table class="table table-sm">
              <thead class="table-light">
                <tr><th>Item</th><th>Qty</th><th>Subtotal</th><th></th></tr>
              </thead>
              <tbody>
              <?php $total = 0; foreach ($cart as $pid => $qty):
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$pid]);
                $p = $stmt->fetch();
                $sub = $p['price'] * $qty;
                $total += $sub;
              ?>
                <tr>
                  <td><?= htmlspecialchars($p['name']) ?></td>
                  <td><input type="number" name="qty[<?= $pid ?>]" value="<?= $qty ?>" min="1" class="form-control form-control-sm"></td>
                  <td>RM <?= number_format($sub, 2) ?></td>
                  <td>
                    <button name="remove_product_id" value="<?= $pid ?>" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr class="fw-bold">
                  <td colspan="2">Total</td>
                  <td colspan="2">RM <?= number_format($total, 2) ?></td>
                </tr>
              </tfoot>
            </table>

            <div class="mb-3">
              <label class="form-label">Payment Method</label>
              <select name="payment_method" class="form-select" required>
                <option value="Cash">Cash</option>
                <option value="Online Card">Online Card</option>
                <option value="E-Wallet">E-Wallet</option>
              </select>
            </div>

            <div class="d-flex gap-2">
              <button name="update_qty" class="btn btn-outline-primary w-50"><i class="bi bi-arrow-repeat"></i> Update Qty</button>
              <button name="checkout" class="btn btn-success w-50"><i class="bi bi-check2-circle"></i> Checkout</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mt-4 text-center fw-bold">✅ Transaction completed successfully!</div>
  <?php endif; ?>
</div>
<div id="total-bar">Current Total: RM <?= number_format($total, 2) ?></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('searchBox')?.addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
      const text = item.textContent.toLowerCase();
      item.style.display = text.includes(keyword) ? '' : 'none';
    });
  });

  function updateLiveTime() {
    const now = new Date();
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    const time = now.toLocaleTimeString();
    const day = days[now.getDay()];
    document.getElementById('liveTime').textContent = `${day} - ${time}`;
  }
  setInterval(updateLiveTime, 1000);
  updateLiveTime();
</script>
</body>
</html>
