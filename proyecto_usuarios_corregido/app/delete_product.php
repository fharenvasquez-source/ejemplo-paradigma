<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/ProductService.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $service = new ProductService();
        $service->eliminar($id);
    } catch (Exception $e) {
    }
}
 
header('Location: products.php');
exit;