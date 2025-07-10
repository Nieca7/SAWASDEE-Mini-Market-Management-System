<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$username = $_SESSION['username'];
$sales = $pdo->prepare("SELECT * FROM sales_reports WHERE cashier = ? ORDER BY report_date DESC");
$sales->execute([$username]);

$rows = $sales->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Sales History</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #dee2e6, #f8f9fa);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .history-card {
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    .table th, .table td {
      vertical-align: middle;
    }
    .table td:last-child {
      text-align: center;
    }
    h2 {
      font-weight: 600;
      color: #343a40;
    }
    .header-bar {
      background: #0d6efd;
      color: white;
      padding: 1rem 2rem;
      border-radius: 12px;
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="header-bar">
    <h2><i class="bi bi-clock-history"></i> My Sales History</h2>
    <a href="employee_dashboard.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back</a>
  </div>
  <div class="card history-card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-primary">
            <tr>
              <th>Date</th>
              <th>Product</th>
              <th>Category</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $row): ?>
            <tr>
              <td><?= $row['report_date'] ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['category']) ?></td>
              <td>RM <?= number_format($row['price'], 2) ?></td>
              <td><?= $row['qty'] ?></td>
              <td>RM <?= number_format($row['amount'], 2) ?></td>
              <td><a href="invoice.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-file-earmark-text"></i> View</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
