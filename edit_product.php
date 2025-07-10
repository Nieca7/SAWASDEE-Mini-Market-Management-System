<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$error = "";

// Validate ID
if (!isset($_GET['id'])) {
    echo "No product ID provided.";
    exit();
}

$product_id = $_GET['id'];

// Fetch existing product
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit();
}

// Extract
$code = $product['code'] ?? '';
$name = $product['name'] ?? '';
$price = $product['price'] ?? '';
$stock = $product['stock'] ?? '';
$category = $product['category'] ?? '';
$image = $product['image'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category = $_POST['category'] ?? '';
    $newImage = $_FILES['image']['name'] ?? '';

    if ($code === '' || $name === '') {
        $error = "Product code and name are required.";
    } else {
        $check = $pdo->prepare("SELECT COUNT(*) FROM products WHERE code = ? AND id != ?");
        $check->execute([$code, $product_id]);
        if ($check->fetchColumn() > 0) {
            $error = "Product code already exists.";
        } else {
            if (!empty($newImage)) {
                $targetDir = "product_images/";
                $targetFile = $targetDir . basename($_FILES["image"]["name"]);
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $image = basename($_FILES["image"]["name"]);
                } else {
                    $error = "Failed to upload image.";
                }
            }

            if (empty($error)) {
                $stmt = $pdo->prepare("UPDATE products SET code = ?, name = ?, price = ?, stock = ?, category = ?, image = ? WHERE id = ?");
                $stmt->execute([$code, $name, $price, $stock, $category, $image, $product_id]);
                header("Location: admin_products.php");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .modal-box {
      max-width: 500px;
      margin: 60px auto;
      background-color: white;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }
    .form-label {
      font-weight: 500;
    }
    .img-thumbnail {
      max-height: 150px;
      margin-top: 5px;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="modal-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="text-primary m-0">Edit Product</h4>
      <a href="admin_products.php" class="btn btn-sm btn-outline-dark">← Back</a>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Product Code</label>
        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($code) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($category) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Price (RM)</label>
        <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($price) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="<?= htmlspecialchars($stock) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Current Image</label><br>
        <?php if (!empty($image) && file_exists("product_images/$image")): ?>
          <img src="product_images/<?= htmlspecialchars($image) ?>" alt="Current Image" class="img-thumbnail">
        <?php else: ?>
          <p class="text-muted">No image uploaded</p>
        <?php endif; ?>
      </div>

      <div class="mb-4">
        <label class="form-label">Upload New Image</label>
        <input type="file" name="image" class="form-control">
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-success">💾 Save Changes</button>
      </div>
    </form>
  </div>
</div>

</body>
</html>
