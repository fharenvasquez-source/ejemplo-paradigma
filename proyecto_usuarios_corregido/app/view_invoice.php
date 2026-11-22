<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/InvoiceService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
 
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: invoices.php');
    exit;
}

$service = new InvoiceService();
$factura = $service->obtenerPorId($id);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="invoices.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver al listado
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-3">
            <i class="fa-solid fa-print me-1"></i> Imprimir Factura
        </button>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-5 bg-white">
            <?php if ($factura): ?>

                <div class="row border-bottom pb-4 mb-4">
                    <div class="col-sm-6">
                        <h3 class="fw-bold text-dark mb-1">TIENDA DEPORTIVA</h3>
                        <p class="text-muted small mb-0">Comprobante Electrónico Autorizado</p>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <h4 class="text-primary fw-bold mb-1">FACTURA #<?= e($factura['id']) ?></h4>
                        <span class="text-muted small d-block">Fecha: <?= e($factura['fecha'] ?? 'N/D') ?></span>
                    </div>
                </div>

                <div class="row mb-4 p-3 bg-light rounded-4">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Cliente / Razón Social:</span>
                        <strong class="text-dark"><?= e($factura['cliente'] ?? 'Consumidor Final') ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">ID de Venta Asociada:</span>
                        <strong class="text-dark">#<?= e($factura['venta_id'] ?? 'N/D') ?></strong>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-3">Detalle de Artículos</h5>
                <div class="table-responsive mb-4">
                    <table class="table align-middle">
                        <thead class="table-light text-secondary small">
                            <tr>
                                <th>Descripción</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCalc = 0;
                            $detalles = $factura['detalles'] ?? $factura['items'] ?? [];
                            if (!empty($detalles)):
                                foreach ($detalles as $det): 
                                    $precioUnitario = $det['precio_unitario'] ?? $det['precio'] ?? $det['costo'] ?? 0;
                                    $subtotal = ($det['cantidad'] ?? 0) * $precioUnitario;
                                    $totalCalc += $subtotal;
                            ?>
                                <tr>
                                    <td class="fw-semibold text-dark"><?= e($det['producto_nombre'] ?? $det['nombre'] ?? $det['descripcion'] ?? 'Artículo deportivo') ?></td>
                                    <td class="text-center"><?= e($det['cantidad']) ?></td>
                                    <td class="text-end">$<?= number_format((float)$precioUnitario, 2) ?></td>
                                    <td class="text-end fw-semibold">$<?= number_format($subtotal, 2) ?></td>
                                </tr>
                            <?php 
                                endforeach; 
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay detalles disponibles para este comprobante.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end align-items-center p-3 bg-light rounded-4">
                    <span class="text-secondary fw-semibold me-3">Total a Pagar:</span>
                    <span class="fs-4 fw-bold text-success">$<?= number_format((float)($factura['total'] ?? $totalCalc), 2) ?></span>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No se encontró la factura solicitada.</p>
                    <a href="invoices.php" class="btn btn-primary rounded-pill px-4">Volver al listado</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 

<?php require_once __DIR__ . '/includes/footer.php'; ?>
