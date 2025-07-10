<?php
session_start();
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'employee'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM sales_reports WHERE report_date BETWEEN ? AND ?";
$params = [$from, $to];
if (!empty($search)) {
    $query .= " AND (cashier LIKE ? OR title LIKE ? OR category LIKE ?)";
    array_push($params, "%$search%", "%$search%", "%$search%");
}
$query .= " ORDER BY report_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalSales = array_sum(array_column($reports, 'amount'));
$totalTransactions = count($reports);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Report - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .card h6 { font-size: 0.9rem; color: #888; }
    .card h4 { font-weight: bold; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
      <h2 class="mb-0"><i class="bi bi-bar-chart-line"></i> Sales Report</h2>
      <a href="admin_dashboard.php" class="btn btn-outline-secondary btn-sm ms-3">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
    <form class="d-flex gap-2" method="GET">
      <input type="date" name="from" value="<?= $from ?>" class="form-control">
      <input type="date" name="to" value="<?= $to ?>" class="form-control">
      <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" class="form-control">
      <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
    </form>
  </div>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card border-success">
        <div class="card-body">
          <h6>Total Sales</h6>
          <h4 class="text-success">RM <?= number_format($totalSales, 2) ?></h4>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-info">
        <div class="card-body">
          <h6>Total Transactions</h6>
          <h4 class="text-info"><?= $totalTransactions ?></h4>
        </div>
      </div>
    </div>
  </div>

  <!-- Export Buttons -->
  <div class="mb-4 d-flex gap-2">
    <a href="export_pdf.php?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-danger">
      <i class="bi bi-file-earmark-pdf"></i> Export PDF
    </a>
    <a href="export_csv.php?from=<?= $from ?>&to=<?= $to ?>" class="btn btn-secondary">
      <i class="bi bi-filetype-csv"></i> Export CSV
    </a>
  </div>

  <!-- Reports Table -->
  <div class="table-responsive">
    <table id="reportsTable" class="table table-striped table-bordered">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Cashier</th>
          <th>Product</th>
          <th>Category</th>
          <th>Qty</th>
          <th>Total (RM)</th>
          <th>Payment</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($reports): ?>
          <?php foreach ($reports as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['report_date']) ?></td>
            <td><?= htmlspecialchars($row['cashier']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= $row['qty'] ?></td>
            <td><?= number_format($row['amount'], 2) ?></td>
            <td><?= htmlspecialchars($row['payment_method'] ?? 'N/A') ?></td>
            <td>
              <a href="invoice.php?id=<?= $row['id'] ?>&return=sales_report.php" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-receipt"></i> Invoice
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center text-muted">No sales data found for the selected range.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#reportsTable').DataTable();
  });
</script>
</body>
</html>
