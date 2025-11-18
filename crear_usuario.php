<?php
// Incluir configuración
require_once 'config.php';

// Verificar que se recibieron los datos por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recibir y limpiar datos
    $username  = $conn->real_escape_string($_POST['username']);
    $nombres   = $conn->real_escape_string($_POST['nombres']);
    $apellidos = $conn->real_escape_string($_POST['apellidos']);
    $email     = $conn->real_escape_string($_POST['email']);
    $password  = $_POST['password']; // se va a hashear
    $rol       = $conn->real_escape_string($_POST['rol']); // administrador, recepcionista, cliente

    // Validar campos obligatorios
    if (empty($username) || empty($nombres) || empty($apellidos) || empty($email) || empty($password) || empty($rol)) {
        die("Todos los campos son obligatorios.");
    }

    // Hashear la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en la base de datos
    $sql = "INSERT INTO usuario (username, password, nombres, apellidos, email, rol) 
            VALUES ('$username', '$password_hash', '$nombres', '$apellidos', '$email', '$rol')";

    if ($conn->query($sql) === TRUE) {
        echo "Usuario creado correctamente.";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
    exit;
}
?>

<!-- Formulario simple para crear usuarios -->
<form method="post">
    <label>Usuario: <input type="text" name="username" required></label><br><br>
    <label>Nombres: <input type="text" name="nombres" required></label><br><br>
    <label>Apellidos: <input type="text" name="apellidos" required></label><br><br>
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Contraseña: <input type="password" name="password" required></label><br><br>
    <label>Rol:
        <select name="rol" required>
            <option value="administrador">Administrador</option>
            <option value="recepcionista">Recepcionista</option>
            <option value="cliente">Cliente</option>
        </select>
    </label><br><br>
    <button type="submit">Crear Usuario</button>
</form>
