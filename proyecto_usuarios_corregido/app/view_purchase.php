<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/PurchaseService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: purchases.php');
    exit;
}

$service = new PurchaseService();
$compra = $service->obtenerPorId($id);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width: 750px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-5 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-file-invoice fs-5"></i>
                    </div>
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">Detalle de Compra #<?= e($id) ?></h2>
                        <p class="text-muted small mb-0">Información general y productos adquiridos.</p>
                    </div>
                </div>
                <a href="purchases.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver
                </a>
            </div>

            <?php if ($compra): ?>
                <div class="row mb-4 p-3 bg-light rounded-4">
                    <div class="col-sm-6 mb-2 mb-sm-0">
                        <span class="text-muted small d-block">Proveedor</span>
                        <strong class="text-dark"><?= e($compra['proveedor_nombre'] ?? $compra['proveedor_id'] ?? 'N/D') ?></strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Fecha de Registro</span>
                        <strong class="text-dark"><?= e($compra['fecha'] ?? 'N/D') ?></strong>
                    </div>
                </div>

                <h5 class="fw-bold text-dark mb-3">Productos en la Orden</h5>
                <div class="table-responsive mb-4">
                    <table class="table align-middle">
                        <thead class="table-light text-secondary small">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Costo Unitario</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalCalc = 0;
                            $detalles = $compra['items'] ?? $compra['detalles'] ?? [];
                            if (!empty($detalles)):
                                foreach ($detalles as $det): 
                                    $subtotal = ($det['cantidad'] ?? 0) * ($det['costo'] ?? $det['precio_unitario'] ?? 0);
                                    $totalCalc += $subtotal;
                            ?>
                                <tr>
                                    <td class="fw-semibold text-dark"><?= e($det['nombre'] ?? $det['producto_nombre'] ?? $det['producto_id']) ?></td>
                                    <td class="text-center"><?= e($det['cantidad']) ?></td>
                                    <td class="text-end">$<?= number_format((float)($det['costo'] ?? $det['precio_unitario'] ?? 0), 2) ?></td>
                                    <td class="text-end fw-semibold">$<?= number_format($subtotal, 2) ?></td>
                                </tr>
                            <?php 
                                endforeach; 
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay detalles registrados para esta compra.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end align-items-center p-3 bg-light rounded-4">
                    <span class="text-secondary fw-semibold me-3">Total General:</span>
                    <span class="fs-4 fw-bold text-success">$<?= number_format((float)($compra['total'] ?? $totalCalc), 2) ?></span>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No se encontró la información de la compra solicitada.</p>
                    <a href="purchases.php" class="btn btn-primary rounded-pill px-4">Volver al listado</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
