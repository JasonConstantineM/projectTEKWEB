<?php
/**
 * Logout API
 * Market Place OutFit
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../classes/User.php';

$user = new User();
$result = $user->logout();

echo json_encode($result);
