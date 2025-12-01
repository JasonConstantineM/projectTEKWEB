<?php
/**
 * Admin Sidebar Template
 * Market Place OutFit
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white sidebar" style="width: 250px; min-height: 100vh;">
    <a href="<?= $baseUrl ?>/admin/index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="bi bi-bag-heart-fill me-2 fs-4"></i>
        <span class="fs-4 fw-bold">OutFit Admin</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= $baseUrl ?>/admin/index.php" class="nav-link text-white <?= $currentPage === 'index.php' ? 'active bg-primary' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>
        <li>
            <a href="<?= $baseUrl ?>/admin/products.php" class="nav-link text-white <?= $currentPage === 'products.php' ? 'active bg-primary' : '' ?>">
                <i class="bi bi-box-seam me-2"></i>Produk
            </a>
        </li>
        <li>
            <a href="<?= $baseUrl ?>/admin/categories.php" class="nav-link text-white <?= $currentPage === 'categories.php' ? 'active bg-primary' : '' ?>">
                <i class="bi bi-tags me-2"></i>Kategori
            </a>
        </li>
        <li>
            <a href="<?= $baseUrl ?>/admin/orders.php" class="nav-link text-white <?= $currentPage === 'orders.php' ? 'active bg-primary' : '' ?>">
                <i class="bi bi-receipt me-2"></i>Pesanan
            </a>
        </li>
        <li>
            <a href="<?= $baseUrl ?>/admin/users.php" class="nav-link text-white <?= $currentPage === 'users.php' ? 'active bg-primary' : '' ?>">
                <i class="bi bi-people me-2"></i>Pengguna
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-2 fs-5"></i>
            <strong><?= htmlspecialchars($currentUser['name']) ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li>
                <a class="dropdown-item" href="<?= $baseUrl ?>/pages/home.php">
                    <i class="bi bi-shop me-2"></i>Lihat Toko
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= $baseUrl ?>/pages/profile.php">
                    <i class="bi bi-person me-2"></i>Profil
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger" href="#" id="logoutBtn">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</div>
