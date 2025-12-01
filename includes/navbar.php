<?php
/**
 * Navbar Template
 * Market Place OutFit
 */

require_once __DIR__ . '/../classes/Cart.php';

$cartCount = 0;
if ($isLoggedIn && !$isAdmin) {
    $cart = new Cart();
    $cartCount = $cart->countItems($currentUser['id']);
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $baseUrl ?>/pages/home.php">
            <i class="bi bi-bag-heart-fill me-2"></i>OutFit
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Search Form -->
            <form class="d-flex mx-auto my-2 my-lg-0" id="searchForm" style="max-width: 400px; width: 100%;">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari produk..." name="search">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $baseUrl ?>/pages/home.php">
                        <i class="bi bi-house-door me-1"></i>Beranda
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>
                    <?php if (!$isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="<?= $baseUrl ?>/pages/cart.php">
                                <i class="bi bi-cart3 me-1"></i>Keranjang
                                <?php if ($cartCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge">
                                        <?= $cartCount ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseUrl ?>/pages/orders.php">
                                <i class="bi bi-receipt me-1"></i>Pesanan
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseUrl ?>/admin/index.php">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($currentUser['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/pages/profile.php">
                                    <i class="bi bi-person me-2"></i>Profil Saya
                                </a>
                            </li>
                            <?php if (!$isAdmin): ?>
                                <li>
                                    <a class="dropdown-item" href="<?= $baseUrl ?>/pages/orders.php">
                                        <i class="bi bi-receipt me-2"></i>Riwayat Pesanan
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" id="logoutBtn">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/index.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
