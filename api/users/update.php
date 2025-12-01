<?php
/**
 * Update User API
 * Market Place OutFit
 * 
 * API ini digunakan untuk memperbarui data pengguna.
 * Hanya user yang login yang dapat mengakses,
 * dan hanya admin yang dapat mengubah data user lain atau mengubah role.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/User.php';

// ==============================
// 1. Cek apakah user sudah login
// ==============================
if (!User::isLoggedIn()) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

// Ambil input JSON dari request body
$input = json_decode(file_get_contents('php://input'), true);

// Jika tidak ada ID dikirim, gunakan ID user yang sedang login
$id = $input['id'] ?? User::getCurrentUserId();

// ===================================================================
// 2. Periksa apakah user boleh mengupdate data user lain
//    - Jika bukan admin, hanya boleh update dirinya sendiri
// ===================================================================
if (!User::isAdmin() && (int)$id !== User::getCurrentUserId()) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'success' => false,
        'message' => 'Akses ditolak'
    ]);
    exit;
}

// ===================================================================
// 3. Cegah user non-admin untuk mengubah role
// ===================================================================
if (!User::isAdmin() && isset($input['role'])) {
    // Role dihapus agar tidak di-update
    unset($input['role']);
}

// ==============================
// 4. Proses update ke database
// ==============================
$user = new User();
$result = $user->update((int)$id, $input);

// Kirimkan hasil update sebagai JSON
echo json_encode($result);
