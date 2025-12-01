<?php
/**
 * Read Orders API
 * Market Place OutFit
 */

header('Content-Type: application/json'); // Mengatur header agar response API berupa JSON

require_once __DIR__ . '/../../classes/User.php';   // Memanggil class User
require_once __DIR__ . '/../../classes/Order.php';  // Memanggil class Order

// Mengecek apakah user sudah login
if (!User::isLoggedIn()) {
    http_response_code(401); // 401 = Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit; // Hentikan proses
}

// Membuat instance dari class Order
$order = new Order();

// Mengambil parameter "id" dari URL (GET)
$id = $_GET['id'] ?? null;

// Jika ID pesanan diberikan → ambil detail pesanan
if ($id) {

    // Ambil data pesanan berdasarkan ID
    $orderData = $order->findById((int)$id);

    // Jika pesanan tidak ditemukan
    if (!$orderData) {
        echo json_encode([
            'success' => false,
            'message' => 'Pesanan tidak ditemukan'
        ]);
        exit;
    }

    // Cek apakah user adalah admin atau pemilik pesanan tersebut
    if (!User::isAdmin() && $orderData['user_id'] !== User::getCurrentUserId()) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Akses ditolak' // User tidak boleh melihat pesanan milik orang lain
        ]);
        exit;
    }

    // Ambil detail item-item dalam pesanan
    $items = $order->getItems((int)$id);

    // Kembalikan data pesanan + item-item
    echo json_encode([
        'success' => true,
        'order' => $orderData,
        'items' => $items
    ]);

} else {
    // Jika tidak ada ID → berarti user minta daftar pesanan

    // Jika admin → dapat melihat semua pesanan
    if (User::isAdmin()) {
        $orders = $order->getAll();
    } 
    // Jika bukan admin → hanya bisa lihat pesanan milik user sendiri
    else {
        $orders = $order->getByUser(User::getCurrentUserId());
    }

    // Return daftar pesanan
    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);
}
