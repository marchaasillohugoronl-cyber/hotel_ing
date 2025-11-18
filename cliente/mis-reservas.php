<?php
include '../config.php';
include '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'];
$sql = "SELECT * FROM v_reservas WHERE id_cliente = ? ORDER BY fecha_reserva DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
?>
<style>
    /* Mis reservas */
.mis-reservas {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 20px;
}

.mis-reservas h2 {
    text-align: center;
    color: #004080;
    margin-bottom: 30px;
}

/* Grid de reservas */
.reservas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

/* Tarjetas individuales */
.reserva-card {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.reserva-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.reserva-card p {
    margin: 10px 0;
    font-size: 1rem;
}

/* Estado de la reserva */
.estado {
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 5px;
    color: #fff;
}

.estado.confirmada { background-color: #28a745; }
.estado.cancelada { background-color: #dc3545; }
.estado.pendiente { background-color: #ffc107; color: #000; }

/* Mensaje cuando no hay reservas */
.no-reservas {
    text-align: center;
    font-size: 1.1rem;
    color: #555;
}

/* Responsive */
@media (max-width: 768px) {
    .reserva-card {
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
<main class="mis-reservas">
    <h2>Mis Reservas</h2>

    <?php if ($res->num_rows > 0): ?>
    <div class="reservas-grid">
        <?php while ($r = $res->fetch_assoc()): ?>
        <div class="reserva-card">
            <p><strong>Código:</strong> <?= htmlspecialchars($r['codigo']) ?></p>
            <p><strong>Fechas:</strong> <?= formatoFecha($r['fecha_entrada']) ?> → <?= formatoFecha($r['fecha_salida']) ?></p>
            <p><strong>Habitaciones:</strong> <?= htmlspecialchars($r['habitaciones']) ?></p>
            <p><strong>Total:</strong> <?= formatoMoneda($r['total']) ?></p>
            <p><strong>Estado:</strong> <span class="estado <?= strtolower($r['estado']) ?>"><?= ucfirst($r['estado']) ?></span></p>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
        <p class="no-reservas">No tienes reservas aún.</p>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>
