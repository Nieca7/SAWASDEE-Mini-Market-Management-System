<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products - Inventory View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h2 class="mb-4">📦 Product Inventory (View Only)</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price (RM)</th>
                    <th>Stock Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['category']) ?></td>
                        <td><?= number_format($p['price'], 2) ?></td>
                        <td>
                            <?php
                                if ($p['stock'] > 10) echo '<span class="text-success">In Stock</span>';
                                elseif ($p['stock'] > 0) echo '<span class="text-warning">Low Stock</span>';
                                else echo '<span class="text-danger">Out of Stock</span>';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>