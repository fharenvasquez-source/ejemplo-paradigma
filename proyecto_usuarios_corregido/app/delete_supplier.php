<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/SupplierService.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $service = new SupplierService();
        $service->eliminar($id);
    } catch (Exception $e) {
    }
} 

header('Location: suppliers.php');
exit;