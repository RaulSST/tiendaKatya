<?php
header('Content-Type: application/json');
session_start();
include_once("conexionBD.php");

$productId = $_POST['producto_id'] ?? null;
$comentarioTexto = $_POST['texto'] ?? null;
$usuarioId = $_SESSION['user_id'] ?? null; // Obtén el ID del usuario de la sesión, o NULL si no hay sesión

if ($productId === null || !is_numeric($productId) || empty($comentarioTexto)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos para el comentario.']);
    exit;
}

$sql = "INSERT INTO comentarios (producto_id, usuario_id, texto) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iis", $productId, $usuarioId, $comentarioTexto); // "i" para int, "s" para string
    if (mysqli_stmt_execute($stmt)) {
        $comentarioId = mysqli_insert_id($conn);

        // Obtener el nombre del usuario
        $sqlUsuario = "SELECT nombre FROM usuarios WHERE id = ?";
        $stmtUsuario = mysqli_prepare($conn, $sqlUsuario);
        mysqli_stmt_bind_param($stmtUsuario, "i", $usuarioId);
        mysqli_stmt_execute($stmtUsuario);
        $resultUsuario = mysqli_stmt_get_result($stmtUsuario);
        $rowUsuario = mysqli_fetch_assoc($resultUsuario);
        $nombreUsuario = $rowUsuario['nombre'];  // Obtiene el nombre del usuario
        mysqli_stmt_close($stmtUsuario);

        $response = array(
            'success' => true,
            'message' => 'Comentario guardado.',
            'id_comentario' => $comentarioId,
            'usuario_id' => $usuarioId,
            'usuario' => $nombreUsuario  // Añade el nombre del usuario a la respuesta
        );
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario: ' . mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
}

mysqli_close($conn);
?>