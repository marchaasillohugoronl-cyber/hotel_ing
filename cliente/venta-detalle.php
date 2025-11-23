<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
$id_venta = intval($_GET['id'] ?? 0);

if (!$id_cliente || !$id_venta) {
    echo '<p>Solicitud inválida.</p>';
    include '../includes/footer.php';
    exit;
}

// Verificar propiedad
$stmt = $conn->prepare("SELECT id_venta, codigo, fecha_venta, total FROM venta_recepcion WHERE id_venta=? AND id_cliente=?");
$stmt->bind_param('ii', $id_venta, $id_cliente);
$stmt->execute();
$r = $stmt->get_result();
if ($r->num_rows === 0) {
    echo '<p>Pedido no encontrado o no autorizado.</p>';
    include '../includes/footer.php';
    exit;
}
$venta = $r->fetch_assoc();

$stmt2 = $conn->prepare("SELECT vd.cantidad, vd.precio, vd.subtotal, p.nombre FROM venta_detalle vd JOIN producto p ON vd.id_producto = p.id_producto WHERE vd.id_venta=?");
$stmt2->bind_param('i', $id_venta);
$stmt2->execute();
$det = $stmt2->get_result();

?>
<main class="container">
    <h2>Detalle pedido: <?= htmlspecialchars($venta['codigo']) ?></h2>
    <p>Fecha: <?= htmlspecialchars($venta['fecha_venta']) ?> | Total: S/ <?= number_format($venta['total'],2) ?></p>

    <?php if ($det->num_rows === 0): ?>
        <p>No hay items registrados.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
            <?php while ($row = $det->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= (int)$row['cantidad'] ?></td>
                    <td>S/ <?= number_format($row['precio'],2) ?></td>
                    <td>S/ <?= number_format($row['subtotal'],2) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="mis-pedidos.php">Volver a mis pedidos</a></p>
</main>

<?php include '../includes/footer.php'; ?>
