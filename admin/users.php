<?php
/**
 * Admin Users Management
 * Market Place OutFit
 */

$pageTitle = 'Kelola Pengguna - Market Place OutFit';
$baseUrl = '..';

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in or not admin
if (!$isLoggedIn || !$isAdmin) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

require_once __DIR__ . '/../classes/User.php';

$userModel = new User();
$users = $userModel->getAll();
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1">
        <nav class="navbar navbar-light bg-white border-bottom px-4">
            <span class="navbar-brand mb-0 h4">Kelola Pengguna</span>
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetUserForm()">
                <i class="bi bi-plus-circle me-2"></i>Tambah Pengguna
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
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Role</th>
                                    <th>Terdaftar</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                                <?= ucfirst($user['role']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-user"
                                                data-id="<?= $user['id'] ?>"
                                                data-name="<?= htmlspecialchars($user['name']) ?>"
                                                data-email="<?= htmlspecialchars($user['email']) ?>"
                                                data-phone="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                                data-address="<?= htmlspecialchars($user['address'] ?? '') ?>"
                                                data-role="<?= $user['role'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php if ($user['id'] !== $currentUser['id']): ?>
                                                <button class="btn btn-sm btn-outline-danger delete-user" data-id="<?= $user['id'] ?>" data-name="<?= htmlspecialchars($user['name']) ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="userName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="tel" class="form-control" id="userPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" id="userAddress" name="address" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="userRole" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-muted" id="passwordHint">(min. 6 karakter)</span></label>
                        <input type="password" class="form-control" id="userPassword" name="password">
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
const currentUserId = <?= $currentUser['id'] ?>;

function resetUserForm() {
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userModalTitle').textContent = 'Tambah Pengguna';
    document.getElementById('userPassword').required = true;
    document.getElementById('passwordHint').textContent = '(min. 6 karakter)';
}

document.addEventListener('DOMContentLoaded', function() {
    // Edit user
    document.querySelectorAll('.edit-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = this.dataset;
            document.getElementById('userId').value = data.id;
            document.getElementById('userName').value = data.name;
            document.getElementById('userEmail').value = data.email;
            document.getElementById('userPhone').value = data.phone;
            document.getElementById('userAddress').value = data.address;
            document.getElementById('userRole').value = data.role;
            document.getElementById('userPassword').value = '';
            document.getElementById('userPassword').required = false;
            document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
            document.getElementById('passwordHint').textContent = '(kosongkan jika tidak ingin mengubah)';

            // Disable role change for self
            if (parseInt(data.id) === currentUserId) {
                document.getElementById('userRole').disabled = true;
            } else {
                document.getElementById('userRole').disabled = false;
            }

            new bootstrap.Modal(document.getElementById('userModal')).show();
        });
    });

    // Delete user
    document.querySelectorAll('.delete-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            Swal.fire({
                title: 'Hapus Pengguna?',
                text: `Apakah Anda yakin ingin menghapus "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(baseUrl + '/api/users/delete.php', {
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

    // Save user
    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('userId').value;
        const url = id ? baseUrl + '/api/users/update.php' : baseUrl + '/api/users/create.php';

        const data = {
            id: id,
            name: document.getElementById('userName').value,
            email: document.getElementById('userEmail').value,
            phone: document.getElementById('userPhone').value,
            address: document.getElementById('userAddress').value,
            role: document.getElementById('userRole').value
        };

        const password = document.getElementById('userPassword').value;
        if (password) {
            data.password = password;
        }

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
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
