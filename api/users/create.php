<?php
/**
 * Create User API
 * Market Place OutFit
 *
 * API untuk membuat user baru.
 * - Hanya admin yang boleh membuat user (termasuk admin baru).
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/User.php';

// ======================================================
// 1. Cek apakah user sudah login dan apakah dia admin
//    Karena membuat user baru adalah aksi sensitif
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
// 2. Ambil data JSON dari request body
//    php://input digunakan agar bisa menerima RAW JSON
// ======================================================
$input = json_decode(file_get_contents('php://input'), true);

// ======================================================
// 3. Buat user baru menggunakan method register()
//    Jika field kosong, isi dengan string kosong agar tidak error
// ======================================================
$user = new User();
$result = $user->register([
    'name'    => $input['name'] ?? '',
    'email'   => $input['email'] ?? '',
    'password'=> $input['password'] ?? '',
    'phone'   => $input['phone'] ?? '',
    'address' => $input['address'] ?? ''
]);

// ======================================================
// 4. Jika user berhasil dibuat DAN role diberikan admin,
//    maka update role user menjadi admin.
//    - Ini hanya boleh dilakukan oleh admin (dicek sebelumnya).
//    - register() biasanya membuat user default sebagai "member".
// ======================================================
if ($result['success'] && isset($input['role']) && $input['role'] === 'admin') {
    $user->update(
        $result['user_id'],     // ID user yang barusan dibuat
        [
            'name'  => $input['name'],
            'email' => $input['email'],
            'role'  => 'admin'
        ]
    );
}

// ======================================================
// 5. Kirim response JSON ke frontend
// ======================================================
echo json_encode($result);
