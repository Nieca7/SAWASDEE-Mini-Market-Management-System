<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$id = $_GET['id'] ?? null;
if ($id) {
  $stmt = $pdo->prepare("UPDATE products SET deleted = 0 WHERE id = ?");
  $stmt->execute([$id]);
}
header("Location: admin_recycle_bin.php");
exit();
