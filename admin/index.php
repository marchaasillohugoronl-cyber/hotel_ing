<?php
// ============================================
// CONFIGURACIÓN E INCLUSIONES
// ============================================
include '../config.php';
include '../includes/funciones.php';

// Verificar permisos
verificarLogin();
verificarRol('admin');

include '../includes/header.php';

// ============================================
// PROCESAR ACCIONES (POST)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $modulo = $_POST['modulo'] ?? '';
    
    // USUARIOS
    if ($modulo == 'usuarios') {
        if ($accion == 'nuevo') {
            $username = $conn->real_escape_string($_POST['username']);
            $nombres = $conn->real_escape_string($_POST['nombres'] ?? '');
            $apellidos = $conn->real_escape_string($_POST['apellidos'] ?? '');
            $email = $conn->real_escape_string($_POST['email'] ?? '');
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $rol = $conn->real_escape_string($_POST['rol']);
            
            $stmt = $conn->prepare("INSERT INTO usuario (username, password, nombres, apellidos, email, rol, estado) VALUES (?, ?, ?, ?, ?, ?, 'activo')");
            $stmt->bind_param('ssssss', $username, $password_hash, $nombres, $apellidos, $email, $rol);
            $stmt->execute();
            $stmt->close();
        } elseif ($accion == 'eliminar') {
            $id = intval($_POST['id_usuario']);
            if (isset($_SESSION['usuario']['id_usuario']) && $_SESSION['usuario']['id_usuario'] != $id) {
                $conn->query("DELETE FROM usuario WHERE id_usuario = $id");
            }
        }
    }
        // PRODUCTOS
        if ($modulo == 'productos') {
            if ($accion == 'nuevo') {
                // Recoger y limpiar datos
                $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
                $codigo = $conn->real_escape_string($_POST['codigo'] ?? '');
                $id_categoria = intval($_POST['id_categoria'] ?? 0);
                $precio = floatval($_POST['precio'] ?? 0);
                $stock = intval($_POST['stock'] ?? 0);
                $descripcion = $conn->real_escape_string($_POST['descripcion'] ?? '');
                $imagen = $conn->real_escape_string($_POST['imagen'] ?? ''); // si tienes upload, luego ajustar

                // Validar campos obligatorios
                if (empty($nombre) || empty($codigo) || $id_categoria <= 0 || $precio <= 0) {
                    echo "Error: Debes completar todos los campos obligatorios.";
                } else {
                    // Preparar consulta
                    $stmt = $conn->prepare("
                        INSERT INTO producto 
                        (codigo, nombre, id_categoria, precio, stock, descripcion, imagen, estado) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')
                    ");
                    $stmt->bind_param('ssiddss', $codigo, $nombre, $id_categoria, $precio, $stock, $descripcion, $imagen);

                    // Ejecutar y verificar
                    if ($stmt->execute()) {
                        echo "Producto agregado correctamente.";
                    } else {
                        echo "Error al agregar producto: " . $stmt->error;
                    }

                    $stmt->close();
                }

            } elseif ($accion == 'eliminar') {
                $id = intval($_POST['id_producto'] ?? 0);
                if ($id > 0) {
                    if ($conn->query("DELETE FROM producto WHERE id_producto = $id")) {
                        echo "Producto eliminado correctamente.";
                    } else {
                        echo "Error al eliminar producto: " . $conn->error;
                    }
                }
            }
        }

    
    // TIPOS DE HABITACIÓN
    if ($modulo == 'tipos') {
        if ($accion == 'nuevo') {
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $capacidad = intval($_POST['capacidad']);
            $precio = floatval($_POST['precio']);
            $conn->query("INSERT INTO tipo_habitacion (nombre, capacidad, precio_noche, estado) VALUES ('$nombre', $capacidad, $precio, 'activo')");
        } elseif ($accion == 'eliminar') {
            $id = intval($_POST['id_tipo']);
            $conn->query("DELETE FROM tipo_habitacion WHERE id_tipo = $id");
        }
    }
    
    // MÉTODOS DE PAGO
    if ($modulo == 'metodos') {
        if ($accion == 'nuevo') {
            $nombre = $conn->real_escape_string($_POST['nombre']);
            $conn->query("INSERT INTO metodo_pago (nombre, estado) VALUES ('$nombre', 'activo')");
        } elseif ($accion == 'eliminar') {
            $id = intval($_POST['id_metodo']);
            $conn->query("DELETE FROM metodo_pago WHERE id_metodo = $id");
        }
    }
}

// ============================================
// OBTENER DATOS PARA ESTADÍSTICAS
// ============================================
$usuarios = $conn->query("SELECT COUNT(*) AS c FROM usuario")->fetch_assoc()['c'];
$clientes = $conn->query("SELECT COUNT(*) AS c FROM cliente")->fetch_assoc()['c'];
$habitaciones = $conn->query("SELECT COUNT(*) AS c FROM habitacion")->fetch_assoc()['c'];
$ventas = $conn->query("SELECT SUM(monto) AS total FROM pago WHERE DATE(fecha_pago) = CURDATE()")->fetch_assoc()['total'];

// Determinar qué sección mostrar
$seccion = $_GET['seccion'] ?? 'dashboard';
?>
<link rel="stylesheet" href="../assets/css/admi.css">

<div class="admin-container">
    <!-- ============================================
         SIDEBAR - MENÚ DE NAVEGACIÓN
         ============================================ -->
    <aside class="sidebar">
        <h3>📋 Panel Admin</h3>
        <a href="?seccion=dashboard" class="<?= $seccion == 'dashboard' ? 'active' : '' ?>">
            <span>📊</span> Dashboard
        </a>
        <a href="?seccion=usuarios" class="<?= $seccion == 'usuarios' ? 'active' : '' ?>">
            <span>👤</span> Usuarios
        </a>
        <a href="?seccion=productos" class="<?= $seccion == 'productos' ? 'active' : '' ?>">
            <span>🛒</span> Productos
        </a>
        <a href="?seccion=tipos" class="<?= $seccion == 'tipos' ? 'active' : '' ?>">
            <span>🏠</span> Tipos Habitación
        </a>
        <a href="?seccion=metodos" class="<?= $seccion == 'metodos' ? 'active' : '' ?>">
            <span>💳</span> Métodos de Pago
        </a>
        <a href="?seccion=reportes" class="<?= $seccion == 'reportes' ? 'active' : '' ?>">
            <span>📈</span> Reportes Financieros
        </a>
    </aside>

    <!-- ============================================
         CONTENIDO PRINCIPAL DINÁMICO
         ============================================ -->
    <div class="main-content">
        <?php if ($seccion == 'dashboard'): ?>
            <!-- DASHBOARD -->
            <div class="page-header">
                <h2>Panel de Administración</h2>
                <p>Bienvenido al sistema de gestión del Hostal El Dulce Descanso</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon">👤</div>
                    <div class="label">Usuarios del Sistema</div>
                    <div class="value"><?= $usuarios ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon">🧳</div>
                    <div class="label">Clientes Registrados</div>
                    <div class="value"><?= $clientes ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon">🏠</div>
                    <div class="label">Habitaciones Totales</div>
                    <div class="value"><?= $habitaciones ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon">💰</div>
                    <div class="label">Ventas del Día</div>
                    <div class="value"><?= formatoMoneda($ventas ?? 0) ?></div>
                </div>
            </div>

        <?php elseif ($seccion == 'usuarios'): ?>
            <!-- USUARIOS -->
            <div class="page-header">
                <h2>Gestión de Usuarios</h2>
                <p>Administra los usuarios del sistema</p>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">➕ Agregar Nuevo Usuario</h3>
                <form method="POST">
                    <input type="hidden" name="modulo" value="usuarios">
                    <input type="hidden" name="accion" value="nuevo">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Username *</label>
                            <input type="text" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Nombres</label>
                            <input type="text" name="nombres">
                        </div>
                        <div class="form-group">
                            <label>Apellidos</label>
                            <input type="text" name="apellidos">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email">
                        </div>
                        <div class="form-group">
                            <label>Contraseña *</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label>Rol *</label>
                            <select name="rol" required>
                                <option value="administrador">Administrador</option>
                                <option value="recepcionista">Recepcionista</option>
                                <option value="cliente">Cliente</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                </form>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">📋 Lista de Usuarios</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $usuarios_list = $conn->query("SELECT * FROM usuario ORDER BY username");
                            while($u = $usuarios_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($u['username']) ?></td>
                                <td><?= htmlspecialchars(trim($u['nombres'].' '.$u['apellidos'])) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= ucfirst($u['rol']) ?></td>
                                <td><?= ucfirst($u['estado']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="modulo" value="usuarios">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar usuario?')">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($seccion == 'productos'): ?>


            <!-- PRODUCTOS -->
            <div class="page-header">
                <h2>Productos y Servicios</h2>
                <p>Administra los productos disponibles</p>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">➕ Agregar Nuevo Producto</h3>
                <form method="POST">
                    <input type="hidden" name="modulo" value="productos">
                    <input type="hidden" name="accion" value="nuevo">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Precio *</label>
                            <input type="number" step="0.01" name="precio" required>
                        </div>
                        <div class="form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" value="0" min="0">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Producto</button>
                </form>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">📋 Lista de Productos</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $productos_list = $conn->query("SELECT * FROM producto ORDER BY nombre");
                            while($p = $productos_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= formatoMoneda($p['precio']) ?></td>
                                <td><?= $p['stock'] ?></td>
                                <td><?= ucfirst($p['estado']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="modulo" value="productos">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar producto?')">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($seccion == 'tipos'): ?>

            
            <!-- TIPOS DE HABITACIÓN -->
            <div class="page-header">
                <h2>Tipos de Habitación</h2>
                <p>Gestiona los tipos de habitaciones disponibles</p>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">➕ Agregar Tipo de Habitación</h3>
                <form method="POST">
                    <input type="hidden" name="modulo" value="tipos">
                    <input type="hidden" name="accion" value="nuevo">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Capacidad *</label>
                            <input type="number" name="capacidad" min="1" required>
                        </div>
                        <div class="form-group">
                            <label>Precio/Noche *</label>
                            <input type="number" step="0.01" name="precio" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Tipo</button>
                </form>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">📋 Lista de Tipos</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Capacidad</th>
                                <th>Precio/Noche</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $tipos_list = $conn->query("SELECT * FROM tipo_habitacion ORDER BY nombre");
                            while($t = $tipos_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($t['nombre']) ?></td>
                                <td><?= $t['capacidad'] ?> personas</td>
                                <td><?= formatoMoneda($t['precio_noche']) ?></td>
                                <td><?= ucfirst($t['estado']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="modulo" value="tipos">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_tipo" value="<?= $t['id_tipo'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar tipo?')">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($seccion == 'metodos'): ?>
            <!-- MÉTODOS DE PAGO -->
            <div class="page-header">
                <h2>Métodos de Pago</h2>
                <p>Administra las formas de pago aceptadas</p>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">➕ Agregar Método de Pago</h3>
                <form method="POST">
                    <input type="hidden" name="modulo" value="metodos">
                    <input type="hidden" name="accion" value="nuevo">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre del Método *</label>
                            <input type="text" name="nombre" required placeholder="Ej: Efectivo, Tarjeta, Yape">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Método</button>
                </form>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">📋 Lista de Métodos</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $metodos_list = $conn->query("SELECT * FROM metodo_pago ORDER BY nombre");
                            while($m = $metodos_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($m['nombre']) ?></td>
                                <td><?= ucfirst($m['estado']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="modulo" value="metodos">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_metodo" value="<?= $m['id_metodo'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar método?')">🗑️ Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($seccion == 'reportes'): ?>
            <!-- REPORTES FINANCIEROS -->
            <?php
            $total_pagos = $conn->query("SELECT SUM(monto) AS t FROM pago")->fetch_assoc()['t'];
            $total_reservas = $conn->query("SELECT COUNT(*) AS c FROM reserva")->fetch_assoc()['c'];
            $total_ventas = $conn->query("SELECT SUM(total) AS v FROM venta_recepcion")->fetch_assoc()['v'];
            $total_alquileres = $conn->query("SELECT COUNT(*) AS c FROM alquiler")->fetch_assoc()['c'];
            $pagos_recientes = $conn->query("SELECT * FROM pago ORDER BY fecha_pago DESC LIMIT 20");
            ?>
            
            <div class="page-header">
                <h2>Reportes Financieros</h2>
                <p>Visualiza el resumen de operaciones financieras</p>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">📊 Resumen General</h3>
                <ul class="summary-list">
                    <li>
                        💰 Total pagos registrados: <strong><?= formatoMoneda($total_pagos ?? 0) ?></strong>
                    </li>
                    <li>
                        📘 Total reservas: <strong><?= $total_reservas ?></strong>
                    </li>
                    <li>
                        🛍️ Total ventas en recepción: <strong><?= formatoMoneda($total_ventas ?? 0) ?></strong>
                    </li>
                    <li>
                        🏨 Total alquileres registrados: <strong><?= $total_alquileres ?></strong>
                    </li>
                </ul>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;">💳 Pagos Recientes</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($pagos_recientes && $pagos_recientes->num_rows > 0): ?>
                                <?php while($p = $pagos_recientes->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['codigo']) ?></td>
                                    <td><?= ucfirst($p['tipo']) ?></td>
                                    <td><?= formatoMoneda($p['monto']) ?></td>
                                    <td><?= formatoFecha($p['fecha_pago']) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #999;">
                                        No hay pagos registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>