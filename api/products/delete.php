<?php
/**
 * Delete Product API
 * Market Place OutFit
 *
 * API ini menangani proses penghapusan produk.
 * Hanya admin yang boleh mengakses endpoint ini.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Product.php';

// ============================================================
// 1. Validasi hak akses: hanya admin yang boleh menghapus produk
// ============================================================
if (!User::isLoggedIn() || !User::isAdmin()) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak'
    ]);
    exit;
}

// ============================================================
// 2. Ambil data request (JSON) dan cek apakah ID produk tersedia
// ============================================================
$input = json_decode(file_get_contents('php://input'), true);
$id    = $input['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID produk diperlukan'
    ]);
    exit;
}

// ============================================================
// 3. Proses penghapusan produk melalui class Product
// ============================================================
$product = new Product();
$result  = $product->delete((int)$id);

// ============================================================
// 4. Kembalikan hasil dalam format JSON
// ============================================================
echo json_encode($result);
