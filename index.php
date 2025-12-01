<?php
/**
 * Login & Register Page
 * Market Place OutFit
 */

session_start();

require_once __DIR__ . '/classes/User.php';

// Redirect if already logged in
if (User::isLoggedIn()) {
    if (User::isAdmin()) {
        header('Location: admin/index.php');
    } else {
        header('Location: pages/home.php');
    }
    exit;
}

$pageTitle = 'Login - Market Place OutFit';
$baseUrl = '.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            prefix: 'tw-',
            important: true,
        }
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
        .auth-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
            border-color: #6366f1;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }
        .nav-pills .nav-link {
            color: #6366f1;
        }
    </style>
</head>
<body class="d-flex align-items-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-10">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <h1 class="text-white fw-bold">
                        <i class="bi bi-bag-heart-fill me-2"></i>OutFit
                    </h1>
                    <p class="text-white-50">Fashion Marketplace Terpercaya</p>
                </div>

                <!-- Auth Card -->
                <div class="card auth-card border-0 shadow-lg rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-pills nav-justified mb-4" id="authTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-3" id="login-tab" data-bs-toggle="pill" data-bs-target="#login" type="button">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-3" id="register-tab" data-bs-toggle="pill" data-bs-target="#register" type="button">
                                    <i class="bi bi-person-plus me-1"></i>Register
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="authTabContent">
                            <!-- Login Form -->
                            <div class="tab-pane fade show active" id="login" role="tabpanel">
                                <form id="loginForm">
                                    <div class="mb-3">
                                        <label for="loginEmail" class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="nama@email.com" required>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="loginPassword" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Masukkan password" required>
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                    </button>
                                </form>

                                <!-- Demo Accounts -->
                                <div class="mt-4 p-3 bg-light rounded-3">
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-info-circle me-1"></i>Akun Demo:
                                    </small>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button class="btn btn-outline-secondary btn-sm w-100 demo-login" data-email="admin@outfit.com" data-password="password">
                                                <i class="bi bi-shield me-1"></i>Admin
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-outline-secondary btn-sm w-100 demo-login" data-email="user1@gmail.com" data-password="password">
                                                <i class="bi bi-person me-1"></i>User
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Register Form -->
                            <div class="tab-pane fade" id="register" role="tabpanel">
                                <form id="registerForm">
                                    <div class="mb-3">
                                        <label for="registerName" class="form-label">Nama Lengkap</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <input type="text" class="form-control" id="registerName" name="name" placeholder="Nama lengkap" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerEmail" class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control" id="registerEmail" name="email" placeholder="nama@email.com" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerPhone" class="form-label">No. Telepon <span class="text-muted">(opsional)</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-telephone"></i>
                                            </span>
                                            <input type="tel" class="form-control" id="registerPhone" name="phone" placeholder="08xxxxxxxxxx">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerPassword" class="form-label">Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-lock"></i>
                                            </span>
                                            <input type="password" class="form-control" id="registerPassword" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="registerConfirmPassword" class="form-label">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-lock-fill"></i>
                                            </span>
                                            <input type="password" class="form-control" id="registerConfirmPassword" name="confirm_password" placeholder="Ulangi password" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                        <i class="bi bi-person-plus me-1"></i>Daftar Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <small class="text-white-50">
                        &copy; <?= date('Y') ?> Market Place OutFit. All rights reserved.
                    </small>
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

            // Toggle password visibility
            $('.toggle-password').on('click', function() {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bi-eye').addClass('bi-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bi-eye-slash').addClass('bi-eye');
                }
            });

            // Demo login buttons
            $('.demo-login').on('click', function() {
                $('#loginEmail').val($(this).data('email'));
                $('#loginPassword').val($(this).data('password'));
            });

            // Login form
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    email: $('#loginEmail').val(),
                    password: $('#loginPassword').val()
                };

                $.ajax({
                    url: baseUrl + '/api/auth/login.php',
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
                                if (response.user.role === 'admin') {
                                    window.location.href = baseUrl + '/admin/index.php';
                                } else {
                                    window.location.href = baseUrl + '/pages/home.php';
                                }
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

            // Register form
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                const password = $('#registerPassword').val();
                const confirmPassword = $('#registerConfirmPassword').val();

                if (password !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Password tidak cocok'
                    });
                    return;
                }

                const formData = {
                    name: $('#registerName').val(),
                    email: $('#registerEmail').val(),
                    phone: $('#registerPhone').val(),
                    password: password
                };

                $.ajax({
                    url: baseUrl + '/api/auth/register.php',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message + '. Silakan login.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Switch to login tab
                                $('#login-tab').tab('show');
                                $('#loginEmail').val($('#registerEmail').val());
                                $('#registerForm')[0].reset();
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
