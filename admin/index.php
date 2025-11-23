<?php
// ============================================
// CONFIGURACIÓN E INCLUSIONES
// ============================================
include '../config.php';
include '../includes/funciones.php';

// Verificar permisos
verificarLogin();
verificarRol('admin');

// ============================================
// ENDPOINTS AJAX (JSON) para la interfaz Admin
// Peticiones AJAX deben incluir `api` en POST
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api'])) {
    header('Content-Type: application/json; charset=utf-8');
    $api = $_POST['api'];

    if ($api === 'list_usuarios') {
        $res = $conn->query("SELECT id_usuario, username, nombres, apellidos, email, rol, estado FROM usuario ORDER BY username");
        $out = [];
        while ($r = $res->fetch_assoc()) {
            $out[] = $r;
        }
        echo json_encode(['success' => true, 'data' => $out]);
        exit;
    }

    if ($api === 'create_usuario') {
        $username = $conn->real_escape_string($_POST['username'] ?? '');
        $nombres = $conn->real_escape_string($_POST['nombres'] ?? '');
        $apellidos = $conn->real_escape_string($_POST['apellidos'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $rol = $conn->real_escape_string($_POST['rol'] ?? 'cliente');

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
            exit;
        }

        // verificar existencia
        $stmt = $conn->prepare("SELECT id_usuario FROM usuario WHERE username = ? LIMIT 1");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Error interno (prepare)']);
            exit;
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            echo json_encode(['success' => false, 'error' => 'El usuario ya existe']);
            exit;
        }
        $stmt->close();

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuario (username, password, nombres, apellidos, email, rol, estado) VALUES (?, ?, ?, ?, ?, ?, 'activo')");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Error interno (prepare insert)']);
            exit;
        }
        $stmt->bind_param('ssssss', $username, $password_hash, $nombres, $apellidos, $email, $rol);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
        exit;
    }

    if ($api === 'delete_usuario') {
        $id = intval($_POST['id_usuario'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'ID inválido']);
            exit;
        }
        // evitar auto-eliminación
        if (isset($_SESSION['usuario']['id_usuario']) && $_SESSION['usuario']['id_usuario'] == $id) {
            echo json_encode(['success' => false, 'error' => 'No puede eliminarse a sí mismo']);
            exit;
        }
        if ($conn->query("DELETE FROM usuario WHERE id_usuario = $id")) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Acción no reconocida']);
    exit;
}
include '../includes/header.php';

// ============================================
// PROCESAR ACCIONES (POST)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $modulo = $_POST['modulo'] ?? '';
    
    // USUARIOS: ahora manejado vía AJAX (api=create_usuario / delete_usuario)
    // Código de creación/eliminación vía formulario clásico ya no es necesario
    // y se omite aquí para evitar duplicidad. Si se requiere compatibilidad
    // hacia atrás, implementar comprobaciones específicas.
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

                // Manejo de imagen subida
                $imagen_db = '';
                if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['imagen'];
                    $allowed = ['jpg','jpeg','png','gif'];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowed)) {
                        echo "Error: Tipo de imagen no permitido.";
                    } else {
                        $upload_dir = __DIR__ . '/../img/productos/';
                        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
                        $unique = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                        $target_full = $upload_dir . $unique;
                        if (@move_uploaded_file($file['tmp_name'], $target_full)) {
                            // Guardar ruta relativa a la web
                            $imagen_db = 'img/productos/' . $unique;
                        } else {
                            echo "Error: No se pudo mover la imagen subida.";
                        }
                    }
                }

                // Validar campos obligatorios
                if (empty($nombre) || empty($codigo) || $id_categoria <= 0 || $precio <= 0) {
                    echo "Error: Debes completar todos los campos obligatorios.";
                } else {
                    // Preparar consulta
                    $stmt = $conn->prepare(
                        "INSERT INTO producto (codigo, nombre, id_categoria, precio, stock, descripcion, imagen, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')"
                    );
                    if ($stmt) {
                        $stmt->bind_param('ssiddss', $codigo, $nombre, $id_categoria, $precio, $stock, $descripcion, $imagen_db);
                        // Ejecutar y verificar
                        if ($stmt->execute()) {
                            echo "Producto agregado correctamente.";
                        } else {
                            echo "Error al agregar producto: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        // evitar fatal: bind_param() on bool
                        echo "Error al preparar consulta de producto: " . $conn->error;
                    }
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
            $nombre = $conn->real_escape_string($_POST['nombre'] ?? '');
            $capacidad = intval($_POST['capacidad'] ?? 0);
            $precio = floatval($_POST['precio'] ?? 0);
            $descripcion = $conn->real_escape_string($_POST['descripcion'] ?? '');

            // Manejo de imagen para tipo
            $imagen_db = '';
            if (!empty($_FILES['imagen_tipo']) && $_FILES['imagen_tipo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['imagen_tipo'];
                $allowed = ['jpg','jpeg','png','gif'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) {
                    echo "Error: Tipo de imagen no permitido para tipo de habitación.";
                } else {
                    $upload_dir = __DIR__ . '/../img/tipos/';
                    if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
                    $unique = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $target_full = $upload_dir . $unique;
                    if (@move_uploaded_file($file['tmp_name'], $target_full)) {
                        $imagen_db = 'img/tipos/' . $unique;
                    } else {
                        echo "Error: No se pudo mover la imagen subida para el tipo.";
                    }
                }
            }

            if (empty($nombre) || $capacidad <= 0 || $precio <= 0) {
                echo "Error: Debes completar los campos obligatorios para el tipo.";
            } else {
                $stmt = $conn->prepare("INSERT INTO tipo_habitacion (nombre, descripcion, capacidad, precio_noche, imagen, estado) VALUES (?, ?, ?, ?, ?, 'activo')");
                if ($stmt) {
                    $stmt->bind_param('ssids', $nombre, $descripcion, $capacidad, $precio, $imagen_db);
                    if ($stmt->execute()) {
                        echo "Tipo de habitación agregado correctamente.";
                    } else {
                        echo "Error al agregar tipo: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    echo "Error al preparar consulta tipo: " . $conn->error;
                }
            }

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

// Cargar categorías de producto para el formulario
$categorias_res = $conn->query("SELECT id_categoria, nombre FROM categoria_producto ORDER BY nombre");
$categorias = [];
if ($categorias_res) {
    while ($c = $categorias_res->fetch_assoc()) $categorias[] = $c;
}

// Determinar qué sección mostrar
$seccion = $_GET['seccion'] ?? 'dashboard';
?>
<link rel="stylesheet" href="../assets/css/admi.css">

<div class="admin-container">
    <button id="sidebar-toggle" class="btn btn-sm" aria-label="Abrir menú" title="Menú">☰</button>
    <!-- ============================================
         SIDEBAR - MENÚ DE NAVEGACIÓN
         ============================================ -->
    <aside class="sidebar">
        <h3><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="M186.67-433.33h260v-180h-260v180Zm0-246.67h586.66v-93.33H186.67V-680Zm0 560q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v270q-15.67-8-32.5-11.5t-34.17-1.84q-21 2-40.83 10.17T696-481.67l-48.33 48.34-201 199.75V-120h-260Zm0-66.67h260v-180h-260v180Zm326.66-246.66h134.34L696-481.67q16.67-16.66 36.5-24.83 19.83-8.17 40.83-10.17v-96.66h-260v180ZM520-80v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8.67 9 12.83 20 4.17 11 4.17 22t-4.33 22.5q-4.34 11.5-13.28 20.5L643-80H520Zm300-263-37-37 37 37ZM580-140h38l121-122-37-37-122 121v38Zm141-141-19-18 37 37-18-19Z"/></svg> Panel Admin</h3>
        <a href="?seccion=dashboard" class="<?= $seccion == 'dashboard' ? 'active' : '' ?>">
            <span><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="M282.67-278h66.66v-276.67h-66.66V-278Zm164 0h66.66v-404h-66.66v404Zm164 0h66.66v-152h-66.66v152Zm-424 158q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586.66q0 27-19.83 46.84Q800.33-120 773.33-120H186.67Zm0-66.67h586.66v-586.66H186.67v586.66Zm0-586.66v586.66-586.66Z"/></svg></span> Dashboard
        </a>
        <a href="?seccion=usuarios" class="<?= $seccion == 'usuarios' ? 'active' : '' ?> big">
            <span><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="M186.67-226.67q58-55 132.43-88.16Q393.53-348 479.93-348t160.9 33.17q74.5 33.16 132.5 88.16v-546.66H186.67v546.66Zm294.66-200.66q58 0 98.34-40.34Q620-508 620-566t-40.33-98.33q-40.34-40.34-98.34-40.34T383-664.33Q342.67-624 342.67-566T383-467.67q40.33 40.34 98.33 40.34ZM186.67-120q-27 0-46.84-19.83Q120-159.67 120-186.67v-586.66q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586.66q0 27-19.83 46.84Q800.33-120 773.33-120H186.67Zm51.66-66.67H721q-56-48.33-116.83-71.5-60.84-23.16-124.17-23.16t-124.5 23.16q-61.17 23.17-117.17 71.5Zm243-307.33q-30 0-51-21t-21-51q0-30 21-51t51-21q30 0 51 21t21 51q0 30-21 51t-51 21Zm-1.33-6.33Z"/></svg></span> Usuarios
        </a>
        <a href="?seccion=productos" class="<?= $seccion == 'productos' ? 'active' : '' ?> big">
            <span><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="M284.53-80.67q-30.86 0-52.7-21.97Q210-124.62 210-155.47q0-30.86 21.98-52.7Q253.95-230 284.81-230t52.69 21.98q21.83 21.97 21.83 52.83t-21.97 52.69q-21.98 21.83-52.83 21.83Zm400 0q-30.86 0-52.7-21.97Q610-124.62 610-155.47q0-30.86 21.98-52.7Q653.95-230 684.81-230t52.69 21.98q21.83 21.97 21.83 52.83t-21.97 52.69q-21.98 21.83-52.83 21.83ZM238.67-734 344-515.33h285.33l120-218.67H238.67ZM206-800.67h589.38q22.98 0 34.97 20.84 11.98 20.83.32 41.83L693.33-490.67q-11 19.34-28.87 30.67-17.87 11.33-39.13 11.33H324l-52 96h487.33V-286H278q-43 0-63-31.83-20-31.84-.33-68.17l60.66-111.33-149.33-316H47.33V-880h121.34L206-800.67Zm138 285.34h285.33H344Z"/></svg></span> Productos
        </a>
        <a href="?seccion=tipos" class="<?= $seccion == 'tipos' ? 'active' : '' ?> big">
            <span><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="M226.67-186.67h140v-246.66h226.66v246.66h140v-380L480-756.67l-253.33 190v380ZM160-120v-480l320-240 320 240v480H526.67v-246.67h-93.34V-120H160Zm320-352Z"/></svg></span> Tipos Habitación
        </a>
        <a href="?seccion=metodos" class="<?= $seccion == 'metodos' ? 'active' : '' ?> big">
            <span><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="M880-733.33v506.66q0 27-19.83 46.84Q840.33-160 813.33-160H146.67q-27 0-46.84-19.83Q80-199.67 80-226.67v-506.66q0-27 19.83-46.84Q119.67-800 146.67-800h666.66q27 0 46.84 19.83Q880-760.33 880-733.33ZM146.67-634h666.66v-99.33H146.67V-634Zm0 139.33v268h666.66v-268H146.67Zm0 268v-506.66 506.66Z"/></svg></span> Métodos de Pago
        </a>
        <a href="?seccion=reportes" class="<?= $seccion == 'reportes' ? 'active' : '' ?> big">
            <span><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="M186.67-120q-27 0-46.84-19.83Q120-159.67 120-186.67V-840h66.67v653.33H840V-120H186.67ZM250-250v-342.67h132.67V-250H250Zm198.67 0v-546.67h132.66V-250H448.67Zm196 0v-180h132.66v180H644.67Z"/></svg></span> Reportes Financieros
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
                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M226-262q59-42.33 121.33-65.5 62.34-23.17 132.67-23.17 70.33 0 133 23.17T734.67-262q41-49.67 59.83-103.67T813.33-480q0-141-96.16-237.17Q621-813.33 480-813.33t-237.17 96.16Q146.67-621 146.67-480q0 60.33 19.16 114.33Q185-311.67 226-262Zm253.88-184.67q-58.21 0-98.05-39.95Q342-526.58 342-584.79t39.96-98.04q39.95-39.84 98.16-39.84 58.21 0 98.05 39.96Q618-642.75 618-584.54t-39.96 98.04q-39.95 39.83-98.16 39.83ZM480.31-80q-82.64 0-155.64-31.5-73-31.5-127.34-85.83Q143-251.67 111.5-324.51T80-480.18q0-82.82 31.5-155.49 31.5-72.66 85.83-127Q251.67-817 324.51-848.5T480.18-880q82.82 0 155.49 31.5 72.66 31.5 127 85.83Q817-708.33 848.5-635.65 880-562.96 880-480.31q0 82.64-31.5 155.64-31.5 73-85.83 127.34Q708.33-143 635.65-111.5 562.96-80 480.31-80Zm-.31-66.67q54.33 0 105-15.83t97.67-52.17q-47-33.66-98-51.5Q533.67-284 480-284t-104.67 17.83q-51 17.84-98 51.5 47 36.34 97.67 52.17 50.67 15.83 105 15.83Zm0-366.66q31.33 0 51.33-20t20-51.34q0-31.33-20-51.33T480-656q-31.33 0-51.33 20t-20 51.33q0 31.34 20 51.34 20 20 51.33 20Zm0-71.34Zm0 369.34Z"/></svg></div>
                    <div class="label">Usuarios del Sistema</div>
                    <div class="value"><?= $usuarios ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M266.67-80v-43.33q-27.67 0-47.17-19.59Q200-162.5 200-190v-463.33q0-27.5 19.58-47.09Q239.17-720 266.67-720h96.66v-123.33q0-15 10.84-25.84Q385-880 400-880h160q15 0 25.83 10.83 10.84 10.84 10.84 25.84V-720h96.66q27.5 0 47.09 19.58Q760-680.83 760-653.33V-190q0 27.5-19.58 47.08-19.59 19.59-47.09 19.59V-80h-66.66v-43.33H333.33V-80h-66.66ZM430-720h100v-93.33H430V-720Zm50 236.67q56.33 0 110.83-13.17t102.5-42.83v-114H266.67v114q48 29.66 102.5 42.83 54.5 13.17 110.83 13.17Zm-33.33 110v-41.34q-48-3.66-93.34-16Q308-443 266.67-466v276h426.66v-276q-41.33 23-86.66 35.33-45.34 12.34-93.34 16v41.34h-66.66Zm33.33 0Zm0-110Zm0 17.33Z"/></svg></div>
                    <div class="label">Clientes Registrados</div>
                    <div class="value"><?= $clientes ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M200.67-160v-383.33L80-451.33 40-504l440-336 172 131.33V-800h106.67v172.67L920-504l-40.67 52.67L758.67-544v384h-232v-240h-93.34v240H200.67Zm66.66-66.67h99.34v-240h226.66v240H692v-367.66l-212-162-212.67 162v367.66Zm129.34-339.66h166.66q0-32.67-25-53.84-25-21.16-58.33-21.16t-58.33 21.06q-25 21.06-25 53.94Zm-30 339.66v-240h226.66v240-240H366.67v240Z"/></svg></div>
                    <div class="label">Habitaciones Totales</div>
                    <div class="value"><?= $habitaciones ?></div>
                </div>
                <div class="stat-card">
                    <div class="icon"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M333.33-120q-89 0-151.16-62.17Q120-244.33 120-333.33q0-38 13-73t36.33-64L318-648.67 222.33-840h516l-95.66 191.33 148 178.34q24 29 36.66 64 12.67 35 13.34 73 0 89-62.34 151.16Q716-120 627.33-120h-294ZM480-332q-27.67 0-46.83-19.5Q414-371 414-398.67q0-27.66 19.17-47.16 19.16-19.5 46.83-19.5 28.33 0 47.83 19.5t19.5 47.16q0 27.67-19.5 47.17T480-332ZM378.33-676.67h203.34l48.66-96.66h-300l48 96.66Zm-45 490h293.34q61 0 103.83-42.83t42.83-103.83q0-26-8.83-50.17T739.33-427L587-610H373.33l-152 182.67Q205-408 195.83-383.67q-9.16 24.34-9.16 50.34 0 61 42.83 103.83t103.83 42.83Z"/></svg></div>
                    <div class="label">Ventas del Día</div>
                    <div class="value"><?= formatoMoneda($ventas ?? 0) ?></div>
                </div>
            </div>

        <?php elseif ($seccion == 'usuarios'): ?>
            <!-- USUARIOS (AJAX: formulario izquierda, resultados derecha) -->
            <div class="page-header">
                <h2>Gestión de Usuarios</h2>
                <p>Administra los usuarios del sistema</p>
            </div>

            <!-- estilos de layout movidos a assets/css/admi.css -->

            <div class="two-col">
                <div class="left-col">
                    <div class="content-box">
                        <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px" fill="#000000"><path d="M446.67-446.67H200v-66.66h246.67V-760h66.66v246.67H760v66.66H513.33V-200h-66.66v-246.67Z"/></svg> Agregar / Crear Usuario</h3>
                        <form id="user-form">
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
                            <div style="margin-top:12px; display:flex; gap:8px;">
                                <button type="submit" class="btn btn-primary">Agregar Usuario</button>
                                <button type="button" id="user-reset" class="btn">Limpiar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="right-col">
                    <div class="content-box">
                        <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px" fill="#000000"><path d="M773.33-120.67H186.67q-27 0-46.84-19.83Q120-160.33 120-187.33v-586q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586q0 27-19.83 46.83-19.84 19.83-46.84 19.83ZM186.67-639.33h586.66v-134H186.67v134Zm120 66.66h-120v385.34h120v-385.34Zm346.66 0v385.34h120v-385.34h-120Zm-66.66 0H373.33v385.34h213.34v-385.34Z"/></svg> Lista de Usuarios</h3>
                        <div id="users-table" class="table-container">
                            Cargando usuarios...
                        </div>
                    </div>
                </div>
            </div>

            <script>
            (function(){
                const endpoint = 'index.php';

                async function fetchJSON(fd) {
                    const res = await fetch(endpoint, { method: 'POST', body: fd });
                    return res.json();
                }

                async function loadUsers(){
                    const fd = new FormData(); fd.append('api','list_usuarios');
                    try {
                        const j = await fetchJSON(fd);
                        if (j.success) renderUsers(j.data);
                        else document.getElementById('users-table').innerText = 'Error al cargar: ' + (j.error||'');
                    } catch(e){
                        document.getElementById('users-table').innerText = 'Error de red';
                    }
                }

                function renderUsers(users){
                    if (!users || users.length === 0) {
                        document.getElementById('users-table').innerHTML = '<div style="color:#666">No hay usuarios registrados.</div>';
                        return;
                    }
                    let html = '<table><thead><tr><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Acción</th></tr></thead><tbody>';
                    for (const u of users) {
                        const full = (u.nombres||'') + ' ' + (u.apellidos||'');
                        html += `<tr>`+
                            `<td>${escapeHtml(u.username)}</td>`+
                            `<td>${escapeHtml(full)}</td>`+
                            `<td>${escapeHtml(u.email||'')}</td>`+
                            `<td>${escapeHtml(capitalize(u.rol||''))}</td>`+
                            `<td>${escapeHtml(capitalize(u.estado||''))}</td>`+
                            `<td><button data-id="${u.id_usuario}" class="btn btn-danger btn-small btn-delete">Eliminar</button></td>`+
                        `</tr>`;
                    }
                    html += '</tbody></table>';
                    document.getElementById('users-table').innerHTML = html;
                }

                function escapeHtml(s){ return String(s).replace(/[&<>"'`]/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;', '`':'&#96;'}[m]; }); }
                function capitalize(s){ return s ? s.charAt(0).toUpperCase()+s.slice(1) : ''; }

                document.getElementById('user-form').addEventListener('submit', async function(e){
                    e.preventDefault();
                    const fd = new FormData(e.target);
                    fd.append('api','create_usuario');
                    const submitBtn = e.target.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    try {
                        const j = await fetchJSON(fd);
                        if (j.success) {
                            alert('Usuario agregado correctamente');
                            e.target.reset();
                            loadUsers();
                        } else {
                            alert('Error: ' + (j.error||'')); 
                        }
                    } catch(err){
                        alert('Error de red');
                    }
                    submitBtn.disabled = false;
                });

                document.getElementById('user-reset').addEventListener('click', function(){ document.getElementById('user-form').reset(); });

                document.getElementById('users-table').addEventListener('click', async function(e){
                    if (e.target && e.target.classList.contains('btn-delete')){
                        const id = e.target.getAttribute('data-id');
                        if (!confirm('¿Eliminar usuario?')) return;
                        const fd = new FormData(); fd.append('api','delete_usuario'); fd.append('id_usuario', id);
                        try {
                            const j = await fetchJSON(fd);
                            if (j.success) {
                                loadUsers();
                            } else {
                                alert('Error: ' + (j.error||''));
                            }
                        } catch(err){ alert('Error de red'); }
                    }
                });

                // inicializar
                loadUsers();
            })();
            </script>

        <?php elseif ($seccion == 'productos'): ?>


            <!-- PRODUCTOS -->
            <div class="page-header">
                <h2>Productos y Servicios</h2>
                <p>Administra los productos disponibles</p>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M446.67-446.67H200v-66.66h246.67V-760h66.66v246.67H760v66.66H513.33V-200h-66.66v-246.67Z"/></svg> Agregar Nuevo Producto</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="modulo" value="productos">
                    <input type="hidden" name="accion" value="nuevo">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Código *</label>
                            <input type="text" name="codigo" required>
                        </div>
                        <div class="form-group">
                            <label>Precio *</label>
                            <input type="number" step="0.01" name="precio" required>
                        </div>
                        <div class="form-group">
                            <label>Categoría *</label>
                            <select name="id_categoria" required>
                                <option value="">-- Seleccionar categoría --</option>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label>Imagen (opcional)</label>
                            <input type="file" name="imagen" accept="image/*">
                        </div>
                        <div class="form-group" style="flex:1 1 100%;">
                            <label>Descripción</label>
                            <textarea name="descripcion" rows="3" style="width:100%;"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Producto</button>
                </form>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M773.33-120.67H186.67q-27 0-46.84-19.83Q120-160.33 120-187.33v-586q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586q0 27-19.83 46.83-19.84 19.83-46.84 19.83ZM186.67-639.33h586.66v-134H186.67v134Zm120 66.66h-120v385.34h120v-385.34Zm346.66 0v385.34h120v-385.34h-120Zm-66.66 0H373.33v385.34h213.34v-385.34Z"/></svg> Lista de Productos</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $productos_list = $conn->query("SELECT p.*, c.nombre AS categoria_nombre FROM producto p LEFT JOIN categoria_producto c ON p.id_categoria = c.id_categoria ORDER BY p.nombre");
                            while($p = $productos_list->fetch_assoc()):
                            ?>
                            <tr>
                                <td>
                                    <?php
                                    $img_path = '';
                                    if (!empty($p['imagen'])) {
                                        if (strpos($p['imagen'], '/') !== false) {
                                            $img_path = $p['imagen'];
                                        } else {
                                            $img_path = 'img/productos/' . $p['imagen'];
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($img_path) && file_exists(__DIR__ . '/../' . $img_path)): ?>
                                        <img src="<?= URL_BASE . htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>" style="height:48px; object-fit:cover; border-radius:6px;">
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['nombre']) ?></td>
                                <td><?= formatoMoneda($p['precio']) ?></td>
                                <td><?= $p['stock'] ?></td>
                                <td><?= htmlspecialchars($p['categoria_nombre'] ?? '') ?></td>
                                <td><?= ucfirst($p['estado']) ?></td>
                                <td>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="modulo" value="productos">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar producto?')"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="m366-299.33 114-115.34 114.67 115.34 50-50.67-114-115.33 114-115.34-50-50.66L480-516 366-631.33l-50.67 50.66L430-465.33 315.33-350 366-299.33ZM267.33-120q-27 0-46.83-19.83-19.83-19.84-19.83-46.84V-740H160v-66.67h192V-840h256v33.33h192V-740h-40.67v553.33q0 27-19.83 46.84Q719.67-120 692.67-120H267.33Zm425.34-620H267.33v553.33h425.34V-740Zm-425.34 0v553.33V-740Z"/></svg></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>

        <?php elseif ($seccion == 'tipos'): ?>

            
            <!-- TIPOS DE HABITACIÓN -->
            <div class="page-header">
                <h2>Tipos de Habitación</h2>
                <p>Gestiona los tipos de habitaciones disponibles</p>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M446.67-446.67H200v-66.66h246.67V-760h66.66v246.67H760v66.66H513.33V-200h-66.66v-246.67Z"/></svg> Agregar Tipo de Habitación</h3>
                <form method="POST" enctype="multipart/form-data">
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
                        <div class="form-group">
                            <label>Imagen (opcional)</label>
                            <input type="file" name="imagen_tipo" accept="image/*">
                        </div>
                        <div class="form-group" style="flex:1 1 100%;">
                            <label>Descripción</label>
                            <textarea name="descripcion" rows="3" style="width:100%;"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Tipo</button>
                </form>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M773.33-120.67H186.67q-27 0-46.84-19.83Q120-160.33 120-187.33v-586q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586q0 27-19.83 46.83-19.84 19.83-46.84 19.83ZM186.67-639.33h586.66v-134H186.67v134Zm120 66.66h-120v385.34h120v-385.34Zm346.66 0v385.34h120v-385.34h-120Zm-66.66 0H373.33v385.34h213.34v-385.34Z"/></svg> Lista de Tipos</h3>
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
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar tipo?')"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="m366-299.33 114-115.34 114.67 115.34 50-50.67-114-115.33 114-115.34-50-50.66L480-516 366-631.33l-50.67 50.66L430-465.33 315.33-350 366-299.33ZM267.33-120q-27 0-46.83-19.83-19.83-19.84-19.83-46.84V-740H160v-66.67h192V-840h256v33.33h192V-740h-40.67v553.33q0 27-19.83 46.84Q719.67-120 692.67-120H267.33Zm425.34-620H267.33v553.33h425.34V-740Zm-425.34 0v553.33V-740Z"/></svg></button>
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
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M446.67-446.67H200v-66.66h246.67V-760h66.66v246.67H760v66.66H513.33V-200h-66.66v-246.67Z"/></svg> Agregar Método de Pago</h3>
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
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M773.33-120.67H186.67q-27 0-46.84-19.83Q120-160.33 120-187.33v-586q0-27 19.83-46.84Q159.67-840 186.67-840h586.66q27 0 46.84 19.83Q840-800.33 840-773.33v586q0 27-19.83 46.83-19.84 19.83-46.84 19.83ZM186.67-639.33h586.66v-134H186.67v134Zm120 66.66h-120v385.34h120v-385.34Zm346.66 0v385.34h120v-385.34h-120Zm-66.66 0H373.33v385.34h213.34v-385.34Z"/></svg> Lista de Métodos</h3>
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
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar método?')"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="m366-299.33 114-115.34 114.67 115.34 50-50.67-114-115.33 114-115.34-50-50.66L480-516 366-631.33l-50.67 50.66L430-465.33 315.33-350 366-299.33ZM267.33-120q-27 0-46.83-19.83-19.83-19.84-19.83-46.84V-740H160v-66.67h192V-840h256v33.33h192V-740h-40.67v553.33q0 27-19.83 46.84Q719.67-120 692.67-120H267.33Zm425.34-620H267.33v553.33h425.34V-740Zm-425.34 0v553.33V-740Z"/></svg></button>
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
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M160-160v-320h146.67v320H160Zm246.67 0v-640h146.66v640H406.67Zm246.66 0v-440H800v440H653.33Z"/></svg> Resumen General</h3>
                <ul class="summary-list">
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M333.33-120q-89 0-151.16-62.17Q120-244.33 120-333.33q0-38 13-73t36.33-64L318-648.67 222.33-840h516l-95.66 191.33 148 178.34q24 29 36.66 64 12.67 35 13.34 73 0 89-62.34 151.16Q716-120 627.33-120h-294ZM480-332q-27.67 0-46.83-19.5Q414-371 414-398.67q0-27.66 19.17-47.16 19.16-19.5 46.83-19.5 28.33 0 47.83 19.5t19.5 47.16q0 27.67-19.5 47.17T480-332ZM378.33-676.67h203.34l48.66-96.66h-300l48 96.66Zm-45 490h293.34q61 0 103.83-42.83t42.83-103.83q0-26-8.83-50.17T739.33-427L587-610H373.33l-152 182.67Q205-408 195.83-383.67q-9.16 24.34-9.16 50.34 0 61 42.83 103.83t103.83 42.83Z"/></svg> Total pagos registrados: <strong><?= formatoMoneda($total_pagos ?? 0) ?></strong>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M560-570.67v-54.66q33-14 67.5-21t72.5-7q26 0 51 4t49 10v50.66q-24-9-48.5-13.5t-51.5-4.5q-38 0-73 9.5t-67 26.5Zm0 220V-406q33-13.67 67.5-20.5t72.5-6.83q26 0 51 4t49 10v50.66q-24-9-48.5-13.5t-51.5-4.5q-38 0-73 9t-67 27Zm0-110v-54.66q33-14 67.5-21t72.5-7q26 0 51 4t49 10v50.66q-24-9-48.5-13.5t-51.5-4.5q-38 0-73 9.5t-67 26.5Zm-308 154q51.38 0 100.02 11.84Q400.67-283 448-259.33v-416q-43.67-28-94.08-43t-101.92-15q-37.33 0-73.5 8.66Q142.33-716 106.67-702v421.33Q139-294 176.83-300.33q37.84-6.34 75.17-6.34Zm262.67 47.34q48-23.67 94.83-35.5 46.83-11.84 98.5-11.84 37.33 0 75.83 6t69.5 16.67v-418q-33.66-16-70.71-23.67-37.05-7.66-74.62-7.66-51.67 0-100.67 15t-92.66 43v416ZM481.33-160q-50-38-108.66-58.67Q314-239.33 252-239.33q-38.36 0-75.35 9.66-36.98 9.67-72.65 25-22.4 11-43.2-2.33Q40-220.33 40-245.33v-469.34q0-13.66 6.5-25.33Q53-751.67 66-758q43.33-21.33 90.26-31.67Q203.19-800 252-800q61.33 0 119.5 16.33 58.17 16.34 109.83 49.67 51-33.33 108.5-49.67Q647.33-800 708-800q48.58 0 95.29 10.33Q850-779.33 893.33-758q13 6.33 19.84 18 6.83 11.67 6.83 25.33v469.34q0 26.26-21.5 39.96t-43.17.7q-35-16-71.98-25.33-36.99-9.33-75.35-9.33-62 0-119.33 21-57.34 21-107.34 58.33Zm-204-330.67Z"/></svg> Total reservas: <strong><?= $total_reservas ?></strong>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M226.67-80q-27 0-46.84-19.83Q160-119.67 160-146.67v-506.66q0-27 19.83-46.84Q199.67-720 226.67-720h100v-6.67q0-64 44.66-108.66Q416-880 480-880t108.67 44.67q44.66 44.66 44.66 108.66v6.67h100q27 0 46.84 19.83Q800-680.33 800-653.33v506.66q0 27-19.83 46.84Q760.33-80 733.33-80H226.67Zm0-66.67h506.66v-506.66h-100v86.66q0 14.17-9.61 23.75-9.62 9.59-23.84 9.59-14.21 0-23.71-9.59-9.5-9.58-9.5-23.75v-86.66H393.33v86.66q0 14.17-9.61 23.75-9.62 9.59-23.84 9.59-14.21 0-23.71-9.59-9.5-9.58-9.5-23.75v-86.66h-100v506.66ZM393.33-720h173.34v-6.67q0-36.33-25.17-61.5-25.17-25.16-61.5-25.16t-61.5 25.16q-25.17 25.17-25.17 61.5v6.67ZM226.67-146.67v-506.66 506.66Z"/></svg> Total ventas en recepción: <strong><?= formatoMoneda($total_ventas ?? 0) ?></strong>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M120-120v-521.33L636.67-840v360h70q0-27.5 19.62-47.08 19.61-19.59 47.16-19.59 27.55 0 47.05 19.59Q840-507.5 840-480v360H120Zm66.67-66.67h180V-480H570v-263.33L186.67-595.67v409Zm246.66 0H570V-300h66.67v113.33h136.66v-226.66h-340v226.66ZM300-546.67Zm303.33 360Zm0-23.33Z"/></svg> Total alquileres registrados: <strong><?= $total_alquileres ?></strong>
                    </li>
                </ul>
            </div>
            
            <div class="content-box">
                <h3 style="color: #004080; margin-bottom: 20px;"><svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#000000"><path d="M880-733.33v506.66q0 27-19.83 46.84Q840.33-160 813.33-160H146.67q-27 0-46.84-19.83Q80-199.67 80-226.67v-506.66q0-27 19.83-46.84Q119.67-800 146.67-800h666.66q27 0 46.84 19.83Q880-760.33 880-733.33ZM146.67-634h666.66v-99.33H146.67V-634Zm0 139.33v268h666.66v-268H146.67Zm0 268v-506.66 506.66Z"/></svg> Pagos Recientes</h3>
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
  <div class="logo">
            <img src="../assets/img/logo.png" alt="Logo" />
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= URL_BASE ?>assets/js/admin-sidebar.js"></script>
<style>
    
</style>
<?php include '../includes/footer.php'; ?>
