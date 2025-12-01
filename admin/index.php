<?php
/**
 * Admin Dashboard
 * Market Place OutFit
 */

$pageTitle = 'Dashboard Admin - Market Place OutFit';
$baseUrl = '..';

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in or not admin
if (!$isLoggedIn || !$isAdmin) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Order.php';
require_once __DIR__ . '/../classes/User.php';

$productModel = new Product();
$categoryModel = new Category();
$orderModel = new Order();
$userModel = new User();

// Get statistics
$totalProducts = $productModel->count();
$totalCategories = $categoryModel->count();
$totalOrders = $orderModel->count();
$totalUsers = $userModel->countByRole('user');
$totalRevenue = $orderModel->getTotalRevenue();
$pendingOrders = $orderModel->countByStatus('pending');
$recentOrders = $orderModel->getRecent(5);
$lowStockProducts = $productModel->getLowStock(10);

// Status labels
$statusLabels = [
    'pending' => 'Menunggu',
    'processing' => 'Diproses',
    'shipped' => 'Dikirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];

$statusColors = [
    'pending' => 'warning',
    'processing' => 'info',
    'shipped' => 'primary',
    'completed' => 'success',
    'cancelled' => 'danger'
];
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1">
        <!-- Top Navbar -->
        <nav class="navbar navbar-light bg-white border-bottom px-4">
            <span class="navbar-brand mb-0 h4">Dashboard</span>
            <span class="text-muted">Selamat datang, <?= htmlspecialchars($currentUser['name']) ?></span>
        </nav>

        <div class="p-4">
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-box-seam text-primary fs-3"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0"><?= $totalProducts ?></h3>
                                    <small class="text-muted">Total Produk</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-receipt text-success fs-3"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0"><?= $totalOrders ?></h3>
                                    <small class="text-muted">Total Pesanan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-people text-warning fs-3"></i>
                                </div>
                                <div class="ms-3">
                                    <h3 class="mb-0"><?= $totalUsers ?></h3>
                                    <small class="text-muted">Total Pengguna</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3">
                                    <i class="bi bi-currency-dollar text-info fs-3"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h6>
                                    <small class="text-muted">Total Pendapatan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Recent Orders -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pesanan Terbaru</h5>
                            <?php if ($pendingOrders > 0): ?>
                                <span class="badge bg-warning"><?= $pendingOrders ?> menunggu</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($recentOrders)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox text-muted display-6"></i>
                                    <p class="text-muted mt-2 mb-0">Belum ada pesanan</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentOrders as $order): ?>
                                                <tr>
                                                    <td><a href="orders.php?id=<?= $order['id'] ?>" class="text-decoration-none">#<?= $order['id'] ?></a></td>
                                                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                                                    <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $statusColors[$order['status']] ?>">
                                                            <?= $statusLabels[$order['status']] ?>
                                                        </span>
                                                    </td>
                                                    <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="orders.php" class="btn btn-sm btn-outline-dark">Lihat Semua Pesanan</a>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Stok Menipis</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($lowStockProducts)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-check-circle text-success display-6"></i>
                                    <p class="text-muted mt-2 mb-0">Semua stok aman</p>
                                </div>
                            <?php else: ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach (array_slice($lowStockProducts, 0, 5) as $prod): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="d-block"><?= htmlspecialchars($prod['name']) ?></span>
                                                <small class="text-muted"><?= htmlspecialchars($prod['category_name']) ?></small>
                                            </div>
                                            <span class="badge bg-<?= $prod['stock'] == 0 ? 'danger' : 'warning' ?> rounded-pill">
                                                <?= $prod['stock'] ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="products.php" class="btn btn-sm btn-outline-dark">Kelola Produk</a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Aksi Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="products.php?action=add" class="btn btn-outline-dark">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                                </a>
                                <a href="categories.php?action=add" class="btn btn-outline-dark">
                                    <i class="bi bi-tags me-2"></i>Tambah Kategori
                                </a>
                                <a href="<?= $baseUrl ?>/pages/home.php" class="btn btn-outline-dark" target="_blank">
                                    <i class="bi bi-shop me-2"></i>Lihat Toko
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
