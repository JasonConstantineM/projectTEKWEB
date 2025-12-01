<?php
/**
 * Profile Page
 * Market Place OutFit
 */

$pageTitle = 'Profil Saya - Market Place OutFit';
$baseUrl = '..';

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in
if (!$isLoggedIn) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

require_once __DIR__ . '/../classes/User.php';

$userModel = new User();
$userDetails = $userModel->findById($currentUser['id']);

require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-4">
                <i class="bi bi-person-circle me-2"></i>Profil Saya
            </h2>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form id="profileForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($userDetails['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($userDetails['email']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($userDetails['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="<?= ucfirst($userDetails['role']) ?>" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Alamat lengkap"><?= htmlspecialchars($userDetails['address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Ubah Password <small class="text-muted">(opsional)</small></h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Ulangi password baru">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Informasi Akun</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Terdaftar sejak:</small>
                            <p class="mb-0"><?= date('d F Y', strtotime($userDetails['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Terakhir diperbarui:</small>
                            <p class="mb-0"><?= $userDetails['updated_at'] ? date('d F Y, H:i', strtotime($userDetails['updated_at'])) : '-' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    const baseUrl = '<?= $baseUrl ?>';

    $('#profileForm').on('submit', function(e) {
        e.preventDefault();

        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();

        if (password && password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Password tidak cocok'
            });
            return;
        }

        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            address: $('#address').val()
        };

        if (password) {
            formData.password = password;
        }

        $.ajax({
            url: baseUrl + '/api/users/update.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan pada server'
                });
            }
        });
    });
});
</script>
</body>
</html>
