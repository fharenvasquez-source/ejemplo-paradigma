<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/UserService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
} 

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$service = new UserService();
$error = '';
$usuario = null;

try {
    $usuario = $service->obtenerPorId($id);
} catch (Exception $e) {
    $error = 'No se pudo cargar la información del usuario.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    if ($nombre === '' || $correo === '' || $rol === '') {
        $error = 'Todos los campos son obligatorios.';
    } else {
        try {
            $datosActualizados = [
                'nombre' => $nombre,
                'correo' => $correo,
                'rol' => $rol
            ];
            
            if (!empty($_POST['contrasena'])) {
                $datosActualizados['contrasena'] = trim($_POST['contrasena']);
            }

            $actualizado = $service->actualizar($id, $datosActualizados);

            if ($actualizado) {
                header('Location: index.php');
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
                    <h2 class="h4 fw-bold text-dark mb-1">Editar Usuario</h2>
                    <p class="text-muted small mb-0">Actualiza los datos del sistema.</p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger d-flex align-items-center rounded-3 py-2 mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($usuario): ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold text-secondary">Nombre completo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                            <input type="text" class="form-control bg-light border-0 py-2" id="nombre" name="nombre" value="<?= e($usuario['nombre'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="correo" class="form-label fw-semibold text-secondary">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" class="form-control bg-light border-0 py-2" id="correo" name="correo" value="<?= e($usuario['correo'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contrasena" class="form-label fw-semibold text-secondary">Nueva Contraseña <span class="text-muted fw-normal small">(Dejar en blanco para mantener la actual)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control bg-light border-0 py-2" id="contrasena" name="contrasena" placeholder="Nueva contraseña opcional">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="rol" class="form-label fw-semibold text-secondary">Rol del usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-shield-halved"></i></span>
                            <select class="form-select bg-light border-0 py-2" id="rol" name="rol" required>
                                <?php $rolActual = $usuario['rol'] ?? ''; ?>
                                <option value="Administrador" <?= $rolActual === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                                <option value="Operador" <?= $rolActual === 'Operador' ? 'selected' : '' ?>>Operador</option>
                                <option value="Vendedor" <?= $rolActual === 'Vendedor' ? 'selected' : '' ?>>Vendedor</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                        <a href="index.php" class="btn btn-outline-secondary px-4 rounded-pill">
                            <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No se encontró el usuario solicitado.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4">Volver al inicio</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>