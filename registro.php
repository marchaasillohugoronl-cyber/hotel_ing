<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $tipo_doc = $_POST['tipo_doc'];
    $num_doc = $_POST['num_doc'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Registrar cliente
    $stmt = $conn->prepare("INSERT INTO cliente (tipo_doc, num_doc, nombres, apellidos, email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $tipo_doc, $num_doc, $nombres, $apellidos, $email);
    $stmt->execute();
    $id_cliente = $stmt->insert_id;

    // Crear usuario asociado
    $stmt2 = $conn->prepare("INSERT INTO usuario (username, password, nombres, apellidos, email, rol, id_cliente) VALUES (?, ?, ?, ?, ?, 'cliente', ?)");
    $stmt2->bind_param("sssssi", $username, $password, $nombres, $apellidos, $email, $id_cliente);
    $stmt2->execute();

    header("Location: login.php?registro=ok");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - <?= NOMBRE_HOSTAL ?></title>
</head>
<body>
<h2>Registro de cliente</h2>
<form method="POST">
    <label>Nombres:</label><br><input type="text" name="nombres" required><br>
    <label>Apellidos:</label><br><input type="text" name="apellidos" required><br>
    <label>Tipo Documento:</label><br>
    <select name="tipo_doc">
        <option>DNI</option><option>CE</option><option>pasaporte</option>
    </select><br>
    <label>N° Documento:</label><br><input type="text" name="num_doc" required><br>
    <label>Email:</label><br><input type="email" name="email"><br>
    <label>Usuario:</label><br><input type="text" name="username" required><br>
    <label>Contraseña:</label><br><input type="password" name="password" required><br><br>
    <button type="submit">Registrar</button>
</form>
</body>
</html>
