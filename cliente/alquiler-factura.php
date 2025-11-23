<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
$id_alquiler = intval($_GET['id'] ?? 0);

if (!$id_cliente || !$id_alquiler) {
    echo '<main class="container"><p>Solicitud inválida.</p><p><a href="mis-alquileres.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}

// Obtener datos del alquiler
$stmt = $conn->prepare("SELECT a.*, c.nombres, c.apellidos FROM alquiler a LEFT JOIN cliente c ON a.id_cliente = c.id_cliente WHERE a.id_alquiler = ? AND a.id_cliente = ? LIMIT 1");
$stmt->bind_param('ii', $id_alquiler, $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    echo '<main class="container"><p>Alquiler no encontrado o no autorizado.</p><p><a href="mis-alquileres.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}
$al = $res->fetch_assoc();
$stmt->close();

// habitaciones
$s2 = $conn->prepare("SELECT ah.id_habitacion, ah.precio_noche, h.numero FROM alquiler_habitacion ah JOIN habitacion h ON ah.id_habitacion = h.id_habitacion WHERE ah.id_alquiler = ?");
$s2->bind_param('i', $id_alquiler);
$s2->execute();
$r2 = $s2->get_result();
$habs = [];
while ($h = $r2->fetch_assoc()) $habs[] = $h;
$s2->close();

?>
<main class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Factura - <?= htmlspecialchars($al['codigo']) ?></h2>
        <div>
            <button onclick="window.print()" class="btn">Descargar / Imprimir</button>
            <a href="mis-alquileres.php" class="btn" style="margin-left:8px;">Volver</a>
        </div>
    </div>

    <section style="margin-top:12px;padding:12px;border:1px solid #e5e7eb;background:#fff;">
        <h3>Datos del cliente</h3>
        <p><?= htmlspecialchars($al['nombres'].' '.$al['apellidos']) ?></p>
        <h3>Detalle del hospedaje</h3>
        <p>Check-in: <?= htmlspecialchars($al['fecha_checkin']) ?> | Checkout programado: <?= htmlspecialchars($al['fecha_checkout_programado']) ?></p>

        <h4>Habitaciones</h4>
        <?php if (empty($habs)): ?>
            <p>No hay habitaciones registradas.</p>
        <?php else: ?>
            <table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%">
                <thead><tr><th>Habitación</th><th>Precio noche</th></tr></thead>
                <tbody>
                <?php foreach ($habs as $h): ?>
                    <tr><td><?= htmlspecialchars($h['numero']) ?></td><td>S/ <?= number_format($h['precio_noche'],2) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h4 style="margin-top:12px">Totales</h4>
        <p>Hospedaje: S/ <?= number_format($al['total_hospedaje'],2) ?></p>
        <p>Servicios: S/ <?= number_format($al['total_servicios'] ?? 0,2) ?></p>
        <p><strong>Total final: S/ <?= number_format($al['total_final'],2) ?></strong></p>
    </section>

</main>

<?php include '../includes/footer.php'; ?>
