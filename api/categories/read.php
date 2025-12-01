<?php
/**
 * Read Categories API
 * Market Place OutFit
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../classes/Category.php';

$category = new Category();

$id = $_GET['id'] ?? null;

if ($id) {
    $data = $category->findById((int)$id);
    if ($data) {
        echo json_encode(['success' => true, 'category' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Kategori tidak ditemukan']);
    }
} else {
    $data = $category->getAll();
    echo json_encode(['success' => true, 'categories' => $data]);
}
