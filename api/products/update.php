<?php
/**
 * Update Product API
 * Market Place OutFit
 *
 * API untuk mengubah data produk.
 * Hanya admin yang memiliki akses untuk update produk.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Product.php';

// ======================================================
// 1. Cek apakah user sudah login dan apakah dia admin
//    Karena hanya admin yang boleh mengupdate produk
// ======================================================
if (!User::isLoggedIn() || !User::isAdmin()) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak'
    ]);
    exit;
}

// ======================================================
// 2. Inisialisasi class Product
// ======================================================
$product = new Product();

// ======================================================
// 3. Ambil ID produk dari form POST
//    Jika tidak ada ID → tidak bisa update
// ======================================================
$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID produk diperlukan'
    ]);
    exit;
}

// ======================================================
// 4. Ambil data yang dikirim dari form
//    Semua data diambil dari $_POST karena form multipart
// ======================================================
$data = [
    'category_id' => $_POST['category_id'] ?? '',
    'name'        => $_POST['name'] ?? '',
    'description' => $_POST['description'] ?? '',
    'price'       => $_POST['price'] ?? 0,
    'stock'       => $_POST['stock'] ?? 0
];

// ======================================================
// 5. Jika ada file gambar baru, sertakan ke data update
//    Memastikan file valid dan tidak ada error upload
// ======================================================
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $data['image'] = $_FILES['image']; // dikirim ke method update
}

// ======================================================
// 6. Proses update produk dengan memanggil method update()
// ======================================================
$result = $product->update((int)$id, $data);

// ======================================================
// 7. Kirim response JSON
// ======================================================
echo json_encode($result);
