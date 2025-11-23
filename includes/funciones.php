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

    // Verificar token de sesión única
    verificarTokenUnico();
}

/**
 * Verifica que el usuario tenga el rol indicado.
 * Si no, redirige fuera de la zona restringida.
 */
function verificarRol($rolPermitido) {
    // Normalizar alias: permitir usar 'admin' como atajo
    if ($rolPermitido === 'admin') {
        $rolPermitido = 'administrador';
    }

    if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== $rolPermitido) {
        header("Location: " . URL_BASE . "login.php");
        exit();
    }

    // Verificar token de sesión única
    verificarTokenUnico();

    // Si el usuario es cliente, intentar obtener id_cliente si no está en sesión
    if ($rolPermitido === 'cliente' || ($_SESSION['usuario']['rol'] ?? '') === 'cliente') {
        if (empty($_SESSION['usuario']['id_cliente'])) {
            global $conn;
            if (isset($conn)) {
                $found = null;
                $email = $conn->real_escape_string($_SESSION['usuario']['email'] ?? '');
                if ($email !== '') {
                    $r = $conn->query("SELECT id_cliente FROM cliente WHERE email = '$email' LIMIT 1");
                    if ($r && $row = $r->fetch_assoc()) $found = (int)$row['id_cliente'];
                }
                if (!$found) {
                    $nombres = $conn->real_escape_string($_SESSION['usuario']['nombres'] ?? '');
                    $apellidos = $conn->real_escape_string($_SESSION['usuario']['apellidos'] ?? '');
                    if ($nombres !== '') {
                        $r2 = $conn->query("SELECT id_cliente FROM cliente WHERE nombres = '$nombres' AND apellidos = '$apellidos' LIMIT 1");
                        if ($r2 && $row2 = $r2->fetch_assoc()) $found = (int)$row2['id_cliente'];
                    }
                }
                if ($found) {
                    $_SESSION['usuario']['id_cliente'] = $found;
                }
            }
        }
    }
}

/**
 * Genera un token de sesión único al iniciar sesión
 */
function generarTokenSesion($usuario_id, $conn) {
    $token = bin2hex(random_bytes(32));
    $_SESSION['session_token'] = $token;

    $sql = "UPDATE usuario SET session_token = ? WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en preparar statement: " . $conn->error);
    }
    $stmt->bind_param("si", $token, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Verifica que el token de sesión coincida con el de la base de datos
 */
function verificarTokenUnico() {
    if (!isset($_SESSION['usuario']['id_usuario']) || !isset($_SESSION['session_token'])) {
        session_destroy();
        header("Location: " . URL_BASE . "login.php");
        exit();
    }

    global $conn;
    $usuario_id = $_SESSION['usuario']['id_usuario'];
    $token_actual = $_SESSION['session_token'];

    $sql = "SELECT session_token FROM usuario WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        session_destroy();
        header("Location: " . URL_BASE . "login.php");
        exit();
    }
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($token_bd);
    $stmt->fetch();
    $stmt->close();

    if ($token_bd !== $token_actual) {
        session_destroy();
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

/**
 * Cierra la sesión actual
 */
function cerrarSesion() {
    session_destroy();
    header("Location: " . URL_BASE . "login.php");
    exit();
}
