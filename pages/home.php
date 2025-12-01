<?php
/**
 * Home Page - Product Catalog
 * Market Place OutFit
 */

$pageTitle = 'Beranda - Market Place OutFit';
$baseUrl = '..';
$extraScripts = ['products.js', 'cart.js'];

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Product.php';

$category = new Category();
$product = new Product();

$categories = $category->getAll();
$products = $product->getAvailable();

// Filter by category if specified
$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
if ($selectedCategory > 0) {
    $products = $product->getAvailableByCategory($selectedCategory);
}

require_once __DIR__ . '/../includes/navbar.php';
?>

<!-- Hero Section -->
<section class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Temukan Gaya Fashionmu</h1>
                <p class="lead mb-4">Koleksi pakaian terlengkap dengan harga terjangkau. Belanja sekarang dan tampil lebih percaya diri!</p>
                <a href="#products" class="btn btn-light btn-lg px-4">
                    <i class="bi bi-bag me-2"></i>Belanja Sekarang
                </a>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <i class="bi bi-bag-heart display-1" style="font-size: 12rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 justify-content-center" id="categoryFilter">
            <a href="?category=0" class="btn <?= $selectedCategory === 0 ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill">
                <i class="bi bi-grid me-1"></i>Semua
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="?category=<?= $cat['id'] ?>" class="btn <?= $selectedCategory === (int)$cat['id'] ? 'btn-dark' : 'btn-outline-dark' ?> rounded-pill">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5" id="products">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold">
                    <i class="bi bi-box-seam me-2"></i>Produk Kami
                    <span class="badge bg-dark ms-2"><?= count($products) ?> item</span>
                </h2>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Tidak ada produk</h4>
                <p class="text-muted">Belum ada produk tersedia untuk kategori ini</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="productGrid">
                <?php foreach ($products as $prod): ?>
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="position-relative">
                                <?php if ($prod['image']): ?>
                                    <img src="<?= $baseUrl ?>/assets/images/products/<?= htmlspecialchars($prod['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($prod['name']) ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="bi bi-image text-white display-4"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="badge bg-primary position-absolute top-0 end-0 m-2">
                                    <?= htmlspecialchars($prod['category_name']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title mb-1"><?= htmlspecialchars($prod['name']) ?></h6>
                                <p class="text-muted small mb-2">
                                    <?= strlen($prod['description']) > 50 ? htmlspecialchars(substr($prod['description'], 0, 50)) . '...' : htmlspecialchars($prod['description']) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0 text-primary fw-bold">
                                        Rp <?= number_format($prod['price'], 0, ',', '.') ?>
                                    </span>
                                    <small class="text-muted">
                                        <i class="bi bi-box me-1"></i>Stok: <?= $prod['stock'] ?>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 pb-3">
                                <div class="d-grid gap-2">
                                    <a href="product-detail.php?id=<?= $prod['id'] ?>" class="btn btn-outline-dark btn-sm">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                    <?php if ($isLoggedIn && !$isAdmin): ?>
                                        <button class="btn btn-dark btn-sm add-to-cart" data-id="<?= $prod['id'] ?>">
                                            <i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang
                                        </button>
                                    <?php elseif (!$isLoggedIn): ?>
                                        <a href="<?= $baseUrl ?>/index.php" class="btn btn-dark btn-sm">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>Login untuk Membeli
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="bi bi-bag-heart-fill me-2"></i>Market Place OutFit</h5>
                <p class="text-white-50 mb-0">Fashion marketplace terpercaya dengan koleksi terlengkap.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-white-50 mb-0">&copy; <?= date('Y') ?> Market Place OutFit. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
