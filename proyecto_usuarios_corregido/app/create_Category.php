<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/src/CategoryService.php';

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre === '') {
        $error = 'El nombre de la categoría es obligatorio.';
    } else {
        try {
            $service = new CategoryService();
            $creado = $service->crear([
                'nombre' => $nombre,
                'descripcion' => $descripcion
            ]);

            if ($creado) {
                header('Location: categories.php');
                exit;
            } else {
                $error = 'La API rechazó el registro de la categoría.';
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
                    <i class="fa-solid fa-folder-plus fs-5"></i>
                </div>
                <div>
                    <h2 class="h4 fw-bold text-dark mb-1">Nueva Categoría</h2>
                    <p class="text-muted small mb-0">Agrega una nueva categoría al sistema.</p>
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
                    <label for="nombre" class="form-label fw-semibold text-secondary">Nombre de la categoría</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 text-muted ps-3"><i class="fa-solid fa-tag"></i></span>
                        <input type="text" class="form-control bg-light border-0 py-2" id="nombre" name="nombre" placeholder="Ej. Electrónica" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="descripcion" class="form-label fw-semibold text-secondary">Descripción</label>
                    <textarea class="form-control bg-light border-0 py-2" id="descripcion" name="descripcion" rows="3" placeholder="Breve descripción..."></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                    <a href="categories.php" class="btn btn-outline-secondary px-4 rounded-pill">
                        <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
<?php require_once __DIR__ . '/includes/footer.php'; ?> 