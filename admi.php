<?php
// Contraseña del administrador
$password_plain = "1234"; // Cambia por la contraseña que quieras

// Hashear la contraseña usando bcrypt
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

echo "Contraseña original: $password_plain\n";
echo "Hash generado: $password_hash\n";
?>


//INSERT INTO usuario (username, password, nombres, apellidos, email, rol, estado)
VALUES ('admin1', '$2y$10$LVLeB6xDzrTV3Sdua/Dgg.AZ0gBMxcHWwsoJ.zsGhPvKVV28CgxxW', 'Admin', 'General', 'admin@hostal.com', 'administrador', 'activo');
