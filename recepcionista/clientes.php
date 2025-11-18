<?php
// CRUD  de Clientes para Recepcionista
include '../config.php';
include '../includes/funciones.php';
verificarLogin();
verificarRol('recepcionista');
header('X-Frame-Options: SAMEORIGIN');

// allow embedding the page inside the dashboard via ?embed=1
$EMBED = isset($_GET['embed']) && $_GET['embed'] === '1';

// AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api'])) {
    header('Content-Type: application/json; charset=utf-8');
    $api = $_POST['api'];

    if ($api === 'list') {
        $q = $conn->real_escape_string(trim($_POST['q'] ?? ''));
        $where = '';
        if ($q !== '') $where = "WHERE (num_doc LIKE '%$q%' OR nombres LIKE '%$q%' OR apellidos LIKE '%$q%')";
        $res = $conn->query("SELECT * FROM cliente $where ORDER BY fecha_registro DESC LIMIT 500");
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode(['status'=>'ok','clientes'=>$rows]); exit;
    }

    if ($api === 'create') {
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

    if ($api === 'get') {
        $id = intval($_POST['id'] ?? 0);
        $r = $conn->query("SELECT * FROM cliente WHERE id_cliente = $id");
        $c = $r->fetch_assoc(); if ($c) echo json_encode(['status'=>'ok','cliente'=>$c]); else echo json_encode(['status'=>'error']); exit;
    }

    if ($api === 'update') {
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

    if ($api === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($conn->query("DELETE FROM cliente WHERE id_cliente = $id")) echo json_encode(['status'=>'ok']); else echo json_encode(['status'=>'error','message'=>$conn->error]); exit;
    }

    echo json_encode(['status'=>'error','message'=>'accion no reconocida']); exit;
}

// RENDER simple HTML
if (!$EMBED) include '../includes/header.php';
?>
<?php if (!$EMBED): ?>
<link rel="stylesheet" href="../assets/css/admi.css">
<?php endif; ?>
<div style="max-width:1000px;margin:18px auto;padding:12px;background:#fff;border-radius:8px;">
  <h2>Clientes - CRUD simple</h2>
  <div style="display:flex;gap:8px;align-items:center;margin-bottom:12px;">
    <button class="btn" onclick="renderNuevo()">➕ Nuevo Cliente</button>
    <input id="q" placeholder="buscar por documento o nombre" style="flex:1;padding:6px;border:1px solid #ddd;border-radius:4px;" oninput="debQ()">
  </div>
  <div id="list">Cargando...</div>
</div>
<script>
async function api(data){
  const res = await fetch(location.pathname, { method:'POST', body: data });
  return res.json();
}
function escapeHtml(s){ if(!s) return ''; return String(s).replace(/[&"'<>]/g, c=>({'&':'&amp;','"':'&quot;','\'':'&#39;','<':'&lt;','>':'&gt;'}[c])); }
let dTimer;
function debQ(){ clearTimeout(dTimer); dTimer = setTimeout(()=> loadList(), 300); }
async function loadList(){ const q = document.getElementById('q').value; const f = new FormData(); f.append('api','list'); f.append('q', q); const j = await api(f); if(j.status==='ok'){ let html = '<table class="tabla-modal"><thead><tr><th>ID</th><th>Documento</th><th>Nombre</th><th>Tel</th><th>Email</th><th>Acción</th></tr></thead><tbody>'; for(const r of j.clientes){ html += `<tr><td>${r.id_cliente}</td><td>${escapeHtml(r.tipo_doc+' '+r.num_doc)}</td><td>${escapeHtml(r.nombres+' '+r.apellidos)}</td><td>${escapeHtml(r.telefono||'')}</td><td>${escapeHtml(r.email||'')}</td><td><button class="btn-accion" onclick="edit(${r.id_cliente})">Editar</button> <button class="btn-accion btn-eliminar" onclick="del(${r.id_cliente})">Eliminar</button></td></tr>`;} html += '</tbody></table>'; document.getElementById('list').innerHTML = html; } else { document.getElementById('list').innerHTML = 'Error: '+(j.message||''); } }

function renderNuevo(){ document.getElementById('list').innerHTML = `
  <h3>Nuevo Cliente</h3>
  <form id="form" onsubmit="return submitNuevo(event)">
    <div class="form-grupo"><label>Tipo doc</label><select name="tipo_doc"><option>DNI</option><option>CE</option><option>pasaporte</option></select></div>
    <div class="form-grupo"><label>Número doc</label><input name="num_doc" required></div>
    <div class="form-row"><div class="form-grupo"><label>Nombres</label><input name="nombres" required></div><div class="form-grupo"><label>Apellidos</label><input name="apellidos"></div></div>
    <div class="form-row"><div class="form-grupo"><label>Email</label><input name="email"></div><div class="form-grupo"><label>Tel</label><input name="telefono"></div></div>
    <div class="form-grupo"><label>Dirección</label><input name="direccion"></div>
    <div class="form-grupo"><label>Fecha Nac</label><input name="fecha_nacimiento" type="date"></div>
    <div style="margin-top:8px;"><button class="btn-submit">Guardar</button> <button type="button" class="btn-cancelar" onclick="loadList()">Cancelar</button></div>
  </form>
`;
}

async function submitNuevo(e){ e.preventDefault(); const f = new FormData(document.getElementById('form')); f.append('api','create'); const j = await api(f); if(j.status==='ok'){ alert('Creado id: '+j.id); loadList(); } else alert('Error: '+(j.message||'')); return false; }

async function edit(id){ const f = new FormData(); f.append('api','get'); f.append('id',id); const j = await api(f); if(j.status!=='ok'){ alert('No encontrado'); return; } const c = j.cliente; document.getElementById('list').innerHTML = `
  <h3>Editar Cliente</h3>
  <form id="formEdit" onsubmit="return submitEdit(event, ${id})">
    <div class="form-grupo"><label>Tipo doc</label><select name="tipo_doc"><option ${c.tipo_doc==='DNI'?'selected':''}>DNI</option><option ${c.tipo_doc==='CE'?'selected':''}>CE</option><option ${c.tipo_doc==='pasaporte'?'selected':''}>pasaporte</option></select></div>
    <div class="form-grupo"><label>Número doc</label><input name="num_doc" value="${escapeHtml(c.num_doc)}" required></div>
    <div class="form-row"><div class="form-grupo"><label>Nombres</label><input name="nombres" value="${escapeHtml(c.nombres)}" required></div><div class="form-grupo"><label>Apellidos</label><input name="apellidos" value="${escapeHtml(c.apellidos)}"></div></div>
    <div class="form-row"><div class="form-grupo"><label>Email</label><input name="email" value="${escapeHtml(c.email||'')}"></div><div class="form-grupo"><label>Tel</label><input name="telefono" value="${escapeHtml(c.telefono||'')}"></div></div>
    <div class="form-grupo"><label>Dirección</label><input name="direccion" value="${escapeHtml(c.direccion||'')}"></div>
    <div class="form-grupo"><label>Fecha Nac</label><input name="fecha_nacimiento" type="date" value="${c.fecha_nacimiento||''}"></div>
    <div style="margin-top:8px;"><button class="btn-submit">Guardar</button> <button type="button" class="btn-cancelar" onclick="loadList()">Cancelar</button></div>
  </form>
`;
}

async function submitEdit(e,id){ e.preventDefault(); const f = new FormData(document.getElementById('formEdit')); f.append('api','update'); f.append('id',id); const j = await api(f); if(j.status==='ok'){ alert('Actualizado'); loadList(); } else alert('Error: '+(j.message||'')); return false; }

async function del(id){ if(!confirm('Eliminar cliente #'+id+'?')) return; const f = new FormData(); f.append('api','delete'); f.append('id',id); const j = await api(f); if(j.status==='ok'){ loadList(); } else alert('Error: '+(j.message||'')); }

// start
loadList();
</script>
<?php if (!$EMBED) include '../includes/footer.php'; ?>