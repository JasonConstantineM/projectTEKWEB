<?php
/**
 * Remove from Cart API
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
$input = json_decode(file_get_contents('php://input'), true);

$productId = $input['product_id'] ?? null;

if (!$productId) {
    echo json_encode(['success' => false, 'message' => 'ID produk diperlukan']);
    exit;
}

$cart = new Cart();
$result = $cart->remove($userId, (int)$productId);

// Get updated total
$result['total'] = $cart->getTotal($userId);

echo json_encode($result);
