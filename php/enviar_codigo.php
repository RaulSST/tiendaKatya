<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$mensaje = '';
$mostrarFormularioRegistro = true; // Mostrar formulario inicial de registro
$mostrarFormularioCodigo = false;
$mostrarBotonVolverRegistro = false; // Nueva variable para controlar el botón "Volver"

// Función para verificar si el correo ya existe
function correoExiste($pdo, $correo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $correo);
    $stmt->execute();
    return (bool) $stmt->fetchColumn();
}

// --- PROCESO DE ENVÍO DE CÓDIGO ---
if (isset($_POST['new-email'])) {
    $correo = $_POST['new-email'];
    $nombre = $_POST['name'];
    $contrasena = $_POST['new-password'];
    $codigo = rand(100000, 999999);

    // Conexión a la base de datos
    $host = '';
    $dbname = '';
    $user = '';
    $pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (correoExiste($pdo, $correo)) {
            $mensaje = "❌ El correo ya está registrado. Intenta con otro.";
            $mostrarFormularioRegistro = false; // Ocultar formulario de registro
            $mostrarBotonVolverRegistro = true; // Mostrar botón "Volver"
        } else {
            // Enviar correo con PHPMailer
            require 'libs/PHPMailer/src/Exception.php';
            require 'libs/PHPMailer/src/PHPMailer.php';
            require 'libs/PHPMailer/src/SMTP.php';

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'correo@gmail.com';
                $mail->Password   = 'unpu nmvb mypw gwqf';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('correo@gmail.com', 'Importaciones Katya');
                $mail->addAddress($correo);
                $mail->isHTML(true);
                $mail->Subject = 'Tu código de verificación';
                $mail->Body    = "Tu código es: <b>$codigo</b>";

                $mail->send();

                $_SESSION['codigo_verificacion'] = $codigo;
                $_SESSION['correo_usuario'] = $correo;
                $_SESSION['nombre_usuario'] = $nombre;
                $_SESSION['contrasena_usuario'] = $contrasena;

                $mensaje = "✅ Código enviado";
                $mostrarFormularioRegistro = false; // Ocultar formulario de registro
                $mostrarFormularioCodigo = true;  // Mostrar formulario de código
            } catch (Exception $e) {
                $mensaje = "❌ Error al enviar el correo: {$mail->ErrorInfo}";
            }
        }
        $pdo = null; // Cerrar conexión PDO
    } catch (PDOException $e) {
        $mensaje = "❌ Error al conectar con la base de datos: " . $e->getMessage();
    }
}

// --- PROCESO DE VERIFICACIÓN DE CÓDIGO ---
if (isset($_POST['codigo'])) {
    $codigoIngresado = $_POST['codigo'];
    $codigoGuardado = $_SESSION['codigo_verificacion'] ?? '';

    if ($codigoIngresado == $codigoGuardado) {
        $mensaje = "✅ ¡Código correcto! Verificación completada.";
        unset($_SESSION['codigo_verificacion']);

        $nombre = $_SESSION['nombre_usuario'] ?? '';
        $correo = $_SESSION['correo_usuario'] ?? '';
        $contrasena = $_SESSION['contrasena_usuario'] ?? '';

        // Conexión a la base de datos para registrar al usuario
        $host = '';
        $dbname = '';
        $user = '';
        $pass = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $hash = password_hash($contrasena, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $correo);
            $stmt->bindParam(':contrasena', $hash);
            $stmt->execute();

            $mensaje .= "<br> <a href='../pages/pagesAutentication/login.php' class='login-btn'>Iniciar sesión</a>.";

            unset($_SESSION['nombre_usuario'], $_SESSION['contrasena_usuario'], $_SESSION['correo_usuario']);
            $mostrarFormularioCodigo = false; // Ocultar formulario de código
            $mostrarFormularioRegistro = false; // Ocultar formulario de registro
            $mostrarBotonVolverRegistro = false; // Ocultar botón "Volver"
        } catch (PDOException $e) {
            $mensaje .= "<br>❌ Error al guardar en la base de datos: " . $e->getMessage();
            $mostrarFormularioCodigo = true; // Mostrar formulario de código en caso de error
            $mostrarFormularioRegistro = false;
            $mostrarBotonVolverRegistro = false; // Ocultar botón "Volver" en este caso
        }
        $pdo = null; // Cerrar conexión PDO
    } else {
        $mensaje = "❌ Código incorrecto. Inténtalo de nuevo.";
        $mostrarFormularioCodigo = true; // Mantener el formulario de código visible
        $mostrarFormularioRegistro = false;
        $mostrarBotonVolverRegistro = false; // Ocultar botón "Volver"
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificación</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            font-family: Arial, sans-serif;
        }

        .center-box {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
            margin: 0 auto;
            margin-top: 100px;
        }

        button {
            padding: 10px 20px;
            margin-top: 15px;
            cursor: pointer;
        }

        .msg {
            margin-top: 15px;
            font-weight: bold;
        }

        .login-btn {
            display: inline-block;
            margin-top: 25px;
            background-color: #ba3b81;
            color: white;
            border: 1px solid #ba3b81;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
        }

        .login-btn:hover {
            background-color: white;
            color: #ba3b81;
            border: 1px solid #ba3b81;
        }

        h2 {
            margin-top: 0px;
            margin-bottom: 3px;
        }

        button {
            width: 150px;
            padding: 10px;
            background-color: #ba3b81;
            color: white;
            border: 1px solid #ba3b81;
            border-radius: 4px;
            margin-top: 15px !important;
            font-weight: 500;
            transition: 0.5s;
            font-size: 16px;
            text-decoration: none; /* Para el botón "Volver" como enlace */
            display: inline-block; /* Para que se comporte como un botón */
        }

        button:hover {
            background-color: white;
            color: #ba3b81;
            border: 1px solid #ba3b81;
        }

        input {
            padding: 8px 5px;
            margin-top: 10px;
            height: 10px;
            width: 55px;
            font-size: 16px;
        }

    </style>
</head>

<body>
    <div class="center-box">
        <h2>Verificación</h2>

        <?php if ($mostrarFormularioRegistro): ?>
            <form method="POST">
                <label for="name">Nombre:</label><br>
                <input type="text" id="name" name="name" required><br>
                <label for="new-email">Nuevo correo:</label><br>
                <input type="email" id="new-email" name="new-email" required><br>
                <label for="new-password">Nueva contraseña:</label><br>
                <input type="password" id="new-password" name="new-password" required><br>
                <button type="submit">Enviar Código</button>
            </form>
            <div class="msg"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if ($mostrarFormularioCodigo): ?>
            <form method="POST">
                <label>Ingresa el código que recibiste en <?= htmlspecialchars($_SESSION['correo_usuario'] ?? '') ?>:</label><br>
                <input type="text" name="codigo" required><br>
                <button type="submit">Verificar</button>
            </form>
            <div class="msg"><?= $mensaje ?></div>
        <?php endif; ?>

        <?php if (!$mostrarFormularioRegistro && !$mostrarFormularioCodigo && $mensaje): ?>
            <div class="msg"><?= $mensaje ?></div>
            <?php if ($mostrarBotonVolverRegistro): ?>
                <button onclick="window.location.href='../pages/pagesAutentication/registro.php'">Volver</button>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</body>

</html>