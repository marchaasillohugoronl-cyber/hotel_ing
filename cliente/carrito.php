<?php
require_once '../config.php';
require_once '../includes/funciones.php';
verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

$cart = $_SESSION['cart'];
$items = [];
$total = 0.0;
if (!empty($cart)){
    $ids = array_keys($cart);
    $ids_list = implode(',', array_map('intval',$ids));
    $res = $conn->query("SELECT id_producto, nombre, precio, stock FROM producto WHERE id_producto IN ($ids_list)");
    $map = [];
    while($r = $res->fetch_assoc()) $map[$r['id_producto']] = $r;
    foreach($cart as $pid => $q){
        if (!isset($map[$pid])) continue;
        $p = $map[$pid];
        $subtotal = $p['precio'] * $q;
        $items[] = ['id_producto'=>$pid,'nombre'=>$p['nombre'],'precio'=>$p['precio'],'cantidad'=>$q,'subtotal'=>$subtotal,'stock'=>$p['stock']];
        $total += $subtotal;
    }
}

?>
<main class="container cliente-page">
    <h2>Carrito de compras</h2>
    <?php if (empty($items)): ?>
        <p class="cart-empty">El carrito está vacío. <a href="solicitar-servicio.php">Ver productos</a></p>
    <?php else: ?>
        <table class="cart-table">
            <thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead>
            <tbody>
            <?php foreach($items as $it): ?>
                <tr>
                    <td><?= htmlspecialchars($it['nombre']) ?></td>
                    <td>S/ <?= number_format($it['precio'],2) ?></td>
                    <td><input type="number" min="1" max="<?= (int)$it['stock'] ?>" value="<?= (int)$it['cantidad'] ?>" class="qty-input" data-id="<?= $it['id_producto'] ?>"></td>
                    <td>S/ <?= number_format($it['subtotal'],2) ?></td>
                    <td><button class="btn btn-outline btn-update" data-id="<?= $it['id_producto'] ?>">Actualizar</button> <button class="btn btn-danger btn-remove" data-id="<?= $it['id_producto'] ?>">Eliminar</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div><strong>Total: S/ <?= number_format($total,2) ?></strong></div>
            <form id="checkoutForm" method="POST" action="checkout.php" style="display:inline-block; margin-left:12px">
                <label><input type="checkbox" name="pay_now" id="pay_now"> Pagar ahora</label>
                <select name="metodo_pago" id="metodo_pago_select" style="display:none; margin-left:8px">
                <?php
                $mps = $conn->query("SELECT id_metodo, nombre FROM metodo_pago WHERE estado='activo' ORDER BY nombre");
                while($mp = $mps->fetch_assoc()):
                ?>
                    <option value="<?= (int)$mp['id_metodo'] ?>"><?= htmlspecialchars($mp['nombre']) ?></option>
                <?php endwhile; ?>
                </select>
                <input type="hidden" name="action" value="checkout">
                <button type="submit" class="btn btn-primary" style="margin-left:10px">Finalizar compra</button>
            </form>

            <button id="btn-clear" class="btn btn-outline" style="margin-left:8px">Vaciar carrito</button>
        </div>
    <?php endif; ?>

    <p style="margin-top:14px"><a href="solicitar-servicio.php">Seguir comprando</a> | <a href="mis-pedidos.php">Mis pedidos</a></p>
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

.container.cliente-page {
    max-width: 1100px;
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
   TABLA DE CARRITO
   ============================================ */
.cart-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1.5rem;
}

.cart-table th, .cart-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.cart-table th {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    font-weight: 600;
}

.cart-table td:nth-child(2),
.cart-table td:nth-child(4) {
    text-align: right; /* Precios y subtotal a la derecha */
    font-weight: 600;
    color: #10b981;
}

.cart-table input.qty-input {
    width: 60px;
    padding: 0.3rem 0.5rem;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    text-align: center;
}

/* ============================================
   BOTONES
   ============================================ */
.btn {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99,102,241,0.3);
}

.btn-outline {
    background: white;
    border: 2px solid #6366f1;
    color: #6366f1;
}

.btn-outline:hover {
    background: #6366f1;
    color: white;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239,68,68,0.3);
}

/* ============================================
   RESUMEN DEL CARRITO
   ============================================ */
.cart-summary {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    font-size: 1.1rem;
    margin-top: 1rem;
}

/* ============================================
   ENLACES
   ============================================ */
a {
    color: #6366f1;
    text-decoration: none;
}

a:hover {
    color: #4f46e5;
}

/* ============================================
   MENSAJE CARRITO VACÍO
   ============================================ */
.cart-empty {
    font-size: 1.1rem;
    color: #64748b;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media(max-width:768px){
    .cart-table th, .cart-table td {
        padding: 0.5rem;
    }

    .cart-table input.qty-input {
        width: 50px;
    }

    .cart-summary {
        flex-direction: column;
        align-items: flex-start;
    }

    .cart-summary button,
    .cart-summary form {
        width: 100%;
        margin-top: 5px;
    }
}

    </style>
</main>

<?php include '../includes/footer.php'; ?>
<script>
var payNowEl = document.getElementById('pay_now');
if (payNowEl) {
    payNowEl.addEventListener('change', function(){
        var sel = document.getElementById('metodo_pago_select'); if(sel) sel.style.display = this.checked? 'inline-block':'none';
    });
}

async function postAction(fd){
    const res = await fetch('cart_api.php',{method:'POST',body:fd});
    return await res.json();
}

document.querySelectorAll('.btn-update').forEach(btn=>btn.addEventListener('click', async (e)=>{
    const id = e.currentTarget.dataset.id;
    const input = document.querySelector('.qty-input[data-id="'+id+'"]');
    const qty = parseInt(input.value) || 1;
    const fd = new FormData(); fd.append('action','update'); fd.append('id',id); fd.append('qty',qty);
    const j = await postAction(fd);
    if (j.status==='ok'){
        if (window.updateCartCounter) updateCartCounter();
        if (window.showCartToast) showCartToast('Cantidad actualizada');
        location.reload();
    } else {
        if (window.showCartToast) showCartToast('Error actualizando'); else alert('Error');
    }
}));

document.querySelectorAll('.btn-remove').forEach(btn=>btn.addEventListener('click', async (e)=>{
    const id = e.currentTarget.dataset.id;
    const fd = new FormData(); fd.append('action','remove'); fd.append('id',id);
    const j = await postAction(fd);
    if (j.status==='ok'){
        if (window.updateCartCounter) updateCartCounter();
        if (window.showCartToast) showCartToast('Producto eliminado');
        location.reload();
    } else {
        if (window.showCartToast) showCartToast('Error eliminando'); else alert('Error');
    }
}));

var btnClear = document.getElementById('btn-clear');
if (btnClear) {
    btnClear.addEventListener('click', async ()=>{
        if(!confirm('Vaciar carrito?')) return;
        const fd = new FormData(); fd.append('action','clear');
        const j = await postAction(fd);
        if (j.status==='ok'){
            if (window.updateCartCounter) updateCartCounter();
            if (window.showCartToast) showCartToast('Carrito vaciado');
            location.reload();
        }
    });
}

</script>
