<?php
/**
 * Category Class
 * Market Place OutFit
 */

require_once __DIR__ . '/Database.php';

class Category
{
    private Database $db;
    private string $table = 'categories';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all categories
     */
    public function getAll(): array
    {
        return $this->db->select(
            "SELECT * FROM {$this->table} ORDER BY name ASC"
        );
    }

    /**
     * Get all categories with product count
     */
    public function getAllWithProductCount(): array
    {
        return $this->db->select(
            "SELECT c.*, COUNT(p.id) as product_count
             FROM {$this->table} c
             LEFT JOIN products p ON c.id = p.category_id
             GROUP BY c.id
             ORDER BY c.name ASC"
        );
    }

    /**
     * Find category by ID
     */
    public function findById(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Create new category
     */
    public function create(array $data): array
    {
        // Validate required fields
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Nama kategori harus diisi'];
        }

        // Check if name already exists
        $existing = $this->db->selectOne(
            "SELECT id FROM {$this->table} WHERE name = :name",
            ['name' => $data['name']]
        );
        if ($existing) {
            return ['success' => false, 'message' => 'Nama kategori sudah ada'];
        }

        $categoryId = $this->db->insert($this->table, [
            'name' => htmlspecialchars($data['name']),
            'description' => $data['description'] ?? null
        ]);

        return [
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'category_id' => $categoryId
        ];
    }

    /**
     * Update category
     */
    public function update(int $id, array $data): array
    {
        // Validate required fields
        if (empty($data['name'])) {
            return ['success' => false, 'message' => 'Nama kategori harus diisi'];
        }

        // Check if category exists
        $category = $this->findById($id);
        if (!$category) {
            return ['success' => false, 'message' => 'Kategori tidak ditemukan'];
        }

        // Check if name is used by other category
        $existing = $this->db->selectOne(
            "SELECT id FROM {$this->table} WHERE name = :name AND id != :id",
            ['name' => $data['name'], 'id' => $id]
        );
        if ($existing) {
            return ['success' => false, 'message' => 'Nama kategori sudah digunakan'];
        }

        $this->db->update($this->table, [
            'name' => htmlspecialchars($data['name']),
            'description' => $data['description'] ?? null
        ], 'id = :id', ['id' => $id]);

        return ['success' => true, 'message' => 'Kategori berhasil diperbarui'];
    }

    /**
     * Delete category
     */
    public function delete(int $id): array
    {
        $category = $this->findById($id);
        if (!$category) {
            return ['success' => false, 'message' => 'Kategori tidak ditemukan'];
        }

        // Check if category has products
        $productCount = $this->db->selectOne(
            "SELECT COUNT(*) as total FROM products WHERE category_id = :id",
            ['id' => $id]
        );
        if ($productCount['total'] > 0) {
            return ['success' => false, 'message' => 'Kategori memiliki produk, hapus produk terlebih dahulu'];
        }

        $this->db->delete($this->table, 'id = :id', ['id' => $id]);

        return ['success' => true, 'message' => 'Kategori berhasil dihapus'];
    }

    /**
     * Count all categories
     */
    public function count(): int
    {
        $result = $this->db->selectOne("SELECT COUNT(*) as total FROM {$this->table}");
        return (int) $result['total'];
    }
}
