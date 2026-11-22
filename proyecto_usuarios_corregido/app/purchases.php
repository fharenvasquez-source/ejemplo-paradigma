<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/PurchaseService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
 
$service = new PurchaseService();
$compras = $service->listar();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Registro de Compras</h2>
            <p class="text-muted small mb-0">Control de entradas de inventario y adquisiciones a proveedores.</p>
        </div>
        <a href="create_purchase.php" class="btn btn-primary px-4 rounded-pill shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Nueva Compra
        </a>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($compras)): ?>
                            <?php foreach ($compras as $comp): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted">#<?= e($comp['id']) ?></td>
                                    <td class="fw-bold text-dark"><?= e($comp['proveedor_nombre'] ?? $comp['proveedor_id'] ?? 'N/D') ?></td>
                                    <td><?= e($comp['fecha'] ?? 'N/D') ?></td>
                                    <td class="fw-semibold text-success">$<?= number_format((float)($comp['total'] ?? 0), 2) ?></td>
                                    <td class="text-end pe-4">
                                        <a href="view_purchase.php?id=<?= $comp['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                            <i class="fa-solid fa-eye me-1"></i> Detalle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No hay compras registradas todavía.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>