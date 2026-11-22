<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/InvoiceService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
 
$service = new InvoiceService();
$facturas = $service->listar();

if (!is_array($facturas)) {
    $facturas = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Generación y Consulta de Facturas</h2>
            <p class="text-muted small mb-0">Control de comprobantes fiscales y simulación de pagos.</p>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4">ID Factura</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($facturas)): ?>
                            <?php foreach ($facturas as $factura): ?>
                                <?php if (is_array($factura)): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold text-muted">#<?= e($factura['id'] ?? 'N/D') ?></td>
                                        <td><?= e($factura['fecha'] ?? 'N/D') ?></td>
                                        <td class="fw-semibold text-success">$<?= number_format((float)($factura['total'] ?? 0), 2) ?></td>
                                        <td class="text-end pe-4">
                                            <?php if (isset($factura['id'])): ?>
                                                <a href="view_invoice.php?id=<?= $factura['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                                    <i class="fa-solid fa-file-invoice-dollar me-1"></i> Ver Factura
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No hay facturas registradas todavía.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>