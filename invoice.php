<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$idOrTx = $_GET['id'] ?? null;
if (!$idOrTx) {
    echo "Invalid request.";
    exit();
}

$transaction_id = null;

// First, check if the given ID is a valid transaction_id
$stmt = $pdo->prepare("SELECT * FROM sales_reports WHERE transaction_id = ?");
$stmt->execute([$idOrTx]);
$items = $stmt->fetchAll();

if (!$items) {
    // Not a transaction_id, try as numeric row ID
    $stmt = $pdo->prepare("SELECT * FROM sales_reports WHERE id = ?");
    $stmt->execute([$idOrTx]);
    $record = $stmt->fetch();

    if (!$record) {
        echo "Invoice not found.";
        exit();
    }

    // Now use transaction_id from that record
    $transaction_id = $record['transaction_id'];

    // Fetch full list of products in this transaction
    $stmt = $pdo->prepare("SELECT * FROM sales_reports WHERE transaction_id = ?");
    $stmt->execute([$transaction_id]);
    $items = $stmt->fetchAll();

    if (!$items) {
        echo "Invoice not found.";
        exit();
    }
} else {
    $transaction_id = $idOrTx;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    @media print { .no-print { display: none; } body { margin: 0; } }
    body {
      background-color: #f8f9fa;
      padding: 2rem;
      font-family: 'Courier New', Courier, monospace;
    }
    .receipt {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 2rem;
      border: 1px dashed #ccc;
      border-radius: 10px;
    }
    .receipt h2 {
      font-size: 1.3rem;
      text-align: center;
      margin-bottom: 1rem;
    }
    .receipt hr {
      margin: 1rem 0;
    }
  </style>
</head>
<body>
<div class="receipt">
  <h2>🧾 SAWASDEE MINI MART</h2>
  <p class="text-center mb-0">167, Jalan Kubang Pasu, Taman Suria</p>
  <p class="text-center mb-3">Tel: 011-54323253</p>

  <p><strong>Transaction ID:</strong> <?= htmlspecialchars($transaction_id) ?></p>
  <p><strong>Date:</strong> <?= date('Y-m-d H:i:s', strtotime($items[0]['report_date'])) ?></p>
  <p><strong>Cashier:</strong> <?= htmlspecialchars($items[0]['cashier']) ?></p>
  <p><strong>Payment:</strong> <?= htmlspecialchars($items[0]['payment_method'] ?? 'N/A') ?></p>

  <hr>
  <table class="table table-sm">
    <thead>
      <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
    </thead>
    <tbody>
      <?php
      $grandTotal = 0;
      foreach ($items as $item):
        $total = $item['qty'] * $item['price'];
        $grandTotal += $total;
      ?>
      <tr>
        <td><?= htmlspecialchars($item['title']) ?></td>
        <td><?= $item['qty'] ?></td>
        <td>RM <?= number_format($item['price'], 2) ?></td>
        <td>RM <?= number_format($total, 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <hr>
  <p class="text-end"><strong>Total: RM <?= number_format($grandTotal, 2) ?></strong></p>
  <p class="text-center">Thank you for shopping with us!</p>

  <div class="text-center no-print mt-3">
    <button class="btn btn-outline-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
    <a href="<?= htmlspecialchars($_GET['return'] ?? 'employee_dashboard.php') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left-circle"></i> Back
    </a>
  </div>
</div>
</body>
</html>
