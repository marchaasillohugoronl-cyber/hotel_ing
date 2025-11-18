<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM usuario WHERE username=? AND estado='activo'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario'] = $user;
            switch ($user['rol']) {
                case 'administrador': header("Location: admin/index.php"); break;
                case 'recepcionista': header("Location: recepcionista/dashboard.php"); break;
                case 'cliente': header("Location: cliente/index.php"); break;
            }
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado o inactivo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - <?= NOMBRE_HOSTAL ?></title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
<h2>Iniciar sesión</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <label>Usuario:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Ingresar</button>
</form>
<p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
</body>
</html>
