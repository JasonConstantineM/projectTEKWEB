<?php
/**
 * Get Cart API
 * Market Place OutFit
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Cart.php';

// Check login
if (!User::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
    exit;
}

$userId = User::getCurrentUserId();

$cart = new Cart();
$items = $cart->getByUser($userId);
$total = $cart->getTotal($userId);
$count = $cart->countItems($userId);

echo json_encode([
    'success' => true,
    'items' => $items,
    'total' => $total,
    'count' => $count
]);
