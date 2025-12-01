/**
 * Main JavaScript
 * Market Place OutFit
 */

// Base URL
const baseUrl = document.querySelector('script[src*="main.js"]')?.src.replace('/assets/js/main.js', '') || '';

// Document Ready
$(document).ready(function() {
    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Logout handler
    $('#logoutBtn').on('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Logout',
            text: 'Apakah Anda yakin ingin keluar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#212529',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: getBaseUrl() + '/api/auth/logout.php',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        window.location.href = getBaseUrl() + '/index.php';
                    },
                    error: function() {
                        window.location.href = getBaseUrl() + '/index.php';
                    }
                });
            }
        });
    });

    // Search form handler
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const searchQuery = $('#searchInput').val().trim();
        if (searchQuery) {
            window.location.href = getBaseUrl() + '/pages/home.php?search=' + encodeURIComponent(searchQuery);
        }
    });
});

/**
 * Get base URL
 */
function getBaseUrl() {
    const scripts = document.getElementsByTagName('script');
    for (let i = 0; i < scripts.length; i++) {
        if (scripts[i].src.includes('main.js')) {
            return scripts[i].src.replace('/assets/js/main.js', '');
        }
    }
    return '';
}

/**
 * Format currency to IDR
 */
function formatCurrency(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

/**
 * Show loading state
 */
function showLoading(element) {
    $(element).addClass('loading');
}

/**
 * Hide loading state
 */
function hideLoading(element) {
    $(element).removeClass('loading');
}

/**
 * Show toast notification
 */
function showToast(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}

/**
 * Confirm delete
 */
function confirmDelete(message, callback) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback();
        }
    });
}

/**
 * Update cart badge
 */
function updateCartBadge(count) {
    const badge = $('#cartBadge');
    if (count > 0) {
        if (badge.length) {
            badge.text(count);
        } else {
            $('a[href*="cart.php"] .bi-cart3').after(
                '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cartBadge">' + count + '</span>'
            );
        }
    } else {
        badge.remove();
    }
}