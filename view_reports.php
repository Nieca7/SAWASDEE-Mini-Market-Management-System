<?php
session_start();
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['admin', 'employee'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$selectedMonth = $_GET['month'] ?? date('m');
$selectedYear = $_GET['year'] ?? date('Y');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
$monthName = date("F", mktime(0, 0, 0, $selectedMonth, 10));

$queryBase = "FROM sales_reports WHERE MONTH(report_date) = ? AND YEAR(report_date) = ?";
$params = [$selectedMonth, $selectedYear];

$totalSales = $pdo->prepare("SELECT SUM(amount) $queryBase");
$totalSales->execute($params);
$totalAmount = $totalSales->fetchColumn() ?? 0;

$topProductStmt = $pdo->prepare("SELECT title, SUM(qty) as total_qty $queryBase GROUP BY title ORDER BY total_qty DESC LIMIT 1");

$topProductStmt->execute($params);
$topProduct = $topProductStmt->fetch(PDO::FETCH_ASSOC);

$categoryBreakdown = $pdo->prepare("SELECT category, SUM(amount) as total $queryBase GROUP BY category");
$categoryBreakdown->execute($params);
$categoryData = $categoryBreakdown->fetchAll(PDO::FETCH_ASSOC);

$productRanking = $pdo->prepare("SELECT title, SUM(qty) as total_qty $queryBase GROUP BY title ORDER BY total_qty DESC LIMIT 5");
$productRanking->execute($params);
$productData = $productRanking->fetchAll(PDO::FETCH_ASSOC);

$weekdaySales = array_fill(0, 7, 0);
for ($d = 1; $d <= $daysInMonth; $d++) {
    $dateStr = sprintf("%04d-%02d-%02d", $selectedYear, $selectedMonth, $d);
    $weekday = date('w', strtotime($dateStr));
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM sales_reports WHERE report_date = ?");
    $stmt->execute([$dateStr]);
    $weekdaySales[$weekday] += (float)($stmt->fetchColumn() ?? 0);
}

$dailyTrend = [];
for ($d = 1; $d <= $daysInMonth; $d++) {
    $dateStr = sprintf("%04d-%02d-%02d", $selectedYear, $selectedMonth, $d);
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM sales_reports WHERE report_date = ?");
    $stmt->execute([$dateStr]);
    $dailyTrend[] = (float)($stmt->fetchColumn() ?? 0);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - <?= $monthName ?> <?= $selectedYear ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📊 Sales Report - <?= $monthName ?> <?= $selectedYear ?></h2>
        <a href="<?= $_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'employee_dashboard.php' ?>" class="btn btn-secondary">⬅ Back</a>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="month" class="form-select">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m == $selectedMonth ? 'selected' : '' ?>>
                        <?= date("F", mktime(0, 0, 0, $m, 1)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="year" class="form-select">
                <?php for ($y = 2022; $y <= date('Y'); $y++): ?>
                    <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-primary" type="submit">Filter</button>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Total Sales</h5>
                    <p class="fs-4">RM <?= number_format($totalAmount, 2) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Top Product</h5>
                    <p class="fs-5"><?= $topProduct['title'] ?? 'N/A' ?> (<?= $topProduct['total_qty'] ?? 0 ?> pcs)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <canvas id="categoryChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="productChart"></canvas>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <canvas id="weekdayChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>

    <button onclick="exportPDF()" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Export to PDF</button>
</div>

<script>
const weekdayLabels = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
const categoryChart = new Chart(document.getElementById('categoryChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($categoryData, 'category')) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn($c) => $c['total'], $categoryData)) ?>,
            backgroundColor: ['#0d6efd', '#ffc107', '#198754', '#dc3545', '#6f42c1']
        }]
    }
});
const productChart = new Chart(document.getElementById('productChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($productData, 'title')) ?>,
        datasets: [{
            label: "Top 5 Products",
            data: <?= json_encode(array_column($productData, 'total_qty')) ?>,
            backgroundColor: '#17a2b8'
        }]
    },
    options: { scales: { y: { beginAtZero: true } } }
});
const weekdayChart = new Chart(document.getElementById('weekdayChart'), {
    type: 'bar',
    data: {
        labels: weekdayLabels,
        datasets: [{
            label: 'Sales by Weekday (RM)',
            data: <?= json_encode($weekdaySales) ?>,
            backgroundColor: '#fd7e14'
        }]
    },
    options: {
        indexAxis: 'y',
        scales: { x: { beginAtZero: true } }
    }
});
const dailyChart = new Chart(document.getElementById('dailyChart'), {
    type: 'line',
    data: {
        labels: Array.from({length: <?= $daysInMonth ?>}, (_, i) => i + 1),
        datasets: [{
            label: 'Daily Sales (RM)',
            data: <?= json_encode($dailyTrend) ?>,
            fill: false,
            borderColor: 'rgba(75,192,192,1)',
            tension: 0.1
        }]
    }
});

async function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    doc.setFontSize(14);
    doc.text("Sawasdee POS - Monthly Sales Report", 10, 10);
    doc.text("Month: <?= $monthName ?> <?= $selectedYear ?>", 10, 20);
    doc.text("Total Sales: RM <?= number_format($totalAmount, 2) ?>", 10, 30);
    doc.text("Top Product: <?= $topProduct['title'] ?? 'N/A' ?>", 10, 40);

    const charts = ['categoryChart','productChart','weekdayChart','dailyChart'];
    let y = 50;
    for (const id of charts) {
        const canvas = document.getElementById(id);
        const imgData = await html2canvas(canvas).then(canvas => canvas.toDataURL("image/png"));
        doc.addImage(imgData, 'PNG', 10, y, 180, 70);
        y += 80;
    }
    doc.save("sales_report_<?= $selectedYear ?>_<?= $selectedMonth ?>.pdf");
}
</script>
</body>
</html>
