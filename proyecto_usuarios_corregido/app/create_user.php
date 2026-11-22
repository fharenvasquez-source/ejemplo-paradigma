<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/UserService.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');
    $rol = trim($_POST['rol'] ?? '');

    if ($nombre === '' || $correo === '' || $contrasena === '' || $rol === '') {
        $error = 'Todos los campos son obligatorios.';
    } else {
        try {
            $service = new UserService();
            $creado = $service->crear([
                'nombre' => $nombre,
                'correo' => $correo,
                'contrasena' => $contrasena,
                'rol' => $rol
            ]);

            if ($creado) {
                header('Location: index.php');
                exit;
            } else {
                $error = 'No se pudo completar el registro. Por favor, verifica los datos e inténtalo de nuevo.';
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
                    <i class="fa-solid fa-user-plus fs-5"></i>
                </div>
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1">Registrar Nuevo Usuario</h2>
                    <p class="text-muted small mb-0">Completa todos los campos requeridos por el sistema.</p>
                </div>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger d-flex align-items-center rounded-3 py-2 mb-4 shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2 fs-5"></i>
                    <div><?= e($error) ?></div>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label for="nombre" class="form-label fw-semibold text-secondary">Nombre completo</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                        <input type="text" class="form-control bg-light border-0 py-2" id="nombre" name="nombre" placeholder="Ej. Juan Pérez" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="correo" class="form-label fw-semibold text-secondary">Correo electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control bg-light border-0 py-2" id="correo" name="correo" placeholder="Ej. correo@dominio.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="contrasena" class="form-label fw-semibold text-secondary">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control bg-light border-0 py-2" id="contrasena" name="contrasena" placeholder="Contraseña de acceso" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="rol" class="form-label fw-semibold text-secondary">Rol del usuario</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-shield-halved"></i></span>
                        <select class="form-select bg-light border-0 py-2" id="rol" name="rol" required>
                            <option value="">Selecciona un rol...</option>
                            <option value="Administrador">Administrador</option>
                            <option value="Operador">Operador</option>
                            <option value="Vendedor">Vendedor</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                    <a href="index.php" class="btn btn-outline-secondary px-4 rounded-pill">
                        <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?> 