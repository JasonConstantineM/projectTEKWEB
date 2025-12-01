<?php
/**
 * Create Order API
 * Market Place OutFit
 */

header('Content-Type: application/json'); 
// Mengatur agar response dari file ini selalu berupa JSON

require_once __DIR__ . '/../../classes/User.php';  
require_once __DIR__ . '/../../classes/Order.php'; 
// Mengimpor class User dan Order

// Mengecek apakah user sudah login
if (!User::isLoggedIn()) {
    http_response_code(401); // 401 = Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit; // Hentikan proses API
}

$userId = User::getCurrentUserId(); 
// Mendapatkan ID user yang sedang login

$input = json_decode(file_get_contents('php://input'), true); 
// Mengambil data JSON yang dikirim melalui body request

$shippingAddress = $input['shipping_address'] ?? ''; 
// Mengambil alamat pengiriman (jika tidak ada → string kosong)


// Validasi: pastikan alamat pengiriman tidak kosong
if (empty($shippingAddress)) {
    echo json_encode([
        'success' => false,
        'message' => 'Alamat pengiriman diperlukan'
    ]);
    exit;
}

$order = new Order(); 
// Membuat instance object Order

// Membuat pesanan berdasarkan item di keranjang user
$result = $order->createFromCart($userId, $shippingAddress);

// Mengembalikan hasil pembuatan order ke frontend
echo json_encode($result);
