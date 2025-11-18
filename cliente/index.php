<?php
require_once '../config.php';
require_once '../includes/funciones.php'; // Asegurar que funciones.php esté disponible

if (!estaLogeado()) {
    header("Location: ../login.php");
    exit();
}

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';
?>

<main class="dashboard">
    <div class="welcome-card">
        <h2>Bienvenido, <?= htmlspecialchars($_SESSION['usuario']['nombres']) ?> 👋</h2>
        <p>Desde tu panel puedes gestionar tus reservas, pagos y servicios.</p>
    </div>

    <div class="dashboard-grid">
        <a href="mis-reservas.php" class="dashboard-card">📅 <span>Mis reservas</span></a>
        <a href="nueva-reserva.php" class="dashboard-card">➕ <span>Nueva reserva</span></a>
        <a href="mis-alquileres.php" class="dashboard-card">🏨 <span>Mis hospedajes</span></a>
        <a href="solicitar-servicio.php" class="dashboard-card">🛎️ <span>Solicitar servicio</span></a>
        <a href="mis-pagos.php" class="dashboard-card">💳 <span>Mis pagos</span></a>
    </div>
</main>
<style>
    /* Dashboard cliente */
.dashboard {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
    background: rgba(255,255,255,0.85);
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
}

.welcome-card {
    background-color: #fff;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    text-align: center;
    margin-bottom: 30px;
}

.welcome-card h2 {
    font-size: 2rem;
    color: #004080;
    margin-bottom: 10px;
}

.welcome-card p {
    font-size: 1.1rem;
    color: #333;
}

/* Grid de opciones */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
}

.dashboard-card {
    background-color: #004080;
    color: #fff;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    font-size: 1.1rem;
    font-weight: bold;
    text-decoration: none;
    transition: transform 0.3s, background-color 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.dashboard-card span {
    display: block;
    margin-top: 10px;
    font-size: 0.9rem;
    font-weight: normal;
}

.dashboard-card:hover {
    background-color: #0066cc;
    transform: translateY(-5px);
}

/* Responsive */
@media (max-width: 768px) {
    .welcome-card h2 {
        font-size: 1.5rem;
    }

    .dashboard-card {
        padding: 20px;
        font-size: 1rem;
    }

    .dashboard {
        padding: 10px;
    }
}

/* Fondo de pantalla */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('../assets/img/fondo_hgeneral.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
}

</style>
<?php include '../includes/footer.php'; ?>
