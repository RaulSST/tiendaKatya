<?php
header('Content-Type: application/json');
session_start();
include_once("conexionBD.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes estar logueado para editar comentarios.']);
    exit;
}

$comentarioId = $_POST['id_comentario'] ?? null;
$nuevoTexto = $_POST['texto'] ?? null;
$usuarioId = $_POST['usuario_id'] ?? null; // Recibir el ID del usuario que intenta editar
$loggedInUserId = $_SESSION['user_id']; // Obtener el ID del usuario de la sesión

if ($comentarioId === null || !is_numeric($comentarioId) || empty($nuevoTexto) || $usuarioId === null || !is_numeric($usuarioId)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos para editar el comentario.']);
    exit;
}

// Verificar si el usuario logueado es el autor del comentario
$sqlVerificar = "SELECT usuario_id FROM comentarios WHERE id = ?";
$stmtVerificar = mysqli_prepare($conn, $sqlVerificar);
mysqli_stmt_bind_param($stmtVerificar, "i", $comentarioId);
mysqli_stmt_execute($stmtVerificar);
$resultVerificar = mysqli_stmt_get_result($stmtVerificar);

if ($rowVerificar = mysqli_fetch_assoc($resultVerificar)) {
    $autorId = $rowVerificar['usuario_id'];
    mysqli_stmt_close($stmtVerificar);

    // Comparamos el ID del autor con el ID del usuario LOGUEADO en la sesión
    if ($autorId != $loggedInUserId || $loggedInUserId != $usuarioId) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para editar este comentario.']);
        mysqli_close($conn);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Comentario no encontrado.']);
    mysqli_close($conn);
    exit;
}

$sqlEditar = "UPDATE comentarios SET texto = ? WHERE id = ?";
$stmtEditar = mysqli_prepare($conn, $sqlEditar);

if ($stmtEditar) {
    mysqli_stmt_bind_param($stmtEditar, "si", $nuevoTexto, $comentarioId);
    if (mysqli_stmt_execute($stmtEditar)) {
        echo json_encode(['success' => true, 'message' => 'Comentario editado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al editar el comentario en la base de datos: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmtEditar);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de edición.']);
}

mysqli_close($conn);
?>