<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paradigma API - Portal de Administración</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="/../assets/img/logo.png">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

<!-- Navbar Superior con Menú Desplegable -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <i class="fa-solid fa-code text-primary fs-4 me-2"></i> Paradigmas API
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item me-2">
                    <a class="nav-link px-3" href="index.php">
                        <i class="fa-solid fa-house me-1"></i> Inicio
                    </a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link px-3 text-warning" href="/../tienda-js/index.html">
                        <i class="fa-solid fa-store me-1"></i> Ver Tienda (Vista Cliente)
                    </a>
                </li>
                
                <li class="nav-item dropdown me-2">
                    <a class="nav-link dropdown-toggle px-3" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-bars-progress me-1"></i> Administración
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow rounded-3 border-0 py-2">
                        <li><a class="dropdown-item py-2" href="index.php"><i class="fa-solid fa-users me-2 text-primary"></i> Gestión de Usuarios</a></li>
                        <li><a class="dropdown-item py-2" href="categories.php"><i class="fa-solid fa-tags me-2 text-primary"></i> Categorías</a></li>
                        <li><a class="dropdown-item py-2" href="products.php"><i class="fa-solid fa-box-archive me-2 text-primary"></i> Productos</a></li>
                        <li><a class="dropdown-item py-2" href="suppliers.php"><i class="fa-solid fa-truck me-2 text-primary"></i> Proveedores</a></li>
                        <li><hr class="dropdown-divider bg-secondary"></li>
                        <li><a class="dropdown-item py-2" href="purchases.php"><i class="fa-solid fa-cart-shopping me-2 text-success"></i> Compras</a></li>
                        <li><a class="dropdown-item py-2" href="inventory.php"><i class="fa-solid fa-warehouse me-2 text-success"></i> Inventario</a></li>
                        <li><a class="dropdown-item py-2" href="sales.php"><i class="fa-solid fa-cash-register me-2 text-success"></i> Ventas</a></li>
                        <li><a class="dropdown-item py-2" href="invoices.php"><i class="fa-solid fa-file-invoice me-2 text-success"></i> Facturas</a></li>
                    </ul>
                </li>

                <?php if (isset($_SESSION['usuario']) || isset($_SESSION['user'])): ?>
                    <?php $usuario_actual = $_SESSION['usuario'] ?? $_SESSION['user']; ?>
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle px-3 text-light d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-circle me-1 text-info fs-5"></i> <?= htmlspecialchars($usuario_actual) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow rounded-3 border-0 py-2">
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="logout.php">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-light btn-sm px-3 rounded-pill" href="login.php">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Iniciar Sesión
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>