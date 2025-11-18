<?php
include '../config.php';
include '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'];

// Obtener alquiler activo
$sql = "SELECT id_alquiler FROM alquiler WHERE id_cliente=? AND estado='activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p>No tienes hospedajes activos.</p>";
    include '../includes/footer.php';
    exit;
}

$alquiler = $res->fetch_assoc()['id_alquiler'];

// Productos disponibles
$productos = $conn->query("SELECT id_producto, nombre, precio FROM producto WHERE estado='activo' ORDER BY nombre");

// Enviar solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['productos'])) {
    $codigo = 'SRV' . time();
    $total = 0;

    // Calcular total
    foreach ($_POST['productos'] as $id_producto) {
        $stmt = $conn->prepare("SELECT precio FROM producto WHERE id_producto=?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $res_p = $stmt->get_result()->fetch_assoc();
        $total += $res_p['precio'];
    }

    // Insertar solicitud
    $stmt = $conn->prepare("INSERT INTO servicio_habitacion (codigo, id_alquiler, total) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $codigo, $alquiler, $total);
    $stmt->execute();

    $msg = "Solicitud registrada correctamente. Código: $codigo. Total: " . formatoMoneda($total);
}
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
    .servicio-form {
        max-width: 800px;
        margin: 20px auto;
        padding: 0 15px;
    }
    .productos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
    }
    .producto-card {
        display: flex;
        flex-direction: column;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 15px;
        background-color: #f9f9f9;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .producto-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .producto-card input {
        margin-bottom: 10px;
    }
    .producto-card .nombre {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .producto-card .precio {
        color: #004080;
    }
    .success {
        color: green;
        font-weight: bold;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    form input, form select, form textarea {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }
    form button {
        background-color: #004080;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 6px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    form button:hover {
        background-color: #0066cc;
    }
</style>
<h2>Solicitar servicio a la habitación</h2>
<?php if (isset($msg)) echo "<p class='success'>$msg</p>"; ?>

<form method="POST" class="servicio-form">
    <p>Selecciona los productos que deseas pedir:</p>
    <div class="productos-grid">
        <?php while ($p = $productos->fetch_assoc()): ?>
            <label class="producto-card">
                <input type="checkbox" name="productos[]" value="<?= $p['id_producto'] ?>">
                <span class="nombre"><?= htmlspecialchars($p['nombre']) ?></span>
                <span class="precio"><?= formatoMoneda($p['precio']) ?></span>
            </label>
        <?php endwhile; ?>
    </div>
    <br>
    <button type="submit">Solicitar servicio</button>
</form>

<?php include '../includes/footer.php'; ?>
