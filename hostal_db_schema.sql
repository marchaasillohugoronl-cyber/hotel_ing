-- ============================================================
-- SISTEMA: Hostal El Dulce Descanso
-- Base de Datos completa + datos iniciales
-- Compatible con PHP 8 y MySQL 8
-- ============================================================

CREATE DATABASE IF NOT EXISTS hostal_dulce_descanso_01 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hostal_dulce_descanso_01;

-- ============================================
-- TABLA: tipo_habitacion
-- ============================================
CREATE TABLE tipo_habitacion (
    id_tipo INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    capacidad INT NOT NULL,
    precio_noche DECIMAL(10,2) NOT NULL,
    tiene_banio BOOLEAN DEFAULT TRUE,
    tiene_tv BOOLEAN DEFAULT TRUE,
    tiene_jacuzzi BOOLEAN DEFAULT FALSE,
    tiene_internet BOOLEAN DEFAULT FALSE,
    imagen VARCHAR(255),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

-- ============================================
-- TABLA: habitacion
-- ============================================
CREATE TABLE habitacion (
    id_habitacion INT PRIMARY KEY AUTO_INCREMENT,
    numero VARCHAR(10) NOT NULL UNIQUE,
    id_tipo INT NOT NULL,
    piso INT NOT NULL,
    estado ENUM('disponible', 'ocupada', 'mantenimiento', 'reservada') DEFAULT 'disponible',
    observaciones TEXT,
    FOREIGN KEY (id_tipo) REFERENCES tipo_habitacion(id_tipo)
);

-- ============================================
-- TABLA: cliente
-- ============================================
CREATE TABLE cliente (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    tipo_doc ENUM('DNI', 'CE', 'pasaporte') NOT NULL,
    num_doc VARCHAR(20) NOT NULL UNIQUE,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_nacimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- TABLA: usuario
-- ============================================
CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol ENUM('administrador', 'recepcionista', 'cliente') NOT NULL,
    id_cliente INT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) ON DELETE CASCADE
);

-- ============================================
-- TABLA: reserva
-- ============================================
CREATE TABLE reserva (
    id_reserva INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    id_cliente INT NOT NULL,
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrada DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    num_noches INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'expirada') DEFAULT 'pendiente',
    tipo ENUM('presencial', 'web') NOT NULL,
    fecha_limite_pago DATETIME,
    observaciones TEXT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);

-- ============================================
-- TABLA: reserva_habitacion
-- ============================================
CREATE TABLE reserva_habitacion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_reserva INT NOT NULL,
    id_habitacion INT NOT NULL,
    precio_noche DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_reserva) REFERENCES reserva(id_reserva) ON DELETE CASCADE,
    FOREIGN KEY (id_habitacion) REFERENCES habitacion(id_habitacion)
);

-- ============================================
-- TABLA: alquiler
-- ============================================
CREATE TABLE alquiler (
    id_alquiler INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    id_cliente INT NOT NULL,
    id_reserva INT NULL,
    fecha_checkin DATETIME NOT NULL,
    fecha_checkout_programado DATETIME NOT NULL,
    fecha_checkout_real DATETIME NULL,
    total_hospedaje DECIMAL(10,2) NOT NULL,
    total_servicios DECIMAL(10,2) DEFAULT 0,
    total_final DECIMAL(10,2) NOT NULL,
    estado ENUM('activo', 'finalizado') DEFAULT 'activo',
    observaciones TEXT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_reserva) REFERENCES reserva(id_reserva)
);

-- ============================================
-- TABLA: alquiler_habitacion
-- ============================================
CREATE TABLE alquiler_habitacion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_alquiler INT NOT NULL,
    id_habitacion INT NOT NULL,
    precio_noche DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_alquiler) REFERENCES alquiler(id_alquiler) ON DELETE CASCADE,
    FOREIGN KEY (id_habitacion) REFERENCES habitacion(id_habitacion)
);

-- ============================================
-- TABLA: metodo_pago
-- ============================================
CREATE TABLE metodo_pago (
    id_metodo INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

-- ============================================
-- TABLA: pago
-- ============================================
CREATE TABLE pago (
    id_pago INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    tipo ENUM('reserva', 'hospedaje', 'servicio', 'venta') NOT NULL,
    id_referencia INT NOT NULL COMMENT 'ID de reserva, alquiler, servicio o venta',
    id_metodo INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comprobante VARCHAR(50),
    observaciones TEXT,
    FOREIGN KEY (id_metodo) REFERENCES metodo_pago(id_metodo)
);

-- ============================================
-- TABLA: categoria_producto
-- ============================================
CREATE TABLE categoria_producto (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    tipo ENUM('aseo', 'bebida', 'comida', 'snack') NOT NULL,
    descripcion TEXT
);

-- ============================================
-- TABLA: producto
-- ============================================
CREATE TABLE producto (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    id_categoria INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    descripcion TEXT,
    imagen VARCHAR(255),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categoria_producto(id_categoria)
);

-- ============================================
-- TABLA: servicio_habitacion
-- ============================================
CREATE TABLE servicio_habitacion (
    id_servicio INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    id_alquiler INT NOT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATETIME NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('solicitado', 'entregado', 'pagado', 'cancelado') DEFAULT 'solicitado',
    observaciones TEXT,
    FOREIGN KEY (id_alquiler) REFERENCES alquiler(id_alquiler)
);

-- ============================================
-- TABLA: servicio_detalle
-- ============================================
CREATE TABLE servicio_detalle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_servicio INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_servicio) REFERENCES servicio_habitacion(id_servicio) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

-- ============================================
-- TABLA: venta_recepcion
-- ============================================
CREATE TABLE venta_recepcion (
    id_venta INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    id_cliente INT NULL,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    id_metodo INT NOT NULL,
    comprobante VARCHAR(50),
    observaciones TEXT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente),
    FOREIGN KEY (id_metodo) REFERENCES metodo_pago(id_metodo)
);

-- ============================================
-- TABLA: venta_detalle
-- ============================================
CREATE TABLE venta_detalle (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES venta_recepcion(id_venta) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

-- ============================================
-- TABLA: contacto
-- ============================================
CREATE TABLE contacto (
    id_contacto INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE
);

-- Vista para mostrar reservas de clientes
CREATE OR REPLACE VIEW v_reservas AS
SELECT r.id_reserva, r.codigo, r.id_cliente, c.nombres, c.apellidos, r.fecha_reserva, r.fecha_entrada, r.fecha_salida, r.num_noches, r.total, r.estado, r.tipo
FROM reserva r
JOIN cliente c ON r.id_cliente = c.id_cliente;

-- Vista para mostrar alquileres activos de clientes
CREATE OR REPLACE VIEW v_alquileres_activos AS
SELECT a.id_alquiler, a.codigo, a.id_cliente, c.nombres, c.apellidos, a.fecha_checkin, a.fecha_checkout_programado, a.fecha_checkout_real, a.total_hospedaje, a.total_servicios, a.total_final, a.estado
FROM alquiler a
JOIN cliente c ON a.id_cliente = c.id_cliente
WHERE a.estado = 'activo';

-- ============================================
-- DATOS INICIALES
-- ============================================

INSERT INTO tipo_habitacion (nombre, descripcion, capacidad, precio_noche, tiene_banio, tiene_tv, tiene_jacuzzi, tiene_internet) VALUES
('Económica', 'Cama de 1 plaza, habitación sencilla sin baño privado.', 1, 50.00, FALSE, FALSE, FALSE, FALSE),
('Simple', 'Cama de 1.5 plazas, baño privado y TV.', 1, 80.00, TRUE, TRUE, FALSE, FALSE),
('Doble', '2 camas de 1.5 plazas.', 2, 120.00, TRUE, TRUE, FALSE, FALSE),
('Matrimonial', 'Cama Queen para pareja.', 2, 150.00, TRUE, TRUE, FALSE, FALSE),
('Suite', 'Cama King, jacuzzi, internet y servicio a la habitación.', 2, 250.00, TRUE, TRUE, TRUE, TRUE);

INSERT INTO habitacion (numero, id_tipo, piso) VALUES
('101', 1, 1), ('102', 1, 1), ('201', 2, 2), ('202', 2, 2),
('301', 3, 3), ('302', 3, 3), ('401', 4, 4), ('402', 4, 4), ('501', 5, 5);

INSERT INTO metodo_pago (nombre, descripcion) VALUES
('Efectivo', 'Pago en efectivo'),
('Transferencia', 'Depósito o transferencia'),
('Yape', 'Pago con Yape'),
('Plin', 'Pago con Plin'),
('Tarjeta', 'Tarjeta de crédito o débito');

INSERT INTO categoria_producto (nombre, tipo, descripcion) VALUES
('Artículos de aseo', 'aseo', 'Productos de baño y limpieza personal'),
('Bebidas', 'bebida', 'Bebidas frías y calientes'),
('Comidas', 'comida', 'Comidas servidas en el hostal'),
('Snacks', 'snack', 'Golosinas y aperitivos');

-- Usuarios iniciales
INSERT INTO usuario (username, password, nombres, apellidos, email, rol, estado)
VALUES
('admin', SHA2('admin123',256), 'Administrador', 'General', 'admin@hostal.com', 'administrador', 'activo'),
('recep1', SHA2('recep123',256), 'María', 'Recepcionista', 'recepcion@hostal.com', 'recepcionista', 'activo');


CREATE OR REPLACE VIEW v_reservas AS
SELECT r.id_reserva, r.codigo, r.id_cliente, c.nombres, c.apellidos, r.fecha_reserva, r.fecha_entrada, r.fecha_salida, r.num_noches, r.total, r.estado, r.tipo
FROM reserva r
JOIN cliente c ON r.id_cliente = c.id_cliente;

CREATE OR REPLACE VIEW v_alquileres_activos AS
SELECT a.id_alquiler, a.codigo, a.id_cliente, c.nombres, c.apellidos, a.fecha_checkin, a.fecha_checkout_programado, a.fecha_checkout_real, a.total_hospedaje, a.total_servicios, a.total_final, a.estado
FROM alquiler a
JOIN cliente c ON a.id_cliente = c.id_cliente
WHERE a.estado = 'activo';


-- ============================================
-- PRODUCTOS INICIALES DEL HOSTAL
-- ============================================

-- Artículos de aseo
INSERT INTO producto (codigo, nombre, id_categoria, precio, stock, descripcion, imagen)
VALUES
('ASEO01', 'Jabón de tocador', 1, 1.50, 50, 'Jabón de tocador individual para huéspedes', 'jabon.jpg'),
('ASEO02', 'Shampoo pequeño', 1, 2.00, 40, 'Shampoo en presentación individual', 'shampoo.jpg'),
('ASEO03', 'Cepillo dental', 1, 1.00, 30, 'Cepillo dental desechable', 'cepillo.jpg');

-- Bebidas
INSERT INTO producto (codigo, nombre, id_categoria, precio, stock, descripcion, imagen)
VALUES
('BEB01', 'Agua mineral 500ml', 2, 2.50, 100, 'Botella de agua mineral', 'agua.jpg'),
('BEB02', 'Refresco 350ml', 2, 3.00, 80, 'Refresco en lata', 'refresco.jpg'),
('BEB03', 'Café instantáneo', 2, 1.50, 60, 'Café soluble para preparar en la habitación', 'cafe.jpg');

-- Comidas
INSERT INTO producto (codigo, nombre, id_categoria, precio, stock, descripcion, imagen)
VALUES
('COM01', 'Desayuno Buffet', 3, 12.50, 50, 'Desayuno completo con frutas, huevos y pan', 'desayuno.jpg'),
('COM02', 'Almuerzo Ejecutivo', 3, 20.00, 40, 'Plato principal con bebida y postre', 'almuerzo.jpg'),
('COM03', 'Cena Ligera', 3, 18.00, 30, 'Cena ligera con ensalada y plato principal', 'cena.jpg');

-- Snacks
INSERT INTO producto (codigo, nombre, id_categoria, precio, stock, descripcion, imagen)
VALUES
('SNA01', 'Galletas surtidas', 4, 1.50, 100, 'Paquete de galletas variadas', 'galletas.jpg'),
('SNA02', 'Chocolates', 4, 2.00, 80, 'Chocolate individual para minibar', 'chocolate.jpg'),
('SNA03', 'Frutos secos', 4, 3.50, 50, 'Mezcla de frutos secos', 'frutos.jpg');
