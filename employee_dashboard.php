<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$cashier = $_SESSION['username'];
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

$query = "
  SELECT DAYNAME(report_date) as day, SUM(amount) as total
  FROM sales_reports
  WHERE cashier = ? AND report_date BETWEEN ? AND ?
  GROUP BY DAYNAME(report_date)
";
$stmt = $pdo->prepare($query);
$stmt->execute([$cashier, $startOfWeek, $endOfWeek]);

$salesData = array_fill_keys(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'], 0);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
  $salesData[$row['day']] = (float)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cashier Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f0f2f5; }
    .sidebar {
      width: 250px;
      background-color: #ffffff;
      height: 100vh;
      position: fixed;
      top: 56px;
      left: 0;
      padding-top: 1rem;
      border-right: 1px solid #dee2e6;
    }
    .content {
      margin-left: 250px;
      padding: 2rem;
      padding-top: 70px;
    }
    .navbar { z-index: 1000; }
    .nav-link.active {
      font-weight: bold;
      background-color: #e9ecef;
      border-radius: 5px;
    }
    .card-header {
      background-color: #0d6efd;
      color: white;
      font-weight: bold;
    }
    .card h6 {
      font-size: 1rem;
      margin-bottom: 1rem;
    }
    .btn-lg {
      font-size: 1.1rem;
      padding: 0.75rem 1.25rem;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <span class="navbar-brand">SAWASDEE POS - Cashier</span>
    <div class="dropdown ms-auto">
      <a class="btn btn-primary dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
        <i class="bi bi-person-circle"></i> <?= $_SESSION['username'] ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="cashier_sales_history.php"><i class="bi bi-clock-history me-2"></i>Sales History</a></li>
        <li><a class="dropdown-item" href="employee_profile.php"><i class="bi bi-person-lines-fill me-2"></i>My Profile</a></li>
        <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="sidebar d-flex flex-column">
  <ul class="nav flex-column px-3">
    <li class="nav-item mb-2"><a href="cashier_pos.php" class="nav-link text-dark <?= basename($_SERVER['PHP_SELF']) === 'cashier_pos.php' ? 'active' : '' ?>"><i class="bi bi-cart-plus me-2"></i>POS System</a></li>
    <li class="nav-item mb-2"><a href="cashier_sales_history.php" class="nav-link text-dark <?= basename($_SERVER['PHP_SELF']) === 'cashier_sales_history.php' ? 'active' : '' ?>"><i class="bi bi-clock-history me-2"></i>Sales History</a></li>
    <li class="nav-item mb-2"><a href="employee_dashboard.php" class="nav-link text-dark <?= basename($_SERVER['PHP_SELF']) === 'employee_dashboard.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
  </ul>
</div>

<div class="content">
  <div class="mb-4">
    <h3 class="mb-1">👋 Welcome back, <?= $_SESSION['username'] ?>!</h3>
    <p class="text-muted">Here’s your cashier summary and tools.</p>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header">📊 Weekly Sales Overview</div>
        <div class="card-body">
          <canvas id="salesChart" height="200"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header">🔧 Quick Actions</div>
        <div class="card-body d-grid gap-3">
          <a href="cashier_pos.php" class="btn btn-lg btn-outline-primary"><i class="bi bi-cash me-2"></i> New Transaction</a>
          <a href="cashier_sales_history.php" class="btn btn-lg btn-outline-secondary"><i class="bi bi-clock-history me-2"></i> View History</a>
          <a href="employee_profile.php" class="btn btn-lg btn-outline-dark"><i class="bi bi-person-lines-fill me-2"></i> My Profile</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    datasets: [{
      label: 'RM Sales',
      data: [
        <?= $salesData['Monday'] ?>,
        <?= $salesData['Tuesday'] ?>,
        <?= $salesData['Wednesday'] ?>,
        <?= $salesData['Thursday'] ?>,
        <?= $salesData['Friday'] ?>,
        <?= $salesData['Saturday'] ?>,
        <?= $salesData['Sunday'] ?>
      ],
      backgroundColor: 'rgba(13, 110, 253, 0.5)',
      borderColor: 'rgba(13, 110, 253, 1)',
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
