<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/SupplierService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
} 

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: suppliers.php');
    exit;
}

$service = new SupplierService();
$error = '';
$proveedor = null;

try {
    $proveedor = $service->obtenerPorId($id);
} catch (Exception $e) {
    $error = 'No se pudo cargar la información del proveedor.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    if ($nombre === '') {
        $error = 'El nombre del proveedor es obligatorio.';
    } else {
        try {
            $actualizado = $service->actualizar($id, [
                'nombre' => $nombre,
                'contacto' => $contacto,
                'telefono' => $telefono,
                'correo' => $correo
            ]);

            if ($actualizado) {
                header('Location: suppliers.php');
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
                    <h2 class="h4 fw-bold text-dark mb-1">Editar Proveedor</h2>
                    <p class="text-muted small mb-0">Actualiza los datos del proveedor.</p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger d-flex align-items-center rounded-3 py-2 mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($proveedor): ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold text-secondary">Nombre de la empresa / Proveedor</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-building"></i></span>
                            <input type="text" class="form-control bg-light border-0 py-2" id="nombre" name="nombre" value="<?= e($proveedor['nombre'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contacto" class="form-label fw-semibold text-secondary">Persona de contacto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                            <input type="text" class="form-control bg-light border-0 py-2" id="contacto" name="contacto" value="<?= e($proveedor['contacto'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label fw-semibold text-secondary">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" class="form-control bg-light border-0 py-2" id="telefono" name="telefono" value="<?= e($proveedor['telefono'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label fw-semibold text-secondary">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" class="form-control bg-light border-0 py-2" id="correo" name="correo" value="<?= e($proveedor['correo'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                        <a href="suppliers.php" class="btn btn-outline-secondary px-4 rounded-pill">
                            <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No se encontró el proveedor solicitado.</p>
                    <a href="suppliers.php" class="btn btn-primary rounded-pill px-4">Volver a proveedores</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>