<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/InventoryService.php';

$service = new InventoryService();
$inventario = $service->listar();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Consulta de Inventario</h2>
            <p class="text-muted small mb-0">Estado actual de existencias y stock de productos.</p>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="ps-4">ID Producto</th>
                            <th>Nombre del Producto</th>
                            <th>Categoría ID</th>
                            <th>Stock Actual</th>
                            <th class="text-end pe-4">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inventario)): ?>
                            <?php foreach ($inventario as $item): ?>
                                <?php 
                                    $stock = (int)($item['stock'] ?? $item['cantidad'] ?? 0);
                                    $badgeClass = $stock > 5 ? 'bg-success' : ($stock > 0 ? 'bg-warning text-dark' : 'bg-danger');
                                    $estadoTexto = $stock > 5 ? 'Disponible' : ($stock > 0 ? 'Stock Bajo' : 'Agotado');
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted">#<?= e($item['id'] ?? $item['producto_id'] ?? '') ?></td>
                                    <td class="fw-bold text-dark"><?= e($item['nombre'] ?? $item['producto_nombre'] ?? 'N/D') ?></td>
                                    <td>Cat. #<?= e($item['categoria_id'] ?? 'N/D') ?></td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1"><?= $stock ?></span></td>
                                    <td class="text-end pe-4">
                                        <span class="badge <?= $badgeClass ?> px-3 py-1"><?= $estadoTexto ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No hay registros de inventario disponibles.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
