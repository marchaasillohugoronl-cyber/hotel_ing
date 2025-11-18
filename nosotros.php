<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nosotros - <?= NOMBRE_HOSTAL ?></title>
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
    margin-bottom: 15px;
    color: #004080;
    text-align: center;
}

main p {
    font-size: 1.1rem;
    margin-bottom: 20px;
    text-align: justify;
}

/* Imágenes */
main img {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 20px;
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

/* Botones y enlaces destacados (opcional) */
.button {
    display: inline-block;
    padding: 10px 20px;
    background-color: #004080;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.button:hover {
    background-color: #0066cc;
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
<body>
<header>
    <h1><?= NOMBRE_HOSTAL ?></h1>
    <nav>
        <a href="index.php">Inicio</a>
        <a href="nosotros.php">Nosotros</a>
        <a href="contacto.php">Contacto</a>
    </nav>
</header>

<main>
    <h2>Sobre nosotros</h2>
    <p>En <strong><?= NOMBRE_HOSTAL ?></strong> ofrecemos una experiencia acogedora y tranquila, ideal para viajeros que buscan descanso y comodidad. Nuestro equipo está comprometido con brindar un servicio amable, eficiente y personalizado.</p>
    <section>
        <h2>Nuestra historia</h2>
        <p>Fundado en 2010, <?= NOMBRE_HOSTAL ?> nació como un proyecto familiar con el objetivo de crear un espacio donde cada huésped se sienta como en casa. A lo largo de los años, hemos crecido y mejorado nuestras instalaciones, manteniendo siempre el trato cálido y personalizado que nos caracteriza.</p>
        <img src="assets/img/baner_01.jpg" alt="Banner del hostal" width="100%">
    </section>
    <section>
        <h2>Nuestro equipo</h2>
        <p>Contamos con un equipo profesional y dedicado, desde la recepción hasta el servicio de limpieza, todos comprometidos con tu bienestar. Valoramos la amabilidad, la honestidad y la atención al detalle.</p>
        <img src="assets/img/habitaciones/suite.jpg" alt="Equipo hostal" width="100%">
    </section>
    <section>
        <h2>Nuestros valores</h2>
        <ul style="margin-bottom:20px;">
            <li>✔️ Hospitalidad y respeto</li>
            <li>✔️ Limpieza y seguridad</li>
            <li>✔️ Atención personalizada</li>
            <li>✔️ Compromiso con el descanso del huésped</li>
        </ul>
    </section>
    <section>
        <h2>¿Por qué elegirnos?</h2>
        <p>Estamos ubicados en una zona céntrica, cerca de los principales atractivos turísticos y comerciales. Ofrecemos habitaciones para todo tipo de viajero, desde opciones económicas hasta suites de lujo.</p>
        <a href="contacto.php" class="button">Contáctanos</a>
    </section>
</main>

<footer>
    <p>&copy; <?= date('Y') ?> <?= NOMBRE_HOSTAL ?>.</p>
</footer>
</body>
</html>
