<?php
/**
 * Admin Products Management
 * Market Place OutFit
 */

$pageTitle = 'Kelola Produk - Market Place OutFit';
$baseUrl = '..';
$extraScripts = ['admin.js'];

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in or not admin
if (!$isLoggedIn || !$isAdmin) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

$productModel = new Product();
$categoryModel = new Category();

$products = $productModel->getAll();
$categories = $categoryModel->getAll();
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1">
        <nav class="navbar navbar-light bg-white border-bottom px-4">
            <span class="navbar-brand mb-0 h4">Kelola Produk</span>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetProductForm()">
                <i class="bi bi-plus-circle me-2"></i>Tambah Produk
            </button>
        </nav>

        <div class="p-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="productsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada produk</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr data-id="<?= $product['id'] ?>">
                                            <td>
                                                <?php if ($product['image']): ?>
                                                    <img src="<?= $baseUrl ?>/assets/images/products/<?= htmlspecialchars($product['image']) ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" alt="">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($product['name']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars(substr($product['description'] ?? '', 0, 50)) ?><?= strlen($product['description'] ?? '') > 50 ? '...' : '' ?></small>
                                            </td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($product['category_name']) ?></span></td>
                                            <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $product['stock'] > 10 ? 'success' : ($product['stock'] > 0 ? 'warning' : 'danger') ?>">
                                                    <?= $product['stock'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-product" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-description="<?= htmlspecialchars($product['description'] ?? '') ?>" data-category="<?= $product['category_id'] ?>" data-price="<?= $product['price'] ?>" data-stock="<?= $product['stock'] ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-product" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="productId" name="id">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="productName" name="name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" id="productCategory" name="category_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="productPrice" name="price" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="productStock" name="stock" required min="0" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Gambar Produk</label>
                            <input type="file" class="form-control" id="productImage" name="image" accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Maks: 2MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark" id="saveProductBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?= $baseUrl ?>';

function resetProductForm() {
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('productModalTitle').textContent = 'Tambah Produk';
}

document.addEventListener('DOMContentLoaded', function() {
    // Edit product
    document.querySelectorAll('.edit-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = this.dataset;
            document.getElementById('productId').value = data.id;
            document.getElementById('productName').value = data.name;
            document.getElementById('productDescription').value = data.description;
            document.getElementById('productCategory').value = data.category;
            document.getElementById('productPrice').value = data.price;
            document.getElementById('productStock').value = data.stock;
            document.getElementById('productModalTitle').textContent = 'Edit Produk';
            new bootstrap.Modal(document.getElementById('productModal')).show();
        });
    });

    // Delete product
    document.querySelectorAll('.delete-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            Swal.fire({
                title: 'Hapus Produk?',
                text: `Apakah Anda yakin ingin menghapus "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(baseUrl + '/api/products/delete.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
                }
            });
        });
    });

    // Save product
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const id = document.getElementById('productId').value;
        const url = id ? baseUrl + '/api/products/update.php' : baseUrl + '/api/products/create.php';

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
