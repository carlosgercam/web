<?php
// 1. CONEXIÓN A LA BASE DE DATOS
// La variable $conn (la conexión activa) estará disponible aquí.
require_once __DIR__ . '/../db_config.php'; 

// 2. Obtener y sanear los datos
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';

// 3. Validar que los campos no estén vacíos
if (empty($nombre) || empty($apellidos) || empty($correo) || empty($contrasena)) {
    echo "Error: Todos los campos son requeridos.";
    exit();
}

// 4. HASHEAR la contraseña (¡SEGURIDAD CLAVE!)
// Genera un hash seguro que se almacena en la DB. NUNCA se debe guardar la contraseña en texto plano.
$contrasena_hashed = password_hash($contrasena, PASSWORD_DEFAULT);

// 5. Preparar la consulta SQL con sentencias preparadas (¡SEGURIDAD contra Inyección SQL!)
$sql = "INSERT INTO users (nombre, apellidos, correo, contrasena) VALUES (?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    // Enlazar las variables a la sentencia preparada (ss de string, string, string, string)
    $stmt->bind_param("ssss", $param_nombre, $param_apellidos, $param_correo, $param_contrasena);
    
    // Establecer los parámetros con los datos del usuario
    $param_nombre = $nombre;
    $param_apellidos = $apellidos;
    $param_correo = $correo;
    $param_contrasena = $contrasena_hashed; // Guardamos el hash
    
    // 6. Ejecutar la sentencia
    if ($stmt->execute()) {
        // Registro guardado exitosamente, redirigir a catálogo
        header("Location: ../catalogo/catalogo.php");
        exit();
    } else {
        // Manejar error: si el error es 1062, significa que el correo ya existe (UNIQUE)
        if ($conn->errno == 1062) {
             echo "Error: Este correo electrónico ya está registrado. Por favor, intenta con otro.";
        } else {
             // Mostrar otros errores de la base de datos
             echo "Error al guardar el registro. Por favor, intenta de nuevo. (" . $stmt->error . ")";
        }
    }
    
    // Cerrar la sentencia
    $stmt->close();
} else {
    echo "Error interno al preparar la consulta SQL.";
}

// 7. Cerrar la conexión
$conn->close();
?>