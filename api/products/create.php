<?php
/**
 * Create Product API
 * Market Place OutFit
 */

header('Content-Type: application/json'); // Mengatur response menjadi JSON

require_once __DIR__ . '/../../classes/User.php';     // Memanggil class User
require_once __DIR__ . '/../../classes/Product.php';  // Memanggil class Product

// Pastikan hanya admin yang bisa mengakses endpoint ini
if (!User::isLoggedIn() || !User::isAdmin()) {
    http_response_code(403); // Set status error 403 Forbidden
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak' // Pesan jika bukan admin
    ]);
    exit;
}

$product = new Product(); // Membuat instance class Product

// Mengambil data dari POST request
$data = [
    'category_id' => $_POST['category_id'] ?? '',      // ID kategori produk
    'name' => $_POST['name'] ?? '',                    // Nama produk
    'description' => $_POST['description'] ?? '',      // Deskripsi produk
    'price' => $_POST['price'] ?? 0,                   // Harga produk
    'stock' => $_POST['stock'] ?? 0                    // Stok produk
];

// Jika ada upload gambar dan tidak terjadi error upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $data['image'] = $_FILES['image']; // Masukkan file gambar ke data
}

// Proses membuat produk baru
$result = $product->create($data);

// Mengembalikan hasil dalam bentuk JSON
echo json_encode($result);
