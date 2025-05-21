<?php
session_start();
include_once("conexionBD.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $direccion = $_POST['direccion'];
    $departamento = $_POST['departamento'];
    $distrito = $_POST['distrito'];
    $codigoPostal = $_POST['codigoPostal'];
    $userId = $_SESSION['user_id'];

    $sql = "UPDATE usuarios SET direccion = ?, departamento = ?, distrito = ?, codigoPostal = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $direccion, $departamento, $distrito, $codigoPostal, $userId);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Dirección actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la dirección: ' . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Método de petición no válido.']);
}
?>