<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/CategoryService.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $service = new CategoryService();
        $service->eliminar($id);
    } catch (Exception $e) {
    }
} 

header('Location: categories.php');
exit;