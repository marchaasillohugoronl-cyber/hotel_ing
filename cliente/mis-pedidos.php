<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
if (!$id_cliente) {
    echo '<p>No se encontró información del cliente.</p>';
    include '../includes/footer.php';
    exit;
}

$stmt = $conn->prepare("SELECT id_venta, codigo, fecha_venta, total FROM venta_recepcion WHERE id_cliente=? ORDER BY fecha_venta DESC");
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

?>
<main class="container">
    <h2>Mis pedidos</h2>
    <?php if ($res->num_rows === 0): ?>
        <p>No tiene pedidos registrados.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr><th>Código</th><th>Fecha</th><th>Total</th><th>Pago</th><th>Detalle</th></tr>
            </thead>
            <tbody>
            <?php while ($r = $res->fetch_assoc()): ?>
                <?php
                    // Verificar si existe pago para esta venta
                    $q = $conn->prepare("SELECT id_pago FROM pago WHERE tipo='venta' AND id_referencia = ? LIMIT 1");
                    $q->bind_param('i', $r['id_venta']); $q->execute(); $qr = $q->get_result();
                    $pagado = ($qr && $qr->num_rows > 0);
                    $id_pago = $pagado ? $qr->fetch_assoc()['id_pago'] : null;
                    $q->close();
                ?>
                <tr>
                    <td><?= htmlspecialchars($r['codigo']) ?></td>
                    <td><?= htmlspecialchars($r['fecha_venta']) ?></td>
                    <td>S/ <?= number_format($r['total'],2) ?></td>
                    <td><?= $pagado ? '<strong style="color:green">Pagado</strong>' : '<a href="pagar-venta.php?id='. (int)$r['id_venta'] .'">Pagar</a>' ?></td>
                    <td><a href="venta-detalle.php?id=<?= $r['id_venta'] ?>">Ver detalle</a></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a href="index.php">Volver al panel</a></p>
</main>
<style>

/* ============================================
   FONDO Y ESTILOS GENERALES
   ============================================ */
body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: url('../assets/img/fondo.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    color: #0f172a;
}

main.container {
    max-width: 1100px;
    margin: 2rem auto;
    padding: 2rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

/* ============================================
   TITULO
   ============================================ */
h2 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ============================================
   TABLA DE PEDIDOS
   ============================================ */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table th, table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

table th {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    font-weight: 600;
    text-align: center;
}

table td {
    text-align: center;
}

table td:nth-child(3) { /* Total */
    text-align: right;
    font-weight: 600;
    color: #10b981;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1f1f1;
    transition: all 0.2s ease-in-out;
}

/* ============================================
   ENLACES
   ============================================ */
a {
    color: #6366f1;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

/* ============================================
   ESTADO DE PAGO
   ============================================ */
td strong {
    color: green;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media(max-width:768px){
    table th, table td {
        padding: 8px;
        font-size: 0.9rem;
    }
}


</style>

<?php include '../includes/footer.php'; ?>
