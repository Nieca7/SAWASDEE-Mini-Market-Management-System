<?php
session_start();
if ($_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];
    $amount = $price * $qty;
    $profit = $amount * 0.2;
    $date = date('Y-m-d');
    $cashier = $_SESSION['username'];
    $payment = $_POST['payment_method'];
    $transaction_id = 'TX' . date('YmdHis') . rand(100, 999);

    $stmt = $pdo->prepare("INSERT INTO sales_reports 
        (transaction_id, title, category, price, qty, amount, profit, report_date, cashier, payment_method) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $transaction_id, $title, $category, $price, $qty,
        $amount, $profit, $date, $cashier, $payment
    ]);

    header("Location: invoice.php?id=$transaction_id");
    exit();
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Add New Sale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Add New Sale</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category" class="form-select" required>
                <option value="Beverages">Beverages</option>
                <option value="Bakery">Bakery</option>
                <option value="Pharmacy">Pharmacy</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Price (RM)</label>
            <input type="number" name="price" step="0.01" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="qty" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select" required>
                <option value="Cash">Cash</option>
                <option value="eWallet">eWallet</option>
                <option value="Card">Card</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save & Generate Invoice</button>
    </form>
</div>
</body>
</html>
