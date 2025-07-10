<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$msg = '';
$editingCategory = null;

// Add new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$name]);
        $msg = "✅ Category added successfully!";
    }
}

// Edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['edit_name'])) {
    $id = $_POST['edit_id'];
    $name = trim($_POST['edit_name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        $msg = "✏️ Category updated!";
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
    header("Location: admin_categories.php");
    exit();
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories - Sawasdee POS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f1f3f5; }
    .card { border-radius: 1rem; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .table td, .table th { vertical-align: middle; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="bi bi-folder2-open"></i> Manage Product Categories</h3>
    <a href="admin_dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>

  <?php if ($msg): ?>
    <div class="alert alert-success"><?= $msg ?></div>
  <?php endif; ?>

  <div class="card p-4 mb-4">
    <h5 class="mb-3">➕ Add New Category</h5>
    <form method="POST" class="row g-3">
      <div class="col-md-8">
        <input type="text" name="category_name" class="form-control" placeholder="Enter category name" required>
      </div>
      <div class="col-md-4 d-grid">
        <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add</button>
      </div>
    </form>
  </div>

  <div class="card p-4">
    <h5 class="mb-3">📋 Existing Categories</h5>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle bg-white">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th class="text-center" style="width: 160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td><?= $cat['id'] ?></td>
              <td><?= htmlspecialchars($cat['name']) ?></td>
              <td class="text-center">
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $cat['id'] ?>"><i class="bi bi-pencil-square"></i> Edit</button>
                <a href="?delete=<?= $cat['id'] ?>" onclick="return confirm('Delete this category?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
              </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $cat['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST">
                    <div class="modal-header">
                      <h5 class="modal-title">✏️ Edit Category</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="edit_id" value="<?= $cat['id'] ?>">
                      <input type="text" name="edit_name" class="form-control" value="<?= htmlspecialchars($cat['name']) ?>" required>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Save Changes</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
