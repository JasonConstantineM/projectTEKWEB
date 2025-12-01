<?php
/**
 * Checkout Page
 * Market Place OutFit
 */

$pageTitle = 'Checkout - Market Place OutFit';
$baseUrl = '..';

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
require_once __DIR__ . '/../classes/User.php';

$cart = new Cart();
$cartItems = $cart->getByUser($currentUser['id']);
$cartTotal = $cart->getTotal($currentUser['id']);

// Redirect if cart is empty
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

// Validate cart
$cartValidation = $cart->validate($currentUser['id']);

// Get user details
$userModel = new User();
$userDetails = $userModel->findById($currentUser['id']);

require_once __DIR__ . '/../includes/navbar.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">
        <i class="bi bi-credit-card me-2"></i>Checkout
    </h2>

    <?php if (!$cartValidation['valid']): ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= $cartValidation['message'] ?>
            <ul class="mb-0 mt-2">
                <?php foreach ($cartValidation['invalid_items'] as $invalid): ?>
                    <li><?= htmlspecialchars($invalid['product_name']) ?>: diminta <?= $invalid['requested'] ?>, tersedia <?= $invalid['available'] ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="cart.php" class="btn btn-outline-danger btn-sm mt-2">Kembali ke Keranjang</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Checkout Form -->
            <div class="col-lg-7">
                <form id="checkoutForm">
                    <!-- Shipping Address -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Alamat Pengiriman</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Penerima</label>
                                <input type="text" class="form-control" id="receiverName" value="<?= htmlspecialchars($userDetails['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. Telepon</label>
                                <input type="tel" class="form-control" id="receiverPhone" value="<?= htmlspecialchars($userDetails['phone'] ?? '') ?>" required placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="shippingAddress" rows="3" required placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, kode pos"><?= htmlspecialchars($userDetails['address'] ?? '') ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan <span class="text-muted">(opsional)</span></label>
                                <textarea class="form-control" id="orderNote" rows="2" placeholder="Catatan untuk penjual atau kurir"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method (Simulation) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Metode Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="cod" checked>
                                <label class="form-check-label" for="cod">
                                    <i class="bi bi-cash-stack me-2"></i>Bayar di Tempat (COD)
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="transfer" value="transfer">
                                <label class="form-check-label" for="transfer">
                                    <i class="bi bi-bank me-2"></i>Transfer Bank
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="ewallet" value="ewallet">
                                <label class="form-check-label" for="ewallet">
                                    <i class="bi bi-phone me-2"></i>E-Wallet (GoPay, OVO, Dana)
                                </label>
                            </div>
                            <small class="text-muted mt-2 d-block">* Ini adalah simulasi pembayaran</small>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                    <?php if ($item['image']): ?>
                                        <img src="<?= $baseUrl ?>/assets/images/products/<?= htmlspecialchars($item['image']) ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;" alt="">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="bi bi-image text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                        <small class="text-muted"><?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($cartTotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkos Kirim</span>
                            <span class="text-success">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold mb-0">Total</span>
                            <span class="h4 text-primary fw-bold mb-0">Rp <?= number_format($cartTotal, 0, ',', '.') ?></span>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-dark btn-lg" id="placeOrderBtn">
                                <i class="bi bi-check-circle me-2"></i>Buat Pesanan
                            </button>
                        </div>

                        <p class="text-muted small text-center mt-3 mb-0">
                            Dengan melakukan pemesanan, Anda menyetujui syarat dan ketentuan yang berlaku.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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

    $('#placeOrderBtn').on('click', function() {
        const receiverName = $('#receiverName').val().trim();
        const receiverPhone = $('#receiverPhone').val().trim();
        const shippingAddress = $('#shippingAddress').val().trim();
        const orderNote = $('#orderNote').val().trim();

        // Validation
        if (!receiverName || !receiverPhone || !shippingAddress) {
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Lengkap',
                text: 'Mohon lengkapi nama, telepon, dan alamat pengiriman'
            });
            return;
        }

        // Confirm order
        Swal.fire({
            title: 'Konfirmasi Pesanan',
            text: 'Apakah Anda yakin ingin membuat pesanan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#212529',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Buat Pesanan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses Pesanan...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create order
                $.ajax({
                    url: baseUrl + '/api/orders/create.php',
                    type: 'POST',
                    data: JSON.stringify({
                        shipping_address: `${receiverName}\n${receiverPhone}\n${shippingAddress}${orderNote ? '\n\nCatatan: ' + orderNote : ''}`
                    }),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pesanan Berhasil!',
                                text: 'Pesanan Anda telah dibuat dengan ID #' + response.order_id,
                                confirmButtonColor: '#212529'
                            }).then(() => {
                                window.location.href = 'orders.php';
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
            }
        });
    });
});
</script>
</body>
</html>
