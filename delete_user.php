<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid user ID.'];
    header("Location: admin_manage_users.php");
    exit();
}

// Optional: prevent self-deletion
$currentUser = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$id]);
$targetUsername = $stmt->fetchColumn();

if ($currentUser === $targetUsername) {
    $_SESSION['alert'] = ['type' => 'warning', 'message' => 'You cannot delete your own account.'];
    header("Location: admin_manage_users.php");
    exit();
}

// Delete the user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['alert'] = ['type' => 'success', 'message' => 'User deleted successfully.'];
header("Location: admin_manage_users.php");
exit();
