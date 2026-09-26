<?php
require_once __DIR__ . '/../config/db.php';

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :q OR category LIKE :q ORDER BY id DESC");
    $stmt->execute(['q' => "%{$q}%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}

$products = $stmt->fetchAll();

$status = $_GET['status'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K-Pop Merch Store</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <header class="page-header">
        <div>
            <h1 class="page-title">✨ K-Pop Merch Store</h1>
            <p class="page-subtitle">Sistem Manajemen Inventaris Merchandise Resmi</p>
        </div>
        <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
    </header>

    <?php if ($status === 'created'): ?>
        <div class="alert alert-success">✓ Produk baru berhasil ditambahkan!</div>
    <?php elseif ($status === 'updated'): ?>
        <div class="alert alert-success">✓ Informasi produk berhasil diperbarui!</div>
    <?php elseif ($status === 'deleted'): ?>
        <div class="alert alert-success">✓ Produk telah berhasil dihapus.</div>
    <?php elseif ($status === 'invalid_token'): ?>
        <div class="alert alert-danger">✕ Permintaan ditolak: Token CSRF tidak valid.</div>
    <?php endif; ?>

    <div style="margin-bottom: 24px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 14px; align-items: center;">
        <form method="GET" action="index.php" class="search-bar">
            <input type="text" name="q" class="search-input" placeholder="Cari Seventeen, LNGSHOT, Cortis..." value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-accent">Cari</button>
            <?php if ($q !== ''): ?>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>
        <span style="font-size: 0.95rem; color: #64748b;">
            Total Produk: <strong><?= count($products) ?></strong> item
        </span>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <h3 style="margin-top: 0; color: #334155;">Tidak Ada Produk Ditemukan</h3>
            <p style="color: #64748b; margin-bottom: 18px;">Belum ada merchandise yang cocok dengan kata kunci pencarian Anda.</p>
            <a href="create.php" class="btn btn-accent">+ Tambah Merchandise Baru</a>
        </div>
    <?php else: ?>
        <div class="products">
            <?php foreach ($products as $p): ?>
                <div class="card">
                    <div>
                        <span class="card-badge"><?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?></span>
                        
                        <!-- Proteksi XSS (Slide 09 & 10) -->
                        <h2 class="card-title"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                        
                        <div class="card-meta">
                            <div class="card-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                            <div class="card-stock">
                                Stok: <strong><?= htmlspecialchars((string)$p['stock'], ENT_QUOTES, 'UTF-8') ?></strong> pcs
                            </div>
                        </div>
                    </div>

                    <div class="card-actions">
                        <a href="edit.php?id=<?= urlencode((string)$p['id']) ?>" class="btn btn-secondary btn-sm" style="flex: 1; text-align: center;">Edit</a>
                        
                        <!-- Proteksi DELETE via POST + CSRF Token (Slide 11 & 12) -->
                        <form method="POST" action="delete.php" style="flex: 1;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus merchandise ini?');">
                            <input type="hidden" name="id" value="<?= htmlspecialchars((string)$p['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">Hapus</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>