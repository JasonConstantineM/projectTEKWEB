<?php
/**
 * Product Class
 * Market Place OutFit
 */

require_once __DIR__ . '/Database.php';

class Product
{
    private Database $db;                 // Instance database
    private string $table = 'products';   // Nama tabel produk

    public function __construct()
    {
        // Mengambil instance database (singleton)
        $this->db = Database::getInstance();
    }

    /**
     * Get all products
     * Mengambil semua produk beserta nama kategorinya
     */
    public function getAll(): array
    {
        return $this->db->select(
            "SELECT p.*, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             ORDER BY p.created_at DESC"
        );
    }

    /**
     * Get products by category
     * Mengambil produk berdasarkan kategori tertentu
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->db->select(
            "SELECT p.*, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = :category_id
             ORDER BY p.created_at DESC",
            ['category_id' => $categoryId]
        );
    }

    /**
     * Get products with stock > 0
     * Mengambil hanya produk yang stoknya masih ada
     */
    public function getAvailable(): array
    {
        return $this->db->select(
            "SELECT p.*, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.stock > 0
             ORDER BY p.created_at DESC"
        );
    }

    /**
     * Get available products by category
     * Mengambil produk berdasarkan kategori dan memiliki stok
     */
    public function getAvailableByCategory(int $categoryId): array
    {
        return $this->db->select(
            "SELECT p.*, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = :category_id AND p.stock > 0
             ORDER BY p.created_at DESC",
            ['category_id' => $categoryId]
        );
    }

    /**
     * Search products
     * Fitur pencarian berdasarkan nama atau deskripsi
     */
    public function search(string $keyword): array
    {
        $keyword = '%' . $keyword . '%';
        return $this->db->select(
            "SELECT p.*, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.name LIKE :keyword OR p.description LIKE :keyword2
             ORDER BY p.created_at DESC",
            ['keyword' => $keyword, 'keyword2' => $keyword]
        );
    }

    /**
     * Find product by ID
     * Mencari produk berdasarkan ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT p.*, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Create new product
     * Menambah produk baru dengan validasi, termasuk upload gambar
     */
    public function create(array $data): array
    {
        // Validasi field wajib
        if (empty($data['name']) || empty($data['category_id']) || !isset($data['price'])) {
            return ['success' => false, 'message' => 'Nama, kategori, dan harga harus diisi'];
        }

        // Validasi harga
        if (!is_numeric($data['price']) || $data['price'] < 0) {
            return ['success' => false, 'message' => 'Harga harus berupa angka positif'];
        }

        // Validasi stok
        $stock = isset($data['stock']) ? (int)$data['stock'] : 0;
        if ($stock < 0) {
            return ['success' => false, 'message' => 'Stok tidak boleh negatif'];
        }

        // Upload gambar jika ada
        $imageName = null;
        if (isset($data['image']) && $data['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadImage($data['image']);
            if (!$uploadResult['success']) {
                return $uploadResult; // Return error upload
            }
            $imageName = $uploadResult['filename'];
        }

        // Insert ke database
        $productId = $this->db->insert($this->table, [
            'category_id' => (int)$data['category_id'],
            'name' => htmlspecialchars($data['name']),
            'description' => $data['description'] ?? null,
            'price' => (float)$data['price'],
            'stock' => $stock,
            'image' => $imageName
        ]);

        return [
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'product_id' => $productId
        ];
    }

    /**
     * Update product
     * Mengedit data produk, termasuk penggantian gambar
     */
    public function update(int $id, array $data): array
    {
        // Cek apakah produk ada
        $product = $this->findById($id);
        if (!$product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan'];
        }

        // Validasi input
        if (empty($data['name']) || empty($data['category_id']) || !isset($data['price'])) {
            return ['success' => false, 'message' => 'Nama, kategori, dan harga harus diisi'];
        }

        // Validasi harga
        if (!is_numeric($data['price']) || $data['price'] < 0) {
            return ['success' => false, 'message' => 'Harga harus berupa angka positif'];
        }

        // Validasi stok
        $stock = isset($data['stock']) ? (int)$data['stock'] : 0;
        if ($stock < 0) {
            return ['success' => false, 'message' => 'Stok tidak boleh negatif'];
        }

        $updateData = [
            'category_id' => (int)$data['category_id'],
            'name' => htmlspecialchars($data['name']),
            'description' => $data['description'] ?? null,
            'price' => (float)$data['price'],
            'stock' => $stock
        ];

        // Jika ada gambar baru, upload dan hapus gambar lama
        if (isset($data['image']) && $data['image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->uploadImage($data['image']);
            if (!$uploadResult['success']) {
                return $uploadResult;
            }
            // Hapus gambar lama
            if ($product['image']) {
                $this->deleteImage($product['image']);
            }
            $updateData['image'] = $uploadResult['filename'];
        }

        // Update database
        $this->db->update($this->table, $updateData, 'id = :id', ['id' => $id]);

        return ['success' => true, 'message' => 'Produk berhasil diperbarui'];
    }

    /**
     * Delete product
     * Menghapus produk beserta gambar
     */
    public function delete(int $id): array
    {
        $product = $this->findById($id);
        if (!$product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan'];
        }

        // Hapus gambar jika ada
        if ($product['image']) {
            $this->deleteImage($product['image']);
        }

        // Hapus data produk
        $this->db->delete($this->table, 'id = :id', ['id' => $id]);

        return ['success' => true, 'message' => 'Produk berhasil dihapus'];
    }

    /**
     * Update stock
     * Menambah/mengurangi stok
     */
    public function updateStock(int $id, int $quantity): bool
    {
        $product = $this->findById($id);
        if (!$product) {
            return false;
        }

        $newStock = $product['stock'] + $quantity;

        // Tidak boleh minus
        if ($newStock < 0) {
            return false;
        }

        $this->db->update($this->table, ['stock' => $newStock], 'id = :id', ['id' => $id]);
        return true;
    }

    /**
     * Check stock availability
     * Cek apakah stok produk cukup
     */
    public function checkStock(int $id, int $quantity): bool
    {
        $product = $this->findById($id);
        return $product && $product['stock'] >= $quantity;
    }

    /**
     * Upload product image
     * Validasi format, ukuran, dan generate nama file unik
     */
    private function uploadImage(array $file): array
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // Validasi format file
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Format gambar tidak valid (JPG, PNG, GIF, WEBP)'];
        }

        // Validasi ukuran
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran gambar maksimal 2MB'];
        }

        // Buat nama file unik
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('product_') . '.' . $extension;
        $uploadPath = __DIR__ . '/../assets/images/products/' . $filename;

        // Pindahkan file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => false, 'message' => 'Gagal mengupload gambar'];
        }

        return ['success' => true, 'filename' => $filename];
    }

    /**
     * Delete product image
     * Menghapus file gambar dari folder
     */
    private function deleteImage(string $filename): void
    {
        $filepath = __DIR__ . '/../assets/images/products/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    /**
     * Count all products
     * Menghitung jumlah produk
     */
    public function count(): int
    {
        $result = $this->db->selectOne("SELECT COUNT(*) as total FROM {$this->table}");
        return (int) $result['total'];
    }

    /**
     * Get low stock products
     * Mengambil produk dengan stok rendah
     */
    public function getLowStock(int $threshold = 10): array
    {
        return $this->db->select(
            "SELECT p.*, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.stock <= :threshold
             ORDER BY p.stock ASC",
            ['threshold' => $threshold]
        );
    }
}
