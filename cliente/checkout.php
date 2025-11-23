<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');

if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: carrito.php'); exit;
}

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;
$action = $_POST['action'] ?? '';
$pay_now = !empty($_POST['pay_now']);
$metodo_pago = intval($_POST['metodo_pago'] ?? 0) ?: null;

// Build items from session
$cart = $_SESSION['cart'];
$ids = array_keys($cart);
if (empty($ids)) { header('Location: carrito.php'); exit; }

$ids_list = implode(',', array_map('intval',$ids));
$res = $conn->query("SELECT id_producto, nombre, precio, stock FROM producto WHERE id_producto IN ($ids_list) FOR UPDATE");
$map = [];
while($r = $res->fetch_assoc()) $map[$r['id_producto']] = $r;

$items = [];
$total = 0.0;
foreach($cart as $pid => $q){
    $pid = intval($pid); $q = intval($q);
    if (!isset($map[$pid])) { $error = 'Producto no encontrado.'; break; }
    $p = $map[$pid];
    if ($q > (int)$p['stock']) { $error = 'Stock insuficiente para: ' . htmlspecialchars($p['nombre']); break; }
    $subtotal = $p['precio'] * $q;
    $items[] = ['id_producto'=>$pid,'cantidad'=>$q,'precio'=>$p['precio'],'subtotal'=>$subtotal];
    $total += $subtotal;
}

if (!empty($error)) {
    $_SESSION['checkout_error'] = $error;
    header('Location: carrito.php'); exit;
}

// Insert sale
$conn->begin_transaction();
try{
    $mp = $conn->query("SELECT id_metodo FROM metodo_pago WHERE estado='activo' LIMIT 1")->fetch_assoc();
    $id_metodo = $mp['id_metodo'] ?? ($metodo_pago ?: 1);

    $codigo = 'V' . time() . rand(100,999);
    $stmtv = $conn->prepare("INSERT INTO venta_recepcion (codigo, id_cliente, total, id_metodo) VALUES (?, ?, ?, ?)");
    $stmtv->bind_param('sidi', $codigo, $id_cliente, $total, $id_metodo);
    $stmtv->execute();
    $id_venta = $stmtv->insert_id;

    $stmt_det = $conn->prepare("INSERT INTO venta_detalle (id_venta, id_producto, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_upd = $conn->prepare("UPDATE producto SET stock = stock - ? WHERE id_producto = ?");
    foreach($items as $it){
        $stmt_det->bind_param('iiidd', $id_venta, $it['id_producto'], $it['cantidad'], $it['precio'], $it['subtotal']);
        $stmt_det->execute();
        $stmt_upd->bind_param('ii', $it['cantidad'], $it['id_producto']);
        $stmt_upd->execute();
    }

    // Register payment if requested
    if ($pay_now) {
        $metodo_sel = $metodo_pago ?: $id_metodo;
        $pcodigo = 'P' . time() . rand(100,999);
        $pcodigo = $conn->real_escape_string($pcodigo);
        $tipo = 'venta';
        $monto_sql = floatval($total);
        $conn->query("INSERT INTO pago (codigo,tipo,id_referencia,id_metodo,monto,comprobante) VALUES ('$pcodigo','$tipo',$id_venta,$metodo_sel,$monto_sql, NULL)");
    }

    $conn->commit();
    // Clear cart
    $_SESSION['cart'] = [];
    $_SESSION['checkout_success'] = 'Compra finalizada. Código: ' . htmlspecialchars($codigo) . ($pay_now ? ' (Pago registrado)' : '');
    header('Location: mis-pedidos.php'); exit;

} catch(Exception $e){
    $conn->rollback();
    $_SESSION['checkout_error'] = 'Error procesando la compra.';
    header('Location: carrito.php'); exit;
}

?>
