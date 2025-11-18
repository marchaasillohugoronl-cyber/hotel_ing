<?php
// Contraseña que quieres guardar
$password_plain = "1234"; 

// Hashear la contraseña usando bcrypt (recomendado)
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

echo "Contraseña original: $password_plain\n";
echo "Hash generado: $password_hash\n";
?>


INSERT INTO usuario (username, password, nombres, apellidos, email, rol, estado)
VALUES ('recep', '$2y$10$dsxTE4aaGd20d75ducr/D.xFAsNeccLQLcWZAP8HTMmUjtj7TCV4K', 'María', 'Recepcionista', 'recepcion@hostal.com', 'recepcionista', 'activo');
