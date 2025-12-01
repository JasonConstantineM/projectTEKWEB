<?php
/**
 * Order Class
 * Market Place OutFit
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/Product.php';

class Order
{
    private Database $db;
    private string $table = 'orders';
    private string $itemsTable = 'order_items';

    public function __construct()
    {
        // Inisialisasi koneksi database menggunakan singleton
        $this->db = Database::getInstance();
    }

    /**
     * Get all orders
     * Mengambil semua data pesanan beserta data user
     */
    public function getAll(): array
    {
        return $this->db->select(
            "SELECT o.*, u.name as user_name, u.email as user_email
             FROM {$this->table} o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC"
        );
    }

    /**
     * Get orders by user
     * Mengambil pesanan berdasarkan ID user
     */
    public function getByUser(int $userId): array
    {
        return $this->db->select(
            "SELECT * FROM {$this->table}
             WHERE user_id = :user_id
             ORDER BY created_at DESC",
            ['user_id' => $userId]
        );
    }

    /**
     * Find order by ID
     * Mengambil detail pesanan + data user
     */
    public function findById(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone
             FROM {$this->table} o
             JOIN users u ON o.user_id = u.id
             WHERE o.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Get order items
     * Mengambil daftar item dalam satu pesanan
     */
    public function getItems(int $orderId): array
    {
        return $this->db->select(
            "SELECT oi.*, p.name as product_name, p.image as product_image
             FROM {$this->itemsTable} oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = :order_id",
            ['order_id' => $orderId]
        );
    }

    /**
     * Create order from cart
     * Membuat pesanan berdasarkan isi keranjang
     */
    public function createFromCart(int $userId, string $shippingAddress): array
    {
        $cart = new Cart();
        $product = new Product();

        // Validasi keranjang (stok cukup, produk valid, dll.)
        $cartValidation = $cart->validate($userId);
        if (!$cartValidation['valid']) {
            return ['success' => false, 'message' => $cartValidation['message']];
        }

        // Ambil list item keranjang
        $cartItems = $cart->getByUser($userId);
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Keranjang kosong'];
        }

        // Validasi alamat
        if (empty(trim($shippingAddress))) {
            return ['success' => false, 'message' => 'Alamat pengiriman harus diisi'];
        }

        // Hitung total harga pesanan
        $totalAmount = $cart->getTotal($userId);

        // Mulai transaksi database
        $this->db->beginTransaction();

        try {
            // Simpan pesanan ke tabel orders
            $orderId = $this->db->insert($this->table, [
                'user_id'         => $userId,
                'total_amount'    => $totalAmount,
                'status'          => 'pending',
                'shipping_address'=> htmlspecialchars($shippingAddress)
            ]);

            // Simpan setiap item pesanan dan kurangi stok produk
            foreach ($cartItems as $item) {
                // Tambah item ke tabel order_items
                $this->db->insert($this->itemsTable, [
                    'order_id'   => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price']
                ]);

                // Kurangi stok produk
                $product->updateStock($item['product_id'], -$item['quantity']);
            }

            // Kosongkan keranjang
            $cart->clear($userId);

            // Commit transaksi
            $this->db->commit();

            return [
                'success'  => true,
                'message'  => 'Pesanan berhasil dibuat',
                'order_id' => $orderId
            ];

        } catch (Exception $e) {
            // Jika error → rollback transaksi
            $this->db->rollback();
            return ['success' => false, 'message' => 'Gagal membuat pesanan: ' . $e->getMessage()];
        }
    }

    /**
     * Update order status
     * Mengubah status pesanan (pending, processing, shipped, completed, cancelled)
     */
    public function updateStatus(int $id, string $status): array
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        // Validasi status
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Status tidak valid'];
        }

        $order = $this->findById($id);
        if (!$order) {
            return ['success' => false, 'message' => 'Pesanan tidak ditemukan'];
        }

        // Jika dibatalkan → kembalikan stok
        if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
            $this->restoreStock($id);
        }

        // Update status pesanan
        $this->db->update($this->table, ['status' => $status], 'id = :id', ['id' => $id]);

        return ['success' => true, 'message' => 'Status pesanan berhasil diperbarui'];
    }

    /**
     * Restore stock when order is cancelled
     * Mengembalikan stok produk jika pesanan dibatalkan
     */
    private function restoreStock(int $orderId): void
    {
        $product = new Product();
        $items = $this->getItems($orderId);

        foreach ($items as $item) {
            $product->updateStock($item['product_id'], $item['quantity']);
        }
    }

    /**
     * Count all orders
     * Menghitung total jumlah pesanan
     */
    public function count(): int
    {
        $result = $this->db->selectOne("SELECT COUNT(*) as total FROM {$this->table}");
        return (int) $result['total'];
    }

    /**
     * Count orders by status
     * Menghitung jumlah pesanan berdasarkan status
     */
    public function countByStatus(string $status): int
    {
        $result = $this->db->selectOne(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE status = :status",
            ['status' => $status]
        );
        return (int) $result['total'];
    }

    /**
     * Get total revenue
     * Mengambil total pendapatan dari pesanan yang valid
     */
    public function getTotalRevenue(): float
    {
        $result = $this->db->selectOne(
            "SELECT SUM(total_amount) as total FROM {$this->table} 
             WHERE status IN ('processing', 'shipped', 'completed')"
        );

        return (float) ($result['total'] ?? 0);
    }

    /**
     * Get recent orders
     * Mengambil pesanan terbaru (default 5)
     */
    public function getRecent(int $limit = 5): array
    {
        return $this->db->select(
            "SELECT o.*, u.name as user_name
             FROM {$this->table} o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC
             LIMIT {$limit}"
        );
    }

    /**
     * Get orders by status
     * Mengambil list pesanan berdasarkan status
     */
    public function getByStatus(string $status): array
    {
        return $this->db->select(
            "SELECT o.*, u.name as user_name, u.email as user_email
             FROM {$this->table} o
             JOIN users u ON o.user_id = u.id
             WHERE o.status = :status
             ORDER BY o.created_at DESC",
            ['status' => $status]
        );
    }
}
