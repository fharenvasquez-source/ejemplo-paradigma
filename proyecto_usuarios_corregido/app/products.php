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
require_once __DIR__ . '/src/ProductService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$service = new ProductService();
$productos = $service->listar();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Gestión de Productos</h2>
            <p class="text-muted small mb-0">Administra el catálogo de productos y precios.</p>
        </div>
        <a href="create_product.php" class="btn btn-primary px-4 rounded-pill shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Producto
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
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Categoría ID</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $prod): ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted">#<?= e($prod['id']) ?></td>
                                    <td class="fw-bold text-dark"><?= e($prod['nombre']) ?></td>
                                    <td>$<?= number_format((float)($prod['precio'] ?? 0), 2) ?></td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1"><?= e($prod['stock'] ?? 0) ?></span></td>
                                    <td><?= e($prod['categoria_id'] ?? 'N/D') ?></td>
                                    <td class="text-end pe-4">
                                        <a href="edit_product.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1">
                                            <i class="fa-solid fa-pen-to-square"></i> Modificar
                                        </a>
                                        <a href="delete_product.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                                            <i class="fa-solid fa-trash"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No hay productos registrados todavía.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
 
<?php require_once __DIR__ . '/includes/footer.php'; ?>