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
<style>
    /* ============================================
    DASHBOARD RECEPCIONISTA - ESTILOS MODERNOS
    ============================================ */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    /* Variables CSS mejoradas */

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

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }

    /* Dashboard Layout */
    .dashboard-container {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        max-width: 1600px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    /* Sidebar Menu */
    .side-menu {
        width: 280px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        padding: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.5);
        position: sticky;
        top: 2rem;
    }

    .side-menu h3 {
        font-size: 1.8rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .side-menu > div {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .side-menu .btn {
        width: 100%;
        padding: 1rem 1.2rem;
        background: rgba(99, 102, 241, 0.05);
        color: var(--dark);
        border: 2px solid transparent;
        border-radius: 15px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: left;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .side-menu .btn:hover {
        background: rgba(99, 102, 241, 0.1);
        border-color: var(--primary);
        transform: translateX(5px);
    }

    .side-menu .btn:active,
    .side-menu .btn.active {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    /* Main Area */
    .main-area {
        flex: 1;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        padding: 3rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.5);
        min-height: 600px;
    }

    .main-area h2 {
        font-size: 2.2rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* Dashboard Grid */
    .dash-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 2px solid var(--border);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        border-color: var(--primary);
    }

    .card strong {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: block;
        margin-top: 0.5rem;
    }

    /* Buttons */
    .btn {
        padding: 0.8rem 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }

    .btn:active {
        transform: translateY(0);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--success), #059669);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-submit:hover {
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-cancelar {
        background: linear-gradient(135deg, var(--gray), #475569);
        box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
    }

    .btn-cancelar:hover {
        box-shadow: 0 6px 20px rgba(100, 116, 139, 0.4);
    }

    .btn-accion {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        margin-right: 0.5rem;
    }

    .btn-editar {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
    }

    .btn-eliminar {
        background: linear-gradient(135deg, var(--error), #dc2626);
        box-shadow: 0 2px 10px rgba(239, 68, 68, 0.3);
    }

    /* Tables */
    .tabla-modal {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 1.5rem;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .tabla-modal thead {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
    }

    .tabla-modal thead th {
        padding: 1.2rem 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tabla-modal tbody tr {
        border-bottom: 1px solid var(--border);
        transition: all 0.2s ease;
    }

    .tabla-modal tbody tr:hover {
        background: rgba(99, 102, 241, 0.05);
    }

    .tabla-modal tbody tr:last-child {
        border-bottom: none;
    }

    .tabla-modal tbody td {
        padding: 1rem;
        color: var(--gray);
    }

    /* Forms */
    .form-grupo {
        margin-bottom: 1.5rem;
    }

    .form-grupo label {
        display: block;
        color: var(--dark);
        font-weight: 600;
        margin-bottom: 0.8rem;
        font-size: 0.95rem;
    }

    .form-grupo input,
    .form-grupo select,
    .form-grupo textarea {
        width: 100%;
        padding: 1rem;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
        font-family: 'Poppins', sans-serif;
    }

    .form-grupo input:focus,
    .form-grupo select:focus,
    .form-grupo textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .form-grupo textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-grupo select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 3rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    /* Search Input */
    input[type="text"]#qclientes,
    input[type="search"] {
        padding: 0.8rem 1.2rem;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        min-width: 300px;
    }

    input[type="text"]#qclientes:focus,
    input[type="search"]:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    /* Alerts */
    .alert {
        padding: 1.2rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .alert.error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
        color: var(--error);
        border: 2px solid rgba(239, 68, 68, 0.3);
    }

    .alert.success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
        color: var(--success);
        border: 2px solid rgba(16, 185, 129, 0.3);
    }

    .alert.info {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
        color: var(--primary);
        border: 2px solid rgba(99, 102, 241, 0.3);
    }

    /* Action buttons in toolbar */
    div[style*="margin-bottom:10px"] {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1.5rem !important;
        flex-wrap: wrap;
    }

    /* Checkboxes */
    input[type="checkbox"] {
        width: 1.2rem;
        height: 1.2rem;
        cursor: pointer;
        accent-color: var(--primary);
    }

    label:has(input[type="checkbox"]) {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    label:has(input[type="checkbox"]):hover {
        background: rgba(99, 102, 241, 0.05);
    }

    /* Lists */
    ul {
        list-style: none;
        padding: 0;
    }

    ul li {
        padding: 0.8rem 1rem;
        background: rgba(99, 102, 241, 0.05);
        border-radius: 10px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    ul li::before {
        content: '✓';
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 0.8rem;
    }

    /* Loading state */
    .loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        color: var(--gray);
        font-size: 1.1rem;
    }

    /* Modal overlay (if needed) */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        padding: 3rem;
        border-radius: 25px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .dashboard-container {
            flex-direction: column;
        }
        
        .side-menu {
            width: 100%;
            position: relative;
            top: 0;
        }
        
        .side-menu > div {
            flex-direction: row;
            flex-wrap: wrap;
        }
        
        .side-menu .btn {
            flex: 1;
            min-width: 150px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 0 1rem;
            margin: 1rem auto;
        }
        
        .main-area {
            padding: 2rem 1.5rem;
        }
        
        .dash-grid {
            grid-template-columns: 1fr;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .tabla-modal {
            font-size: 0.85rem;
        }
        
        .tabla-modal thead th,
        .tabla-modal tbody td {
            padding: 0.8rem 0.5rem;
        }
        
        input[type="text"]#qclientes {
            min-width: 100%;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .main-area > * {
        animation: fadeIn 0.4s ease-out;
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
<?php include '../includes/footer.php'; ?>
