/**
 * Cart JavaScript
 * Market Place OutFit
 */

$(document).ready(function() {
    const baseUrl = getBaseUrl();

    // Add to cart (from product list)
    $('.add-to-cart').on('click', function() {
        const productId = $(this).data('id');
        addToCart(productId, 1);
    });

    // Cart quantity controls
    $('.cart-qty-minus').on('click', function() {
        const input = $(this).siblings('.cart-qty');
        let val = parseInt(input.val()) || 1;
        if (val > 1) {
            input.val(val - 1);
            updateCartItem(input.data('product-id'), val - 1);
        }
    });

    $('.cart-qty-plus').on('click', function() {
        const input = $(this).siblings('.cart-qty');
        const max = parseInt(input.attr('max')) || 999;
        let val = parseInt(input.val()) || 1;
        if (val < max) {
            input.val(val + 1);
            updateCartItem(input.data('product-id'), val + 1);
        }
    });

    // Cart quantity input change
    $('.cart-qty').on('change', function() {
        const productId = $(this).data('product-id');
        const quantity = parseInt($(this).val()) || 1;
        updateCartItem(productId, quantity);
    });

    // Remove from cart
    $('.remove-from-cart').on('click', function() {
        const productId = $(this).data('product-id');
        removeFromCart(productId);
    });
});

/**
 * Add product to cart
 */
function addToCart(productId, quantity = 1) {
    const baseUrl = getBaseUrl();

    $.ajax({
        url: baseUrl + '/api/cart/add.php',
        type: 'POST',
        data: JSON.stringify({
            product_id: productId,
            quantity: quantity
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showToast(response.message, 'success');
                updateCartBadge(response.cart_count);
            } else {
                showToast(response.message, 'error');
            }
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Diperlukan',
                    text: 'Silakan login untuk menambahkan produk ke keranjang',
                    confirmButtonText: 'Login',
                    confirmButtonColor: '#212529'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = baseUrl + '/index.php';
                    }
                });
            } else {
                showToast('Terjadi kesalahan', 'error');
            }
        }
    });
}

/**
 * Update cart item quantity
 */
function updateCartItem(productId, quantity) {
    const baseUrl = getBaseUrl();
    const row = $(`tr[data-product-id="${productId}"]`);

    $.ajax({
        url: baseUrl + '/api/cart/update.php',
        type: 'POST',
        data: JSON.stringify({
            product_id: productId,
            quantity: quantity
        }),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update subtotal
                row.find('.item-subtotal').text(formatCurrency(response.subtotal));
                // Update total
                $('#cartTotal').text(formatCurrency(response.total));
                updateCartBadge(response.cart_count);
            } else {
                showToast(response.message, 'error');
                location.reload();
            }
        },
        error: function() {
            showToast('Terjadi kesalahan', 'error');
        }
    });
}

/**
 * Remove item from cart
 */
function removeFromCart(productId) {
    const baseUrl = getBaseUrl();

    Swal.fire({
        title: 'Hapus dari Keranjang?',
        text: 'Produk ini akan dihapus dari keranjang belanja',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl + '/api/cart/remove.php',
                type: 'POST',
                data: JSON.stringify({
                    product_id: productId
                }),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        // Remove row
                        $(`tr[data-product-id="${productId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            // Update total
                            $('#cartTotal').text(formatCurrency(response.total));
                            updateCartBadge(response.cart_count);
                            // If cart is empty, reload
                            if ($('#cartTable tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        showToast(response.message, 'error');
                    }
                },
                error: function() {
                    showToast('Terjadi kesalahan', 'error');
                }
            });
        }
    });
}
