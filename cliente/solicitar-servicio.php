<?php
require_once '../config.php';
require_once '../includes/funciones.php';

verificarLogin();
verificarRol('cliente');
include '../includes/header.php';

$id_cliente = $_SESSION['usuario']['id_cliente'] ?? null;

// Obtener productos activos
$productos = [];
$res = $conn->query("SELECT id_producto, codigo, nombre, precio, stock, imagen FROM producto WHERE estado='activo' ORDER BY nombre");
while ($row = $res->fetch_assoc()) {
    $productos[] = $row;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cantidades = $_POST['cantidad'] ?? [];
    $items = [];
    $total = 0.0;

    foreach ($cantidades as $idp => $cant) {
        $idp = intval($idp);
        $cant = intval($cant);
        if ($cant > 0) {
            $stmt = $conn->prepare("SELECT nombre, precio, stock FROM producto WHERE id_producto=? AND estado='activo'");
            $stmt->bind_param('i', $idp);
            $stmt->execute();
            $r = $stmt->get_result();
            if ($r->num_rows === 0) continue;
            $p = $r->fetch_assoc();
            if ($cant > (int)$p['stock']) {
                $error = 'Stock insuficiente para: ' . htmlspecialchars($p['nombre']);
                break;
            }
            $subtotal = $cant * (float)$p['precio'];
            $items[] = ['id_producto'=>$idp, 'cantidad'=>$cant, 'precio'=>$p['precio'], 'subtotal'=>$subtotal];
            $total += $subtotal;
        }
    }

    if (empty($items) && !$error) {
        $error = 'Seleccione al menos un producto con cantidad mayor a 0.';
    }

    if (!$error) {
        $conn->begin_transaction();
        try {
            $mp = $conn->query("SELECT id_metodo FROM metodo_pago WHERE estado='activo' LIMIT 1")->fetch_assoc();
            $id_metodo = $mp['id_metodo'] ?? 1;

            $codigo = 'V' . time() . rand(100,999);
            $stmtv = $conn->prepare("INSERT INTO venta_recepcion (codigo, id_cliente, total, id_metodo) VALUES (?, ?, ?, ?)");
            $stmtv->bind_param('sidi', $codigo, $id_cliente, $total, $id_metodo);
            $stmtv->execute();
            $id_venta = $stmtv->insert_id;

            $stmt_det = $conn->prepare("INSERT INTO venta_detalle (id_venta, id_producto, cantidad, precio, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt_upd = $conn->prepare("UPDATE producto SET stock = stock - ? WHERE id_producto = ?");
            foreach ($items as $it) {
                $stmt_det->bind_param('iiidd', $id_venta, $it['id_producto'], $it['cantidad'], $it['precio'], $it['subtotal']);
                $stmt_det->execute();
                $stmt_upd->bind_param('ii', $it['cantidad'], $it['id_producto']);
                $stmt_upd->execute();
            }

            if (!empty($_POST['pay_now'])) {
                $metodo_sel = intval($_POST['metodo_pago'] ?? $id_metodo);
                $pcodigo = 'P' . time() . rand(100,999);
                $tipo = 'venta';
                $monto_sql = floatval($total);
                $conn->query("INSERT INTO pago (codigo, tipo, id_referencia, id_metodo, monto, comprobante) VALUES ('$pcodigo','$tipo',$id_venta,$metodo_sel,$monto_sql,NULL)");
            }

            $conn->commit();
            $success = 'Pedido creado correctamente. Código: ' . htmlspecialchars($codigo) . (!empty($_POST['pay_now']) ? ' (Pago registrado)' : '');
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Error al procesar el pedido.';
        }
    }
}
?>

<main class="container cliente-page">
    <h2>Solicitar pedido (productos)</h2>
    <p><a href="carrito.php">Ver carrito</a></p>
    <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

    <form method="POST">
        <div class="product-grid">
            <?php foreach ($productos as $p): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($p['imagen']) && file_exists(__DIR__ . '/../' . $p['imagen'])): ?>
                            <img src="<?= URL_BASE . htmlspecialchars($p['imagen']) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>">
                        <?php else: ?>
                            <div class="image-placeholder">Imagen</div>
                        <?php endif; ?>
                    </div>
                    <h4><?= htmlspecialchars($p['nombre']) ?></h4>
                    <p class="muted">Código: <?= htmlspecialchars($p['codigo']) ?></p>
                    <div class="price">S/ <?= number_format($p['precio'],2) ?></div>
                    <div class="muted">Stock: <?= (int)$p['stock'] ?></div>
                    <div style="margin-top:8px">
                        <input type="number" name="cantidad[<?= $p['id_producto'] ?>]" min="0" max="<?= (int)$p['stock'] ?>" value="0" class="qty-input" data-id="<?= $p['id_producto'] ?>">
                    </div>
                    <div style="margin-top:10px; display:flex; gap:8px">
                        <button type="button" class="btn btn-primary btn-add-cart" data-id="<?= $p['id_producto'] ?>">Agregar</button>
                        <button type="submit" class="btn btn-outline" name="comprar" value="<?= $p['id_producto'] ?>">Solicitar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:12px">
            <label><input type="checkbox" name="pay_now" id="pay_now_checkbox"> Pagar ahora</label>
            <select name="metodo_pago" id="metodo_pago_select" style="margin-left:12px; display:none;">
                <?php
                $mps = $conn->query("SELECT id_metodo, nombre FROM metodo_pago WHERE estado='activo' ORDER BY nombre");
                while($mp = $mps->fetch_assoc()):
                ?>
                    <option value="<?= (int)$mp['id_metodo'] ?>"><?= htmlspecialchars($mp['nombre']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div style="margin-top:14px">
            <button type="submit" class="btn btn-primary">Realizar pedido</button>
        </div>
    </form>

    <p style="margin-top:18px"><a href="mis-pedidos.php">Ver mis pedidos</a> | <a href="index.php">Volver al panel</a></p>
</main>

<?php include '../includes/footer.php'; ?>
<style>
    /* ============================================
   PEDIDO DE PRODUCTOS - ESTILOS MODERNOS
   ============================================ */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --secondary: #8b5cf6;
    --accent: #ec4899;
    --success: #10b981;
    --warning: #f59e0b;
    --error: #ef4444;
    --dark: #0f172a;
    --light: #f8fafc;
    --gray: #64748b;
    --border: #e2e8f0;
}

* {
    font-family: 'Poppins', sans-serif;
}

/* Container */
.cliente-page {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 2rem;
}

.cliente-page h2 {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
}

.cliente-page > p:first-of-type {
    margin-bottom: 2rem;
}

.cliente-page > p a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    background: rgba(99, 102, 241, 0.1);
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.cliente-page > p a::before {
    content: '🛒';
}

.cliente-page > p a:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

/* Mensajes de alerta */
.cliente-page > p[style*="color:red"],
.cliente-page > p[style*="color:green"] {
    padding: 1.2rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    from { transform: translateY(-20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.cliente-page > p[style*="color:red"] {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
    color: var(--error) !important;
    border: 2px solid rgba(239, 68, 68, 0.3);
}

.cliente-page > p[style*="color:red"]::before {
    content: '❌';
    font-size: 1.5rem;
}

.cliente-page > p[style*="color:green"] {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
    color: var(--success) !important;
    border: 2px solid rgba(16, 185, 129, 0.3);
}

.cliente-page > p[style*="color:green"]::before {
    content: '✅';
    font-size: 1.5rem;
}

/* Product Grid */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

/* Product Card */
.product-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    border-color: var(--primary);
}

/* Product Image */
.product-image {
    height: 160px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.06), rgba(139, 92, 246, 0.06));
    border-radius: 15px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1rem;
    position: relative;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.image-placeholder {
    font-size: 2.4rem;
    color: var(--primary);
}

@keyframes shine {
    0% { transform: translateX(-100%) rotate(45deg); }
    100% { transform: translateX(100%) rotate(45deg); }
}

.product-card h4 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.product-card .muted {
    color: var(--gray);
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.product-card .price {
    font-size: 1.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 1rem 0;
}

/* Quantity Input */
.qty-input {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-size: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    font-weight: 600;
}

.qty-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

/* Buttons Container */
.product-card > div[style*="display:flex"] {
    display: flex !important;
    gap: 0.5rem !important;
    margin-top: 1rem !important;
}

/* Buttons */
.btn {
    padding: 0.8rem 1.2rem;
    border: none;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    flex: 1;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
}

.btn-outline {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-outline:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

.btn-add-cart::before {
    content: '🛒';
}

/* Payment Options */
.cliente-page > form > div[style*="margin-top:12px"] {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.cliente-page > form > div[style*="margin-top:12px"] label {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-weight: 600;
    color: var(--dark);
    cursor: pointer;
}

.cliente-page > form > div[style*="margin-top:12px"] input[type="checkbox"] {
    width: 1.3rem;
    height: 1.3rem;
    cursor: pointer;
    accent-color: var(--primary);
}

.cliente-page > form > div[style*="margin-top:12px"] select {
    padding: 0.7rem 1rem;
    border: 2px solid var(--border);
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
    cursor: pointer;
}

.cliente-page > form > div[style*="margin-top:12px"] select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

/* Submit Button Container */
.cliente-page > form > div[style*="margin-top:14px"] {
    margin-top: 2rem !important;
    display: flex;
    justify-content: center;
}

.cliente-page > form > div[style*="margin-top:14px"] .btn-primary {
    padding: 1.2rem 3rem;
    font-size: 1.1rem;
}

/* Bottom Links */
.cliente-page > p[style*="margin-top:18px"] {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    text-align: center;
    margin-top: 2rem !important;
}

.cliente-page > p[style*="margin-top:18px"] a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-block;
}

.cliente-page > p[style*="margin-top:18px"] a:hover {
    background: rgba(99, 102, 241, 0.1);
    transform: translateY(-2px);
}

/* Loading State */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn.loading {
    pointer-events: none;
}

.btn.loading::after {
    content: '';
    width: 16px;
    height: 16px;
    border: 2px solid white;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    margin-left: 0.5rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Stock Badge */
.product-card .muted:last-of-type {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    background: rgba(99, 102, 241, 0.1);
    color: var(--primary);
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

/* Responsive */
@media (max-width: 768px) {
    .cliente-page {
        padding: 0 1rem;
    }
    
    .cliente-page h2 {
        font-size: 2rem;
    }
    
    .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .product-card > div[style*="display:flex"] {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .product-grid {
        grid-template-columns: 1fr;
    }
    
    .cliente-page > form > div[style*="margin-top:12px"] {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .cliente-page > form > div[style*="margin-top:12px"] select {
        width: 100%;
    }
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

::-webkit-scrollbar-track {
    background: var(--light);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
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
<script>
document.getElementById('pay_now_checkbox').addEventListener('change', function(){
    document.getElementById('metodo_pago_select').style.display = this.checked ? 'inline-block' : 'none';
});

document.querySelectorAll('.btn-add-cart').forEach(btn=>{
    btn.addEventListener('click', async (e)=>{
        const id = e.currentTarget.dataset.id;
        const qty = parseInt(document.querySelector('.qty-input[data-id="'+id+'"]').value) || 1;
        const fd = new FormData();
        fd.append('action','add');
        fd.append('id', id);
        fd.append('qty', qty);
        try {
            const res = await fetch('cart_api.php', {method:'POST', body: fd});
            const j = await res.json();
            if (j.status === 'ok') {



                
                // Producto agregado, recargar la página
                location.reload();
            } else {
                alert('Error agregando al carrito');
            }
        } catch(err) {
            alert('Error de comunicación');
        }
    });
});
</script>
