<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contacto - Hostal Estrella</title>
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
    --success: #10b981;
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

/* Main Content */
main {
    position: relative;
    z-index: 10;
    max-width: 1200px;
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
    font-size: 3rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.hero p {
    font-size: 1.2rem;
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
}

/* Success Message */
.success-message {
    background: linear-gradient(135deg, var(--success), #059669);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    font-weight: 500;
    text-align: center;
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
    animation: slideIn 0.5s ease-out;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}

.success-message i {
    font-size: 1.5rem;
}

@keyframes slideIn {
    from { transform: translateX(-100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Contact Grid */
.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    margin-top: 2rem;
}

/* Contact Form */
.contact-form {
    background: white;
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
}

.contact-form h3 {
    font-size: 1.8rem;
    color: var(--primary);
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
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
    top: 1.2rem;
    color: var(--gray);
    font-size: 1.1rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid #e2e8f0;
    border-radius: 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
    font-family: 'Poppins', sans-serif;
}

.form-group textarea {
    min-height: 150px;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
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
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
}

.btn-submit:active {
    transform: translateY(-1px);
}

/* Contact Info */
.contact-info-section {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.info-card {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
}

.info-card h3 {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: rgba(99, 102, 241, 0.05);
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.info-item:hover {
    background: rgba(99, 102, 241, 0.1);
    transform: translateX(5px);
}

.info-item i {
    font-size: 1.3rem;
    color: var(--primary);
    min-width: 30px;
    margin-top: 0.2rem;
}

.info-item div strong {
    display: block;
    color: var(--dark);
    margin-bottom: 0.3rem;
}

.info-item div {
    color: var(--gray);
}

/* Social Links */
.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.social-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
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

/* Map */
.map-container {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    height: 350px;
}

.map-container iframe {
    border: 0;
}

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
    
    .contact-container {
        grid-template-columns: 1fr;
    }
    
    .social-links {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    main {
        padding: 0 1rem;
    }
    
    .card-glass,
    .contact-form,
    .info-card {
        padding: 1.5rem;
    }
    
    .hero {
        padding: 3rem 1.5rem;
    }
    
    .hero h2 {
        font-size: 2rem;
    }
}
/* Fondo de pantalla */
body {
    background: url('assets/img/fondo.png') no-repeat center center fixed;
    background-size: cover;
    position: relative;
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
        <h2><i class="fas fa-comments"></i> Contáctanos</h2>
        <p>Estamos aquí para ayudarte. Envíanos tu mensaje y nos pondremos en contacto contigo lo antes posible.</p>
    </div>

    <div class="card-glass">
        <div class="contact-container">
            <!-- Formulario de Contacto -->
            <div class="contact-form">
                <h3>
                    <i class="fas fa-paper-plane"></i>
                    Envíanos un Mensaje
                </h3>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Nombre Completo</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" name="nombre" placeholder="Tu nombre completo" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="tu@email.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Teléfono (Opcional)</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="telefono" placeholder="+51 987 654 321">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Asunto</label>
                        <div class="input-wrapper">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="asunto" placeholder="¿En qué podemos ayudarte?" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mensaje</label>
                        <div class="input-wrapper">
                            <i class="fas fa-message"></i>
                            <textarea name="mensaje" placeholder="Escribe tu mensaje aquí..." required></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Mensaje
                    </button>
                </form>
            </div>

            <!-- Información de Contacto -->
            <div class="contact-info-section">
                <div class="info-card">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        Información de Contacto
                    </h3>

                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Dirección</strong>
                            Av. Principal 100, Centro, Lima
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Teléfono</strong>
                            +51 987 654 321
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            info@hostal.com
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Horario de Atención</strong>
                            Lunes a Domingo: 24 horas
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <h3>
                        <i class="fas fa-share-alt"></i>
                        Síguenos en Redes
                    </h3>
                    
                    <div class="social-links">
                        <a href="https://facebook.com" target="_blank" class="social-btn facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://instagram.com" target="_blank" class="social-btn instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/51987654321" target="_blank" class="social-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mapa -->
    <div class="card-glass">
        <h2 style="color: var(--primary); margin-bottom: 2rem; text-align: center;">
            <i class="fas fa-map-marked-alt"></i> Encuéntranos
        </h2>
        <div class="map-container">
            <iframe width="100%" height="350" frameborder="0" style="border:0"
            src="https://maps.google.com/maps?q=Av.%20de%20la%20Calle%20100%20Lima&t=&z=13&ie=UTF8&iwloc=&output=embed" allowfullscreen></iframe>
        </div>
    </div>
</main>

<footer>
    <p>&copy; 2024 Hostal Estrella. Todos los derechos reservados.</p>
</footer>

<script>
// Mostrar mensaje de éxito si existe
<?php if (isset($msg)): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const main = document.querySelector('main');
        const successMsg = document.createElement('div');
        successMsg.className = 'success-message';
        successMsg.innerHTML = '<i class="fas fa-check-circle"></i> <?= $msg ?>';
        main.insertBefore(successMsg, main.firstChild);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            successMsg.style.animation = 'slideIn 0.5s ease-out reverse';
            setTimeout(() => successMsg.remove(), 500);
        }, 5000);
    });
<?php endif; ?>
</script>

</body>
</html>