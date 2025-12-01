<?php
/**
 * Cart Page
 * Market Place OutFit
 */

$pageTitle = 'Keranjang Belanja - Market Place OutFit';
$baseUrl = '..';
$extraScripts = ['cart.js'];

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in
if (!$isLoggedIn) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

// Redirect if admin
if ($isAdmin) {
    header('Location: ' . $baseUrl . '/admin/index.php');
    exit;
}

require_once __DIR__ . '/../classes/Cart.php';

$cart = new Cart();
$cartItems = $cart->getByUser($currentUser['id']);
$cartTotal = $cart->getTotal($currentUser['id']);

require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-cart3 me-2"></i>Keranjang Belanja
    </h2>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Keranjang Kosong</h4>
            <p class="text-muted">Belum ada produk di keranjang belanja Anda</p>
            <a href="home.php" class="btn btn-dark">
                <i class="bi bi-bag me-2"></i>Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="cartTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 100px;">Produk</th>
                                        <th>Detail</th>
                                        <th style="width: 150px;">Jumlah</th>
                                        <th style="width: 150px;">Subtotal</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr data-product-id="<?= $item['product_id'] ?>">
                                            <td>
                                                <?php if ($item['image']): ?>
                                                    <img src="<?= $baseUrl ?>/assets/images/products/<?= htmlspecialchars($item['image']) ?>" class="rounded" style="width: 80px; height: 80px; object-fit: cover;" alt="">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                        <i class="bi bi-image text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                <small class="text-muted">Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                                                <?php if ($item['quantity'] > $item['stock']): ?>
                                                    <br><small class="text-danger">Stok tidak cukup (tersedia: <?= $item['stock'] ?>)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary cart-qty-minus" type="button">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <input type="number" class="form-control text-center cart-qty" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>" data-product-id="<?= $item['product_id'] ?>">
                                                    <button class="btn btn-outline-secondary cart-qty-plus" type="button">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold item-subtotal">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-danger remove-from-cart" data-product-id="<?= $item['product_id'] ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="home.php" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
                    </a>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Item</span>
                            <span id="totalItems"><?= count($cartItems) ?> produk</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total</span>
                            <span class="h4 text-primary fw-bold mb-0" id="cartTotal">Rp <?= number_format($cartTotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-grid">
                            <a href="checkout.php" class="btn btn-dark btn-lg" id="checkoutBtn">
                                <i class="bi bi-credit-card me-2"></i>Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
