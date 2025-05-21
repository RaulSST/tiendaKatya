<?php
session_start();
include_once("../../php/conexionBD.php"); // Asegúrate de que la ruta sea correcta

// Verificar si existe la cookie "recordarme_token" para eliminarla
if (isset($_COOKIE['recordarme_token'])) {
    $token_a_eliminar = $_COOKIE['recordarme_token'];

    // Eliminar el token de la base de datos
    $sql_eliminar_token = "DELETE FROM tokens WHERE token = ?";
    $stmt = mysqli_prepare($conn, $sql_eliminar_token);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token_a_eliminar);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Eliminar la cookie del navegador
        setcookie('recordarme_token', '', time() - 3600, '/');
    } else {
        // Opcional: Manejar el error al preparar la consulta de eliminación del token
        error_log("Error al preparar la consulta de eliminación del token en logout.php: " . mysqli_error($conn));
    }
}

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión actual
session_destroy();

// Eliminar la cookie de sesión (opcional, ya que se destruye la sesión)
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000,
    $params["path"], $params["domain"],
    $params["secure"], $params["httponly"]
);

// Redirigir al usuario a la página de inicio o de login
header("Location: ../../index.php"); // Ajusta la ruta según tu estructura de carpetas
exit();
?>