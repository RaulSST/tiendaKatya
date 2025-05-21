<?php
include_once("conexionBD.php");

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = $_GET['id'];
    $sqlComentarios = "SELECT com.id AS id, u.nombre AS usuario, com.texto, com.fecha_creacion, com.usuario_id
                       FROM comentarios com
                       LEFT JOIN usuarios u ON com.usuario_id = u.id
                       WHERE com.producto_id = ? AND com.activo = TRUE
                       ORDER BY com.fecha_creacion DESC";
    $stmtComentarios = mysqli_prepare($conn, $sqlComentarios);
    mysqli_stmt_bind_param($stmtComentarios, "i", $productId);
    mysqli_stmt_execute($stmtComentarios);
    $resultComentarios = mysqli_stmt_get_result($stmtComentarios);
    $comments_data = [];
    while ($rowComentario = mysqli_fetch_assoc($resultComentarios)) {
        $comments_data[] = $rowComentario;
    }
    mysqli_stmt_close($stmtComentarios);

    $response = array('success' => true, 'comments' => $comments_data);
    echo json_encode($response);
} else {
    $response = array('success' => false, 'message' => 'ID de producto inválido.');
    echo json_encode($response);
}

mysqli_close($conn);
?>