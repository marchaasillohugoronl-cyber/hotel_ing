<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= NOMBRE_HOSTAL ?></title>
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/style.css">
</head>
<body>

<style>
/* Mejoras al header */
header {
    background-color: #004080;
    color: #fff;
    padding: 20px 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
header h1 {
    font-size: 1.8rem;
    margin: 0;
}
header nav {
    display: flex;
    gap: 15px;
}
header nav a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s;
}
header nav a:hover {
    color: #ffcc00;
}
/* Mejoras adicionales al header */
header .auth-links {
    display: flex;
    gap: 10px;
}
header .auth-links a {
    background-color: #ffcc00;
    color: #004080;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s;
}
header .auth-links a:hover {
    background-color: #ffd633;
}
</style>

<header>
    <div class="container">
        <h1><?= NOMBRE_HOSTAL ?></h1>
        <nav>
            <a href="<?= URL_BASE ?>index.php">Inicio</a>
            <a href="<?= URL_BASE ?>nosotros.php">Nosotros</a>
            <a href="<?= URL_BASE ?>contacto.php">Contacto</a>

            <?php if (estaLogeado()): ?>
                <?php if (obtenerRolUsuario() === 'cliente'): ?>
                    <a href="<?= URL_BASE ?>cliente/index.php">Mi cuenta</a>
                <?php elseif (obtenerRolUsuario() === 'recepcionista'): ?>
                    <a href="<?= URL_BASE ?>recepcionista/index.php">Panel recepción</a>
                <?php elseif (obtenerRolUsuario() === 'administrador'): ?>
                    <a href="<?= URL_BASE ?>admin/index.php">Panel admin</a>
                <?php endif; ?>
                <a href="<?= URL_BASE ?>logout.php">Cerrar sesión</a>
            <?php else: ?>
                <div class="auth-links">
                    <a href="<?= URL_BASE ?>login.php">Iniciar Secion</a>
                    <a href="<?= URL_BASE ?>registro.php">Registro</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="container">
