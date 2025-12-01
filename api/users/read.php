<?php
/**
 * Read Users API
 * Market Place OutFit
 * 
 * API untuk membaca data user.
 * - Jika ada parameter ?id= : ambil data 1 user
 * - Jika tanpa id : admin saja yang boleh melihat semua users
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/User.php';

// ======================================================
// 1. Cek apakah user sudah login
// ======================================================
if (!User::isLoggedIn()) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        'success' => false,
        'message' => 'Silakan login terlebih dahulu'
    ]);
    exit;
}

$user = new User();

// Ambil ID user dari query string (jika ada)
$id = $_GET['id'] ?? null;

if ($id) {

    // ======================================================
    // 2. Jika ingin melihat detail user tertentu:
    //    - Admin bebas lihat siapa saja
    //    - Non-admin hanya boleh lihat data dirinya sendiri
    // ======================================================
    if (!User::isAdmin() && (int)$id !== User::getCurrentUserId()) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Akses ditolak'
        ]);
        exit;
    }

    // Ambil data user berdasarkan ID
    $userData = $user->findById((int)$id);

    if ($userData) {
        echo json_encode([
            'success' => true,
            'user' => $userData
        ]);
    } else {
        // Jika user tidak ditemukan
        echo json_encode([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ]);
    }

} else {

    // ======================================================
    // 3. Jika tanpa parameter ID:
    //    Hanya admin yang boleh melihat daftar semua user
    // ======================================================
    if (!User::isAdmin()) {
        http_response_code(403); // Forbidden
        echo json_encode([
            'success' => false,
            'message' => 'Akses ditolak'
        ]);
        exit;
    }

    // Ambil semua user dari database
    $users = $user->getAll();

    echo json_encode([
        'success' => true,
        'users' => $users
    ]);
}
