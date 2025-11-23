<?php
require_once '../config.php';
require_once '../includes/funciones.php'; 

// Verificar si hay sesión activa
if (!estaLogeado()) {
    header("Location: ../login.php");
    exit();
}

// Verificar login y token único
verificarLogin();

// Verificar rol 'cliente'
verificarRol('cliente');

?>

<style>
/* ============================================
   RESET + BODY STYLES
=============================================== */
* { margin: 0; padding: 0; box-sizing: border-box; }
html, body {
    height: 100%;
    font-family: "Poppins", sans-serif;
    background: url('../assets/img/fondo_hgeneral.png') no-repeat center center fixed;
    background-size: cover;
    backdrop-filter: blur(12px);
    display: flex;
    flex-direction: column;
}

/* ============================================
   FULLSCREEN CONTAINER
=============================================== */
.dashboard-container {
    flex: 1; /* Ocupa todo el espacio disponible */
    display: flex;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(8px);
}

/* ============================================
   SIDEBAR
=============================================== */
.sidebar {
    width: 260px; min-width: 260px;
    display: flex; flex-direction: column; padding: 30px;
    background: rgba(0, 0, 50, 0.45);
    border-right: 1px solid rgba(255,255,255,.15);
    backdrop-filter: blur(12px);
    box-shadow: 0 0 25px rgba(0, 132, 255, 0.25);
    animation: slideLeft 0.8s ease-out;
}
@keyframes slideLeft { from { transform: translateX(-80px); opacity:0; } to { transform: translateX(0); opacity:1; } }
.sidebar .logo img { width: 150px; filter: drop-shadow(0 0 6px rgba(255,255,255,.3)); margin-bottom: 35px; }
.sidebar .menu { list-style: none; flex: 1; }
.sidebar .menu li { margin-bottom: 18px; }
.sidebar .menu a {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 18px; text-decoration: none; color: white; font-weight: 500;
    border-radius: 10px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.12);
    transition: 0.3s;
}
.sidebar .menu a:hover {
    background: rgba(0,136,255,0.4);
    box-shadow: 0 0 12px rgba(0,136,255,0.7);
    transform: translateX(6px);
}
.btn-logout {
    background: rgba(255,20,20,0.35);
    border: 1px solid rgba(255,40,40,0.7);
    padding: 12px; width: 100%;
    color: white; font-weight: bold; border-radius: 10px;
    transition: 0.3s;
}
.btn-logout:hover {
    background: rgba(255,20,20,0.6);
    box-shadow: 0 0 12px rgba(255,40,40,1);
}

/* ============================================
   MAIN CONTENT
=============================================== */
.main-content {
    flex: 1; /* Ocupa el espacio restante */
    padding: 40px;
    overflow-y: auto;
    animation: fadeIn 1s ease-out;
}
@keyframes fadeIn { from { opacity:0; transform: translateY(40px); } to { opacity:1; transform: translateY(0); } }

.welcome-card {
    background: rgba(255,255,255,0.15); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.25);
    padding: 25px 30px; border-radius: 18px; color: white; margin-bottom: 35px;
    box-shadow: 0 0 20px rgba(255,255,255,0.08);
}
.welcome-card h2 { font-size: 2rem; font-weight:600; color:#fff; }

.dashboard-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 25px;
}
.dashboard-card {
    background: rgba(0,119,255,0.25); backdrop-filter: blur(10px);
    padding:30px 20px; border-radius:15px; text-align:center; color:#fff;
    font-size:1.3rem; font-weight:500; text-decoration:none;
    border:1px solid rgba(255,255,255,0.18);
    box-shadow:0 0 20px rgba(0,136,255,0.3);
    transition:0.4s; transform-style:preserve-3d;
}
.dashboard-card:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow:0 0 25px rgba(0,136,255,0.7);
    background: rgba(0,136,255,0.45);
}

/* ============================================
   FOOTER
=============================================== */
footer {
    background: rgba(0,0,50,0.45);
    color: #fff;
    text-align: center;
    padding: 15px;
    border-top: 1px solid rgba(255,255,255,.2);
}

/* ============================================
   RESPONSIVE
=============================================== */
@media (max-width:850px) {
    .dashboard-container { flex-direction: column; }
    .sidebar { width:100%; flex-direction:row; justify-content:space-between; overflow-x:auto; min-width:100%; }
    .menu { display:flex; flex-direction:row; gap:10px; }
    .menu li { margin:0; }
    .main-content { padding:20px; }
}
</style>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <nav class="sidebar">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Logo" />
        </div>

        <ul class="menu">
            <li><a href="mis-reservas.php">📅 Mis Reservas</a></li>
            <li><a href="nueva-reserva.php">✨ Nueva Reserva</a></li>
            <li><a href="solicitar-servicio.php">🛎️ Servicios</a></li>
            <li><a href="mis-pagos.php">💳 Mis Pagos</a></li>
        </ul>

        <form method="POST" action="../logout.php" class="logout-form">
            <button type="submit" class="btn-logout">🚪 Cerrar sesión</button>
        </form>
    </nav>

    <!-- MAIN CONTENT -->
    <section class="main-content">
        <div class="welcome-card">
            <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario']['nombres'] ?? 'Usuario') ?> 👋</h2>
            <p>Gestiona tus reservas, pagos y servicios.</p>
        </div>

        <div class="dashboard-grid">
            <a href="mis-reservas.php" class="dashboard-card">📅 Mis Reservas</a>
            <a href="nueva-reserva.php" class="dashboard-card">➕ Nueva Reserva</a>
            <a href="solicitar-servicio.php" class="dashboard-card">🛎️ Servicios</a>
            <a href="mis-pagos.php" class="dashboard-card">💳 Mis Pagos</a>
        </div>
    </section>

</div>

<!-- FOOTER FIJO ABAJO -->
    
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h4><i class="fas fa-hotel"></i> <?= NOMBRE_HOSTAL ?></h4>
            <p>Tu comodidad es nuestra prioridad. Ofrecemos las mejores habitaciones con servicios de calidad.</p>
            <div class="social-links">
                <a href="https://facebook.com" target="_blank" aria-label="Facebook">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="https://instagram.com" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://twitter.com" target="_blank" aria-label="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://wa.me/51987654321" target="_blank" aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
        
        <div class="footer-section">
            <h4><i class="fas fa-link"></i> Enlaces Rápidos</h4>
            <ul>
                <li><a href="<?= URL_BASE ?>index.php">Inicio</a></li>
                <li><a href="<?= URL_BASE ?>nosotros.php">Nosotros</a></li>
                <li><a href="<?= URL_BASE ?>contacto.php">Contacto</a></li>
                <li><a href="<?= URL_BASE ?>login.php">Iniciar Sesión</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4><i class="fas fa-map-marker-alt"></i> Contacto</h4>
            <ul class="contact-info">
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Av. Principal 100, Lima</span>
                </li>
                <li>
                    <i class="fas fa-phone"></i>
                    <span>+51 987 654 321</span>
                </li>
                <li>
                    <i class="fas fa-envelope"></i>
                    <span>info@hostal.com</span>
                </li>
                <li>
                    <i class="fas fa-clock"></i>
                    <span>24/7 Atención</span>
                </li>
            </ul>
        </div>
        

    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= NOMBRE_HOSTAL ?> — Todos los derechos reservados.</p>
            <div class="footer-links">
                <a href="#">Términos y Condiciones</a>
                <span>|</span>
                <a href="#">Política de Privacidad</a>
                <span>|</span>
                <a href="#">Libro de Reclamaciones</a>
            </div>
        </div>
    </div>
</footer>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
    --primary: #6366f1;
    --secondary: #8b5cf6;
    --dark: #0f172a;
    --footer-bg: #1e293b;
    --footer-text: #cbd5e1;
}

/* Footer Principal */
footer {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: var(--footer-text);
    padding: 4rem 0 0;
    margin-top: 4rem;
    position: relative;
    overflow: hidden;
}

footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
}

.footer-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem 3rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 3rem;
}

.footer-section h4 {
    color: white;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.footer-section h4 i {
    color: var(--primary);
}

.footer-section p {
    line-height: 1.8;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

/* Social Links */
.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.social-links a {
    width: 45px;
    height: 45px;
    background: rgba(99, 102, 241, 0.1);
    border: 2px solid rgba(99, 102, 241, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.social-links a:hover {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
}

/* Enlaces del Footer */
.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 0.8rem;
}

.footer-section ul li a {
    color: var(--footer-text);
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-section ul li a:hover {
    color: white;
    transform: translateX(5px);
}

.footer-section ul li a::before {
    content: '→';
    color: var(--primary);
    transition: transform 0.3s ease;
}

.footer-section ul li a:hover::before {
    transform: translateX(5px);
}

/* Contact Info */
.contact-info li {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.contact-info li i {
    color: var(--primary);
    font-size: 1.1rem;
    margin-top: 0.2rem;
    min-width: 20px;
}

.contact-info li span {
    color: var(--footer-text);
}

/* Newsletter Form */
.newsletter-form {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.newsletter-form input {
    flex: 1;
    padding: 0.9rem 1.2rem;
    border: 2px solid rgba(99, 102, 241, 0.3);
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    color: white;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
}

.newsletter-form input:focus {
    outline: none;
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.1);
}

.newsletter-form input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.newsletter-form button {
    padding: 0.9rem 1.5rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border: none;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.newsletter-form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
}

/* Footer Bottom */
.footer-bottom {
    background: rgba(0, 0, 0, 0.3);
    padding: 1.5rem 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom .container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.footer-bottom p {
    margin: 0;
    font-size: 0.9rem;
    color: var(--footer-text);
}

.footer-links {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.footer-links a {
    color: var(--footer-text);
    text-decoration: none;
    font-size: 0.85rem;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: white;
}

.footer-links span {
    color: rgba(255, 255, 255, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    footer {
        padding: 3rem 0 0;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        padding: 0 1.5rem 2rem;
        gap: 2rem;
    }
    
    .footer-bottom .container {
        flex-direction: column;
        text-align: center;
        padding: 0 1.5rem;
    }
    
    .footer-links {
        justify-content: center;
    }
    
    .social-links {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .footer-section h4 {
        font-size: 1.1rem;
    }
    
    .newsletter-form {
        flex-direction: column;
    }
    
    .newsletter-form button {
        width: 100%;
    }
}

/* Scroll to top button (opcional) */
.scroll-top {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
}

.scroll-top.visible {
    opacity: 1;
    visibility: visible;
}

.scroll-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 30px rgba(99, 102, 241, 0.6);
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.footer-section {
    animation: fadeInUp 0.6s ease-out;
}

.footer-section:nth-child(1) { animation-delay: 0.1s; }
.footer-section:nth-child(2) { animation-delay: 0.2s; }
.footer-section:nth-child(3) { animation-delay: 0.3s; }
.footer-section:nth-child(4) { animation-delay: 0.4s; }
</style>

<!-- Scroll to Top Button -->
<button class="scroll-top" id="scrollTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
// Scroll to top button visibility
window.addEventListener('scroll', function() {
    const scrollTop = document.getElementById('scrollTop');
    if (window.pageYOffset > 300) {
        scrollTop.classList.add('visible');
    } else {
        scrollTop.classList.remove('visible');
    }
});

// Newsletter form (ejemplo básico)
document.querySelector('.newsletter-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    
    
    alert('¡Gracias por suscribirte! Te enviaremos nuestras mejores ofertas a: ' + email);
    this.reset();
});
</script>

<script src="<?= URL_BASE ?>assets/js/main.js"></script>
</footer>

