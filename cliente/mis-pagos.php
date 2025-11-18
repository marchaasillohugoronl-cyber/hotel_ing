<?php
include '../config.php';
include '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'];

$sql = "SELECT p.*, m.nombre AS metodo 
        FROM pago p
        JOIN metodo_pago m ON p.id_metodo = m.id_metodo
        WHERE p.id_referencia IN (
            SELECT id_reserva FROM reserva WHERE id_cliente = ?
            UNION
            SELECT id_alquiler FROM alquiler WHERE id_cliente = ?
        )
        ORDER BY p.fecha_pago DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_cliente, $id_cliente);
$stmt->execute();
$res = $stmt->get_result();
?>
<style>
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
<h2>Mis Pagos</h2>

<?php if ($res->num_rows === 0): ?>
    <p>No has realizado pagos aún.</p>
<?php else: ?>
    <div class="tabla-pagos-wrapper">
        <table class="tabla-pagos">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Método</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($p = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($p['codigo']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($p['tipo'])) ?></td>
                    <td><?= htmlspecialchars($p['metodo']) ?></td>
                    <td><?= formatoMoneda($p['monto']) ?></td>
                    <td><?= formatoFecha($p['fecha_pago']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
