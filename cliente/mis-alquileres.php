<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
if (!$id_cliente) {
    echo '<main class="container"><h2>Mis hospedajes</h2><p style="color:red;">No se identificó al cliente en sesión.</p><p><a href="index.php">Volver al panel</a></p></main>';
    include '../includes/footer.php';
    exit;
}

// Obtener alquileres del cliente
$stmt = $conn->prepare("SELECT id_alquiler, codigo, fecha_checkin, fecha_checkout_programado, fecha_checkout_real, total_hospedaje, total_servicios, total_final, estado FROM alquiler WHERE id_cliente = ? ORDER BY fecha_checkin DESC");
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
$alquileres = [];
while ($row = $res->fetch_assoc()) $alquileres[] = $row;
$stmt->close();
?>

<main class="container">
    <h2>Mis hospedajes</h2>
    <?php if (empty($alquileres)): ?>
        <p>No tienes hospedajes registrados.</p>
        <p><a href="index.php">Volver al panel</a></p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse;margin-bottom:18px">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Check-in</th>
                    <th>Checkout programado</th>
                    <th>Checkout real</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($alquileres as $al): ?>
                <tr>
                    <td><?= htmlspecialchars($al['codigo']) ?></td>
                    <td><?= htmlspecialchars($al['fecha_checkin']) ?></td>
                    <td><?= htmlspecialchars($al['fecha_checkout_programado']) ?></td>
                    <td><?= htmlspecialchars($al['fecha_checkout_real'] ?? '') ?></td>
                    <td>S/ <?= number_format($al['total_final'] ?? ($al['total_hospedaje'] + ($al['total_servicios'] ?? 0)), 2) ?></td>
                    <td><?= htmlspecialchars($al['estado']) ?></td>
                    <td>
                        <button type="button" onclick="toggleDetails(<?= (int)$al['id_alquiler'] ?>)">Ver</button>
                        <a class="btn" href="alquiler-factura.php?id=<?= (int)$al['id_alquiler'] ?>" style="margin-left:8px; padding:6px 10px; background:#2b6cb0;color:#fff;border-radius:4px;text-decoration:none;">Factura</a>
                    </td>
                </tr>
                <tr id="det-<?= (int)$al['id_alquiler'] ?>" style="display:none;background:#fafafa">
                    <td colspan="7">
                        <?php
                        // Obtener habitaciones asociadas
                        $s2 = $conn->prepare("SELECT ah.id_habitacion, ah.precio_noche, h.numero FROM alquiler_habitacion ah JOIN habitacion h ON ah.id_habitacion = h.id_habitacion WHERE ah.id_alquiler = ?");
                        $s2->bind_param('i', $al['id_alquiler']);
                        $s2->execute();
                        $r2 = $s2->get_result();
                        if ($r2 && $r2->num_rows > 0):
                        ?>
                            <strong>Habitaciones:</strong>
                            <ul>
                            <?php while ($h = $r2->fetch_assoc()): ?>
                                <li>Habitación <?= htmlspecialchars($h['numero']) ?> — S/ <?= number_format($h['precio_noche'],2) ?></li>
                            <?php endwhile; ?>
                            </ul>
                        <?php else: ?>
                            <p>No hay habitaciones registradas para este hospedaje.</p>
                        <?php endif; $s2->close(); ?>
                        <p><strong>Detalle de totales:</strong> Hospedaje S/ <?= number_format($al['total_hospedaje'],2) ?>, Servicios S/ <?= number_format($al['total_servicios'] ?? 0,2) ?>, Total final S/ <?= number_format($al['total_final'],2) ?></p>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="index.php">Volver al panel</a></p>
    <?php endif; ?>
</main>

<script>
function toggleDetails(id){
    const el = document.getElementById('det-'+id);
    if(!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display==='') ? 'table-row' : 'none';
}
</script>

<?php include '../includes/footer.php'; ?>
