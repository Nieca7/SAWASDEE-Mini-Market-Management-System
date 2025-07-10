<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];
    $amount = $price * $qty;
    $profit = $amount * 0.2; // Example profit logic (20%)

    $stmt = $pdo->prepare("UPDATE sales_reports SET title = ?, category = ?, price = ?, qty = ?, amount = ?, profit = ? WHERE id = ?");
    $stmt->execute([$title, $category, $price, $qty, $amount, $profit, $id]);

    header("Location: employee_dashboard.php");
    exit();
}


$stmt = $pdo->prepare("SELECT * FROM sales_reports WHERE id = ?");
$stmt->execute([$id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Edit Sales Report</h2>
    <form method="POST">
    <div class="mb-3">
    <label class="form-label">Product Name</label>
    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($report['title']); ?>" required>
</div>
<div class="mb-3">
    <label class="form-label">Category</label>
    <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($report['category']); ?>" required>
</div>
<div class="mb-3">
    <label class="form-label">Price (RM)</label>
    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $report['price']; ?>" required>
</div>
<div class="mb-3">
    <label class="form-label">Quantity</label>
    <input type="number" name="qty" class="form-control" value="<?php echo $report['qty']; ?>" required>
</div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="employee_dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
