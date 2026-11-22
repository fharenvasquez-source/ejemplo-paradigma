<?php

if (!function_exists('e')) {
    function e($valor) {
        return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
    }
}

require_once __DIR__ . '/../config.php';

if (file_exists(__DIR__ . '/ApiException.php')) {
    require_once __DIR__ . '/ApiException.php';
}

spl_autoload_register(function ($clase) {
    $rutaArchivo = dirname(__DIR__) . '/src/' . $clase . '.php';
    if (file_exists($rutaArchivo)) {
        require_once $rutaArchivo;
    }
});