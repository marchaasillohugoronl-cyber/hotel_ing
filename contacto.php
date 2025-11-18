<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $asunto = $_POST['asunto'];
    $mensaje = $_POST['mensaje'];

    $stmt = $conn->prepare("INSERT INTO contacto (nombre, email, telefono, asunto, mensaje) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $email, $telefono, $asunto, $mensaje);
    $stmt->execute();

    $msg = "Mensaje enviado correctamente. ¡Gracias por contactarnos!";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contacto - <?= NOMBRE_HOSTAL ?></title>
</head>
<body>
<header>
    <h1><?= NOMBRE_HOSTAL ?></h1>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="nosotros.php">Nosotros</a>
        <a href="contacto.php">Contacto</a>
    </nav>
</header>
<style>
    /* Reset básico */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Tipografía general */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f9f9f9;
}

/* Header */
header {
    background-color: #004080;
    color: #fff;
    padding: 20px 0;
    text-align: center;
}

header h1 {
    margin-bottom: 10px;
    font-size: 2.5rem;
}

nav a {
    color: #fff;
    text-decoration: none;
    margin: 0 15px;
    font-weight: bold;
    transition: color 0.3s;
}

nav a:hover {
    color: #ffcc00;
}

/* Main */
main {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
}

main h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    color: #004080;
    text-align: center;
}

main p {
    font-size: 1.1rem;
    margin-bottom: 20px;
    text-align: justify;
}

/* Formulario */
form {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

form input[type="text"],
form input[type="email"],
form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
}

form textarea {
    resize: vertical;
    min-height: 100px;
}

form button {
    background-color: #004080;
    color: #fff;
    padding: 10px 20px;
    border: none;
    font-size: 1rem;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

form button:hover {
    background-color: #0066cc;
}

/* Mensaje de éxito */
.success-msg {
    color: green;
    font-weight: bold;
    margin-bottom: 20px;
    text-align: center;
}

/* Footer */
footer {
    background-color: #00264d;
    color: #fff;
    text-align: center;
    padding: 20px 0;
    margin-top: 40px;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    header h1 {
        font-size: 2rem;
    }

    nav a {
        display: block;
        margin: 10px 0;
    }

    main h2 {
        font-size: 1.5rem;
    }
}

</style>
<main>
    <h2>Contáctanos</h2>
    <?php if (isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>
    <form method="POST">
        <label>Nombre:</label><br><input type="text" name="nombre" required><br>
        <label>Email:</label><br><input type="email" name="email" required><br>
        <label>Teléfono:</label><br><input type="text" name="telefono"><br>
        <label>Asunto:</label><br><input type="text" name="asunto" required><br>
        <label>Mensaje:</label><br><textarea name="mensaje" required></textarea><br><br>
        <button type="submit">Enviar mensaje</button>
    </form>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> <?= NOMBRE_HOSTAL ?>.</p>
</footer>
</body>
</html>
