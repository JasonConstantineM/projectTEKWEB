/**
 * Auth JavaScript
 * Market Place OutFit
 */

$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        const input = $(this).siblings('input[type="password"], input[type="text"]');
        const icon = $(this).find('i');

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Form validation styling
    $('form').on('submit', function() {
        const form = $(this);
        form.find('input[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

    // Remove invalid class on input
    $('input').on('input', function() {
        $(this).removeClass('is-invalid');
    });
});

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    // This would typically check session or token
    return document.cookie.includes('PHPSESSID');
}

/**
 * Redirect to login page
 */
function redirectToLogin() {
    const currentUrl = window.location.href;
    window.location.href = '/index.php?redirect=' + encodeURIComponent(currentUrl);
}
