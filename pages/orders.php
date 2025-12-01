<?php
/**
 * Orders Page - User Order History
 * Market Place OutFit
 */

$pageTitle = 'Pesanan Saya - Market Place OutFit';
$baseUrl = '..';

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in
if (!$isLoggedIn) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

// Redirect if admin
if ($isAdmin) {
    header('Location: ' . $baseUrl . '/admin/orders.php');
    exit;
}

require_once __DIR__ . '/../classes/Order.php';

$orderModel = new Order();
$orders = $orderModel->getByUser($currentUser['id']);

require_once __DIR__ . '/../includes/navbar.php';

// Status badge colors
$statusColors = [
    'pending' => 'warning',
    'processing' => 'info',
    'shipped' => 'primary',
    'completed' => 'success',
    'cancelled' => 'danger'
];

$statusLabels = [
    'pending' => 'Menunggu',
    'processing' => 'Diproses',
    'shipped' => 'Dikirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-receipt me-2"></i>Pesanan Saya
    </h2>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <i class="bi bi-receipt-cutoff display-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Belum Ada Pesanan</h4>
            <p class="text-muted">Anda belum pernah melakukan pemesanan</p>
            <a href="home.php" class="btn btn-dark">
                <i class="bi bi-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($orders as $order): ?>
                <?php $orderItems = $orderModel->getItems($order['id']); ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">Order #<?= $order['id'] ?></span>
                                <small class="text-muted ms-2">
                                    <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                                </small>
                            </div>
                            <span class="badge bg-<?= $statusColors[$order['status']] ?>">
                                <?= $statusLabels[$order['status']] ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <!-- Order Items -->
                            <div class="mb-3">
                                <?php foreach ($orderItems as $item): ?>
                                    <div class="d-flex gap-3 mb-2">
                                        <?php if ($item['product_image']): ?>
                                            <img src="<?= $baseUrl ?>/assets/images/products/<?= htmlspecialchars($item['product_image']) ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" alt="">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="bi bi-image text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                            <small class="text-muted"><?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                                        </div>
                                        <div>
                                            <span class="fw-bold">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr>

                            <!-- Shipping Address -->
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Alamat Pengiriman:</small>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <small class="text-muted">Total Pembayaran:</small>
                                    <h4 class="text-primary fw-bold mb-0">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
