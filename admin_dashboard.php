// Final touch: Notification bell with badge + dynamic greeting message
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$totalSales = $pdo->query("SELECT SUM(amount) FROM sales_reports")->fetchColumn() ?? 0;
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?? 0;
$totalEmployees = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn() ?? 0;
$lowStock = $pdo->query("SELECT * FROM products WHERE stock < 5 ORDER BY stock ASC")->fetchAll(PDO::FETCH_ASSOC);
$weeklySales = $pdo->query("SELECT DATE(report_date) as date, SUM(amount) as total FROM sales_reports GROUP BY DATE(report_date) ORDER BY date DESC LIMIT 7")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Sawasdee POS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f4f6f9; color: #000; }
    .sidebar {
      width: 260px;
      background: linear-gradient(135deg, #3f72af, #112d4e);
      height: 100vh;
      position: fixed;
      top: 56px;
      left: 0;
      padding-top: 1rem;
      color: #fff;
      transition: width 0.3s ease;
      overflow-x: hidden;
    }
    .sidebar.collapsed { width: 70px; }
    .sidebar a { color: #e6eaf2; }
    .sidebar .nav-link { white-space: nowrap; }
    .sidebar.collapsed .nav-link span { display: none; }
    .content { margin-left: 260px; padding: 2rem; padding-top: 70px; transition: margin-left 0.3s ease; }
    .content.collapsed { margin-left: 70px; }
    .card-summary {
      border-left: 4px solid #0d6efd;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      animation: fadeIn 0.8s ease;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .clock-date { font-weight: 500; font-size: 0.95rem; color: #ffffff; }
    .alert-bounce { animation: bounce 1s infinite alternate; }
    @keyframes bounce { from { transform: scale(1); } to { transform: scale(1.05); } }
    .badge-notify {
      background: red;
      color: white;
      border-radius: 50%;
      position: absolute;
      top: 5px;
      right: 5px;
      font-size: 0.6rem;
      padding: 2px 6px;
    }
    @media (max-width: 768px) {
      .sidebar { position: absolute; z-index: 1030; height: 100%; }
      .content { margin-left: 0; padding-top: 100px; }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <button class="btn btn-outline-light me-3" id="toggleSidebar"><i class="bi bi-list"></i></button>
    <span class="navbar-brand">SAWASDEE POS - Admin</span>
    <div class="d-flex align-items-center ms-auto position-relative">
      <div class="me-4 position-relative">
      </div>
      <div class="me-3 clock-date" id="datetime"></div>
      <div class="dropdown">
        <a class="btn btn-primary dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="admin_profile.php"><i class="bi bi-person-lines-fill me-2"></i>Profile</a></li>
          <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="sidebar d-flex flex-column" id="sidebar">
  <ul class="nav flex-column px-3">
    <li class="nav-item mb-2"><a href="admin_dashboard.php" class="nav-link"><i class="bi bi-speedometer2 me-2"></i><span>Dashboard</span></a></li>
    <li class="nav-item mb-2"><a href="admin_products.php" class="nav-link"><i class="bi bi-box-seam me-2"></i><span>Products</span></a></li>
    <li class="nav-item mb-2"><a href="admin_categories.php" class="nav-link"><i class="bi bi-tags me-2"></i><span>Categories</span></a></li>
    <li class="nav-item mb-2"><a href="admin_suppliers.php" class="nav-link"><i class="bi bi-building me-2"></i><span>Suppliers</span></a></li>
    <li class="nav-item mb-2"><a href="admin_manage_users.php" class="nav-link"><i class="bi bi-people me-2"></i><span>Users</span></a></li>
    <li class="nav-item mb-2"><a href="admin_qr_scanner.php" class="nav-link"><i class="bi bi-upc-scan me-2"></i><span>QR Scanner</span></a></li>
    <li class="nav-item mb-2"><a href="view_reports.php" class="nav-link"><i class="bi bi-bar-chart-line me-2"></i><span>Sales Data</span></a></li>
    <li class="nav-item mb-2"><a href="sales_report.php" class="nav-link"><i class="bi bi-file-earmark-bar-graph me-2"></i><span>Sales Reports</span></a></li>
  </ul>
</div>

<div class="content" id="mainContent">
  <h3 class="mb-4" id="greeting"></h3>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card card-summary">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-cash-coin text-primary"></i> Total Sales</h5>
          <p class="card-text fs-4 text-success">RM <?= number_format($totalSales, 2) ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-summary">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-box-seam text-primary"></i> Total Products</h5>
          <p class="card-text fs-4"><?= $totalProducts ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-summary">
        <div class="card-body">
          <h5 class="card-title"><i class="bi bi-people text-primary"></i> Employees</h5>
          <p class="card-text fs-4"><?= $totalEmployees ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-5">
    <div class="card-header">📈 Weekly Sales Overview</div>
    <div class="card-body">
      <canvas id="salesChart"></canvas>
    </div>
  </div>

  <div class="mt-5">
    <h5 class="alert-bounce">⚠️ Low Stock Alerts</h5>
    <?php if (count($lowStock) > 0): ?>
      <div class="table-responsive mt-3">
        <table class="table table-bordered bg-white">
          <thead class="table-light">
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Stock Left</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lowStock as $item): ?>
            <tr>
              <td><?= htmlspecialchars($item['name']) ?></td>
              <td><?= htmlspecialchars($item['category']) ?></td>
              <td class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> <?= $item['stock'] ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-muted">All products are sufficiently stocked.</p>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: [<?= implode(',', array_map(fn($r) => '"' . $r['date'] . '"', array_reverse($weeklySales))) ?>],
    datasets: [{
      label: 'Sales (RM)',
      data: [<?= implode(',', array_map(fn($r) => $r['total'], array_reverse($weeklySales))) ?>],
      borderColor: 'rgba(75, 192, 192, 1)',
      tension: 0.3,
      fill: false
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    scales: { y: { beginAtZero: true } }
  }
});

function updateTime() {
  const now = new Date();
  const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
  const date = now.toLocaleDateString('en-GB', options);
  const time = now.toLocaleTimeString('en-GB');
  document.getElementById('datetime').textContent = `${date} | ${time}`;

  const hour = now.getHours();
  let greet = "Good Evening";
  if (hour < 12) greet = "Good Morning";
  else if (hour < 18) greet = "Good Afternoon";
  document.getElementById('greeting').textContent = `${greet}, Admin!`;
}
setInterval(updateTime, 1000);
updateTime();

document.getElementById('toggleSidebar').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('collapsed');
  document.getElementById('mainContent').classList.toggle('collapsed');
});
</script>
</body>
</html>