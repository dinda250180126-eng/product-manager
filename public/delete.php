<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Metode HTTP tidak diizinkan.");
}

$submittedToken = $_POST['csrf'] ?? '';
if (!hash_equals($_SESSION['csrf'] ?? '', $submittedToken)) {
    header("Location: index.php?status=invalid_token");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: index.php?status=deleted");
exit;