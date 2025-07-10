<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM sales_reports WHERE id = $id");
$report = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head><title>Report Details</title></head>
<body>
    <h2><?php echo $report['title']; ?></h2>
    <p><strong>Date:</strong> <?php echo $report['report_date']; ?></p>
    <p><?php echo nl2br($report['report_content']); ?></p>

    <button onclick="window.print()">🖨️ Print</button>
    <a href="dashboard.php">⬅ Back to Dashboard</a>
</body>
</html>
