<?php
require_once '../config.php';
require_once '../includes/funciones.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

// Ensure cart exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];

$action = $_POST['action'] ?? ($_GET['action'] ?? 'get');
switch ($action) {
    case 'add':
        $id = intval($_POST['id'] ?? 0);
        $qty = max(0, intval($_POST['qty'] ?? 1));
        if ($id<=0 || $qty<=0) { echo json_encode(['status'=>'error','message'=>'Parametros invalidos']); exit; }
        if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] += $qty; else $_SESSION['cart'][$id] = $qty;
        echo json_encode(['status'=>'ok','cart'=>$_SESSION['cart']]); exit;
    case 'update':
        $id = intval($_POST['id'] ?? 0);
        $qty = max(0, intval($_POST['qty'] ?? 0));
        if ($id<=0) { echo json_encode(['status'=>'error']); exit; }
        if ($qty<=0) { unset($_SESSION['cart'][$id]); } else { $_SESSION['cart'][$id] = $qty; }
        echo json_encode(['status'=>'ok','cart'=>$_SESSION['cart']]); exit;
    case 'remove':
        $id = intval($_POST['id'] ?? 0);
        if ($id>0) unset($_SESSION['cart'][$id]);
        echo json_encode(['status'=>'ok','cart'=>$_SESSION['cart']]); exit;
    case 'clear':
        $_SESSION['cart'] = []; echo json_encode(['status'=>'ok']); exit;
    case 'get':
    default:
        // return cart with product details
        $out = ['items'=>[], 'total'=>0];
        if (!empty($_SESSION['cart'])){
            $ids = array_keys($_SESSION['cart']);
            $ids_list = implode(',', array_map('intval',$ids));
            $sql = "SELECT id_producto, nombre, precio, stock, codigo FROM producto WHERE id_producto IN ($ids_list)";
            $res = $conn->query($sql);
            $map = [];
            while($r = $res->fetch_assoc()) $map[$r['id_producto']] = $r;
            foreach($_SESSION['cart'] as $pid => $q){
                if (!isset($map[$pid])) continue;
                $p = $map[$pid];
                $subtotal = $p['precio'] * $q;
                $out['items'][] = ['id_producto'=>$pid,'nombre'=>$p['nombre'],'precio'=>$p['precio'],'cantidad'=>$q,'subtotal'=>$subtotal,'stock'=>$p['stock']];
                $out['total'] += $subtotal;
            }
        }
        echo json_encode(['status'=>'ok','cart'=>$out]); exit;
}
