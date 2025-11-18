<?php
include '../config.php';
include '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_entrada = $_POST['fecha_entrada'];
    $fecha_salida  = $_POST['fecha_salida'];
    $id_tipo       = $_POST['id_tipo'];
    $id_cliente    = $_SESSION['usuario']['id_cliente'];

    // Validar fechas
    if (strtotime($fecha_salida) <= strtotime($fecha_entrada)) {
        $error = "La fecha de salida debe ser posterior a la fecha de entrada.";
    } else {
        // Calcular noches
        $noches = (strtotime($fecha_salida) - strtotime($fecha_entrada)) / (60*60*24);

        // Obtener precio base
        $stmt_tipo = $conn->prepare("SELECT precio_noche FROM tipo_habitacion WHERE id_tipo=? AND estado='activo'");
        $stmt_tipo->bind_param("i", $id_tipo);
        $stmt_tipo->execute();
        $res_tipo = $stmt_tipo->get_result();
        $precio = $res_tipo->fetch_assoc()['precio_noche'];
        $total  = $precio * $noches;

        // Insertar reserva
        $codigo = 'RSV' . time();
        $stmt = $conn->prepare("INSERT INTO reserva (codigo, id_cliente, fecha_entrada, fecha_salida, num_noches, total, estado, tipo) 
                                VALUES (?, ?, ?, ?, ?, ?, 'pendiente', 'web')");
        $stmt->bind_param("sissid", $codigo, $id_cliente, $fecha_entrada, $fecha_salida, $noches, $total);
        $stmt->execute();

        $msg = " Reserva registrada correctamente. Código: $codigo (Total: ".formatoMoneda($total).")";
    }
}

// Obtener tipos de habitación
$tipos = $conn->query("SELECT id_tipo, nombre, precio_noche FROM tipo_habitacion WHERE estado='activo'");
?>
<style>
    /* Formulario nueva reserva */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('../assets/img/fondo_hgeneral.png') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
}

.main-content {
    max-width: 700px;
    margin: 40px auto;
    background: rgba(255,255,255,0.96);
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    padding: 30px;
}

h2 {
    color: #004080;
    text-align: center;
    margin-bottom: 20px;
}

/* Formulario nueva reserva */
.nueva-reserva {
    max-width: 500px;
    margin: 40px auto;
    padding: 0 20px;
}

.nueva-reserva h2 {
    text-align: center;
    color: #004080;
    margin-bottom: 25px;
}

.reserva-form {
    background-color: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.reserva-form label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
}

.reserva-form input,
.reserva-form select {
    width: 100%;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

.reserva-form button {
    margin-top: 20px;
    width: 100%;
    background-color: #004080;
    color: #fff;
    padding: 12px;
    font-size: 1.1rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.reserva-form button:hover {
    background-color: #0066cc;
}

/* Mensajes */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: bold;
    text-align: center;
}

.alert.success {
    background-color: #28a745;
    color: #fff;
}

.alert.error {
    background-color: #dc3545;
    color: #fff;
}

</style>
<main class="main-content">
    <h2>Nueva Reserva</h2>

    <?php if (isset($msg)) echo "<div class='alert success'>$msg</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

    <form method="POST" class="reserva-form">
        <label>Tipo de habitación:</label>
        <select name="id_tipo" required>
            <?php while ($t = $tipos->fetch_assoc()): ?>
                <option value="<?= $t['id_tipo'] ?>">
                    <?= $t['nombre'] ?> - <?= formatoMoneda($t['precio_noche']) ?>/noche
                </option>
            <?php endwhile; ?>
        </select>

        <label>Fecha de entrada:</label>
        <input type="date" name="fecha_entrada" required>

        <label>Fecha de salida:</label>
        <input type="date" name="fecha_salida" required>

        <button type="submit">Reservar</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
            <input type="date" name="fecha_entrada" required>
