<?php
/**
 * Cart Class
 * Market Place OutFit
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Product.php';

class Cart
{
    private Database $db;
    private string $table = 'carts';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    //Get cart items for user
    
    public function getByUser(int $userId): array
    {
        return $this->db->select(
            "SELECT c.*, p.name as product_name, p.price, p.stock, p.image,
                    (p.price * c.quantity) as subtotal
             FROM {$this->table} c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = :user_id
             ORDER BY c.created_at DESC",
            ['user_id' => $userId]
        );
    }

    //Get cart item
    
    public function getItem(int $userId, int $productId): ?array
    {
        return $this->db->selectOne(
            "SELECT c.*, p.name as product_name, p.price, p.stock
             FROM {$this->table} c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = :user_id AND c.product_id = :product_id",
            ['user_id' => $userId, 'product_id' => $productId]
        );
    }

    // Add item to cart
     
    public function add(int $userId, int $productId, int $quantity = 1): array
    {
        // Validate quantity
        if ($quantity < 1) {
            return ['success' => false, 'message' => 'Jumlah minimal 1'];
        }

        // Check if product exists and has stock
        $product = new Product();
        $productData = $product->findById($productId);
        if (!$productData) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan'];
        }

        // Check existing cart item
        $existingItem = $this->getItem($userId, $productId);
        $totalQuantity = $existingItem ? $existingItem['quantity'] + $quantity : $quantity;

        // Check stock availability
        if ($totalQuantity > $productData['stock']) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi (tersedia: ' . $productData['stock'] . ')'];
        }

        if ($existingItem) {
            // Update quantity
            $this->db->update(
                $this->table,
                ['quantity' => $totalQuantity],
                'user_id = :user_id AND product_id = :product_id',
                ['user_id' => $userId, 'product_id' => $productId]
            );
        } else {
            // Insert new item
            $this->db->insert($this->table, [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return [
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $this->countItems($userId)
        ];
    }

    // Update cart item quantity
    public function updateQuantity(int $userId, int $productId, int $quantity): array
    {
        if ($quantity < 1) {
            return $this->remove($userId, $productId);
        }

        // Check if item exists
        $item = $this->getItem($userId, $productId);
        if (!$item) {
            return ['success' => false, 'message' => 'Item tidak ditemukan di keranjang'];
        }

        // Check stock availability
        if ($quantity > $item['stock']) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi (tersedia: ' . $item['stock'] . ')'];
        }

        $this->db->update(
            $this->table,
            ['quantity' => $quantity],
            'user_id = :user_id AND product_id = :product_id',
            ['user_id' => $userId, 'product_id' => $productId]
        );

        return [
            'success' => true,
            'message' => 'Jumlah berhasil diperbarui',
            'subtotal' => $item['price'] * $quantity
        ];
    }

    //Remove item from cart
    public function remove(int $userId, int $productId): array
    {
        $this->db->delete(
            $this->table,
            'user_id = :user_id AND product_id = :product_id',
            ['user_id' => $userId, 'product_id' => $productId]
        );

        return [
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang',
            'cart_count' => $this->countItems($userId)
        ];
    }

    //Clear all items from cart
    public function clear(int $userId): array
    {
        $this->db->delete($this->table, 'user_id = :user_id', ['user_id' => $userId]);

        return ['success' => true, 'message' => 'Keranjang berhasil dikosongkan'];
    }

    // Get cart total
    public function getTotal(int $userId): float
    {
        $result = $this->db->selectOne(
            "SELECT SUM(p.price * c.quantity) as total
             FROM {$this->table} c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = :user_id",
            ['user_id' => $userId]
        );
        return (float) ($result['total'] ?? 0);
    }

    // Count cart items
     
    public function countItems(int $userId): int
    {
        $result = $this->db->selectOne(
            "SELECT SUM(quantity) as total FROM {$this->table} WHERE user_id = :user_id",
            ['user_id' => $userId]
        );
        return (int) ($result['total'] ?? 0);
    }

    //Validate cart items (check stock)
    public function validate(int $userId): array
    {
        $items = $this->getByUser($userId);
        $invalidItems = [];

        foreach ($items as $item) {
            if ($item['quantity'] > $item['stock']) {
                $invalidItems[] = [
                    'product_name' => $item['product_name'],
                    'requested' => $item['quantity'],
                    'available' => $item['stock']
                ];
            }
        }

        if (!empty($invalidItems)) {
            return [
                'valid' => false,
                'message' => 'Beberapa item melebihi stok yang tersedia',
                'invalid_items' => $invalidItems
            ];
        }

        return ['valid' => true];
    }
}
