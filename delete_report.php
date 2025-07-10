<?php
session_start();
if ($_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM sales_reports WHERE id = ?");
$stmt->execute([$id]);

header("Location: employee_dashboard.php");
exit();
?>
