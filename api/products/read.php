<?php
/**
 * Read Products API
 * Market Place OutFit
 *
 * API ini digunakan untuk mengambil data produk.
 * Bisa berdasarkan:
 *  - ID produk
 *  - ID kategori
 *  - keyword pencarian
 *  - atau semua produk yang tersedia
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/Product.php';

// ================================================
// 1. Inisialisasi class Product
// ================================================
$product = new Product();

// ================================================
// 2. Ambil parameter dari URL (GET)
// ================================================
$id         = $_GET['id'] ?? null;           // Jika butuh produk tertentu
$categoryId = $_GET['category_id'] ?? null;  // Jika filter berdasarkan kategori
$search     = $_GET['search'] ?? null;       // Jika fitur pencarian dipakai

// ================================================
// 3. Cek jenis request berdasarkan parameter
// ================================================

// ------------------------------
// Jika request berdasarkan ID produk
// ------------------------------
if ($id) {
    $data = $product->findById((int)$id);

    if ($data) {
        echo json_encode([
            'success' => true,
            'product' => $data
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Produk tidak ditemukan'
        ]);
    }

// ------------------------------
// Jika request berdasarkan kategori
// ------------------------------
} elseif ($categoryId) {

    // Mengambil produk yang tersedia (stock > 0)
    $data = $product->getAvailableByCategory((int)$categoryId);

    echo json_encode([
        'success' => true,
        'products' => $data
    ]);

// ------------------------------
// Jika ada parameter pencarian (search)
// ------------------------------
} elseif ($search) {

    // Pencarian produk berdasarkan nama/deskripsi
    $data = $product->search($search);

    echo json_encode([
        'success' => true,
        'products' => $data
    ]);

// ------------------------------
// Jika tidak ada parameter → ambil semua produk tersedia
// ------------------------------
} else {
    $data = $product->getAvailable();

    echo json_encode([
        'success' => true,
        'products' => $data
    ]);
}
