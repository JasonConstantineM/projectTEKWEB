<?php
/**
 * Delete User API
 * Market Place OutFit
 *
 * API untuk menghapus user.
 * - Hanya admin yang boleh melakukan penghapusan
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/User.php';

// ======================================================
// 1. Cek apakah user sudah login DAN apakah dia admin
//    - Delete user adalah aksi sensitif, jadi harus admin
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
// 2. Ambil input JSON dari body request
//    php://input digunakan untuk membaca raw JSON
// ======================================================
$input = json_decode(file_get_contents('php://input'), true);

// Ambil ID user yang ingin dihapus
$id = $input['id'] ?? null;

// Jika ID tidak ada, kembalikan error
if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID user diperlukan'
    ]);
    exit;
}

// ======================================================
// 3. Jalankan proses delete user
// ======================================================
$user = new User();
$result = $user->delete((int)$id);

// ======================================================
// 4. Kirim response JSON hasil delete
// ======================================================
echo json_encode($result);
