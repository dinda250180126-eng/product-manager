<?php
// public/create.php - CREATE Produk Baru (Validasi Server-Side & PRG)
require_once __DIR__ . '/../config/db.php';

$errors = [];
$name = '';
$category = 'Album';
$price = '';
$stock = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil & Normalisasi Input (Slide 05)
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Umum');
    $rawPrice = $_POST['price'] ?? '';
    $rawStock = $_POST['stock'] ?? '';

    // 2. Validasi Aturan Bisnis Server-Side
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

    // 3. Validasi Nama Produk Unik (Slide 07)
    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT id FROM products WHERE name = :name LIMIT 1");
        $checkStmt->execute(['name' => $name]);
        if ($checkStmt->fetch()) {
            $errors['name'] = 'Nama merchandise ini sudah terdaftar di database.';
        }
    }

    // 4. Eksekusi Prepared Statement & Pola PRG (Slide 06 & 08)
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock) VALUES (:name, :category, :price, :stock)");
        $stmt->execute([
            'name'     => $name,
            'category' => $category !== '' ? $category : 'Umum',
            'price'    => $price,
            'stock'    => $stock
        ]);

        // Pola PRG: Redirect agar saat direfresh tidak ada data dobel (Slide 06)
        header("Location: index.php?status=created");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Merchandise Baru</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <div class="form-panel">
        <h2 style="margin: 0 0 20px 0; color: #0f172a;">✨ Tambah Merchandise Baru</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                ✕ Harap perbaiki beberapa input sebelum menyimpan data.
            </div>
        <?php endif; ?>

        <form method="POST" action="create.php">
            <div class="form-group">
                <label for="name">Nama Merchandise *</label>
                <input type="text" id="name" name="name" class="form-control" required minlength="3" 
                       value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="Contoh: aespa Official Lightstick Ver 1">
                <?php if (isset($errors['name'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" id="category" name="category" class="form-control" 
                       value="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="Contoh: Lightstick, Album, Photocard, Fashion">
            </div>

            <div class="form-group">
                <label for="price">Harga Satuan (Rp) *</label>
                <input type="number" id="price" name="price" class="form-control" required min="1" step="any" 
                       value="<?= htmlspecialchars((string)$rawPrice, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="Contoh: 750000">
                <?php if (isset($errors['price'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="stock">Jumlah Stok *</label>
                <input type="number" id="stock" name="stock" class="form-control" required min="0" 
                       value="<?= htmlspecialchars((string)$rawStock, ENT_QUOTES, 'UTF-8') ?>" 
                       placeholder="Contoh: 20">
                <?php if (isset($errors['stock'])): ?>
                    <div class="form-error"><?= htmlspecialchars($errors['stock'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 26px;">
                <button type="submit" class="btn btn-accent" style="flex: 1;">Simpan Produk</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>