<?php
/**
 * Product Detail Page
 * Market Place OutFit
 */

require_once __DIR__ . '/../classes/Product.php';

$productModel = new Product();
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productModel->findById($productId);

if (!$product) {
    header('Location: home.php');
    exit;
}

$pageTitle = $product['name'] . ' - Market Place OutFit';
$baseUrl = '..';
$extraScripts = ['cart.js'];

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="home.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item"><a href="home.php?category=<?= $product['category_id'] ?>" class="text-decoration-none"><?= htmlspecialchars($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <?php if ($product['image']): ?>
                    <img src="<?= $baseUrl ?>/assets/images/products/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="max-height: 500px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 400px;">
                        <i class="bi bi-image text-white display-1"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <span class="badge bg-primary mb-3"><?= htmlspecialchars($product['category_name']) ?></span>
            <h1 class="fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h1>

            <div class="mb-4">
                <span class="h2 text-primary fw-bold">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
            </div>

            <div class="mb-4">
                <h5 class="fw-semibold">Deskripsi Produk</h5>
                <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'] ?? 'Tidak ada deskripsi')) ?></p>
            </div>

            <div class="mb-4">
                <div class="d-flex align-items-center gap-4">
                    <div>
                        <span class="text-muted">Stok Tersedia:</span>
                        <span class="fw-bold <?= $product['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $product['stock'] > 0 ? $product['stock'] . ' item' : 'Habis' ?>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($isLoggedIn && !$isAdmin && $product['stock'] > 0): ?>
                <div class="row g-3 mb-4">
                    <div class="col-auto">
                        <label class="form-label">Jumlah:</label>
                        <div class="input-group" style="width: 150px;">
                            <button class="btn btn-outline-secondary qty-minus" type="button">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                            <button class="btn btn-outline-secondary qty-plus" type="button">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex">
                    <button class="btn btn-dark btn-lg px-5 add-to-cart-detail" data-id="<?= $product['id'] ?>">
                        <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
                    </button>
                    <a href="cart.php" class="btn btn-outline-dark btn-lg">
                        <i class="bi bi-cart3 me-2"></i>Lihat Keranjang
                    </a>
                </div>
            <?php elseif (!$isLoggedIn): ?>
                <a href="<?= $baseUrl ?>/index.php" class="btn btn-dark btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login untuk Membeli
                </a>
            <?php elseif ($product['stock'] <= 0): ?>
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="bi bi-x-circle me-2"></i>Stok Habis
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('quantity');
    const maxQty = <?= $product['stock'] ?>;

    document.querySelector('.qty-minus')?.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        if (val > 1) qtyInput.value = val - 1;
    });

    document.querySelector('.qty-plus')?.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        if (val < maxQty) qtyInput.value = val + 1;
    });

    document.querySelector('.add-to-cart-detail')?.addEventListener('click', function() {
        const productId = this.dataset.id;
        const quantity = parseInt(qtyInput.value) || 1;
        addToCart(productId, quantity);
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
