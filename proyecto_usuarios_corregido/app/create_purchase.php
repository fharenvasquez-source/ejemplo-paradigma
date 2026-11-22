<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/PurchaseService.php';
require_once __DIR__ . '/src/SupplierService.php';
require_once __DIR__ . '/src/ProductService.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
} 

$error = '';
$supplierService = new SupplierService();
$productService = new ProductService();

$proveedores = $supplierService->listar();
$productos = $productService->listar();

if (!isset($_SESSION['purchase_items']) || empty($_SESSION['purchase_items'])) {
    $_SESSION['purchase_items'] = [
        ['producto_id' => '', 'cantidad' => 1, 'costo' => '']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['detalles']) && is_array($_POST['detalles'])) {
        $_SESSION['purchase_items'] = $_POST['detalles'];
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'add_row') {
        $_SESSION['purchase_items'][] = ['producto_id' => '', 'cantidad' => 1, 'costo' => ''];
        header('Location: create_purchase.php');
        exit;
    }

    if ($action === 'remove_row') {
        $indexToRemove = (int)($_POST['remove_index'] ?? -1);
        if (isset($_SESSION['purchase_items'][$indexToRemove])) {
            unset($_SESSION['purchase_items'][$indexToRemove]);
            $_SESSION['purchase_items'] = array_values($_SESSION['purchase_items']);
        }
        header('Location: create_purchase.php');
        exit;
    }

    if ($action === 'save_purchase') {
        $proveedor_id = filter_input(INPUT_POST, 'proveedor_id', FILTER_VALIDATE_INT);
        $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));
        
        $detalles_raw = $_SESSION['purchase_items'];
        $items = [];

        foreach ($detalles_raw as $item) {
            if (!empty($item['producto_id']) && !empty($item['cantidad']) && !empty($item['costo'])) {
                $items[] = [
                    'producto_id' => (int)$item['producto_id'],
                    'cantidad' => (int)$item['cantidad'],
                    'precio_unitario' => (float)$item['costo']
                ];
            }
        }

        if (!$proveedor_id) {
            $error = 'Debe seleccionar un proveedor válido.';
        } elseif (empty($items)) {
            $error = 'Debe agregar al menos un producto válido con cantidad y costo.';
        } else {
            try {
                $service = new PurchaseService();
                $creado = $service->crear([
                    'proveedor_id' => $proveedor_id,
                    'fecha' => $fecha . 'T00:00:00',
                    'items' => $items
                ]);

                if ($creado) {
                    unset($_SESSION['purchase_items']);
                    header('Location: purchases.php');
                    exit;
                } else {
                    $error = 'No se pudo registrar la compra. Verifica la información ingresada.';
                }
            } catch (Exception $e) {
                $error = 'Error de conexión: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5" style="max-width: 800px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="card-body p-5 bg-white">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary text-white rounded-circle p-3 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="fa-solid fa-cart-shopping fs-5"></i>
                </div>
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1">Nueva Compra / Carga de Inventario</h2>
                    <p class="text-muted small mb-0">Registra una entrada de mercancía al sistema.</p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger d-flex align-items-center rounded-3 py-2 mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="proveedor_id" class="form-label fw-semibold text-secondary">Proveedor</label>
                        <select class="form-select bg-light border-0 py-2" id="proveedor_id" name="proveedor_id">
                            <option value="">Seleccione un proveedor...</option>
                            <?php foreach ($proveedores as $p): ?>
                                <option value="<?= e($p['id']) ?>"><?= e($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha" class="form-label fw-semibold text-secondary">Fecha de Compra</label>
                        <input type="date" class="form-control bg-light border-0 py-2" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <hr class="text-muted my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Productos Adquiridos</h5>
                    <button type="submit" name="action" value="add_row" class="btn btn-sm btn-outline-primary rounded-pill px-3" formnovalidate>
                        <i class="fa-solid fa-plus me-1"></i> Agregar Línea
                    </button>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table align-middle">
                        <thead class="table-light text-secondary small">
                            <tr>
                                <th style="width: 45%;">Producto</th>
                                <th style="width: 20%;">Cantidad</th>
                                <th style="width: 25%;">Costo Unitario</th>
                                <th style="width: 10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['purchase_items'] as $index => $item): ?>
                                <tr>
                                    <td>
                                        <select class="form-select bg-light border-0" name="detalles[<?= $index ?>][producto_id]">
                                            <option value="">Seleccione...</option>
                                            <?php foreach ($productos as $prod): ?>
                                                <option value="<?= e($prod['id']) ?>" <?= ($item['producto_id'] == $prod['id']) ? 'selected' : '' ?>>
                                                    <?= e($prod['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control bg-light border-0" name="detalles[<?= $index ?>][cantidad]" min="1" value="<?= e($item['cantidad']) ?>">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control bg-light border-0" name="detalles[<?= $index ?>][costo]" min="0" placeholder="0.00" value="<?= e($item['costo']) ?>">
                                    </td>
                                    <td class="text-center">
                                        <?php if (count($_SESSION['purchase_items']) > 1): ?>
                                            <button type="submit" name="action" value="remove_row" onclick="this.form.remove_index.value='<?= $index ?>';" class="btn btn-sm text-danger" formnovalidate>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <input type="hidden" name="remove_index" value="-1">

                <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                    <a href="purchases.php" class="btn btn-outline-secondary px-4 rounded-pill">
                        <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                    </a>
                    <button type="submit" name="action" value="save_purchase" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Compra
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>