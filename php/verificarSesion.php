<?php
session_start();
include_once("conexionBD.php"); // Asegúrate de que la ruta sea correcta

// Función para buscar un token válido en la base de datos y obtener el ID del usuario
function verificarToken($conn, $token) {
    $sql = "SELECT usuario_id FROM tokens WHERE token = ? AND expiracion > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            return $row['usuario_id'];
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// Verificar si existe la cookie "recordarme_token" y el usuario NO está logueado
if (!isset($_SESSION['user_id']) && isset($_COOKIE['recordarme_token'])) {
    $token_en_cookie = $_COOKIE['recordarme_token'];

    // Verificar el token en la base de datos
    $user_id = verificarToken($conn, $token_en_cookie);

    if ($user_id) {
        // Token válido, iniciar sesión automáticamente al usuario
        $sql_usuario = "SELECT id, nombre, email FROM usuarios WHERE id = ?";
        $stmt_usuario = mysqli_prepare($conn, $sql_usuario);
        if ($stmt_usuario) {
            mysqli_stmt_bind_param($stmt_usuario, "i", $user_id);
            mysqli_stmt_execute($stmt_usuario);
            $result_usuario = mysqli_stmt_get_result($stmt_usuario);
            if ($user_data = mysqli_fetch_assoc($result_usuario)) {
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['user_name'] = $user_data['nombre'];
                $_SESSION['user_email'] = $user_data['email'];
                // Opcional: Podrías regenerar el token y actualizar la cookie aquí para mayor seguridad
            }
            mysqli_stmt_close($stmt_usuario);
        }
    } else {
        // Token no válido o expirado, eliminar la cookie
        setcookie('recordarme_token', '', time() - 3600, '/');
    }
}

// Ahora, $isLoggedIn debería reflejar correctamente si el usuario está logueado
// ya sea por la sesión tradicional o por la cookie "recordarme_token"
$isLoggedIn = isset($_SESSION['user_id']); // Asigna un valor booleano directamente
?>