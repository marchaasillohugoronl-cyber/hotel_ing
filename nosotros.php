<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nosotros - Hostal Estrella</title>
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
    font-size: 3rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.hero p {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.95);
    max-width: 800px;
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
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card-glass p {
    color: var(--gray);
    font-size: 1.1rem;
    line-height: 1.8;
    margin-bottom: 1.5rem;
}

/* Image Container */
.image-container {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    margin: 2rem 0;
    transition: all 0.3s ease;
}

.image-container:hover {
    transform: scale(1.02);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.image-container img {
    width: 100%;
    height: auto;
    display: block;
}

/* Timeline Section */
.timeline {
    position: relative;
    padding: 2rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    transform: translateX(-50%);
}

.timeline-item {
    display: flex;
    align-items: center;
    margin-bottom: 3rem;
    position: relative;
}

.timeline-item:nth-child(odd) {
    flex-direction: row;
}

.timeline-item:nth-child(even) {
    flex-direction: row-reverse;
}

.timeline-content {
    flex: 1;
    padding: 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin: 0 2rem;
    transition: all 0.3s ease;
}

.timeline-content:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.timeline-icon {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 5px 20px rgba(99, 102, 241, 0.4);
}

/* Values Grid */
.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.value-card {
    background: white;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    text-align: center;
}

.value-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
}

.value-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
}

.value-card h3 {
    color: var(--primary);
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.value-card p {
    color: var(--gray);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* Features List */
.features-list {
    list-style: none;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.features-list li {
    padding: 1.5rem;
    background: rgba(99, 102, 241, 0.05);
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.features-list li:hover {
    background: rgba(99, 102, 241, 0.1);
    transform: translateX(5px);
}

.features-list li i {
    font-size: 1.5rem;
    color: var(--primary);
}

/* CTA Section */
.cta-section {
    text-align: center;
    padding: 3rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 25px;
    color: white;
    margin: 3rem 0;
}

.cta-section h2 {
    color: white;
    background: none;
    -webkit-background-clip: unset;
    -webkit-text-fill-color: white;
    margin-bottom: 1rem;
}

.cta-section p {
    color: rgba(255, 255, 255, 0.95);
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

.btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    padding: 1.2rem 2.5rem;
    background: white;
    color: var(--primary);
    text-decoration: none;
    border-radius: 15px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.btn-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

/* Stats Section */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.stat-card {
    text-align: center;
    padding: 2rem;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.stat-number {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: var(--gray);
    font-size: 1rem;
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
    
    .timeline::before {
        left: 30px;
    }
    
    .timeline-item,
    .timeline-item:nth-child(even) {
        flex-direction: row;
    }
    
    .timeline-content {
        margin-left: 80px;
        margin-right: 0;
    }
    
    .timeline-icon {
        left: 30px;
    }
    
    .values-grid,
    .features-list {
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
    
    .cta-section {
        padding: 2rem 1.5rem;
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
        <h2><i class="fas fa-heart"></i> Sobre Nosotros</h2>
        <p>En <strong>Hostal Estrella</strong> ofrecemos una experiencia acogedora y tranquila, ideal para viajeros que buscan descanso y comodidad. Nuestro equipo está comprometido con brindar un servicio amable, eficiente y personalizado.</p>
    </div>

    <!-- Estadísticas -->
    <div class="card-glass">
        <h2><i class="fas fa-chart-line"></i> Nuestra Trayectoria en Números</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">14+</div>
                <div class="stat-label">Años de experiencia</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">50K+</div>
                <div class="stat-label">Huéspedes satisfechos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">4.8</div>
                <div class="stat-label">Calificación promedio</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Atención disponible</div>
            </div>
        </div>
    </div>

    <!-- Historia -->
    <div class="card-glass">
        <h2><i class="fas fa-book-open"></i> Nuestra Historia</h2>
        <p>Fundado en 2010, <strong>Hostal Estrella</strong> nació como un proyecto familiar con el objetivo de crear un espacio donde cada huésped se sienta como en casa. A lo largo de los años, hemos crecido y mejorado nuestras instalaciones, manteniendo siempre el trato cálido y personalizado que nos caracteriza.</p>
        
        <div class="image-container">
            <img src="assets/img/baner_01.jpg" alt="Banner del hostal">
        </div>

        <p>Lo que comenzó como un pequeño hostal familiar ha evolucionado hasta convertirse en uno de los lugares más acogedores de la ciudad, sin perder nunca nuestros valores fundamentales de hospitalidad y calidez humana.</p>
    </div>

    <!-- Equipo -->
    <div class="card-glass">
        <h2><i class="fas fa-users"></i> Nuestro Equipo</h2>
        <p>Contamos con un equipo profesional y dedicado, desde la recepción hasta el servicio de limpieza, todos comprometidos con tu bienestar. Valoramos la amabilidad, la honestidad y la atención al detalle.</p>
        
        <div class="image-container">
            <img src="assets/img/habitaciones/suite.jpg" alt="Equipo hostal">
        </div>

        <p>Cada miembro de nuestro equipo está capacitado para brindarte la mejor experiencia posible. Creemos que un servicio excepcional comienza con personas apasionadas por lo que hacen.</p>
    </div>

    <!-- Valores -->
    <div class="card-glass">
        <h2><i class="fas fa-star"></i> Nuestros Valores</h2>
        <p>Los principios que guían cada decisión y acción en nuestro hostal:</p>
        
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Hospitalidad y Respeto</h3>
                <p>Tratamos a cada huésped con la calidez y respeto que merece, creando un ambiente acogedor.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Limpieza y Seguridad</h3>
                <p>Mantenemos los más altos estándares de limpieza y seguridad en todas nuestras instalaciones.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3>Atención Personalizada</h3>
                <p>Cada huésped es único, por eso ofrecemos un servicio adaptado a sus necesidades específicas.</p>
            </div>

            <div class="value-card">
                <div class="value-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <h3>Compromiso con el Descanso</h3>
                <p>Tu comodidad y descanso son nuestra prioridad número uno en todo momento.</p>
            </div>
        </div>
    </div>

    <!-- Por qué elegirnos -->
    <div class="card-glass">
        <h2><i class="fas fa-trophy"></i> ¿Por Qué Elegirnos?</h2>
        <p>Ofrecemos ventajas únicas que nos distinguen de otros hostales en la ciudad:</p>
        
        <ul class="features-list">
            <li>
                <i class="fas fa-map-marker-alt"></i>
                <span>Ubicación céntrica cerca de atractivos turísticos</span>
            </li>
            <li>
                <i class="fas fa-dollar-sign"></i>
                <span>Precios competitivos y excelente relación calidad-precio</span>
            </li>
            <li>
                <i class="fas fa-wifi"></i>
                <span>WiFi de alta velocidad en todas las áreas</span>
            </li>
            <li>
                <i class="fas fa-concierge-bell"></i>
                <span>Servicio de recepción 24/7</span>
            </li>
            <li>
                <i class="fas fa-utensils"></i>
                <span>Desayuno incluido con opciones variadas</span>
            </li>
            <li>
                <i class="fas fa-parking"></i>
                <span>Estacionamiento gratuito disponible</span>
            </li>
        </ul>

        <p style="margin-top: 2rem;">Estamos ubicados en una zona céntrica, cerca de los principales atractivos turísticos y comerciales. Ofrecemos habitaciones para todo tipo de viajero, desde opciones económicas hasta suites de lujo.</p>
    </div>

    <!-- CTA -->
    <div class="cta-section">
        <h2>¿Listo para tu Próxima Aventura?</h2>
        <p>Únete a miles de viajeros satisfechos que han elegido Hostal Estrella como su hogar lejos de casa.</p>
        <a href="contacto.php" class="btn-cta">
            <i class="fas fa-envelope"></i>
            Contáctanos Ahora
        </a>
    </div>
</main>

<footer>
    <p>&copy; 2024 Hostal Estrella. Todos los derechos reservados.</p>
</footer>

</body>
</html>