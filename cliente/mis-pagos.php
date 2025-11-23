<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
if (!$id_cliente) {
    echo '<main class="container"><h2>Mis pagos</h2><p style="color:red;">No se identificó al cliente en sesión.</p><p><a href="index.php">Volver</a></p></main>';
    include '../includes/footer.php';
    exit;
}

// Recolectar pagos asociados al cliente (reserva, hospedaje/alquiler, venta)
$sql = "(
    SELECT p.id_pago, p.codigo, p.tipo, p.id_referencia, p.monto, p.fecha_pago, m.nombre AS metodo, 'Reserva' AS origen
    FROM pago p
    JOIN metodo_pago m ON p.id_metodo = m.id_metodo
    JOIN reserva r ON p.id_referencia = r.id_reserva
    WHERE p.tipo = 'reserva' AND r.id_cliente = ?
) UNION (
    SELECT p.id_pago, p.codigo, p.tipo, p.id_referencia, p.monto, p.fecha_pago, m.nombre AS metodo, 'Hospedaje' AS origen
    FROM pago p
    JOIN metodo_pago m ON p.id_metodo = m.id_metodo
    JOIN alquiler a ON p.id_referencia = a.id_alquiler
    WHERE p.tipo = 'hospedaje' AND a.id_cliente = ?
) UNION (
    SELECT p.id_pago, p.codigo, p.tipo, p.id_referencia, p.monto, p.fecha_pago, m.nombre AS metodo, 'Venta' AS origen
    FROM pago p
    JOIN metodo_pago m ON p.id_metodo = m.id_metodo
    JOIN venta_recepcion v ON p.id_referencia = v.id_venta
    WHERE p.tipo = 'venta' AND v.id_cliente = ?
)
ORDER BY fecha_pago DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $id_cliente, $id_cliente, $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

?>
<main class="container">
    <h2>Mis pagos</h2>
    <?php if (!$res || $res->num_rows === 0): ?>
        <p>No se han encontrado pagos asociados a tu cuenta.</p>
        <p><a href="index.php">Volver al panel</a></p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0" style="width:100%;border-collapse:collapse">
            <thead><tr><th>Código</th><th>Origen</th><th>Referencia</th><th>Método</th><th>Monto</th><th>Fecha</th><th>Acción</th></tr></thead>
            <tbody>
            <?php while ($p = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($p['codigo']) ?></td>
                    <td><?= htmlspecialchars($p['origen']) ?></td>
                    <td><?= htmlspecialchars($p['id_referencia']) ?></td>
                    <td><?= htmlspecialchars($p['metodo']) ?></td>
                    <td>S/ <?= number_format($p['monto'],2) ?></td>
                    <td><?= htmlspecialchars($p['fecha_pago']) ?></td>
                    <td><a href="pago-detalle.php?id=<?= (int)$p['id_pago'] ?>">Ver</a></td>
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
    overflow-x: hidden;
    color: #0f172a;
}

.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

/* ============================================
   TITULOS
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
   TABLA DE PAGOS
   ============================================ */
table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 15px;
    overflow: hidden;
}

thead {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

thead th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.95rem;
}

tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

tbody tr:hover {
    background: rgba(99, 102, 241, 0.05);
}

tbody td {
    padding: 1rem;
    font-size: 0.95rem;
}

tbody td:last-child {
    text-align: center;
}

tbody td:nth-child(5) {
    text-align: right; /* Montos alineados a la derecha */
    font-weight: 600;
    color: #10b981;
}

/* ============================================
   ENLACES Y BOTONES
   ============================================ */
a {
    color: #6366f1;
    text-decoration: none;
    transition: all 0.3s ease;
}

a:hover {
    color: #4f46e5;
}

button, .btn {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    transition: all 0.3s ease;
}

button:hover, .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }

    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tbody tr {
        margin-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    tbody td {
        padding: 0.5rem;
        text-align: right;
        position: relative;
    }

    tbody td::before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        text-align: left;
    }

    tbody td:last-child {
        text-align: center;
    }
}

</style>
<?php include '../includes/footer.php'; ?>
