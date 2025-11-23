</main>

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
        
        <div class="footer-section">
            <h4><i class="fas fa-bell"></i> Newsletter</h4>
            <p>Suscríbete para recibir ofertas especiales</p>
            <form class="newsletter-form" onsubmit="return false;">
                <input type="email" placeholder="Tu email" required>
                <button type="submit"><i class="fas fa-paper-plane"></i></button>
            </form>
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

<?php if (isset($_SESSION['usuario']) && strtolower($_SESSION['usuario']['rol'] ?? '') === 'cliente'): ?>
<div class="cart-badge">
    <a href="<?= URL_BASE ?>cliente/carrito.php" class="cart-link">
        <span class="cart-icon">🛒</span>
        <span id="cart-count" class="cart-count">0</span>
    </a>
</div>
<?php endif; ?>

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
    
    // Aquí podrías agregar la lógica AJAX para suscribirse
    alert('¡Gracias por suscribirte! Te enviaremos nuestras mejores ofertas a: ' + email);
    this.reset();
});
</script>

<script src="<?= URL_BASE ?>assets/js/main.js"></script>
</body>
</html>