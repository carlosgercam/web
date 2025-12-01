<?php
// 1. INCLUIR CONEXIÓN A LA BASE DE DATOS
// Asegúrate de que esta ruta a db_config.php sea correcta para tu estructura de carpetas.
require_once __DIR__ . '/../db_config.php'; 

// 2. Obtener y sanear los datos del formulario
$usuario = trim($_POST['usuario'] ?? '');
$contrasena_ingresada = $_POST['contrasena'] ?? '';
$credencial_valida = false;

// 3. CONSULTAR EL USUARIO EN LA TABLA 'users' (Busca por correo o nombre)
// Se usa la función prepare() para prevenir ataques de inyección SQL.
$sql = "SELECT contrasena FROM users WHERE correo = ? OR nombre = ?";

if ($stmt = $conn->prepare($sql)) {
    // Enlazar el texto ingresado a los parámetros de la consulta (correo y nombre)
    $stmt->bind_param("ss", $usuario, $usuario);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Almacenar el resultado para contar filas
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        // Enlazar el resultado de la columna 'contrasena' a una variable
        $stmt->bind_result($contrasena_hashed);
        $stmt->fetch();
        
        // 4. VERIFICAR CONTRASEÑA con el hash guardado
        // password_verify() compara la contraseña ingresada con el hash de la DB.
        if (password_verify($contrasena_ingresada, $contrasena_hashed)) {
            $credencial_valida = true;
            // Aquí puedes iniciar una sesión de PHP si lo deseas: session_start();
        }
    }
    
    // Cerrar la sentencia
    $stmt->close();
}

// 5. CERRAR CONEXIÓN
$conn->close();

// El resto del código HTML y JavaScript para mostrar el resultado
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resultado del login</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.7);
      font-family: 'Segoe UI', sans-serif;
    }
    
    .modal {
      background-color: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
      text-align: center;
      min-width: 300px;
    }
    
    .modal h2 {
      margin: 0 0 20px 0;
      font-size: 24px;
    }
    
    .modal.success h2 {
      color: #28a745;
    }
    
    .modal.error h2 {
      color: #dc3545;
    }
    
    .modal p {
      margin: 0;
      color: #666;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="modal <?php echo $credencial_valida ? 'success' : 'error'; ?>">
    <h2><?php echo $credencial_valida ? '✓ Bienvenido' : '✗ Credenciales incorrectas'; ?></h2>
    <p><?php echo $credencial_valida ? 'Redirigiendo al catálogo...' : 'Usuario o contraseña incorrectos. Intenta de nuevo.'; ?></p>
  </div>
  
  <script>
    <?php if ($credencial_valida): ?>
      setTimeout(function() {
        window.location.href = '../catalogo/catalogo.php';
      }, 2000);
    <?php else: ?>
      setTimeout(function() {
        window.location.href = 'in.sesion.php';
      }, 3000);
    <?php endif; ?>
  </script>
</body>
</html>