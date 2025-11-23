<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/funciones.php';

// Procesar envío del formulario
$error_message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error_message = 'Usuario y contraseña son requeridos.';
    } else {
        $sql = "SELECT id_usuario, username, password, nombres, apellidos, email, rol, estado FROM usuario WHERE username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $res = $stmt->get_result();
            $user = $res->fetch_assoc();
            $stmt->close();

            if ($user) {
                if (!empty($user['estado']) && $user['estado'] !== 'activo') {
                    $error_message = 'Cuenta desactivada. Contacte al administrador.';
                } elseif (password_verify($password, $user['password'])) {
                    // Normalizar rol a minúsculas
                    $rol = strtolower($user['rol'] ?? '');

                    // Poblar sesión de usuario
                    $_SESSION['usuario'] = [
                        'id_usuario' => (int)$user['id_usuario'],
                        'username' => $user['username'],
                        'nombres' => $user['nombres'],
                        'apellidos' => $user['apellidos'],
                        'email' => $user['email'],
                        'rol' => $rol
                    ];

                    // Generar token de sesión único en BD
                    generarTokenSesion((int)$user['id_usuario'], $conn);

                    // Redirigir según rol
                    if ($rol === 'cliente') {
                        header('Location: ' . URL_BASE . 'cliente/index.php');
                        exit();
                    } elseif ($rol === 'recepcionista') {
                        header('Location: ' . URL_BASE . 'recepcionista/index.php');
                        exit();
                    } elseif ($rol === 'administrador' || $rol === 'admin') {
                        header('Location: ' . URL_BASE . 'admin/index.php');
                        exit();
                    } else {
                        // Rol desconocido: redirigir al inicio
                        header('Location: ' . URL_BASE . 'index.php');
                        exit();
                    }
                } else {
                    $error_message = 'Credenciales inválidas.';
                }
            } else {
                $error_message = 'Usuario no encontrado.';
            }
        } else {
            $error_message = 'Error interno (DB).';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hostal Estrella - Bienvenido</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --accent: #ec4899;
    --dark: #0f172a;
    --light: #f8fafc;
    --gray: #64748b;
}

body, html {
    height: 100%;
    scroll-behavior: smooth;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow-x: hidden;
}

/* Animated Background */
.bg-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    overflow: hidden;
}

.bg-animation span {
    position: absolute;
    display: block;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.1);
    animation: float 15s infinite;
    border-radius: 50%;
}

.bg-animation span:nth-child(1) { left: 10%; animation-delay: 0s; }
.bg-animation span:nth-child(2) { left: 20%; animation-delay: 2s; width: 30px; height: 30px; }
.bg-animation span:nth-child(3) { left: 30%; animation-delay: 4s; }
.bg-animation span:nth-child(4) { left: 40%; animation-delay: 6s; width: 25px; height: 25px; }
.bg-animation span:nth-child(5) { left: 50%; animation-delay: 8s; }
.bg-animation span:nth-child(6) { left: 60%; animation-delay: 10s; width: 35px; height: 35px; }
.bg-animation span:nth-child(7) { left: 70%; animation-delay: 12s; }
.bg-animation span:nth-child(8) { left: 80%; animation-delay: 14s; width: 28px; height: 28px; }

@keyframes float {
    0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
}

/* Header Moderno */
header {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.5s ease-out;
}

@keyframes slideDown {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.header-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.logo i {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.logo h1 {
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

nav {
    display: flex;
    gap: 0.5rem;
}

nav a {
    color: var(--dark);
    text-decoration: none;
    padding: 0.7rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

nav a::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--primary);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

nav a:hover::before {
    width: 80%;
}

nav a:hover {
    color: var(--primary);
    transform: translateY(-2px);
}

.btn-login {
    background: linear-gradient(135deg, var(--primary), var(--secondary)) !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.btn-login:hover {
    box-shadow: 0 6px 25px rgba(99, 102, 241, 0.4);
    transform: translateY(-3px) !important;
}

/* Main Content */
main {
    position: relative;
    z-index: 10;
    max-width: 1400px;
    margin: 3rem auto;
    padding: 0 2rem;
}

/* Hero Section */
.hero {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 30px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 3rem;
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.hero h2 {
    font-size: 3.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.hero p {
    font-size: 1.3rem;
    color: rgba(255, 255, 255, 0.95);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.8;
}

/* Cards Glassmorphism */
.card-glass {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    padding: 3rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.5);
    animation: fadeInUp 0.8s ease-out;
    transition: all 0.3s ease;
}

.card-glass:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
}

.card-glass h2 {
    font-size: 2.2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 2rem;
    display: inline-block;
}

/* Fondo de pantalla */
body {
    background: url('assets/img/fondo.png') no-repeat center center fixed;
    background-size: cover;
    position: relative;
}
/* Login Form */
.login-form {
    max-width: 450px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    color: var(--dark);
    font-weight: 600;
    margin-bottom: 0.8rem;
    font-size: 0.95rem;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    left: 1.2rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 1.1rem;
}

.form-group input {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid #e2e8f0;
    border-radius: 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

.btn-submit {
    width: 100%;
    padding: 1.2rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
    margin-top: 1rem;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
}

.btn-submit:active {
    transform: translateY(-1px);
}

.register-link {
    text-align: center;
    margin-top: 1.5rem;
    color: var(--dark);
}

.register-link a {
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.register-link a:hover {
    color: var(--secondary);
    text-decoration: underline;
}

/* Gallery Grid */
.gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.room-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.room-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.room-card img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.room-card:hover img {
    transform: scale(1.1);
}

.room-meta {
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    font-weight: 600;
    text-align: center;
    font-size: 1.1rem;
}

/* Contact Section */
.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 3rem;
    margin-top: 2rem;
}

.map-container {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    height: 350px;
}

.map-container iframe {
    border: 0;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.contact-info h3 {
    font-size: 1.8rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(99, 102, 241, 0.05);
    border-radius: 15px;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: rgba(99, 102, 241, 0.1);
    transform: translateX(5px);
}

.info-item i {
    font-size: 1.5rem;
    color: var(--primary);
    min-width: 30px;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.social-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.social-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.social-btn.facebook { background: #1877f2; }
.social-btn.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
.social-btn.whatsapp { background: #25d366; }

/* Footer */
footer {
    background: var(--dark);
    color: white;
    text-align: center;
    padding: 2rem;
    margin-top: 4rem;
    position: relative;
    z-index: 10;
}

footer p {
    opacity: 0.9;
    font-size: 0.95rem;
}

/* Error Message */
.error-message {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    animation: shake 0.5s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

/* Responsive */
@media (max-width: 968px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .hero h2 {
        font-size: 2.5rem;
    }
    
    .gallery {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    main {
        padding: 0 1rem;
    }
    
    .card-glass {
        padding: 2rem 1.5rem;
    }
    
    .hero {
        padding: 3rem 1.5rem;
    }
    
    .hero h2 {
        font-size: 2rem;
    }
    
    .social-links {
        flex-direction: column;
    }
}
</style>
</head>
<body>

<div class="bg-animation">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
</div>

<header>
    <div class="header-content">
        <div class="logo">
            <i class="fas fa-hotel"></i>
            <h1>Hostal Estrella</h1>
        </div>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="nosotros.php"><i class="fas fa-info-circle"></i> Nosotros</a>
            <a href="contacto.php"><i class="fas fa-envelope"></i> Contacto</a>
            <a href="registro.php"><i class="fas fa-user-plus"></i> Registrarse</a>
        </nav>
    </div>
</header>

<main>
    <div class="hero">
        <h2>Bienvenido a tu Hogar</h2>
        <p>Experimenta el confort y la calidez que mereces. Tu descanso es nuestra prioridad en el corazón de la ciudad.</p>
    </div>

    <div class="card-glass">
        <h2><i class="fas fa-key"></i> Iniciar Sesión</h2>
        
        <div class="login-form">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Usuario</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Ingresa tu usuario" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Ingresa tu contraseña" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
            </form>
            
            <div class="register-link">
                ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
        </div>
    </div>

    <div class="card-glass">
        <h2><i class="fas fa-bed"></i> Nuestras Habitaciones</h2>
        <p style="color: var(--gray); margin-bottom: 2rem;">Descubre espacios diseñados para tu máximo confort</p>
        
        <div class="gallery">
            <div class="room-card">
                <img src="assets/img/habitaciones/ecomico.jpg" alt="Económica">
                <div class="room-meta">
                    <i class="fas fa-star"></i> Económica
                </div>
            </div>
            <div class="room-card">
                <img src="assets/img/habitaciones/simple.jpg" alt="Simple">
                <div class="room-meta">
                    <i class="fas fa-star"></i> Simple
                </div>
            </div>
            <div class="room-card">
                <img src="assets/img/habitaciones/double.jpg" alt="Doble">
                <div class="room-meta">
                    <i class="fas fa-star"></i> Doble
                </div>
            </div>
            <div class="room-card">
                <img src="assets/img/habitaciones/suite.jpg" alt="Suite">
                <div class="room-meta">
                    <i class="fas fa-crown"></i> Suite Premium
                </div>
            </div>
            <div class="room-card">
                <img src="assets/img/habitaciones/imagen_matrimonial.jpg" alt="Matrimonial">
                <div class="room-meta">
                    <i class="fas fa-heart"></i> Matrimonial
                </div>
            </div>
        </div>
    </div>

    <div class="card-glass">
        <h2><i class="fas fa-map-marker-alt"></i> Ubicación y Contacto</h2>
        
        <div class="contact-grid">
            <div class="map-container">
                <iframe width="100%" height="350" frameborder="0" style="border:0"
                src="https://maps.google.com/maps?q=Av.%20de%20la%20Calle%20100%20Lima&t=&z=13&ie=UTF8&iwloc=&output=embed" allowfullscreen></iframe>
            </div>

            <div class="contact-info">
                <h3>Información de Contacto</h3>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Dirección</strong><br>
                        Av. Principal 100, Centro, Lima
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Teléfono</strong><br>
                        +51 987 654 321
                    </div>
                </div>

                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>Email</strong><br>
                        info@hostal.com
                    </div>
                </div>

                <div>
                    <h4 style="margin-bottom: 1rem; color: var(--primary);">
                        <i class="fas fa-share-alt"></i> Síguenos
                    </h4>
                    <div class="social-links">
                        <a href="https://facebook.com" target="_blank" class="social-btn facebook">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="https://instagram.com" target="_blank" class="social-btn instagram">
                            <i class="fab fa-instagram"></i> Instagram
                        </a>
                        <a href="https://wa.me/51987654321" target="_blank" class="social-btn whatsapp">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 Hostal Estrella. Todos los derechos reservados.</p>
</footer>

</body>
</html>