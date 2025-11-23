<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;

// Intentar resolver id_cliente si no está presente en la sesión
if (empty($id_cliente) && isset($_SESSION['usuario'])) {
    // 1) Si la sesión incluye id_usuario, buscar en tabla usuario
    $id_usuario = $_SESSION['usuario']['id_usuario'] ?? null;
    if ($id_usuario) {
        $s = $conn->prepare("SELECT id_cliente FROM usuario WHERE id_usuario = ? LIMIT 1");
        $s->bind_param('i', $id_usuario);
        $s->execute();
        $resu = $s->get_result();
        if ($resu && $rowu = $resu->fetch_assoc()) {
            $id_cliente = (int)$rowu['id_cliente'];
            if ($id_cliente) $_SESSION['usuario']['id_cliente'] = $id_cliente;
        }
        $s->close();
    }

    // 2) Si aún no, intentar buscar por email
    if (empty($id_cliente)) {
        $email = $_SESSION['usuario']['email'] ?? '';
        if ($email !== '') {
            $re = $conn->real_escape_string($email);
            $rq = $conn->query("SELECT id_cliente FROM cliente WHERE email = '$re' LIMIT 1");
            if ($rq && $ro = $rq->fetch_assoc()) {
                $id_cliente = (int)$ro['id_cliente'];
                $_SESSION['usuario']['id_cliente'] = $id_cliente;
            }
        }
    }

    // 3) Intentar por nombres y apellidos
    if (empty($id_cliente)) {
        $n = $conn->real_escape_string($_SESSION['usuario']['nombres'] ?? '');
        $a = $conn->real_escape_string($_SESSION['usuario']['apellidos'] ?? '');
        if ($n !== '') {
            $rq2 = $conn->query("SELECT id_cliente FROM cliente WHERE nombres = '$n' AND apellidos = '$a' LIMIT 1");
            if ($rq2 && $ro2 = $rq2->fetch_assoc()) {
                $id_cliente = (int)$ro2['id_cliente'];
                $_SESSION['usuario']['id_cliente'] = $id_cliente;
            }
        }
    }
}

// Obtener tipos de habitación
$tipos = [];
$res = $conn->query("SELECT id_tipo, nombre, precio_noche FROM tipo_habitacion WHERE estado='activo'");
while ($row = $res->fetch_assoc()) {
    $tipos[] = $row;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tipo = intval($_POST['id_tipo']);
    $fecha_entrada = $_POST['fecha_entrada'];
    $fecha_salida = $_POST['fecha_salida'];
    if (!$id_cliente) {
        $error = 'No se identificó al cliente en sesión.';
    } elseif (!$fecha_entrada || !$fecha_salida || strtotime($fecha_salida) <= strtotime($fecha_entrada)) {
        $error = 'Fechas inválidas. Asegúrese que la fecha de salida sea posterior a la de entrada.';
    } else {
        // Calcular noches
        $d1 = new DateTime($fecha_entrada);
        $d2 = new DateTime($fecha_salida);
        $interval = $d1->diff($d2);
        $noches = (int)$interval->days;

        // Obtener precio por noche
        $stmt = $conn->prepare("SELECT precio_noche FROM tipo_habitacion WHERE id_tipo=? AND estado='activo'");
        $stmt->bind_param('i', $id_tipo);
        $stmt->execute();
        $r = $stmt->get_result();
        if ($r->num_rows === 0) {
            $error = 'Tipo de habitación no válido.';
        } else {
            $row = $r->fetch_assoc();
            $precio = (float)$row['precio_noche'];
            $total = $precio * $noches;

            // Generar código simple
            $codigo = 'R' . time() . rand(100,999);

            $stmt2 = $conn->prepare("INSERT INTO reserva (codigo, id_cliente, fecha_entrada, fecha_salida, num_noches, total, tipo) VALUES (?, ?, ?, ?, ?, ?, 'web')");
            $stmt2->bind_param('sissid', $codigo, $id_cliente, $fecha_entrada, $fecha_salida, $noches, $total);
            if ($stmt2->execute()) {
                header('Location: mis-reservas.php?ok=1');
                exit;
            } else {
                $error = 'Error al guardar la reserva.';
            }
        }
    }
}
?>
<main class="container">
    <h2>Nueva reserva</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Tipo de habitación:</label><br>
        <select name="id_tipo" required>
            <option value="">-- Seleccione --</option>
            <?php foreach ($tipos as $t): ?>
                <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['nombre']) ?> - S/ <?= number_format($t['precio_noche'],2) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Fecha entrada:</label><br>
        <input type="date" name="fecha_entrada" required><br><br>

        <label>Fecha salida:</label><br>
        <input type="date" name="fecha_salida" required><br><br>

        <button type="submit">Reservar</button>
    </form>
    <p><a href="index.php">Volver al panel</a></p>
</main>
<style>
    <!-- CSS agregado -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }
    main.container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
    }
    form label {
        font-weight: bold;
        display: block;
        margin-top: 15px;
        margin-bottom: 5px;
    }
    form select, form input[type="date"] {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }
    button {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        background-color: #007BFF;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
    }
    button:hover {
        background-color: #0056b3;
    }
    p {
        text-align: center;
        margin-top: 20px;
    }
    a {
        color: #007BFF;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    p.error {
        color: #dc3545;
        font-weight: bold;
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

</style>
<?php include '../includes/footer.php'; ?>
