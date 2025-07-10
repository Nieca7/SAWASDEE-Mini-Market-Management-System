<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $company = $_POST['company'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $address = $_POST['address'];

  $stmt = $pdo->prepare("INSERT INTO suppliers (name, company, phone, email, address) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$name, $company, $phone, $email, $address]);
  header("Location: admin_suppliers.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Supplier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e0ecf8, #f9f9f9);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .form-wrapper {
      max-width: 600px;
      margin: auto;
      background: white;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .form-label {
      font-weight: 500;
    }
    .btn-success {
      background: linear-gradient(135deg, #28a745, #218838);
      border: none;
    }
    .btn-success:hover {
      background: linear-gradient(135deg, #218838, #1e7e34);
    }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="form-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><i class="bi bi-person-plus"></i> Add New Supplier</h4>
      <a href="admin_suppliers.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
    </div>
    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Company</label>
        <input type="text" name="company" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea name="address" rows="3" class="form-control"></textarea>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Supplier</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
