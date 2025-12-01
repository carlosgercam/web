<?php
// Configuración de la base de datos para XAMPP (por defecto)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Contraseña de root en XAMPP suele estar vacía
define('DB_NAME', 'autopremium');

// Intentar la conexión usando MySQLi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    // Detener la ejecución del script y mostrar el error de conexión
    die("ERROR: No se pudo conectar a la base de datos: " . $conn->connect_error);
}

// Opcional: Establecer el juego de caracteres a utf8 para evitar problemas con acentos y caracteres especiales
$conn->set_charset("utf8");
?>