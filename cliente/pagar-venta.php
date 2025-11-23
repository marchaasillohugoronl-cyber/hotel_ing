<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
$id_venta = intval($_GET['id'] ?? 0);

if (!$id_cliente || !$id_venta) {
    echo '<main class="container"><p>Solicitud inválida.</p><p><a href="mis-pedidos.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}

// Verificar que la venta pertenezca al cliente
$stmt = $conn->prepare("SELECT id_venta, codigo, total FROM venta_recepcion WHERE id_venta = ? AND id_cliente = ? LIMIT 1");
$stmt->bind_param('ii', $id_venta, $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    echo '<main class="container"><p>Pedido no encontrado o no autorizado.</p><p><a href="mis-pedidos.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}
$venta = $res->fetch_assoc();
$stmt->close();

// Si POST -> registrar pago
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metodo = intval($_POST['metodo'] ?? 0);
    if ($metodo <= 0) $error = 'Seleccione un método de pago.';
    if (!$error) {
        $codigo = 'P' . time() . rand(100,999);
        $monto = floatval($venta['total']);
        $codigo_e = $conn->real_escape_string($codigo);
        $tipo = 'venta';
        if ($conn->query("INSERT INTO pago (codigo,tipo,id_referencia,id_metodo,monto,comprobante) VALUES ('$codigo_e','$tipo',{$venta['id_venta']},$metodo,$monto,NULL)")) {
            $success = 'Pago registrado correctamente.';
            header('Location: mis-pedidos.php?paid=1'); exit;
        } else $error = 'Error al registrar pago.';
    }
}

// Obtener metodos de pago
$metodos = $conn->query("SELECT id_metodo, nombre FROM metodo_pago WHERE estado='activo' ORDER BY nombre");
?>
<main class="container">
    <h2>Pagar pedido: <?= htmlspecialchars($venta['codigo']) ?></h2>
    <?php if($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if($success): ?><p style="color:green"><?= htmlspecialchars($success) ?></p><?php endif; ?>
    <p>Total: S/ <?= number_format($venta['total'],2) ?></p>
    <form method="POST">
        <label>Método de pago:</label>
        <select name="metodo" required>
            <option value="">-- Seleccione --</option>
            <?php while($m = $metodos->fetch_assoc()): ?>
                <option value="<?= (int)$m['id_metodo'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
            <?php endwhile; ?>
        </select>
        <br><br>
        <button type="submit">Confirmar pago</button>
        <a href="mis-pedidos.php" style="margin-left:12px">Cancelar</a>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
