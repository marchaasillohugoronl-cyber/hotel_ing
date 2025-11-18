<?php
include '../config.php';
include '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'];
$sql = "SELECT * FROM v_alquileres_activos WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
?>
<style>
    /* Mis hospedajes */
.mis-hospedajes {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
}

.mis-hospedajes h2 {
    text-align: center;
    color: #004080;
    margin-bottom: 30px;
}

/* Grid de hospedajes */
.hospedajes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

/* Tarjetas individuales */
.hospedaje-card {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.hospedaje-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.hospedaje-card p {
    margin: 8px 0;
    font-size: 1rem;
}

/* Estado del hospedaje */
.estado {
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 5px;
    color: #fff;
}

.estado.activo { background-color: #28a745; }
.estado.finalizado { background-color: #6c757d; }
.estado.cancelado { background-color: #dc3545; }

/* Mensaje cuando no hay hospedajes */
.no-hospedajes {
    text-align: center;
    font-size: 1.1rem;
    color: #555;
}

/* Responsive */
@media (max-width: 768px) {
    .hospedaje-card {
        padding: 15px;
    }
}

/* Estilos modernos */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('../assets/img/fondo_hgeneral.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
}
.main-content {
    max-width: 900px;
    margin: 40px auto;
    background: rgba(255,255,255,0.92);
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    padding: 30px;
}
h2 {
    color: #004080;
    text-align: center;
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table th, table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}
table th {
    background-color: #004080;
    color: #fff;
}
table tr:nth-child(even) {
    background-color: #f9f9f9;
}
table tr:hover {
    background-color: #e6f0ff;
}

</style>
<main class="mis-hospedajes">
    <h2>Mis Hospedajes Activos</h2>

    <?php if ($res->num_rows > 0): ?>
        <div class="hospedajes-grid">
            <?php while ($a = $res->fetch_assoc()): ?>
                <div class="hospedaje-card">
                    <p><strong>Código:</strong> <?= htmlspecialchars($a['codigo']) ?></p>
                    <p><strong>Check-in:</strong> <?= formatoFecha($a['fecha_checkin']) ?></p>
                    <p><strong>Check-out:</strong> <?= formatoFecha($a['fecha_checkout_programado']) ?></p>
                    <p><strong>Habitaciones:</strong> <?= htmlspecialchars($a['habitaciones']) ?></p>
                    <p><strong>Total:</strong> <?= formatoMoneda($a['total_final']) ?></p>
                    <p><strong>Estado:</strong> <span class="estado <?= strtolower($a['estado']) ?>"><?= ucfirst($a['estado']) ?></span></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="no-hospedajes">No tienes hospedajes activos.</p>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
