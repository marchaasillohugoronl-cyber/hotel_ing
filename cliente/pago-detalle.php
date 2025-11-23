<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
$id_pago = intval($_GET['id'] ?? 0);

if (!$id_cliente || !$id_pago) {
    echo '<main class="container"><p>Solicitud inválida.</p><p><a href="mis-pagos.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}

// Buscar pago y verificar que pertenezca al cliente
$stmt = $conn->prepare("SELECT p.*, m.nombre AS metodo FROM pago p JOIN metodo_pago m ON p.id_metodo = m.id_metodo WHERE p.id_pago = ? LIMIT 1");
$stmt->bind_param('i', $id_pago);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    echo '<main class="container"><p>Pago no encontrado.</p><p><a href="mis-pagos.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}
$p = $res->fetch_assoc();
$stmt->close();

// Determinar si la referencia pertenece al cliente
$pertenece = false;
switch ($p['tipo']) {
    case 'reserva':
        $q = $conn->prepare("SELECT id_reserva FROM reserva WHERE id_reserva = ? AND id_cliente = ? LIMIT 1");
        $q->bind_param('ii', $p['id_referencia'], $id_cliente); $q->execute(); $rq = $q->get_result(); if($rq && $rq->num_rows) $pertenece = true; $q->close();
        break;
    case 'hospedaje':
        $q = $conn->prepare("SELECT id_alquiler FROM alquiler WHERE id_alquiler = ? AND id_cliente = ? LIMIT 1");
        $q->bind_param('ii', $p['id_referencia'], $id_cliente); $q->execute(); $rq = $q->get_result(); if($rq && $rq->num_rows) $pertenece = true; $q->close();
        break;
    case 'venta':
        $q = $conn->prepare("SELECT id_venta FROM venta_recepcion WHERE id_venta = ? AND id_cliente = ? LIMIT 1");
        $q->bind_param('ii', $p['id_referencia'], $id_cliente); $q->execute(); $rq = $q->get_result(); if($rq && $rq->num_rows) $pertenece = true; $q->close();
        break;
}

if (!$pertenece) {
    echo '<main class="container"><p>No autorizado para ver este pago.</p><p><a href="mis-pagos.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}

?>
<main class="container">
    <h2>Detalle de pago: <?= htmlspecialchars($p['codigo']) ?></h2>
    <p>Tipo: <?= htmlspecialchars($p['tipo']) ?> | Referencia: <?= (int)$p['id_referencia'] ?></p>
    <p>Método: <?= htmlspecialchars($p['metodo']) ?> | Monto: S/ <?= number_format($p['monto'],2) ?></p>
    <p>Fecha: <?= htmlspecialchars($p['fecha_pago']) ?></p>
    <?php if (!empty($p['observaciones'])): ?>
        <h4>Observaciones</h4>
        <p><?= nl2br(htmlspecialchars($p['observaciones'])) ?></p>
    <?php endif; ?>
    <p><a href="mis-pagos.php">Volver a mis pagos</a></p>
</main>

<?php include '../includes/footer.php'; ?>
