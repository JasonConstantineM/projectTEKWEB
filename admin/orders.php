<?php
/**
 * Admin Orders Management
 * Market Place OutFit
 */

$pageTitle = 'Kelola Pesanan - Market Place OutFit';
$baseUrl = '..';

require_once __DIR__ . '/../includes/header.php';

// Redirect if not logged in or not admin
if (!$isLoggedIn || !$isAdmin) {
    header('Location: ' . $baseUrl . '/index.php');
    exit;
}

require_once __DIR__ . '/../classes/Order.php';

$orderModel = new Order();
$orders = $orderModel->getAll();

// Status labels and colors
$statusLabels = [
    'pending' => 'Menunggu',
    'processing' => 'Diproses',
    'shipped' => 'Dikirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];

$statusColors = [
    'pending' => 'warning',
    'processing' => 'info',
    'shipped' => 'primary',
    'completed' => 'success',
    'cancelled' => 'danger'
];
?>

<div class="d-flex">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="flex-grow-1">
        <nav class="navbar navbar-light bg-white border-bottom px-4">
            <span class="navbar-brand mb-0 h4">Kelola Pesanan</span>
        </nav>

        <div class="p-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada pesanan</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><strong>#<?= $order['id'] ?></strong></td>
                                            <td>
                                                <?= htmlspecialchars($order['user_name']) ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($order['user_email']) ?></small>
                                            </td>
                                            <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-<?= $statusColors[$order['status']] ?>">
                                                    <?= $statusLabels[$order['status']] ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary view-order" data-id="<?= $order['id'] ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <select class="form-select form-select-sm d-inline-block update-status" data-id="<?= $order['id'] ?>" style="width: auto;">
                                                    <?php foreach ($statusLabels as $key => $label): ?>
                                                        <option value="<?= $key ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                                                    <?php endforeach; ?>
                                                </select>
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

<!-- Order Detail Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesanan #<span id="orderIdDisplay"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const baseUrl = '<?= $baseUrl ?>';

document.addEventListener('DOMContentLoaded', function() {
    // View order detail
    document.querySelectorAll('.view-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('orderIdDisplay').textContent = id;

            fetch(baseUrl + '/api/orders/read.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const order = data.order;
                        const items = data.items;

                        let itemsHtml = '';
                        items.forEach(item => {
                            itemsHtml += `
                                <div class="d-flex gap-3 mb-2">
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="bi bi-image text-white"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">${item.product_name}</h6>
                                        <small class="text-muted">${item.quantity} x Rp ${Number(item.price).toLocaleString('id-ID')}</small>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Rp ${Number(item.price * item.quantity).toLocaleString('id-ID')}</span>
                                    </div>
                                </div>
                            `;
                        });

                        document.getElementById('orderDetailContent').innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informasi Customer</h6>
                                    <p class="mb-1"><strong>${order.user_name}</strong></p>
                                    <p class="mb-1">${order.user_email}</p>
                                    <p class="mb-0">${order.user_phone || '-'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Alamat Pengiriman</h6>
                                    <p class="mb-0">${order.shipping_address.replace(/\n/g, '<br>')}</p>
                                </div>
                            </div>
                            <hr>
                            <h6>Item Pesanan</h6>
                            ${itemsHtml}
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong class="text-primary">Rp ${Number(order.total_amount).toLocaleString('id-ID')}</strong>
                            </div>
                        `;
                    }
                });

            new bootstrap.Modal(document.getElementById('orderModal')).show();
        });
    });

    // Update status
    document.querySelectorAll('.update-status').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.dataset.id;
            const status = this.value;

            Swal.fire({
                title: 'Update Status?',
                text: 'Apakah Anda yakin ingin mengubah status pesanan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#212529',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(baseUrl + '/api/orders/update.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id, status: status })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
                } else {
                    location.reload();
                }
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
