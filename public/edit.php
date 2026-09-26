<?php
require_once __DIR__ . '/../config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

$errors = [];
$name = $product['name'];
$category = $product['category'];
$price = $product['price'];
$stock = $product['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Umum');
    $rawPrice = $_POST['price'] ?? '';
    $rawStock = $_POST['stock'] ?? '';

 
    if (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama produk minimal 3 karakter.';
    }

    $price = filter_var($rawPrice, FILTER_VALIDATE_FLOAT);
    if ($price === false || $price <= 0) {
        $errors['price'] = 'Harga harus berupa angka lebih besar dari 0.';
    }

    $stock = filter_var($rawStock, FILTER_VALIDATE_INT);
    if ($stock === false || $stock < 0) {
        $errors['stock'] = 'Stok tidak boleh negatif.';
    }

    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT id FROM products WHERE name = :name AND id != :id LIMIT 1");
        $checkStmt->execute(['name' => $name, 'id' => $id]);
        if ($checkStmt->fetch()) {
            $errors['name'] = 'Nama merchandise ini sudah dipakai produk lain.';
        }
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare("UPDATE products SET name = :name, category = :category, price = :price, stock = :stock WHERE id = :id");
        $updateStmt->execute([
            'name'     => $name,
            'category' => $category !== '' ? $category : 'Umum',
            'price'    => $price,
            'stock'    => $stock,
            'id'       => $id
        ]);

        header("Location: index.php?status=updated");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Merchandise</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <div class="form-panel">
        <h2 style="margin: 0 0 20px 0; color: #0f172a;">✏️ Edit Merchandise #<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?></h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                ✕ Harap perbaiki input sebelum menyimpan perubahan.
            </div>
        <?php endif; ?>

        <form method="POST" action="edit.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="name">Nama Merchandise *</label>
                <input type="text" id="name" name="name" class="form-control" required minlength="3" 
                       value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['name'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" class="form-control" 
                       value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="price">Harga Satuan (Rp) *</label>
                <input type="number" id="price" name="price" class="form-control" required min="1" step="any" 
                       value="<?= htmlspecialchars((string)$price, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['price'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="stock">Jumlah Stok *</label>
                <input type="number" id="stock" name="stock" class="form-control" required min="0" 
                       value="<?= htmlspecialchars((string)$stock, ENT_QUOTES, 'UTF-8') ?>">
                <?php if (isset($errors['stock'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['stock'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 26px;">
                <button type="submit" class="btn btn-accent" style="flex: 1;">Perbarui Data</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>