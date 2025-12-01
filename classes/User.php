<?php
/**
 * User Class
 * Market Place OutFit
 *
 * Mengelola register, login, update profil, delete user,
 * dan fungsi lain terkait user.
 */

require_once __DIR__ . '/Database.php';

class User
{
    // Instance database & nama tabel
    private Database $db;
    private string $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance(); // ambil instance Database (singleton)
    }

    /**
     * Register user baru
     */
    public function register(array $data): array
    {
        // Pastikan semua field wajib diisi
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Semua field harus diisi'];
        }

        // Validasi format email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Format email tidak valid'];
        }

        // Cek apakah email sudah digunakan
        if ($this->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }

        // Validasi panjang password
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password minimal 6 karakter'];
        }

        // Enkripsi password
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        // Input user ke database
        $userId = $this->db->insert($this->table, [
            'name' => htmlspecialchars($data['name']),
            'email' => $data['email'],
            'password' => $hashedPassword,
            'role' => 'user',            // default role
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null
        ]);

        return [
            'success' => true,
            'message' => 'Registrasi berhasil',
            'user_id' => $userId
        ];
    }

    /**
     * Login user
     */
    public function login(string $email, string $password): array
    {
        // Validasi input kosong
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email dan password harus diisi'];
        }

        // Ambil data user berdasarkan email
        $user = $this->findByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => 'Email atau password salah'];
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email atau password salah'];
        }

        // Mulai session jika belum ada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Simpan data user ke session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['logged_in']  = true;

        return [
            'success' => true,
            'message' => 'Login berhasil',
            'user' => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role']
            ]
        ];
    }

    /**
     * Logout user
     */
    public function logout(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Hapus semua session user
        session_unset();
        session_destroy();

        return ['success' => true, 'message' => 'Logout berhasil'];
    }

    /**
     * Cek apakah user sudah login
     */
    public static function isLoggedIn(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Cek apakah user adalah admin
     */
    public static function isAdmin(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Ambil data user yang sedang login
     */
    public static function getCurrentUser(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id'    => $_SESSION['user_id'],
            'name'  => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role'  => $_SESSION['user_role']
        ];
    }

    /**
     * Ambil ID dari user yang sedang login
     */
    public static function getCurrentUserId(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Cari user berdasarkan email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE email = :email",
            ['email' => $email]
        );
    }

    /**
     * Cari user berdasarkan ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT id, name, email, role, phone, address, created_at, updated_at 
             FROM {$this->table} WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Ambil semua user (admin + user)
     */
    public function getAll(): array
    {
        return $this->db->select(
            "SELECT id, name, email, role, phone, address, created_at, updated_at 
             FROM {$this->table} ORDER BY created_at DESC"
        );
    }

    /**
     * Ambil semua user biasa (role = user)
     */
    public function getAllUsers(): array
    {
        return $this->db->select(
            "SELECT id, name, email, role, phone, address, created_at, updated_at 
             FROM {$this->table} WHERE role = 'user' ORDER BY created_at DESC"
        );
    }

    /**
     * Update profil user
     */
    public function update(int $id, array $data): array
    {
        // Validasi input wajib
        if (empty($data['name']) || empty($data['email'])) {
            return ['success' => false, 'message' => 'Nama dan email harus diisi'];
        }

        // Cek apakah email digunakan user lain
        $existingUser = $this->findByEmail($data['email']);
        if ($existingUser && $existingUser['id'] !== $id) {
            return ['success' => false, 'message' => 'Email sudah digunakan'];
        }

        // Data yang boleh di-update
        $updateData = [
            'name'    => htmlspecialchars($data['name']),
            'email'   => $data['email'],
            'phone'   => $data['phone'] ?? null,
            'address' => $data['address'] ?? null
        ];

        // Hanya admin yang bisa ubah role
        if (isset($data['role']) && in_array($data['role'], ['admin', 'user'])) {
            $updateData['role'] = $data['role'];
        }

        // Jika password ingin diubah
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                return ['success' => false, 'message' => 'Password minimal 6 karakter'];
            }
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        // Update database
        $this->db->update($this->table, $updateData, 'id = :id', ['id' => $id]);

        // Update session jika user sedang login
        if (self::getCurrentUserId() === $id) {
            $_SESSION['user_name']  = $updateData['name'];
            $_SESSION['user_email'] = $updateData['email'];

            if (isset($updateData['role'])) {
                $_SESSION['user_role'] = $updateData['role'];
            }
        }

        return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
    }

    /**
     * Hapus user
     */
    public function delete(int $id): array
    {
        // Cegah menghapus akun sendiri
        if (self::getCurrentUserId() === $id) {
            return ['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri'];
        }

        // Cek user ada atau tidak
        $user = $this->findById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        // Hapus user
        $this->db->delete($this->table, 'id = :id', ['id' => $id]);

        return ['success' => true, 'message' => 'User berhasil dihapus'];
    }

    /**
     * Hitung total user
     */
    public function count(): int
    {
        $result = $this->db->selectOne("SELECT COUNT(*) as total FROM {$this->table}");
        return (int)$result['total'];
    }

    /**
     * Hitung jumlah user berdasarkan role
     */
    public function countByRole(string $role): int
    {
        $result = $this->db->selectOne(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE role = :role",
            ['role' => $role]
        );
        return (int)$result['total'];
    }
}
