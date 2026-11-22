<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/ProductService.php';
require_once __DIR__ . '/src/CategoryService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}
 
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: products.php');
    exit;
}

$service = new ProductService();
$categoryService = new CategoryService();
$categorias = $categoryService->listar();

$error = '';
$producto = null;

try {
    $producto = $service->obtenerPorId($id);
} catch (Exception $e) {
    $error = 'No se pudo cargar la información del producto.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);

    if ($nombre === '' || $precio === false || $stock === false || $categoria_id === false) {
        $error = 'Todos los campos son obligatorios y válidos.';
    } else {
        try {
            $actualizado = $service->actualizar($id, [
                'nombre' => $nombre,
                'precio' => $precio,
                'stock' => $stock,
                'categoria_id' => $categoria_id
            ]);

            if ($actualizado) {
                header('Location: products.php');
                exit;
            } else {
                $error = 'La API rechazó la actualización.';
            }
        } catch (Exception $e) {
            $error = 'Error de conexión: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width: 650px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-5 bg-white">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary text-white rounded-circle p-3 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-pen-to-square fs-5"></i>
                </div>
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1">Editar Producto</h2>
                    <p class="text-muted small mb-0">Actualiza los datos del producto.</p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger d-flex align-items-center rounded-3 py-2 mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($producto): ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold text-secondary">Nombre del producto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-tag"></i></span>
                            <input type="text" class="form-control bg-light border-0 py-2" id="nombre" name="nombre" value="<?= e($producto['nombre'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label fw-semibold text-secondary">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-dollar-sign"></i></span>
                                <input type="number" step="0.01" class="form-control bg-light border-0 py-2" id="precio" name="precio" value="<?= e($producto['precio'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label fw-semibold text-secondary">Stock</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-cubes"></i></span>
                                <input type="number" class="form-control bg-light border-0 py-2" id="stock" name="stock" value="<?= e($producto['stock'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="categoria_id" class="form-label fw-semibold text-secondary">Categoría</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-folder"></i></span>
                            <select class="form-select bg-light border-0 py-2" id="categoria_id" name="categoria_id" required>
                                <?php $catActual = $producto['categoria_id'] ?? ''; ?>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $catActual == $cat['id'] ? 'selected' : '' ?>><?= e($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                        <a href="products.php" class="btn btn-outline-secondary px-4 rounded-pill">
                            <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No se encontró el producto solicitado.</p>
                    <a href="products.php" class="btn btn-primary rounded-pill px-4">Volver a productos</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>