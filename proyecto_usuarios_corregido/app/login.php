<?php
session_start();
$error = '';

$redirect_destino = $_GET['redirect'] ?? $_POST['redirect'] ?? 'products.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? $_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? $_POST['contrasena'] ?? '');
    $ruta_json = __DIR__ . '/../proyecto_usuarios/data/usuarios.json'; 
    $usuario_encontrado = null;

    if (file_exists($ruta_json)) {
        $datos_json = file_get_contents($ruta_json);
        $usuarios = json_decode($datos_json, true);

        if (is_array($usuarios)) {
            foreach ($usuarios as $user) {
                $email_registrado = $user['correo'] ?? $user['email'] ?? $user['username'] ?? '';
                if ($email_registrado === $correo) {
                    $usuario_encontrado = $user;
                    break;
                }
            }
        }
    }

    if ($usuario_encontrado) {
        $password_guardada = $usuario_encontrado['password'] ?? $usuario_encontrado['contrasena'] ?? '';

        $password_valida = false;
        if (strlen($password_guardada) === 64) {
            $password_valida = (hash('sha256', $password) === $password_guardada);
        } 
        else if (!empty($password_guardada) && password_get_info($password_guardada)['algo']) {
            $password_valida = password_verify($password, $password_guardada);
        } 
        else {
            $password_valida = ($password === $password_guardada);
        }

        if ($password_valida) {
            $_SESSION['usuario'] = $usuario_encontrado['nombre'] ?? $correo;
            
            $redirect_limpio = filter_var($redirect_destino, FILTER_SANITIZE_URL);
            
            header('Location: ' . $redirect_limpio);
            exit();
        } else {
            $error = 'Contraseña incorrecta.';
        }
    } else {
        $error = 'El correo no está registrado o no se encontró el archivo de datos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Paradigmas API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

<div class="card border-0 shadow-lg rounded-4 p-4" style="width: 100%; max-width: 400px;">
    <div class="card-body">
        <div class="text-center mb-4">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 60px; height: 60px;">
                <i class="fa-solid fa-user-lock fs-4"></i>
            </div>
            <h3 class="fw-bold text-dark">Iniciar Sesión</h3>
            <p class="text-muted small">Ingresa tus credenciales de administración</p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger d-flex align-items-center py-2 mb-3 rounded-3 small" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <div><?= htmlspecialchars($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php?redirect=<?= urlencode($redirect_destino) ?>">
            
            <!-- Campo oculto que retiene exactamente la página de origen -->
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect_destino) ?>">

            <div class="mb-3">
                <label for="correo" class="form-label fw-semibold text-secondary small">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-0 py-2" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-secondary small">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-key text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-0 py-2" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm fw-semibold mb-2">
                Entrar
            </button>
            <a href="products.php" class="btn btn-outline-secondary w-100 py-2 rounded-pill small">
                <i class="fa-solid fa-arrow-left me-1"></i> Regresar
            </a>
        </form>
    </div>
</div>

</body>
</html>