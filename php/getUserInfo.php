<?php
session_start();
include_once("conexionBD.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT nombre, apellidos, email, telefono, direccion, departamento, distrito, codigoPostal FROM usuarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(['success' => true, 'userData' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontró información del usuario.']);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>