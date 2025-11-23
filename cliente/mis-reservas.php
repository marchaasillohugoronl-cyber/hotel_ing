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

// Cancelar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar']) && isset($_POST['id_reserva'])) {
    $idr = intval($_POST['id_reserva']);
    $stmt = $conn->prepare("UPDATE reserva SET estado='cancelada' WHERE id_reserva=? AND id_cliente=?");
    $stmt->bind_param('ii', $idr, $id_cliente);
    $stmt->execute();
}

$stmt = $conn->prepare("SELECT id_reserva, codigo, fecha_reserva, fecha_entrada, fecha_salida, num_noches, total, estado FROM reserva WHERE id_cliente=? ORDER BY fecha_reserva DESC");
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

?>
<!-- CSS agregado -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    main.container {
        max-width: 900px;
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    table th, table td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    table th {
        background-color: #007BFF;
        color: white;
    }
    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    table tr:hover {
        background-color: #f1f1f1;
    }
    button {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
    }
    button:hover {
        background-color: #c82333;
    }
    a {
        text-decoration: none;
        color: #007BFF;
    }
    a:hover {
        text-decoration: underline;
    }
    p {
        text-align: center;
    }
    /* ============================================
   FONDO DE PANTALLA
   ============================================ */
body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: url('../assets/img/fondo.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}
</style>

<main class="container">
    <h2>Mis reservas</h2>
    <?php if (isset($_GET['ok'])): ?>
        <p style="color:green;">Reserva creada correctamente.</p>
    <?php endif; ?>
    <?php if ($res->num_rows === 0): ?>
        <p>No tiene reservas registradas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Código</th><th>Fecha reserva</th><th>Entrada</th><th>Salida</th><th>Noches</th><th>Total</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php while ($r = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($r['codigo']) ?></td>
                    <td><?= htmlspecialchars($r['fecha_reserva']) ?></td>
                    <td><?= htmlspecialchars($r['fecha_entrada']) ?></td>
                    <td><?= htmlspecialchars($r['fecha_salida']) ?></td>
                    <td><?= (int)$r['num_noches'] ?></td>
                    <td>S/ <?= number_format($r['total'],2) ?></td>
                    <td><?= htmlspecialchars($r['estado']) ?></td>
                    <td>
                        <?php if ($r['estado'] !== 'cancelada' && $r['estado'] !== 'confirmada'): ?>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                                <button type="submit" name="cancelar" onclick="return confirm('Cancelar reserva?')">Cancelar</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a href="index.php">Volver al panel</a></p>
</main>
<?php include '../includes/footer.php'; ?>
