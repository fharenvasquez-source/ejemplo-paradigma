<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/PurchaseService.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $service = new PurchaseService();
        $service->eliminar($id);
    } catch (Exception $e) {
    }
} 

header('Location: purchases.php');
exit;