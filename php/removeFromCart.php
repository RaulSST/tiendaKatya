<?php
session_start();
include_once("conexionBD.php"); // Asegúrate de que la ruta sea correcta
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_SESSION['user_id'])) {
    $producto_id = $_POST['id'];
    $usuario_id = $_SESSION['user_id'];

    $sql = "DELETE FROM carrito WHERE usuario_id = ? AND producto_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $usuario_id, $producto_id);
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_affected_rows($conn) > 0) {
                echo json_encode(['success' => true]);
            } else {
                // El producto no existía en el carrito del usuario
                echo json_encode(['success' => false, 'message' => 'El producto no se encontró en tu carrito.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta de eliminación: ' . mysqli_error($conn)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminación: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida o usuario no identificado.']);
}

mysqli_close($conn);
exit;
?>