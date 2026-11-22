<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/SaleService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
 
$service = new SaleService();
$ventas = $service->listar();

if (!is_array($ventas)) {
    $ventas = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Consulta de Ventas</h2>
            <p class="text-muted small mb-0">Historial de transacciones y salidas de inventario.</p>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4">ID Venta</th>
                            <th>Cliente / Usuario</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ventas)): ?>
                            <?php foreach ($ventas as $venta): ?>
                                <?php if (is_array($venta)): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">#<?= e($venta['id'] ?? 'N/D') ?></td>
                                        <td class="fw-bold text-dark"><?= e($venta['cliente'] ?? $venta['usuario_nombre'] ?? 'General') ?></td>
                                        <td><?= e($venta['fecha'] ?? 'N/D') ?></td>
                                        <td class="fw-semibold text-success">$<?= number_format((float)($venta['total'] ?? 0), 2) ?></td>
                                        <td class="text-end pe-4">
                                            <?php if (isset($venta['id'])): ?>
                                                <a href="view_sale.php?id=<?= $venta['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                    <i class="fa-solid fa-eye"></i> Detalle
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No hay ventas registradas todavía o la API no devolvió datos válidos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>