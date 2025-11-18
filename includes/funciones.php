<?php
// includes/funciones.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que haya sesión activa. 
 * Si no, redirige a login.php
 */
function verificarLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: " . URL_BASE . "login.php");
        exit();
    }
}

/**
 * Verifica que el usuario tenga el rol indicado.
 * Si no, redirige fuera de la zona restringida.
 */
function verificarRol($rolPermitido) {
    // Normalizar alias: permitir que algunas partes del código usen 'admin' como atajo
    if ($rolPermitido === 'admin') {
        $rolPermitido = 'administrador';
    }

    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== $rolPermitido) {
        header("Location: " . URL_BASE . "login.php");
        exit();
    }
}

/**
 * Formatear moneda con símbolo.
 */
function formatoMoneda($valor) {
    return "S/ " . number_format($valor, 2);
}

/**
 * Formatear fecha en estilo latino.
 */
function formatoFecha($fecha) {
    return date("d/m/Y", strtotime($fecha));
}

/**
 * Devuelve el nombre completo del usuario en sesión.
 */
function nombreUsuario() {
    if (isset($_SESSION['usuario'])) {
        return $_SESSION['usuario']['nombres'] . ' ' . $_SESSION['usuario']['apellidos'];
    }
    return "Invitado";
}

/**
 * Verifica si hay sesión activa.
 * Devuelve true si el usuario está logeado, false en caso contrario.
 */
function estaLogeado() {
    return isset($_SESSION['usuario']);
}

/**
 * Devuelve el rol del usuario logeado o null si no hay sesión.
 */
function obtenerRolUsuario() {
    return $_SESSION['usuario']['rol'] ?? null;
}
