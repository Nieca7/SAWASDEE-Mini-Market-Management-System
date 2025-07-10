<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check admin login
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Capture product fields
$name = $_POST['name'];
$category = $_POST['category'];
$price = $_POST['price'];
$stock = $_POST['stock'];

$imageName = 'default.png'; // fallback
$uploadDir = 'product_images/';

// Create image folder if not exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Image upload process
if (
    isset($_FILES['image']) &&
    $_FILES['image']['error'] === UPLOAD_ERR_OK &&
    !empty($_FILES['image']['tmp_name'])
) {
    $tmpName = $_FILES['image']['tmp_name'];
    $originalName = basename($_FILES['image']['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    if (!in_array($ext, $allowedTypes)) {
        die('❌ Invalid image type. Allowed: jpg, jpeg, png, gif, webp.');
    }

    if ($_FILES['image']['size'] > $maxSize) {
        die('❌ Image is too large (max 2MB).');
    }

    $uniqueName = uniqid('product_', true) . '.' . $ext;
    $destination = $uploadDir . $uniqueName;

    if (move_uploaded_file($tmpName, $destination)) {
        $imageName = $uniqueName; // Use only if upload succeeded
    } else {
        die('❌ Failed to upload the image.');
    }
}

// Insert into database
$stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image, deleted) VALUES (?, ?, ?, ?, ?, 0)");
$stmt->execute([$name, $category, $price, $stock, $imageName]);

// Redirect back to product list
header("Location: admin_products.php");
exit();
?>
