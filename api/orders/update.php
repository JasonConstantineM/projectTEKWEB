<?php
/**
 * Update Order API
 * Market Place OutFit
 */

header('Content-Type: application/json'); // Mengatur agar response API dikembalikan dalam format JSON

require_once __DIR__ . '/../../classes/User.php';  // Memanggil class User
require_once __DIR__ . '/../../classes/Order.php'; // Memanggil class Order

// Mengecek apakah user sudah login dan apakah user adalah admin
if (!User::isLoggedIn() || !User::isAdmin()) {
    http_response_code(403); // Kirim status error 403 Forbidden
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak' // Pesan untuk user non-admin
    ]);
    exit; // Hentikan proses API
}

// Mengambil data JSON dari body request ( biasanya request dari AJAX / fetch )
$input = json_decode(file_get_contents('php://input'), true);

// Menangkap ID pesanan dan status baru dari request
$id = $input['id'] ?? null;          // ID order yang ingin di-update
$status = $input['status'] ?? null;  // Status baru (misal: pending, diproses, dikirim, selesai)

// Validasi: pastikan ID dan status tidak kosong
if (!$id || !$status) {
    echo json_encode([
        'success' => false,
        'message' => 'ID dan status diperlukan' // Pesan error jika data kurang
    ]);
    exit;
}

// Membuat instance class Order
$order = new Order();

// Memanggil method untuk update status order
$result = $order->updateStatus((int)$id, $status);

// Mengembalikan hasil update dalam format JSON
echo json_encode($result);
