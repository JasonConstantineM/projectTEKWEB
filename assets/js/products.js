/**
 * Products JavaScript
 * Market Place OutFit
 */

$(document).ready(function() {
    const baseUrl = getBaseUrl();

    // Category filter (AJAX version - optional)
    // Currently using page reload, but this can be used for AJAX filtering
    $('#categoryFilter a').on('click', function(e) {
        // Uncomment below for AJAX filtering
        // e.preventDefault();
        // const categoryId = $(this).attr('href').split('=')[1];
        // filterProducts(categoryId);
    });

    // Product search with debounce
    let searchTimeout;
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        const query = $(this).val().trim();

        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchProducts(query);
            }, 500);
        }
    });
});

/**
 * Filter products by category (AJAX)
 */
function filterProducts(categoryId) {
    const baseUrl = getBaseUrl();

    $.ajax({
        url: baseUrl + '/api/products/read.php',
        type: 'GET',
        data: categoryId > 0 ? { category_id: categoryId } : {},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderProducts(response.products);
                // Update active filter
                $('#categoryFilter a').removeClass('btn-dark').addClass('btn-outline-dark');
                $(`#categoryFilter a[href*="category=${categoryId}"]`).removeClass('btn-outline-dark').addClass('btn-dark');
            }
        },
        error: function() {
            showToast('Gagal memuat produk', 'error');
        }
    });
}

/**
 * Search products (AJAX)
 */
function searchProducts(query) {
    const baseUrl = getBaseUrl();

    $.ajax({
        url: baseUrl + '/api/products/read.php',
        type: 'GET',
        data: { search: query },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                renderProducts(response.products);
            }
        },
        error: function() {
            showToast('Gagal mencari produk', 'error');
        }
    });
}

/**
 * Render products grid
 */
function renderProducts(products) {
    const baseUrl = getBaseUrl();
    const grid = $('#productGrid');
    grid.empty();

    if (products.length === 0) {
        grid.html(`
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Tidak ada produk</h4>
                <p class="text-muted">Produk tidak ditemukan</p>
            </div>
        `);
        return;
    }

    products.forEach(product => {
        const imageHtml = product.image
            ? `<img src="${baseUrl}/assets/images/products/${product.image}" class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">`
            : `<div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;"><i class="bi bi-image text-white display-4"></i></div>`;

        const description = product.description
            ? (product.description.length > 50 ? product.description.substring(0, 50) + '...' : product.description)
            : '';

        grid.append(`
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <div class="position-relative">
                        ${imageHtml}
                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">${product.category_name}</span>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title mb-1">${product.name}</h6>
                        <p class="text-muted small mb-2">${description}</p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="h5 mb-0 text-primary fw-bold">${formatCurrency(product.price)}</span>
                            <small class="text-muted"><i class="bi bi-box me-1"></i>Stok: ${product.stock}</small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <div class="d-grid gap-2">
                            <a href="product-detail.php?id=${product.id}" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-eye me-1"></i>Detail
                            </a>
                            <button class="btn btn-dark btn-sm add-to-cart" data-id="${product.id}">
                                <i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
    });

    // Re-attach event handlers
    $('.add-to-cart').off('click').on('click', function() {
        const productId = $(this).data('id');
        addToCart(productId, 1);
    });
}
