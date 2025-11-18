<?php
// ==========================================
// CONFIGURACIÓN GENERAL
// ==========================================

// Parámetros de conexión
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hostal_dulce_descanso_01";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8mb4");

// Iniciar sesión global
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Constantes globales
define("URL_BASE", "http://localhost/hostal/");
define("NOMBRE_HOSTAL", "Hostal El Dulce Descanso");
?>
