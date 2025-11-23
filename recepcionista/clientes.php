<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Clientes</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
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
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 1rem;
}

/* Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 25px;
    padding: 3rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.5);
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Header */
h2 {
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

h3 {
    font-size: 1.8rem;
    color: var(--primary);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

/* Toolbar */
.toolbar {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    min-width: 300px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 1.2rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray);
    font-size: 1.1rem;
}

#q {
    width: 100%;
    padding: 1rem 1rem 1rem 3rem;
    border: 2px solid var(--border);
    border-radius: 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

#q:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

/* Buttons */
.btn {
    padding: 1rem 1.5rem;
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
    padding: 0.6rem 1rem;
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

/* Table */
.tabla-modal {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
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
form {
    max-width: 800px;
}

.form-grupo {
    margin-bottom: 1.5rem;
}

.form-grupo label {
    display: block;
    color: var(--dark);
    font-weight: 600;
    margin-bottom: 0.8rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-grupo label i {
    color: var(--primary);
}

.form-grupo input,
.form-grupo select {
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
.form-grupo select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
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

/* Loading */
.loading {
    text-align: center;
    padding: 3rem;
    color: var(--gray);
    font-size: 1.1rem;
}

.loading i {
    font-size: 2rem;
    color: var(--primary);
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: var(--gray);
}

.empty-state i {
    font-size: 4rem;
    color: var(--primary);
    opacity: 0.3;
    margin-bottom: 1rem;
}

/* Error message */
.error-msg {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
    color: var(--error);
    padding: 1.2rem 1.5rem;
    border-radius: 12px;
    border: 2px solid rgba(239, 68, 68, 0.3);
    display: flex;
    align-items: center;
    gap: 1rem;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        padding: 2rem 1.5rem;
    }
    
    h2 {
        font-size: 1.8rem;
    }
    
    .toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        min-width: 100%;
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
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    body {
        padding: 1rem 0.5rem;
    }
    
    .container {
        padding: 1.5rem 1rem;
        border-radius: 20px;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    .btn-accion {
        padding: 0.5rem 0.8rem;
        font-size: 0.85rem;
    }
}
</style>
</head>
<body>

<div class="container">
    <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
    
    <div class="toolbar">
        <button class="btn" onclick="renderNuevo()">
            <i class="fas fa-user-plus"></i> Nuevo Cliente
        </button>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input id="q" placeholder="Buscar por documento o nombre..." oninput="debQ()">
        </div>
    </div>
    
    <div id="list">
        <div class="loading">
            <i class="fas fa-spinner"></i>
            <p>Cargando clientes...</p>
        </div>
    </div>
</div>

<script>
async function api(data){
    // Usar el endpoint central de API en este directorio
    const res = await fetch('dashboard.php', { method:'POST', body: data });
    return res.json();
}

function escapeHtml(s){ 
    if(!s) return ''; 
    return String(s).replace(/[&"'<>]/g, c=>({'&':'&amp;','"':'&quot;','\'':'&#39;','<':'&lt;','>':'&gt;'}[c])); 
}

let dTimer;
function debQ(){ 
    clearTimeout(dTimer); 
    dTimer = setTimeout(()=> loadList(), 300); 
}

async function loadList(){ 
    const q = document.getElementById('q').value; 
    const f = new FormData(); 
    f.append('api','list_clientes'); 
    f.append('q', q); 
    const j = await api(f); 
    
    if(j.status==='ok'){ 
        if(j.clientes.length === 0) {
            document.getElementById('list').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>No se encontraron clientes</p>
                </div>
            `;
            return;
        }
        
        let html = '<table class="tabla-modal"><thead><tr><th><i class="fas fa-hashtag"></i> ID</th><th><i class="fas fa-id-card"></i> Documento</th><th><i class="fas fa-user"></i> Nombre</th><th><i class="fas fa-phone"></i> Teléfono</th><th><i class="fas fa-envelope"></i> Email</th><th><i class="fas fa-cog"></i> Acciones</th></tr></thead><tbody>'; 
        
        for(const r of j.clientes){ 
            html += `<tr>
                <td>${r.id_cliente}</td>
                <td>${escapeHtml(r.tipo_doc+' '+r.num_doc)}</td>
                <td><strong>${escapeHtml(r.nombres+' '+r.apellidos)}</strong></td>
                <td>${escapeHtml(r.telefono||'-')}</td>
                <td>${escapeHtml(r.email||'-')}</td>
                <td>
                    <button class="btn btn-accion btn-editar" onclick="edit(${r.id_cliente})">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    <button class="btn btn-accion btn-eliminar" onclick="del(${r.id_cliente})">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </td>
            </tr>`;
        } 
        
        html += '</tbody></table>'; 
        document.getElementById('list').innerHTML = html; 
    } else { 
        document.getElementById('list').innerHTML = `
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i>
                <span>Error: ${j.message||'No se pudieron cargar los clientes'}</span>
            </div>
        `; 
    } 
}

function renderNuevo(){ 
    document.getElementById('list').innerHTML = `
        <h3><i class="fas fa-user-plus"></i> Nuevo Cliente</h3>
        <form id="form" onsubmit="return submitNuevo(event)">
            <div class="form-grupo">
                <label><i class="fas fa-id-card"></i> Tipo de Documento</label>
                <select name="tipo_doc">
                    <option value="DNI">DNI</option>
                    <option value="CE">Carné de Extranjería</option>
                    <option value="pasaporte">Pasaporte</option>
                </select>
            </div>
            
            <div class="form-grupo">
                <label><i class="fas fa-hashtag"></i> Número de Documento</label>
                <input name="num_doc" placeholder="Ej: 12345678" required>
            </div>
            
            <div class="form-row">
                <div class="form-grupo">
                    <label><i class="fas fa-user"></i> Nombres</label>
                    <input name="nombres" placeholder="Nombres completos" required>
                </div>
                <div class="form-grupo">
                    <label><i class="fas fa-user"></i> Apellidos</label>
                    <input name="apellidos" placeholder="Apellidos completos">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-grupo">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input name="email" type="email" placeholder="correo@ejemplo.com">
                </div>
                <div class="form-grupo">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input name="telefono" placeholder="+51 987 654 321">
                </div>
            </div>
            
            <div class="form-grupo">
                <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
                <input name="direccion" placeholder="Dirección completa">
            </div>
            
            <div class="form-grupo">
                <label><i class="fas fa-birthday-cake"></i> Fecha de Nacimiento</label>
                <input name="fecha_nacimiento" type="date">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="btn btn-submit">
                    <i class="fas fa-save"></i> Guardar Cliente
                </button>
                <button type="button" class="btn btn-cancelar" onclick="loadList()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    `;
}

async function submitNuevo(e){ 
    e.preventDefault(); 
    const f = new FormData(document.getElementById('form')); 
    f.append('api','create_cliente'); 
    const j = await api(f); 
    
    if(j.status==='ok'){ 
        alert('✅ Cliente creado exitosamente (ID: '+j.id+')'); 
        loadList(); 
    } else {
        alert('❌ Error: '+(j.message||'No se pudo crear el cliente')); 
    }
    return false; 
}

async function edit(id){ 
    const f = new FormData(); 
    f.append('api','get_cliente'); 
    f.append('id',id); 
    const j = await api(f); 
    
    if(j.status!=='ok'){ 
        alert('❌ Cliente no encontrado'); 
        return; 
    } 
    
    const c = j.cliente; 
    document.getElementById('list').innerHTML = `
        <h3><i class="fas fa-user-edit"></i> Editar Cliente</h3>
        <form id="formEdit" onsubmit="return submitEdit(event, ${id})">
            <div class="form-grupo">
                <label><i class="fas fa-id-card"></i> Tipo de Documento</label>
                <select name="tipo_doc">
                    <option value="DNI" ${c.tipo_doc==='DNI'?'selected':''}>DNI</option>
                    <option value="CE" ${c.tipo_doc==='CE'?'selected':''}>Carné de Extranjería</option>
                    <option value="pasaporte" ${c.tipo_doc==='pasaporte'?'selected':''}>Pasaporte</option>
                </select>
            </div>
            
            <div class="form-grupo">
                <label><i class="fas fa-hashtag"></i> Número de Documento</label>
                <input name="num_doc" value="${escapeHtml(c.num_doc)}" required>
            </div>
            
            <div class="form-row">
                <div class="form-grupo">
                    <label><i class="fas fa-user"></i> Nombres</label>
                    <input name="nombres" value="${escapeHtml(c.nombres)}" required>
                </div>
                <div class="form-grupo">
                    <label><i class="fas fa-user"></i> Apellidos</label>
                    <input name="apellidos" value="${escapeHtml(c.apellidos)}">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-grupo">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input name="email" type="email" value="${escapeHtml(c.email||'')}">
                </div>
                <div class="form-grupo">
                    <label><i class="fas fa-phone"></i> Teléfono</label>
                    <input name="telefono" value="${escapeHtml(c.telefono||'')}">
                </div>
            </div>
            
            <div class="form-grupo">
                <label><i class="fas fa-map-marker-alt"></i> Dirección</label>
                <input name="direccion" value="${escapeHtml(c.direccion||'')}">
            </div>
            
            <div class="form-grupo">
                <label><i class="fas fa-birthday-cake"></i> Fecha de Nacimiento</label>
                <input name="fecha_nacimiento" type="date" value="${c.fecha_nacimiento||''}">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button class="btn btn-submit">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <button type="button" class="btn btn-cancelar" onclick="loadList()">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </form>
    `;
}

async function submitEdit(e,id){ 
    e.preventDefault(); 
    const f = new FormData(document.getElementById('formEdit')); 
    f.append('api','update_cliente'); 
    f.append('id',id); 
    const j = await api(f); 
    
    if(j.status==='ok'){ 
        alert('✅ Cliente actualizado exitosamente'); 
        loadList(); 
    } else {
        alert('❌ Error: '+(j.message||'No se pudo actualizar el cliente')); 
    }
    return false; 
}

async function del(id){ 
    if(!confirm('¿Estás seguro de eliminar este cliente?\n\nEsta acción no se puede deshacer.')) return; 
    
    const f = new FormData(); 
    f.append('api','delete_cliente'); 
    f.append('id',id); 
    const j = await api(f); 
    
    if(j.status==='ok'){ 
        alert('✅ Cliente eliminado exitosamente');
        loadList(); 
    } else {
        alert('❌ Error: '+(j.message||'No se pudo eliminar el cliente')); 
    }
}

// Inicializar
loadList();
</script>

</body>
</html>