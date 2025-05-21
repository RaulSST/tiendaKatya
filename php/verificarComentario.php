    <?php
    header('Content-Type: application/json');
    session_start();
    include_once("conexionBD.php");

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['puede_editar' => false, 'message' => 'Debes estar logueado para editar comentarios.']);
        exit;
    }

    $comentarioId = $_POST['id_comentario'] ?? null;
    $loggedInUserId = $_SESSION['user_id'];

    if ($comentarioId === null || !is_numeric($comentarioId)) {
        echo json_encode(['puede_editar' => false, 'message' => 'Datos inválidos para verificar permiso de edición.']);
        exit;
    }

    $sqlVerificar = "SELECT usuario_id FROM comentarios WHERE id = ?";
    $stmtVerificar = mysqli_prepare($conn, $sqlVerificar);
    mysqli_stmt_bind_param($stmtVerificar, "i", $comentarioId);
    mysqli_stmt_execute($stmtVerificar);
    $resultVerificar = mysqli_stmt_get_result($stmtVerificar);

    if ($rowVerificar = mysqli_fetch_assoc($resultVerificar)) {
        $autorId = $rowVerificar['usuario_id'];
        mysqli_stmt_close($stmtVerificar);

        if ($autorId == $loggedInUserId) {
            echo json_encode(['puede_editar' => true]); // Devuelve solo un booleano
        } else {
            echo json_encode(['puede_editar' => false, 'message' => 'No tienes permiso para editar este comentario.']);
        }
    } else {
        echo json_encode(['puede_editar' => false, 'message' => 'Comentario no encontrado.']);
    }

    mysqli_close($conn);
    ?>
    