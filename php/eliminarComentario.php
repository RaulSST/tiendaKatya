<?php
header('Content-Type: application/json');
session_start();
include_once("conexionBD.php");

// Verificar si el usuario está logueado en el backend
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes estar logueado para eliminar comentarios.']);
    exit;
}

$comentarioId = $_POST['id_comentario'] ?? null;
$usuarioId = $_POST['usuario_id'] ?? null; // Recibimos el ID del usuario que intenta eliminar
$loggedInUserId = $_SESSION['user_id']; // Obtener el ID del usuario de la sesión

if ($comentarioId === null || !is_numeric($comentarioId) || $usuarioId === null || !is_numeric($usuarioId)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos para eliminar el comentario.']);
    exit;
}

// Verificar que el usuario logueado es el autor del comentario
$sqlVerificar = "SELECT usuario_id FROM comentarios WHERE id = ?";
$stmtVerificar = mysqli_prepare($conn, $sqlVerificar);
mysqli_stmt_bind_param($stmtVerificar, "i", $comentarioId);
mysqli_stmt_execute($stmtVerificar);
$resultVerificar = mysqli_stmt_get_result($stmtVerificar);

if ($rowVerificar = mysqli_fetch_assoc($resultVerificar)) {
    $autorId = $rowVerificar['usuario_id'];
    mysqli_stmt_close($stmtVerificar);

    // Comparamos el ID del autor con el ID del usuario LOGUEADO en la sesión
    if ($autorId != $loggedInUserId) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar este comentario.']);
        mysqli_close($conn);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Comentario no encontrado.']);
    mysqli_close($conn);
    exit;
}


$sql = "UPDATE comentarios SET activo = FALSE WHERE id = ?"; // O podrías eliminarlo permanentemente con DELETE
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $comentarioId);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Comentario eliminado.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el comentario: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
}

mysqli_close($conn);
?>