<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$mensaje = '';
$mostrarFormularioCodigo = false;
$reenviarCodigo = false;

// Procesar la solicitud de recuperación
if (isset($_POST['email'])) {
  $correo = $_POST['email'];
  $codigo = rand(100000, 999999);

  // Conexión a la base de datos para verificar si el correo existe
  $host = 'localhost';
  $dbname = 'u349915096_tiendaKatya';
  $user = 'u349915096_raul';
  $pass = 'Soysebastian100';

  try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $correo);
    $stmt->execute();
    $existe = $stmt->fetchColumn();

    if ($existe) {
      // Enviar código de verificación con PHPMailer
      require 'libs/PHPMailer/src/Exception.php';
      require 'libs/PHPMailer/src/PHPMailer.php';
      require 'libs/PHPMailer/src/SMTP.php';

      $mail = new PHPMailer(true);

      try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tamararaul8@gmail.com';
        $mail->Password   = 'unpu nmvb mypw gwqf';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom('tamararaul8@gmail.com', 'Importaciones Katya - Recuperación de Contraseña');
        $mail->addAddress($correo);

        $mail->isHTML(true);
        $mail->Subject = 'Código de Recuperación de Contraseña';
        $mail->Body    = "Tu código de recuperación es: <b>$codigo</b>";

        $mail->send();

        $_SESSION['codigo_recuperacion'] = $codigo;
        $_SESSION['codigo_expiracion'] = time() + 300; // Expira en 30 segundos para prueba
        $_SESSION['correo_enviado'] = $correo;
        $titulo = "Ingresa el código enviado que recibiste en ";
        $mensaje = "✅ Código enviado";
        $mostrarFormularioCodigo = true;
      } catch (Exception $e) {
        $mensaje = "❌ Error al enviar el correo: {$mail->ErrorInfo}";
      }
    } else {
      $mensaje = "❌ El correo no está registrado. Intenta con otro.";
    }
  } catch (PDOException $e) {
    $mensaje = "❌ Error al conectar con la base de datos: " . $e->getMessage();
  }
}

// Verificar el código
if (isset($_POST['codigo'])) {
  $codigoIngresado = $_POST['codigo'];
  $codigoGuardado = $_SESSION['codigo_recuperacion'] ?? '';
  $codigoExpiracion = $_SESSION['codigo_expiracion'] ?? 0;

  if (time() > $codigoExpiracion) {

    // Reenviar el código automáticamente
    $correo = $_SESSION['correo_enviado']; // Obtener el correo de sesión
    $codigoNuevo = rand(100000, 999999);   // Generar un nuevo código

    // Enviar un nuevo código automáticamente
    require 'libs/PHPMailer/src/Exception.php';
    require 'libs/PHPMailer/src/PHPMailer.php';
    require 'libs/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host       = 'smtp.gmail.com';
      $mail->SMTPAuth   = true;
      $mail->Username   = 'tamararaul8@gmail.com';
      $mail->Password   = 'unpu nmvb mypw gwqf';
      $mail->SMTPSecure = 'tls';
      $mail->Port       = 587;

      $mail->CharSet = 'UTF-8';

      $mail->setFrom('tamararaul8@gmail.com', 'Importaciones Katya - Recuperación de Contraseña');
      $mail->addAddress($correo);

      $mail->isHTML(true);
      $mail->Subject = 'Nuevo Código de Recuperación de Contraseña';
      $mail->Body    = "Tu nuevo código de recuperación es: <b>$codigoNuevo</b>";

      $mail->send();

      // Actualizar código y expiración
      $_SESSION['codigo_recuperacion'] = $codigoNuevo;
      $_SESSION['codigo_expiracion'] = time() + 300; // Expira en 30 segundos para prueba
      $titulo = "Ingresa el nuevo código reenviado que recibiste en ";
      $mensaje = "❌ El código ha expirado. Nuevo código enviado ✅";
    } catch (Exception $e) {
      $mensaje = "❌ Error al reenviar el código";
    }

    // Volver a mostrar el formulario para que el usuario ingrese el nuevo código
    $mostrarFormularioCodigo = true;
  } elseif ($codigoIngresado == $codigoGuardado) {
    $mensaje = "✅ ¡Código correcto! Ahora puedes cambiar tu contraseña.";

    unset($_SESSION['codigo_recuperacion']);
    unset($_SESSION['codigo_expiracion']);

    // Redirigir a una página donde el usuario puede cambiar la contraseña
    header("Location: cambiarContrasena.php");
    exit(); // Asegúrate de detener el script después de la redirección
  } else {
    $mensaje = "❌ Código incorrecto. Inténtalo de nuevo.";
    $mostrarFormularioCodigo = true;
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
      width: 500px;
      margin: 0 auto;
      margin-top: 200px;
    }

    input {
      padding: 8px 5px;
      margin-top: 10px;
      height: 10px;
      width: 55px;
      font-size: 16px;
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
      cursor: pointer;
    }
    button:hover {
      background-color: white;
      color: #ba3b81;
      border: 1px solid #ba3b81;
    }

    .msg {
      margin-top: 15px;
      font-weight: bold;
    }

    h2 {
      margin-top: 0px;
      margin-bottom: 3px;
    }
  </style>
</head>

<body>
  <div class="center-box">
    <h2>Verificación</h2>

    <?php if ($mostrarFormularioCodigo): ?>

      <form method="POST">
        <label>
          <?php  
            echo $titulo . htmlspecialchars($_SESSION['correo_enviado']);
          ?>:
        </label>
        <br>
        <input type="text" name="codigo" required><br>
        <button type="submit">Verificar</button>
      </form>
      <div class="msg"><?= $mensaje ?></div>

    <?php endif; ?>

  </div>
</body>

</html>