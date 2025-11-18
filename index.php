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
                case 'recepcionista': header("Location: recepcionista/index.php"); break;
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


<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= NOMBRE_HOSTAL ?> - Bienvenido</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">

    <!-- Font Awesome para íconos profesionales -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

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
}


/* Header */
header {
    background-color: #161616ff;
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
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

main section h2 {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #004080;
}

main section p {
    font-size: 1.1rem;
    margin-bottom: 20px;
}

main section img {
    border-radius: 8px;
}

/* Galería de habitaciones */
.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
    margin-top: 20px;
}
.gallery .card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    overflow: hidden;
}
.gallery .card img { width:100%; height:160px; object-fit:cover; display:block; }
.gallery .card .meta { padding:10px; font-size:0.95rem; }

/* Contacto y ubicación */
.contact-row {
    display:flex;
    gap:20px;
    align-items:flex-start;
    margin-top:30px;
}

.contact-row .map { flex:1; min-height:220px; }

.contact-row .info {
    width:320px;
    background:#fff;
    padding:15px;
    border-radius:8px;
    box-shadow:0 2px 6px rgba(0,0,0,0.06);
}

/* Redes sociales */
.socials h4 {
    margin-bottom: 8px;
    color:#004080;
}

.socials a {
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:10px;
    color:#004080;
    text-decoration:none;
    font-weight:600;
    font-size:1rem;
    transition:0.3s ease;
}

.socials a i {
    font-size:1.3rem;
}

.socials a.facebook:hover { color:#1877f2; }
.socials a.instagram:hover { color:#e1306c; }
.socials a.whatsapp:hover { color:#25d366; }

/* Responsive */
@media (max-width:900px) {
    .contact-row { flex-direction:column; }
    .contact-row .info { width:100%; }
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
</style>

<body class="fondo_hgeneral">
<header>
    <h1><?= NOMBRE_HOSTAL ?></h1>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="nosotros.php">Nosotros</a>
        <a href="contacto.php">Contacto</a>
        <a href="login.php">Iniciar Secion</a>
        <a href="registro.php">Registrarse como cliente</a>
    </nav>
</header>

<main>
    <section>
        <h2>Bienvenido a <?= NOMBRE_HOSTAL ?></h2>
        <p>Tu descanso es nuestra prioridad. Disfruta de habitaciones cómodas y un servicio cálido en el corazón de la ciudad.</p>
    </section>
            <h1><body>
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
            </body></h1>
    <section>
        <h2>Nuestras Habitaciones</h2>
        <p>Conoce algunas de nuestras habitaciones. Reserva la que más se adapte a tu comodidad.</p>

        <div class="gallery">
            <div class="card">
                <img src="assets/img/habitaciones/ecomico.jpg" alt="Habitación Económica">
                <div class="meta">Económica — Cama sencilla</div>
            </div>
            <div class="card">
                <img src="assets/img/habitaciones/simple.jpg" alt="Habitación Simple">
                <div class="meta">Simple — Baño privado y TV</div>
            </div>
            <div class="card">
                <img src="assets/img/habitaciones/double.jpg" alt="Habitación Doble">
                <div class="meta">Doble — Dos camas</div>
            </div>
            <div class="card">
                <img src="assets/img/habitaciones/suite.jpg" alt="Suite">
                <div class="meta">Suite — Cama King y jacuzzi</div>
            </div>
            <div class="card">
                <img src="assets/img/habitaciones/imagen_matrimonial.jpg" alt="Habitación Matrimonial">
                <div class="meta">Matrimonial — Cama Queen</div>
            </div>
        </div>
    </section>

    <section>
        <h2>Ubicación y Contacto</h2>
        <div class="contact-row">
            <div class="map">
                <iframe width="100%" height="260" frameborder="0" style="border:0"
                        src="https://maps.google.com/maps?q=Av.%20de%20la%20Calle%20100%20Lima&t=&z=13&ie=UTF8&iwloc=&output=embed" allowfullscreen>
                </iframe>
            </div>

            <div class="info">
                <h3>Contacto</h3>
                <p><strong>Dirección:</strong> Av. Principal 100, Centro, Lima</p>
                <p><strong>Teléfono:</strong> +51 987 654 321</p>
                <p><strong>Email:</strong> info@hostal.com</p>

                <!-- Redes sociales -->
                <div class="socials">
                    <h4>Síguenos</h4>

                    <a href="https://www.facebook.com/" target="_blank" class="facebook">
                        <i class="fa-brands fa-facebook"></i> Facebook
                    </a>

                    <a href="https://www.instagram.com/" target="_blank" class="instagram">
                        <i class="fa-brands fa-instagram"></i> Instagram
                    </a>

                    <a href="https://wa.me/51987654321" target="_blank" class="whatsapp">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp
                    </a>
                </div>

            </div>
        </div>
    </section>

</main>

<footer>
    <p>&copy; <?= date('Y') ?> <?= NOMBRE_HOSTAL ?>. Todos los derechos reservados.</p>
</footer>

</body>
</html>
