<?php
// Dashboard SPA para Recepcionista - CRUD funcional (clientes, tipos, habitaciones)
include '../config.php';
include '../includes/funciones.php';
verificarLogin();
verificarRol('recepcionista');
header('X-Frame-Options: SAMEORIGIN');

// Manejo AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api'])) {
    header('Content-Type: application/json; charset=utf-8');
    $api = $_POST['api'];

    // Estadísticas simples
    if ($api === 'stats') {
        $usuarios = $conn->query("SELECT COUNT(*) AS c FROM usuario")->fetch_assoc()['c'] ?? 0;
        $clientes = $conn->query("SELECT COUNT(*) AS c FROM cliente")->fetch_assoc()['c'] ?? 0;
        $habitaciones = $conn->query("SELECT COUNT(*) AS c FROM habitacion")->fetch_assoc()['c'] ?? 0;
        $ocupadas = $conn->query("SELECT COUNT(*) AS c FROM habitacion WHERE estado = 'ocupada'")->fetch_assoc()['c'] ?? 0;
        echo json_encode(['status'=>'ok','usuarios'=>$usuarios,'clientes'=>$clientes,'habitaciones'=>$habitaciones,'ocupadas'=>$ocupadas]);
        exit;
    }

    // ---------------- CLIENTES ----------------
    if ($api === 'list_clientes') {
        $q = $conn->real_escape_string(trim($_POST['q'] ?? ''));
        $where = '';
        if ($q !== '') $where = "WHERE (num_doc LIKE '%$q%' OR nombres LIKE '%$q%' OR apellidos LIKE '%$q%' OR email LIKE '%$q%')";
        $res = $conn->query("SELECT * FROM cliente $where ORDER BY fecha_registro DESC LIMIT 200");
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode(['status'=>'ok','clientes'=>$rows]); exit;
    }

    if ($api === 'create_cliente') {
        $tipo = $conn->real_escape_string($_POST['tipo_doc'] ?? 'DNI');
        $num = $conn->real_escape_string($_POST['num_doc'] ?? '');
        $nombres = $conn->real_escape_string($_POST['nombres'] ?? '');
        $apellidos = $conn->real_escape_string($_POST['apellidos'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? null);
        $telefono = $conn->real_escape_string($_POST['telefono'] ?? null);
        $direccion = $conn->real_escape_string($_POST['direccion'] ?? null);
        $fecha_nac = $_POST['fecha_nacimiento'] ?: null;
        if ($num === '' || $nombres === '') { echo json_encode(['status'=>'error','message'=>'Documento y nombres requeridos']); exit; }
        $stmt = $conn->prepare("INSERT INTO cliente (tipo_doc,num_doc,nombres,apellidos,email,telefono,direccion,fecha_nacimiento) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssssssss',$tipo,$num,$nombres,$apellidos,$email,$telefono,$direccion,$fecha_nac);
        if ($stmt->execute()) echo json_encode(['status'=>'ok','id'=>$stmt->insert_id]); else echo json_encode(['status'=>'error','message'=>$stmt->error]);
        $stmt->close(); exit;
    }

    if ($api === 'get_cliente') {
        $id = intval($_POST['id'] ?? 0);
        $r = $conn->query("SELECT * FROM cliente WHERE id_cliente = $id");
        $c = $r->fetch_assoc(); if ($c) echo json_encode(['status'=>'ok','cliente'=>$c]); else echo json_encode(['status'=>'error']); exit;
    }

    if ($api === 'update_cliente') {
        $id = intval($_POST['id'] ?? 0);
        $tipo = $conn->real_escape_string($_POST['tipo_doc'] ?? 'DNI');
        $num = $conn->real_escape_string($_POST['num_doc'] ?? '');
        $nombres = $conn->real_escape_string($_POST['nombres'] ?? '');
        $apellidos = $conn->real_escape_string($_POST['apellidos'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? null);
        $telefono = $conn->real_escape_string($_POST['telefono'] ?? null);
        $direccion = $conn->real_escape_string($_POST['direccion'] ?? null);
        $fecha_nac = $_POST['fecha_nacimiento'] ?: null;
        $stmt = $conn->prepare("UPDATE cliente SET tipo_doc=?, num_doc=?, nombres=?, apellidos=?, email=?, telefono=?, direccion=?, fecha_nacimiento=? WHERE id_cliente=?");
        $stmt->bind_param('ssssssssi',$tipo,$num,$nombres,$apellidos,$email,$telefono,$direccion,$fecha_nac,$id);
        if ($stmt->execute()) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$stmt->error]);
        $stmt->close(); exit;
    }

    if ($api === 'delete_cliente') {
        $id = intval($_POST['id'] ?? 0);
        if ($conn->query("DELETE FROM cliente WHERE id_cliente = $id")) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$conn->error]); exit;
    }

    // --------------- TIPOS ---------------
    if ($api === 'list_tipos') {
        $res = $conn->query("SELECT * FROM tipo_habitacion ORDER BY nombre"); $rows = []; while ($r = $res->fetch_assoc()) $rows[] = $r; echo json_encode(['status'=>'ok','tipos'=>$rows]); exit;
    }
    if ($api === 'create_tipo') {
        $nombre = $conn->real_escape_string($_POST['nombre'] ?? ''); $cap = intval($_POST['capacidad'] ?? 1); $precio = floatval($_POST['precio'] ?? 0);
        if ($nombre==='') { echo json_encode(['status'=>'error','message'=>'Nombre requerido']); exit; }
        $stmt = $conn->prepare("INSERT INTO tipo_habitacion (nombre, descripcion, capacidad, precio_noche, estado) VALUES (?, '', ?, ?, 'activo')");
        $stmt->bind_param('sdi',$nombre,$cap,$precio); if ($stmt->execute()) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit;
    }
    if ($api === 'get_tipo') { $id = intval($_POST['id'] ?? 0); $r = $conn->query("SELECT * FROM tipo_habitacion WHERE id_tipo = $id"); $t = $r->fetch_assoc(); if ($t) echo json_encode(['status'=>'ok','tipo'=>$t]); else echo json_encode(['status'=>'error']); exit; }
    if ($api === 'update_tipo') { $id=intval($_POST['id']??0); $nombre=$conn->real_escape_string($_POST['nombre']??''); $cap=intval($_POST['capacidad']??1); $precio=floatval($_POST['precio']??0); $stmt=$conn->prepare("UPDATE tipo_habitacion SET nombre=?, capacidad=?, precio_noche=? WHERE id_tipo=?"); $stmt->bind_param('sidi',$nombre,$cap,$precio,$id); if($stmt->execute()) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit; }
    if ($api === 'delete_tipo') { $id=intval($_POST['id']??0); if($conn->query("DELETE FROM tipo_habitacion WHERE id_tipo=$id")) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }

    // --------------- HABITACIONES ---------------
    if ($api === 'list_habitaciones') {
        $res = $conn->query("SELECT h.*, t.nombre AS tipo_nombre FROM habitacion h LEFT JOIN tipo_habitacion t ON h.id_tipo = t.id_tipo ORDER BY h.numero"); $rows=[]; while($r=$res->fetch_assoc()) $rows[]=$r; echo json_encode(['status'=>'ok','habitaciones'=>$rows]); exit;
    }
    if ($api === 'create_habitacion') {
        $numero = $conn->real_escape_string($_POST['numero'] ?? ''); $id_tipo = intval($_POST['id_tipo'] ?? 0); $piso = intval($_POST['piso'] ?? 1); $estado = $conn->real_escape_string($_POST['estado'] ?? 'disponible');
        if ($numero===''||$id_tipo<=0) { echo json_encode(['status'=>'error','message'=>'Datos incompletos']); exit; }
        $stmt = $conn->prepare("INSERT INTO habitacion (numero, id_tipo, piso, estado) VALUES (?,?,?,?)"); $stmt->bind_param('siis',$numero,$id_tipo,$piso,$estado); if($stmt->execute()) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit;
    }
    if ($api === 'get_habitacion') { $id=intval($_POST['id']??0); $r=$conn->query("SELECT * FROM habitacion WHERE id_habitacion=$id"); $h=$r->fetch_assoc(); if($h) echo json_encode(['status'=>'ok','habitacion'=>$h]); else echo json_encode(['status'=>'error']); exit; }
    if ($api === 'update_habitacion') { $id=intval($_POST['id']??0); $numero=$conn->real_escape_string($_POST['numero']??''); $id_tipo=intval($_POST['id_tipo']??0); $piso=intval($_POST['piso']??1); $estado=$conn->real_escape_string($_POST['estado']??'disponible'); $stmt=$conn->prepare("UPDATE habitacion SET numero=?, id_tipo=?, piso=?, estado=? WHERE id_habitacion=?"); $stmt->bind_param('siisi',$numero,$id_tipo,$piso,$estado,$id); if($stmt->execute()) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit; }
    if ($api === 'delete_habitacion') { $id=intval($_POST['id']??0); if($conn->query("DELETE FROM habitacion WHERE id_habitacion=$id")) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }

    // --------------- CATEGORIAS DE PRODUCTO ---------------
    if ($api === 'list_categorias') {
        $res = $conn->query("SELECT * FROM categoria_producto ORDER BY nombre"); $rows = []; while ($r = $res->fetch_assoc()) $rows[] = $r; echo json_encode(['status'=>'ok','categorias'=>$rows]); exit;
    }
    if ($api === 'create_categoria') {
        $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
        $tipo = $conn->real_escape_string($_POST['tipo'] ?? 'comida');
        $desc = $conn->real_escape_string($_POST['descripcion'] ?? '');
        if ($nombre === '') { echo json_encode(['status'=>'error','message'=>'Nombre requerido']); exit; }
        $stmt = $conn->prepare("INSERT INTO categoria_producto (nombre, tipo, descripcion) VALUES (?,?,?)");
        $stmt->bind_param('sss',$nombre,$tipo,$desc);
        if ($stmt->execute()) echo json_encode(['status'=>'ok','id'=>$stmt->insert_id]); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit;
    }
    if ($api === 'get_categoria') { $id = intval($_POST['id'] ?? 0); $r = $conn->query("SELECT * FROM categoria_producto WHERE id_categoria = $id"); $t = $r->fetch_assoc(); if ($t) echo json_encode(['status'=>'ok','categoria'=>$t]); else echo json_encode(['status'=>'error']); exit; }
    if ($api === 'update_categoria') { $id = intval($_POST['id'] ?? 0); $nombre = $conn->real_escape_string($_POST['nombre'] ?? ''); $tipo = $conn->real_escape_string($_POST['tipo'] ?? 'comida'); $desc = $conn->real_escape_string($_POST['descripcion'] ?? ''); $stmt = $conn->prepare("UPDATE categoria_producto SET nombre=?, tipo=?, descripcion=? WHERE id_categoria=?"); $stmt->bind_param('sssi',$nombre,$tipo,$desc,$id); if ($stmt->execute()) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit; }
    if ($api === 'delete_categoria') { $id = intval($_POST['id'] ?? 0); if ($conn->query("DELETE FROM categoria_producto WHERE id_categoria=$id")) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }

    // --------------- PRODUCTOS ---------------
    if ($api === 'list_productos') {
        $res = $conn->query("SELECT p.*, c.nombre AS categoria_nombre FROM producto p LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria ORDER BY p.nombre"); $rows = []; while ($r = $res->fetch_assoc()) $rows[] = $r; echo json_encode(['status'=>'ok','productos'=>$rows]); exit;
    }
    if ($api === 'create_producto') {
        $codigo = $conn->real_escape_string($_POST['codigo'] ?? '');
        $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
        $id_cat = intval($_POST['id_categoria'] ?? 0);
        $precio = floatval($_POST['precio'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $desc = $conn->real_escape_string($_POST['descripcion'] ?? '');
        $estado = $conn->real_escape_string($_POST['estado'] ?? 'activo');
        if ($codigo === '' || $nombre === '' || $id_cat <= 0) { echo json_encode(['status'=>'error','message'=>'Datos incompletos']); exit; }
        $stmt = $conn->prepare("INSERT INTO producto (codigo,nombre,id_categoria,precio,stock,descripcion,estado) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param('ssidiss',$codigo,$nombre,$id_cat,$precio,$stock,$desc,$estado);
        if ($stmt->execute()) echo json_encode(['status'=>'ok','id'=>$stmt->insert_id]); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit;
    }
    if ($api === 'get_producto') { $id = intval($_POST['id'] ?? 0); $r = $conn->query("SELECT * FROM producto WHERE id_producto = $id"); $p = $r->fetch_assoc(); if ($p) echo json_encode(['status'=>'ok','producto'=>$p]); else echo json_encode(['status'=>'error']); exit; }
    if ($api === 'update_producto') { $id = intval($_POST['id'] ?? 0); $codigo = $conn->real_escape_string($_POST['codigo'] ?? ''); $nombre = $conn->real_escape_string($_POST['nombre'] ?? ''); $id_cat = intval($_POST['id_categoria'] ?? 0); $precio = floatval($_POST['precio'] ?? 0); $stock = intval($_POST['stock'] ?? 0); $desc = $conn->real_escape_string($_POST['descripcion'] ?? ''); $estado = $conn->real_escape_string($_POST['estado'] ?? 'activo'); $stmt = $conn->prepare("UPDATE producto SET codigo=?, nombre=?, id_categoria=?, precio=?, stock=?, descripcion=?, estado=? WHERE id_producto=?"); $stmt->bind_param('ssidissi',$codigo,$nombre,$id_cat,$precio,$stock,$desc,$estado,$id); if ($stmt->execute()) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$stmt->error]); $stmt->close(); exit; }
    if ($api === 'delete_producto') { $id = intval($_POST['id'] ?? 0); if ($conn->query("DELETE FROM producto WHERE id_producto=$id")) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }

    // --------------- RESERVAS ---------------
    if ($api === 'list_reservas') {
        $res = $conn->query("SELECT r.*, c.nombres, c.apellidos FROM reserva r LEFT JOIN cliente c ON r.id_cliente = c.id_cliente ORDER BY r.fecha_reserva DESC LIMIT 300"); $rows = []; while ($r = $res->fetch_assoc()) $rows[] = $r; echo json_encode(['status'=>'ok','reservas'=>$rows]); exit;
    }

    if ($api === 'search_disponibles') {
        $fecha_entrada = $conn->real_escape_string($_POST['fecha_entrada'] ?? '');
        $fecha_salida = $conn->real_escape_string($_POST['fecha_salida'] ?? '');
        $id_tipo = intval($_POST['id_tipo'] ?? 0);
        if ($fecha_entrada==='' || $fecha_salida==='') { echo json_encode(['status'=>'error','message'=>'Fechas requeridas']); exit; }
        // buscar habitaciones que no estén reservadas o alquiladas en el rango
        $condTipo = $id_tipo>0 ? "AND h.id_tipo = $id_tipo" : '';
        $sql = "SELECT h.*, t.precio_noche FROM habitacion h LEFT JOIN tipo_habitacion t ON h.id_tipo = t.id_tipo WHERE 1=1 $condTipo AND h.estado <> 'mantenimiento' AND h.id_habitacion NOT IN (
            SELECT rh.id_habitacion FROM reserva_habitacion rh JOIN reserva r ON rh.id_reserva = r.id_reserva WHERE r.estado <> 'cancelada' AND NOT (r.fecha_salida <= '$fecha_entrada' OR r.fecha_entrada >= '$fecha_salida')
        ) AND h.id_habitacion NOT IN (
            SELECT ah.id_habitacion FROM alquiler_habitacion ah JOIN alquiler a ON ah.id_alquiler = a.id_alquiler WHERE a.estado = 'activo' AND NOT (a.fecha_checkout_programado <= '$fecha_entrada' OR a.fecha_checkin >= '$fecha_salida')
        ) ORDER BY h.numero";
        $res = $conn->query($sql); $rows = []; while ($r = $res->fetch_assoc()) $rows[] = $r; echo json_encode(['status'=>'ok','habitaciones'=>$rows]); exit;
    }

    if ($api === 'create_reserva') {
        $id_cliente = intval($_POST['id_cliente'] ?? 0);
        $fecha_entrada = $conn->real_escape_string($_POST['fecha_entrada'] ?? '');
        $fecha_salida = $conn->real_escape_string($_POST['fecha_salida'] ?? '');
        $tipo_res = $conn->real_escape_string($_POST['tipo'] ?? 'presencial');
        $observ = $conn->real_escape_string($_POST['observaciones'] ?? '');
        $habitaciones = $_POST['habitaciones'] ?? [];
        if ($id_cliente<=0 || $fecha_entrada==='' || $fecha_salida==='' || empty($habitaciones)) { echo json_encode(['status'=>'error','message'=>'Datos incompletos']); exit; }
        $num_noches = max(1, intval((strtotime($fecha_salida) - strtotime($fecha_entrada)) / 86400));
        // calcular total
        $total = 0;
        $placeholders = [];
        foreach ($habitaciones as $hid) {
            $hid = intval($hid);
            $r = $conn->query("SELECT t.precio_noche FROM habitacion h LEFT JOIN tipo_habitacion t ON h.id_tipo = t.id_tipo WHERE h.id_habitacion = $hid");
            $p = $r->fetch_assoc(); $precio = $p['precio_noche'] ?? 0; $total += $precio * $num_noches; $placeholders[] = ['id'=>$hid,'precio'=>$precio];
        }
        $codigo = 'R'.time().rand(100,999);
        $stmt = $conn->prepare("INSERT INTO reserva (codigo,id_cliente,fecha_entrada,fecha_salida,num_noches,total,tipo,observaciones) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('sissidss',$codigo,$id_cliente,$fecha_entrada,$fecha_salida,$num_noches,$total,$tipo_res,$observ);
        if (!$stmt->execute()) { echo json_encode(['status'=>'error','message'=>$stmt->error]); exit; }
        $id_res = $stmt->insert_id; $stmt->close();
        foreach ($placeholders as $ph) {
            $stmt2 = $conn->prepare("INSERT INTO reserva_habitacion (id_reserva,id_habitacion,precio_noche) VALUES (?,?,?)");
            $stmt2->bind_param('iid',$id_res,$ph['id'],$ph['precio']); $stmt2->execute(); $stmt2->close();
        }
        echo json_encode(['status'=>'ok','id_reserva'=>$id_res]); exit;
    }

    if ($api === 'get_reserva') { $id = intval($_POST['id'] ?? 0); $r = $conn->query("SELECT r.*, c.nombres, c.apellidos FROM reserva r LEFT JOIN cliente c ON r.id_cliente=c.id_cliente WHERE r.id_reserva=$id"); $resv = $r->fetch_assoc(); if(!$resv){ echo json_encode(['status'=>'error']); exit; } $rh = $conn->query("SELECT rh.*, h.numero FROM reserva_habitacion rh LEFT JOIN habitacion h ON rh.id_habitacion=h.id_habitacion WHERE rh.id_reserva=$id"); $habs = []; while($x=$rh->fetch_assoc()) $habs[]=$x; echo json_encode(['status'=>'ok','reserva'=>$resv,'habitaciones'=>$habs]); exit; }

    if ($api === 'cancel_reserva') { $id = intval($_POST['id'] ?? 0); if($conn->query("UPDATE reserva SET estado='cancelada' WHERE id_reserva=$id")) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$conn->error]); exit; }

    echo json_encode(['status'=>'error','message'=>'accion no reconocida']); exit;
}

// ---------------- RENDER HTML ----------------
include '../includes/header.php';
?>
<link rel="stylesheet" href="../assets/css/admi.css">
<style>
/* small layout tweaks for dashboard */
.dash-grid { display:grid; grid-template-columns: repeat(4,1fr); gap:16px; margin-bottom:18px; }
.card { background:#fff; padding:18px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
.side-menu { width:260px; }
.main-area { flex:1; }
</style>

<div style="display:flex; gap:18px; align-items:flex-start;">
    <aside class="side-menu">
        <h3>Recepción</h3>
        <div style="display:flex; flex-direction:column; gap:8px;">
            <button class="btn" onclick="view('dashboard')">🏠 Dashboard</button>
            <button class="btn" onclick="view('clientes')">👥 Clientes</button>
            <button class="btn" onclick="view('tipos')">🏷️ Tipos Habitación</button>
            <button class="btn" onclick="view('habitaciones')">🏠 Habitaciones</button>
            <button class="btn" onclick="view('reservas')">📋 Reservas</button>
            <button class="btn" onclick="view('productos')">🛒 Productos</button>
            <button class="btn" onclick="view('categorias')">🏷️ Categorías</button>
        </div>
    </aside>

    <main class="main-area">
        <div id="page"></div>
    </main>
</div>

<script>
async function api(data){
    const res = await fetch(location.pathname.split('/').slice(0,-1).join('/') + '/dashboard.php', { method:'POST', body: data });
    return res.json();
}

function escapeHtml(s){ if(!s) return ''; return String(s).replace(/[&"'<>]/g, c=>({'&':'&amp;','"':'&quot;','\'':'&#39;','<':'&lt;','>':'&gt;'}[c])); }

async function view(name){
    if(name==='dashboard'){
        document.getElementById('page').innerHTML = '<h2>Dashboard</h2><div id="stats" class="dash-grid"></div>';
        const fd = new FormData(); fd.append('api','stats'); const j = await api(fd);
        if(j.status==='ok'){
            document.getElementById('stats').innerHTML = `
                <div class="card">Usuarios: <strong>${j.usuarios}</strong></div>
                <div class="card">Clientes: <strong>${j.clientes}</strong></div>
                <div class="card">Habitaciones: <strong>${j.habitaciones}</strong></div>
                <div class="card">Ocupadas: <strong>${j.ocupadas}</strong></div>
            `;
        }
        return;
    }

    if(name==='clientes'){
        document.getElementById('page').innerHTML = `
            <h2>Clientes</h2>
            <div style="margin-bottom:10px;"><button class="btn" onclick="renderNuevoCliente()">➕ Nuevo Cliente</button>
            <input id="qclientes" placeholder="buscar" style="margin-left:12px;padding:6px;" oninput="debQ()"></div>
            <div id="clientes-list">Cargando...</div>
        `;
        loadClientes(); return;
    }

    if(name==='tipos'){
        document.getElementById('page').innerHTML = '<h2>Tipos Habitación</h2><div id="tipos-list">Cargando...</div>';
        loadTipos(); return;
    }

    if(name==='habitaciones'){
        document.getElementById('page').innerHTML = '<h2>Habitaciones</h2><div id="habitaciones-list">Cargando...</div>';
        loadHabitaciones(); return;
    }

    if(name==='reservas'){
        document.getElementById('page').innerHTML = `
            <h2>Reservas</h2>
            <div style="margin-bottom:10px;">
              <button class="btn" onclick="renderNuevoReserva()">➕ Nueva Reserva</button>
              <button class="btn" onclick="loadReservas()">🔄 Refrescar</button>
            </div>
            <div id="reservas-list">Cargando...</div>
        `;
        loadReservas(); return;
    }

    if(name==='productos'){
        document.getElementById('page').innerHTML = `
            <h2>Productos</h2>
            <div style="margin-bottom:10px;"><button class="btn" onclick="renderNuevoProducto()">➕ Nuevo Producto</button></div>
            <div id="productos-list">Cargando...</div>
        `;
        loadProductos(); return;
    }

    if(name==='categorias'){
        document.getElementById('page').innerHTML = `
            <h2>Categorías de Productos</h2>
            <div style="margin-bottom:10px;"><button class="btn" onclick="renderNuevaCategoria()">➕ Nueva Categoría</button></div>
            <div id="categorias-list">Cargando...</div>
        `;
        loadCategorias(); return;
    }
}

// ---------- Clientes ----------
let debTimer;
function debQ(){ clearTimeout(debTimer); debTimer = setTimeout(()=> loadClientes(), 300); }
async function loadClientes(){ const q = document.getElementById('qclientes') ? document.getElementById('qclientes').value : ''; const fd = new FormData(); fd.append('api','list_clientes'); fd.append('q',q); const j = await api(fd); if(j.status==='ok'){ const rows = j.clientes; let html = '<table class="tabla-modal"><thead><tr><th>ID</th><th>Documento</th><th>Nombre</th><th>Tel</th><th>Email</th><th>Acción</th></tr></thead><tbody>'; for(const r of rows){ html += `<tr><td>${r.id_cliente}</td><td>${escapeHtml(r.tipo_doc+' '+r.num_doc)}</td><td>${escapeHtml(r.nombres+' '+r.apellidos)}</td><td>${escapeHtml(r.telefono)}</td><td>${escapeHtml(r.email)}</td><td><button class="btn-accion btn-editar" onclick="editCliente(${r.id_cliente})">Editar</button><button class="btn-accion btn-eliminar" onclick="delCliente(${r.id_cliente})">Eliminar</button></td></tr>`;} html += '</tbody></table>'; document.getElementById('clientes-list').innerHTML = html; } else document.getElementById('clientes-list').innerHTML = 'Error'; }

function renderNuevoCliente(){
        // open modal new client
        openClientModal('new');
}

async function submitNuevoCliente(e){
    // kept for backward compatibility if form is used elsewhere
    e.preventDefault(); const f=new FormData(document.getElementById('formNuevoCliente')); f.append('api','create_cliente'); const j=await api(f); if(j.status==='ok'){ alert('Cliente creado'); loadClientes(); } else alert('Error: '+(j.message||'')); return false;
}

async function editCliente(id){ const f=new FormData(); f.append('api','get_cliente'); f.append('id',id); const j=await api(f); if(j.status!=='ok'){ alert('No encontrado'); return; } const c=j.cliente; document.getElementById('page').innerHTML = `
    <h2>Editar Cliente</h2>
    <form id="formEditCliente" onsubmit="return submitEditCliente(event, ${id})">
      <div class="form-grupo"><label>Tipo doc</label><select name="tipo_doc"><option ${c.tipo_doc==='DNI'?'selected':''}>DNI</option><option ${c.tipo_doc==='CE'?'selected':''}>CE</option><option ${c.tipo_doc==='pasaporte'?'selected':''}>pasaporte</option></select></div>
      <div class="form-grupo"><label>Número doc</label><input name="num_doc" value="${escapeHtml(c.num_doc)}" required></div>
      <div class="form-row"><div class="form-grupo"><label>Nombres</label><input name="nombres" value="${escapeHtml(c.nombres)}" required></div><div class="form-grupo"><label>Apellidos</label><input name="apellidos" value="${escapeHtml(c.apellidos)}" required></div></div>
      <div class="form-row"><div class="form-grupo"><label>Email</label><input name="email" value="${escapeHtml(c.email)}"></div><div class="form-grupo"><label>Teléfono</label><input name="telefono" value="${escapeHtml(c.telefono)}"></div></div>
      <div class="form-grupo"><label>Dirección</label><input name="direccion" value="${escapeHtml(c.direccion)}"></div>
      <div class="form-grupo"><label>Fecha Nac</label><input name="fecha_nacimiento" type="date" value="${c.fecha_nacimiento||''}"></div>
      <button class="btn-submit">Guardar</button>
      <button type="button" class="btn-cancelar" onclick="view('clientes')">Cancelar</button>
    </form>
  `; }

async function submitEditCliente(e,id){ e.preventDefault(); const f=new FormData(document.getElementById('formEditCliente')); f.append('api','update_cliente'); f.append('id',id); const j=await api(f); if(j.status==='ok'){ alert('Actualizado'); view('clientes'); } else alert('Error: '+(j.message||'')); return false; }

async function delCliente(id){ if(!confirm('Eliminar cliente #'+id+'?')) return; const f=new FormData(); f.append('api','delete_cliente'); f.append('id',id); const j=await api(f); if(j.status==='ok') view('clientes'); else alert('Error'); }

// ---------- Reservas ----------
async function loadReservas(){
        const f = new FormData(); f.append('api','list_reservas'); const j = await api(f);
        const out = document.getElementById('reservas-list');
        if(j.status!=='ok'){ out.innerHTML = '<div class="alert error">Error cargando reservas</div>'; return; }
        let html = '<table class="tabla-modal"><thead><tr><th>ID</th><th>Código</th><th>Cliente</th><th>Entrada</th><th>Salida</th><th>Noches</th><th>Total</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';
        for(const r of j.reservas){
                html += `<tr><td>${r.id_reserva}</td><td>${escapeHtml(r.codigo)}</td><td>${escapeHtml((r.nombres||'')+' '+(r.apellidos||''))}</td><td>${r.fecha_entrada}</td><td>${r.fecha_salida}</td><td>${r.num_noches}</td><td>${r.total}</td><td>${r.estado||''}</td><td><button class="btn-accion" onclick="viewReserva(${r.id_reserva})">Ver</button>${r.estado!=='cancelada'?` <button class="btn-accion btn-eliminar" onclick="cancelReserva(${r.id_reserva})">Cancelar</button>`:''}</td></tr>`;
        }
        html += '</tbody></table>';
        out.innerHTML = html;
}

function renderNuevoReserva(){
        document.getElementById('page').innerHTML = `
            <h2>Nueva Reserva</h2>
            <div style="max-width:700px">
                <form id="frmNewReserva" onsubmit="return buscarDisponiblesReserva(event)">
                    <label>Cliente:</label>
                    <select id="sel_cliente" name="id_cliente" required><option value="">Cargando...</option></select>
                    <label>Tipo habitación:</label>
                    <select id="sel_tipo" name="id_tipo"><option value="0">-- Cualquiera --</option></select>
                    <label>Fecha entrada:</label><input type="date" id="res_fecha_entrada" name="fecha_entrada" required>
                    <label>Fecha salida:</label><input type="date" id="res_fecha_salida" name="fecha_salida" required>
                    <div style="margin-top:8px"><button class="btn">Buscar habitaciones</button> <button type="button" class="btn-cancelar" onclick="view('reservas')">Cancelar</button></div>
                </form>
                <div id="reservas-disponibles" style="margin-top:12px"></div>
            </div>
        `;
        // load clientes and tipos
        (async()=>{
                const f1 = new FormData(); f1.append('api','list_clientes'); const jc = await api(f1);
                const selc = document.getElementById('sel_cliente'); selc.innerHTML = '';
                if(jc.status==='ok') for(const c of jc.clientes) selc.innerHTML += `<option value="${c.id_cliente}">${escapeHtml(c.nombres+' '+c.apellidos)} (${escapeHtml(c.num_doc)})</option>`;
                const f2 = new FormData(); f2.append('api','list_tipos'); const jt = await api(f2);
                const selt = document.getElementById('sel_tipo'); if(jt.status==='ok') for(const t of jt.tipos) selt.innerHTML += `<option value="${t.id_tipo}">${escapeHtml(t.nombre)} - ${t.precio_noche}</option>`;
        })();
}

async function buscarDisponiblesReserva(e){
        e && e.preventDefault();
        const fecha_entrada = document.getElementById('res_fecha_entrada').value;
        const fecha_salida = document.getElementById('res_fecha_salida').value;
        const id_tipo = document.getElementById('sel_tipo').value;
        if(!fecha_entrada||!fecha_salida){ alert('Seleccione fechas'); return false; }
        const f = new FormData(); f.append('api','search_disponibles'); f.append('fecha_entrada',fecha_entrada); f.append('fecha_salida',fecha_salida); f.append('id_tipo',id_tipo);
        const j = await api(f); const cont = document.getElementById('reservas-disponibles');
        if(j.status!=='ok'){ cont.innerHTML = '<div class="alert error">'+(j.message||'Error')+'</div>'; return false; }
        if(j.habitaciones.length===0){ cont.innerHTML = '<div class="alert">No hay habitaciones disponibles.</div>'; return false; }
        let html = '<form id="frmConfirmReserva" onsubmit="return confirmarReservaDesdeRecepcion(event)">';
        html += '<h3>Habitaciones disponibles</h3><div style="display:flex;flex-direction:column;gap:6px">';
        for(const h of j.habitaciones){ html += `<label><input type="checkbox" name="hab" value="${h.id_habitacion}"> Habit. ${escapeHtml(h.numero)} — Precio: ${h.precio_noche}</label>`; }
        html += '</div><label>Observaciones:</label><textarea id="res_observaciones" style="width:100%"></textarea><div style="margin-top:8px"><button class="btn">Confirmar reserva</button></div></form>';
        cont.innerHTML = html; return false;
}

async function confirmarReservaDesdeRecepcion(e){
        e && e.preventDefault();
        const checks = Array.from(document.querySelectorAll('#frmConfirmReserva input[name="hab"]:checked')).map(n=>n.value);
        if(checks.length===0){ alert('Seleccione al menos una habitación'); return false; }
        const id_cliente = document.getElementById('sel_cliente').value;
        if(!id_cliente){ alert('Seleccione un cliente'); return false; }
        const fecha_entrada = document.getElementById('res_fecha_entrada').value;
        const fecha_salida = document.getElementById('res_fecha_salida').value;
        const observ = document.getElementById('res_observaciones').value || '';
        const f = new FormData(); f.append('api','create_reserva'); f.append('id_cliente', id_cliente); f.append('fecha_entrada', fecha_entrada); f.append('fecha_salida', fecha_salida); f.append('tipo','presencial'); for(const h of checks) f.append('habitaciones[]', h); f.append('observaciones', observ);
        const j = await api(f);
        if(j.status==='ok'){ alert('Reserva creada (ID: '+j.id_reserva+')'); loadReservas(); view('reservas'); } else alert('Error: '+(j.message||''));
        return false;
}

async function viewReserva(id){ const f=new FormData(); f.append('api','get_reserva'); f.append('id',id); const j=await api(f); if(j.status!=='ok'){ alert('No encontrado'); return; } const r=j.reserva; let html = `<h3>Reserva ${r.codigo}</h3><p>Cliente: ${escapeHtml(r.nombres+' '+r.apellidos)}</p><p>Entrada: ${r.fecha_entrada} - Salida: ${r.fecha_salida}</p><p>Total: ${r.total}</p>`; html += '<h4>Habitaciones</h4><ul>'; for(const h of j.habitaciones) html += `<li>Habit. ${escapeHtml(h.numero)} — Precio: ${h.precio_noche}</li>`; html += '</ul><div><button class="btn" onclick="loadReservas();view(\'reservas\')">Volver</button></div>'; document.getElementById('page').innerHTML = html; }

async function cancelReserva(id){ if(!confirm('Cancelar reserva #'+id+'?')) return; const f=new FormData(); f.append('api','cancel_reserva'); f.append('id',id); const j = await api(f); if(j.status==='ok'){ alert('Cancelada'); loadReservas(); } else alert('Error: '+(j.message||'')); }

// ---------- Tipos ----------
async function loadTipos(){ const f=new FormData(); f.append('api','list_tipos'); const j=await api(f); if(j.status==='ok'){ let html = '<button class="btn" onclick="renderNuevoTipo()">➕ Nuevo Tipo</button>'; html += '<table class="tabla-modal"><thead><tr><th>ID</th><th>Nombre</th><th>Cap</th><th>Precio</th><th>Acción</th></tr></thead><tbody>'; for(const t of j.tipos){ html += `<tr><td>${t.id_tipo}</td><td>${escapeHtml(t.nombre)}</td><td>${t.capacidad}</td><td>${t.precio_noche}</td><td><button class="btn-accion btn-editar" onclick="editTipo(${t.id_tipo})">Editar</button><button class="btn-accion btn-eliminar" onclick="delTipo(${t.id_tipo})">Eliminar</button></td></tr>`;} html += '</tbody></table>'; document.getElementById('tipos-list').innerHTML = html; } }

function renderNuevoTipo(){ document.getElementById('page').innerHTML = `
    <h2>Nuevo Tipo</h2>
    <form id="formNuevoTipo" onsubmit="return submitNuevoTipo(event)">
      <div class="form-grupo"><label>Nombre</label><input name="nombre" required></div>
      <div class="form-grupo"><label>Capacidad</label><input name="capacidad" type="number" value="1" min="1" required></div>
      <div class="form-grupo"><label>Precio</label><input name="precio" type="number" step="0.01" value="0.00" required></div>
      <button class="btn-submit">Guardar</button>
      <button type="button" class="btn-cancelar" onclick="view('tipos')">Cancelar</button>
    </form>
  `; }

async function submitNuevoTipo(e){ e.preventDefault(); const f=new FormData(document.getElementById('formNuevoTipo')); f.append('api','create_tipo'); const j=await api(f); if(j.status==='ok'){ alert('Creado'); view('tipos'); } else alert('Error: '+(j.message||'')); return false; }

async function editTipo(id){ const f=new FormData(); f.append('api','get_tipo'); f.append('id',id); const j=await api(f); if(j.status!=='ok'){ alert('No'); return; } const t=j.tipo; document.getElementById('page').innerHTML = `<h2>Editar Tipo</h2><form id="formEditTipo" onsubmit="return submitEditTipo(event, ${id})"><div class="form-grupo"><label>Nombre</label><input name="nombre" value="${escapeHtml(t.nombre)}" required></div><div class="form-grupo"><label>Capacidad</label><input name="capacidad" type="number" value="${t.capacidad}" min="1" required></div><div class="form-grupo"><label>Precio</label><input name="precio" type="number" step="0.01" value="${t.precio_noche}" required></div><button class="btn-submit">Guardar</button><button type="button" class="btn-cancelar" onclick="view('tipos')">Cancelar</button></form>`; }

async function submitEditTipo(e,id){ e.preventDefault(); const f=new FormData(document.getElementById('formEditTipo')); f.append('api','update_tipo'); f.append('id',id); const j=await api(f); if(j.status==='ok'){ alert('Actualizado'); view('tipos'); } else alert('Error: '+(j.message||'')); return false; }

async function delTipo(id){ if(!confirm('Eliminar tipo #'+id+'?')) return; const f=new FormData(); f.append('api','delete_tipo'); f.append('id',id); const j=await api(f); if(j.status==='ok') view('tipos'); else alert('Error'); }

// ---------- Habitaciones ----------
async function loadHabitaciones(){ const f=new FormData(); f.append('api','list_habitaciones'); const j=await api(f); if(j.status==='ok'){ let html = '<button class="btn" onclick="renderNuevaHabitacion()">➕ Nueva Habitación</button>'; html += '<table class="tabla-modal"><thead><tr><th>ID</th><th>Número</th><th>Tipo</th><th>Piso</th><th>Estado</th><th>Acción</th></tr></thead><tbody>'; for(const h of j.habitaciones){ html += `<tr><td>${h.id_habitacion}</td><td>${escapeHtml(h.numero)}</td><td>${escapeHtml(h.tipo_nombre||'')}</td><td>${h.piso}</td><td>${h.estado}</td><td><button class="btn-accion btn-editar" onclick="editHabitacion(${h.id_habitacion})">Editar</button><button class="btn-accion btn-eliminar" onclick="delHabitacion(${h.id_habitacion})">Eliminar</button></td></tr>`;} html += '</tbody></table>'; document.getElementById('habitaciones-list').innerHTML = html; } }

async function renderNuevaHabitacion(){ const ft = new FormData(); ft.append('api','list_tipos'); const jt = await api(ft); let options=''; for(const t of jt.tipos) options += `<option value="${t.id_tipo}">${escapeHtml(t.nombre)}</option>`; document.getElementById('page').innerHTML = `<h2>Nueva Habitación</h2><form id="formNewHab" onsubmit="return submitNewHab(event)"><div class="form-grupo"><label>Número</label><input name="numero" required></div><div class="form-grupo"><label>Tipo</label><select name="id_tipo">${options}</select></div><div class="form-grupo"><label>Piso</label><input name="piso" type="number" value="1" required></div><div class="form-grupo"><label>Estado</label><select name="estado"><option>disponible</option><option>ocupada</option><option>mantenimiento</option><option>reservada</option></select></div><button class="btn-submit">Guardar</button><button type="button" class="btn-cancelar" onclick="view('habitaciones')">Cancelar</button></form>`; }

async function submitNewHab(e){ e.preventDefault(); const f=new FormData(document.getElementById('formNewHab')); f.append('api','create_habitacion'); const j=await api(f); if(j.status==='ok'){ alert('Creada'); view('habitaciones'); } else alert('Error: '+(j.message||'')); return false; }

async function editHabitacion(id){ const f=new FormData(); f.append('api','get_habitacion'); f.append('id',id); const j=await api(f); if(j.status!=='ok'){ alert('No'); return; } const h=j.habitacion; const ft=new FormData(); ft.append('api','list_tipos'); const jt=await api(ft); let options=''; for(const t of jt.tipos) options += `<option value="${t.id_tipo}" ${t.id_tipo==h.id_tipo?'selected':''}>${escapeHtml(t.nombre)}</option>`; document.getElementById('page').innerHTML = `<h2>Editar Habitación</h2><form id="formEditHab" onsubmit="return submitEditHab(event, ${id})"><div class="form-grupo"><label>Número</label><input name="numero" value="${escapeHtml(h.numero)}" required></div><div class="form-grupo"><label>Tipo</label><select name="id_tipo">${options}</select></div><div class="form-grupo"><label>Piso</label><input name="piso" type="number" value="${h.piso}" required></div><div class="form-grupo"><label>Estado</label><select name="estado"><option ${h.estado==='disponible'?'selected':''}>disponible</option><option ${h.estado==='ocupada'?'selected':''}>ocupada</option><option ${h.estado==='mantenimiento'?'selected':''}>mantenimiento</option><option ${h.estado==='reservada'?'selected':''}>reservada</option></select></div><button class="btn-submit">Guardar</button><button type="button" class="btn-cancelar" onclick="view('habitaciones')">Cancelar</button></form>`; }

async function submitEditHab(e,id){ e.preventDefault(); const f=new FormData(document.getElementById('formEditHab')); f.append('api','update_habitacion'); f.append('id',id); const j=await api(f); if(j.status==='ok'){ alert('Actualizado'); view('habitaciones'); } else alert('Error: '+(j.message||'')); return false; }

async function delHabitacion(id){ if(!confirm('Eliminar habitación #'+id+'?')) return; const f=new FormData(); f.append('api','delete_habitacion'); f.append('id',id); const j=await api(f); if(j.status==='ok') view('habitaciones'); else alert('Error'); }

// ---------- Categorías (producto) ----------
async function loadCategorias(){ const f=new FormData(); f.append('api','list_categorias'); const j=await api(f); if(j.status==='ok'){ let html = '<table class="tabla-modal"><thead><tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Acción</th></tr></thead><tbody>'; for(const c of j.categorias){ html += `<tr><td>${c.id_categoria}</td><td>${escapeHtml(c.nombre)}</td><td>${escapeHtml(c.tipo)}</td><td><button class="btn-accion btn-editar" onclick="editCategoria(${c.id_categoria})">Editar</button><button class="btn-accion btn-eliminar" onclick="delCategoria(${c.id_categoria})">Eliminar</button></td></tr>`;} html += '</tbody></table>'; document.getElementById('categorias-list').innerHTML = '<button class="btn" onclick="renderNuevaCategoria()">➕ Nueva Categoría</button>' + html; } else document.getElementById('categorias-list').innerHTML='Error'; }

function renderNuevaCategoria(){ document.getElementById('page').innerHTML = `
        <h2>Nueva Categoría</h2>
        <form id="formNuevaCategoria" onsubmit="return submitNuevaCategoria(event)">
            <div class="form-grupo"><label>Nombre</label><input name="nombre" required></div>
            <div class="form-grupo"><label>Tipo</label><select name="tipo"><option>aseo</option><option>bebida</option><option>comida</option><option>snack</option></select></div>
            <div class="form-grupo"><label>Descripción</label><textarea name="descripcion"></textarea></div>
            <button class="btn-submit">Guardar</button>
            <button type="button" class="btn-cancelar" onclick="view('categorias')">Cancelar</button>
        </form>
    `; }

async function submitNuevaCategoria(e){ e.preventDefault(); const f=new FormData(document.getElementById('formNuevaCategoria')); f.append('api','create_categoria'); const j=await api(f); if(j.status==='ok'){ alert('Creada'); view('categorias'); } else alert('Error: '+(j.message||'')); return false; }

async function editCategoria(id){ const f=new FormData(); f.append('api','get_categoria'); f.append('id',id); const j=await api(f); if(j.status!=='ok'){ alert('No encontrado'); return; } const c=j.categoria; document.getElementById('page').innerHTML = `
        <h2>Editar Categoría</h2>
        <form id="formEditCategoria" onsubmit="return submitEditCategoria(event, ${id})">
            <div class="form-grupo"><label>Nombre</label><input name="nombre" value="${escapeHtml(c.nombre)}" required></div>
            <div class="form-grupo"><label>Tipo</label><select name="tipo"><option ${c.tipo==='aseo'?'selected':''}>aseo</option><option ${c.tipo==='bebida'?'selected':''}>bebida</option><option ${c.tipo==='comida'?'selected':''}>comida</option><option ${c.tipo==='snack'?'selected':''}>snack</option></select></div>
            <div class="form-grupo"><label>Descripción</label><textarea name="descripcion">${escapeHtml(c.descripcion||'')}</textarea></div>
            <button class="btn-submit">Guardar</button>
            <button type="button" class="btn-cancelar" onclick="view('categorias')">Cancelar</button>
        </form>
    `; }

async function submitEditCategoria(e,id){ e.preventDefault(); const f=new FormData(document.getElementById('formEditCategoria')); f.append('api','update_categoria'); f.append('id',id); const j=await api(f); if(j.status==='ok'){ alert('Actualizado'); view('categorias'); } else alert('Error: '+(j.message||'')); return false; }

async function delCategoria(id){ if(!confirm('Eliminar categoría #'+id+'?')) return; const f=new FormData(); f.append('api','delete_categoria'); f.append('id',id); const j=await api(f); if(j.status==='ok') view('categorias'); else alert('Error'); }

// ---------- Productos ----------
async function loadProductos(){ const f=new FormData(); f.append('api','list_productos'); const j=await api(f); if(j.status==='ok'){ let html = '<table class="tabla-modal"><thead><tr><th>ID</th><th>Código</th><th>Nombre</th><th>Categoria</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acción</th></tr></thead><tbody>'; for(const p of j.productos){ html += `<tr><td>${p.id_producto}</td><td>${escapeHtml(p.codigo)}</td><td>${escapeHtml(p.nombre)}</td><td>${escapeHtml(p.categoria_nombre||'')}</td><td>${p.precio}</td><td>${p.stock}</td><td>${p.estado}</td><td><button class="btn-accion btn-editar" onclick="editProducto(${p.id_producto})">Editar</button><button class="btn-accion btn-eliminar" onclick="delProducto(${p.id_producto})">Eliminar</button></td></tr>`;} html += '</tbody></table>'; document.getElementById('productos-list').innerHTML = '<button class="btn" onclick="renderNuevoProducto()">➕ Nuevo Producto</button>' + html; } else document.getElementById('productos-list').innerHTML='Error'; }

function renderNuevoProducto(){ document.getElementById('page').innerHTML = '<h2>Nuevo Producto</h2><div id="form-producto-container">Cargando...</div>'; renderProductoForm(); }

async function renderProductoForm(data){ // if data provided -> edit, else new
    const ft = new FormData(); ft.append('api','list_categorias'); const jt = await api(ft); let options=''; for(const c of jt.categorias) options += `<option value="${c.id_categoria}" ${data && data.id_categoria==c.id_categoria?'selected':''}>${escapeHtml(c.nombre)}</option>`;
    const formHtml = `
        <form id="formProducto" onsubmit="return submitProducto(event, ${data?data.id_producto:0})">
            <div class="form-grupo"><label>Código</label><input name="codigo" value="${data?escapeHtml(data.codigo):''}" required></div>
            <div class="form-grupo"><label>Nombre</label><input name="nombre" value="${data?escapeHtml(data.nombre):''}" required></div>
            <div class="form-grupo"><label>Categoría</label><select name="id_categoria">${options}</select></div>
            <div class="form-row"><div class="form-grupo"><label>Precio</label><input name="precio" type="number" step="0.01" value="${data?data.precio:0}"></div><div class="form-grupo"><label>Stock</label><input name="stock" type="number" value="${data?data.stock:0}"></div></div>
            <div class="form-grupo"><label>Descripción</label><textarea name="descripcion">${data?escapeHtml(data.descripcion||''):''}</textarea></div>
            <div class="form-grupo"><label>Estado</label><select name="estado"><option ${!data||data.estado==='activo'?'selected':''}>activo</option><option ${data&&data.estado==='inactivo'?'selected':''}>inactivo</option></select></div>
            <button class="btn-submit">Guardar</button>
            <button type="button" class="btn-cancelar" onclick="view('productos')">Cancelar</button>
        </form>
    `;
    document.getElementById('form-producto-container').innerHTML = formHtml;
}

async function submitProducto(e,id){ e.preventDefault(); const form = document.getElementById('formProducto'); const f = new FormData(form); f.append('api', id>0 ? 'update_producto' : 'create_producto'); if(id>0) f.append('id', id); const j = await api(f); if(j.status==='ok'){ alert(id>0?'Actualizado':'Creado'); view('productos'); } else alert('Error: '+(j.message||'')); return false; }

 

async function editProducto(id){ const f=new FormData(); f.append('api','get_producto'); f.append('id',id); const j=await api(f); if(j.status!=='ok'){ alert('No encontrado'); return; } renderProductoForm(j.producto); }

async function delProducto(id){ if(!confirm('Eliminar producto #'+id+'?')) return; const f=new FormData(); f.append('api','delete_producto'); f.append('id',id); const j=await api(f); if(j.status==='ok') view('productos'); else alert('Error'); }

// Inicio
document.addEventListener('DOMContentLoaded', ()=> view('dashboard'));
</script>

<?php include '../includes/footer.php'; ?>
