<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario']) && !isset($_SESSION['user'])) {
    $pagina_actual = basename($_SERVER['PHP_SELF']);
    header('Location: login.php?redirect=' . urlencode($pagina_actual));
    exit();
}
?>

<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/SupplierService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
 
$service = new SupplierService();
$proveedores = $service->listar();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Registro de Proveedores</h2>
            <p class="text-muted small mb-0">Administra los proveedores de la cadena de suministro.</p>
        </div>
        <a href="create_supplier.php" class="btn btn-primary px-4 rounded-pill shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Proveedor
        </a>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($proveedores)): ?>
                            <?php foreach ($proveedores as $prov): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted">#<?= e($prov['id']) ?></td>
                                    <td class="fw-bold text-dark"><?= e($prov['nombre']) ?></td>
                                    <td><?= e($prov['contacto'] ?? 'N/D') ?></td>
                                    <td><?= e($prov['telefono'] ?? 'N/D') ?></td>
                                    <td class="text-end pe-4">
                                        <a href="edit_supplier.php?id=<?= $prov['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                            <i class="fa-solid fa-pen-to-square"></i> Modificar
                                        </a>
                                        <a href="delete_supplier.php?id=<?= $prov['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('¿Estás seguro de eliminar este proveedor?');">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hay proveedores registrados todavía.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>