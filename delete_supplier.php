<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
  echo "Invalid supplier ID.";
  exit();
}

try {
  $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
  $stmt->execute([$id]);
  header("Location: admin_suppliers.php?deleted=1");
  exit();
} catch (PDOException $e) {
  echo "Error deleting supplier: " . $e->getMessage();
  exit();
}
