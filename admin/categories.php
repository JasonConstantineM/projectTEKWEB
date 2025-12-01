<?php
/**
 * Admin Categories Management
 * Market Place OutFit
 */

$pageTitle = 'Kelola Kategori - Market Place OutFit';
$baseUrl = '..';

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in or not admin
if (!$isLoggedIn || !$isAdmin) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

require_once __DIR__ . '/../classes/Category.php';

$categoryModel = new Category();
$categories = $categoryModel->getAllWithProductCount();
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1">
        <nav class="navbar navbar-light bg-white border-bottom px-4">
            <span class="navbar-brand mb-0 h4">Kelola Kategori</span>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetCategoryForm()">
                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
            </button>
        </nav>

        <div class="p-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Produk</th>
                                    <th>Tanggal Dibuat</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada kategori</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td><?= $cat['id'] ?></td>
                                            <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                            <td><?= htmlspecialchars($cat['description'] ?? '-') ?></td>
                                            <td><span class="badge bg-primary"><?= $cat['product_count'] ?> produk</span></td>
                                            <td><?= date('d/m/Y', strtotime($cat['created_at'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-category" data-id="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>" data-description="<?= htmlspecialchars($cat['description'] ?? '') ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-category" data-id="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>" data-count="<?= $cat['product_count'] ?>">
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" id="categoryId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?= $baseUrl ?>';

function resetCategoryForm() {
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModalTitle').textContent = 'Tambah Kategori';
}

document.addEventListener('DOMContentLoaded', function() {
    // Edit category
    document.querySelectorAll('.edit-category').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = this.dataset;
            document.getElementById('categoryId').value = data.id;
            document.getElementById('categoryName').value = data.name;
            document.getElementById('categoryDescription').value = data.description;
            document.getElementById('categoryModalTitle').textContent = 'Edit Kategori';
            new bootstrap.Modal(document.getElementById('categoryModal')).show();
        });
    });

    // Delete category
    document.querySelectorAll('.delete-category').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const count = this.dataset.count;

            if (count > 0) {
                Swal.fire('Tidak Bisa Dihapus', `Kategori "${name}" memiliki ${count} produk. Hapus produk terlebih dahulu.`, 'warning');
                return;
            }

            Swal.fire({
                title: 'Hapus Kategori?',
                text: `Apakah Anda yakin ingin menghapus "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(baseUrl + '/api/categories/delete.php', {
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

    // Save category
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('categoryId').value;
        const url = id ? baseUrl + '/api/categories/update.php' : baseUrl + '/api/categories/create.php';

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: id,
                name: document.getElementById('categoryName').value,
                description: document.getElementById('categoryDescription').value
            })
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
