<?php
session_start(); 

include_once("../../php/conexionBD.php");

function getAllCategories() {
  global $conn;
  $categories = [];
  $sql = "SELECT id, nombre FROM categorias ORDER BY id ASC";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $categories[] = $row; // Devuelve ID y nombre
    }
    mysqli_free_result($result);
  } else {
    error_log("Error al obtener categorías en account.php: " . mysqli_error($conn));
  }
  return $categories;
}

$categories = getAllCategories(); // Obtiene las categorías para la navegación

$error = "";

function generarToken()
{
    return bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Evitar inyecciones SQL
    $email = mysqli_real_escape_string($conn, $email);
    // No escapamos la contraseña aquí porque password_verify maneja esto,
    // pero sí la que viene de la base de datos si la obtuviéramos como string directo.
    // En este caso, $password es el input del usuario.

    // Consulta SQL para comprobar si el correo existe y obtener todos los datos, incluido tipo_usuario
    $sql = "SELECT id, nombre, email, contrasena, tipo_usuario FROM usuarios WHERE email = '$email'";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        $user = mysqli_fetch_assoc($result);

        // Verificar si el correo está registrado
        if ($user) {
            // Verificar si la contraseña es correcta
            if (password_verify($password, $user['contrasena'])) {
                // Contraseña correcta, iniciamos la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['tipo_usuario']; // Almacenar el tipo de usuario en la sesión

                // *** Lógica para crear token y cookie ***
                $token = generarToken();
                $expiracion = date('Y-m-d H:i:s', strtotime('+6 months'));

                $sql_insert_token = "INSERT INTO tokens (usuario_id, token, expiracion) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql_insert_token);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "iss", $user['id'], $token, $expiracion);
                    $ejecucion_exitosa = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    if ($ejecucion_exitosa) {
                        // Establecer la cookie "recordarme_token" con duración de 6 meses
                        setcookie('recordarme_token', $token, strtotime('+6 months'), '/');
                    } else {
                        // Opcional: Manejar el error al insertar el token
                        $error = "Error al guardar la información para recordar la sesión.";
                    }
                } else {
                    // Opcional: Manejar el error en la preparación de la consulta
                    $error = "Error en la base de datos.";
                }

                // *** Redirigir al usuario según su tipo ***
                if ($_SESSION['user_type'] === 'administrador') {
                    header('Location: ../pagesAdmin/admin.php'); // Redirige al panel de administración
                } else {
                    header('Location: ../../index.php'); // Redirige al usuario normal a la página principal
                }
                exit();
            } else {
                // Contraseña incorrecta
                $error = "Contraseña incorrecta.";
            }
        } else {
            // Usuario no encontrado
            $error = "No existe una cuenta con ese correo electrónico";
        }
    } else {
        // Error en la consulta
        $error = "Error al consultar la base de datos.";
    }
}

// Cierra la conexión a la base de datos al final del script PHP
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Importaciones Katya</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesAutentication.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Futura&family=Roboto&family=Helvetica&family=Garamond&family=Caslon&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
</head>

<body>

  <header id="header">
    <div class="container">
      <img src="../../img/logoKatya.png" class="logoTienda" alt="Importaciones Katya">

      <form action="../pagesProductos/buscar.php" method="GET" class="search-form">
        <div class="input-container">
          <input type="text" name="query" placeholder="Buscar" />
          <button type="submit">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </form>

      <div class="icon-container">
        <a href="../pagesAccount/account-lista-deseos.php" class="icon">
          <i class="bi bi-heart"></i>
        </a>

        <a href="../pagesPago/cart.php" class="icon">
          <i class="bi bi-cart"></i>
        </a>

        <a href="login.php" class="icon">
          <i class="bi bi-person"></i>
        </a>
      </div>
    </div>
  </header>

  <nav id="navContainer">

    <nav id="navDesktop" class="container">
      <ul class="navbar-nav">
        <li class="inicio"><a href="../../index.php?category_id=0">Inicio</a></li>
        <?php foreach ($categories as $category): ?>
          <li><a href="../../index.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <nav class="navbar navbar-mobile" id="navMobile">

      <img src="../../img/logoKatya.png" class="logoTienda" alt="Importaciones Katya">

      <div class="grupo-final">
        <img src="../../img/turespana.jpg" class="logo-turespana" alt="Turespaña">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
          aria-controls="offcanvasMenu">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menú</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" href="../../index.php?category_id=0">Inicio</a>
            </li>
            <?php foreach ($categories as $category): ?>
              <li class="nav-item">
                <a class="nav-link" href="../../index.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a>
              </li>
            <?php endforeach; ?>
            <li class="nav-item mt-2">
              <select id="languageSelect" class="form-select">
                <option value="ESPAÑOL" selected>ESPAÑOL</option>
                <option value="INGLÉS">INGLÉS</option>
                <option value="FRANCÉS">FRANCÉS</option>
              </select>
            </li>
          </ul>
        </div>
      </div>
    </nav>

  </nav>

  <div id="containFormLogin" class="container">

    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <a href="login.php">Acceso</a>
      </div>
    </div>

    <form action="" method="POST" id="formLogin">
      <h1 class="tituloForm">Iniciar Sesión</h1>

      <label for="email" class="labelForm">Correo Electrónico <span style="color:#fd3d57;">*</span></label>
      <input type="email" id="email" name="email" class="inputForm" required>

      <label for="password" class="labelForm">Contraseña <span style="color:#fd3d57;">*</span></label>
      <div style="position: relative;">
        <input type="password" id="password" name="password" class="inputForm" required>
        <i class="bi bi-eye-slash password-toggle" data-input="password"
          style="position: absolute; right: 10px; top: 50%; transform: translateY(-75%); cursor: pointer;"></i>
      </div>

      <div class="recuperarContrasena">
        <a href="forgot-password.php">¿Olvidaste tu contraseña?</a>
      </div>

      <button type="submit" class="btnAutenticacion">INICIAR SESIÓN</button>

      <p class="parrafoForm">¿No tienes cuenta? <a id="btnRegistro" href="registro.php">Regístrate</a></p>

      <?php if (!empty($error)) : ?>
        <div class="error">
          <?php echo $error; ?>
        </div>
      <?php endif; ?>
    </form>

  </div>

  <footer id="footer">
    <div class="container">
      <div class="row">

        <div class="col-lg-4 mb-4 mb-md-0">
          <div class="row">
            <div class="col-12 col-md-6 col-lg-12">
              <div class="footer_logo">
                <img loading="lazy" src="../../img/logoKatya.png" alt="logo" class="logoTienda">
              </div>
              <div class="footet_text">
                <p>Importaciones Katya es tu tienda de confianza en productos para el hogar, moda y mucho más.<br>
                  Traemos lo mejor en variedad, calidad y precios directamente para ti.<br>
                  ¡Gracias por elegirnos!</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4 mb-3 mb-md-0">
          <div class="row">
            <div class="col-6">
              <div class="footer_menu">
                <h4 class="footer_title">Mi Cuenta</h4>
                <a href="../pagesAccount/account-order-history.php">Lista de Pedidos</a>
                <a href="../pagesAccount/account-lista-deseos.php">Lista de deseos</a>
                <a href="../pagesAccount/account.php">Administrar cuenta</a>
              </div>
            </div>

            <div class="col-6">
              <div class="footer_menu">
                <h4 class="footer_title">Informacion</h4>
                <a href="../pagesFooter/terminos-condiciones.php">Términos y condiciones</a>
                <a href="../pagesFooter/politica-privacidad.php">Politica de privacidad</a>
                <a href="../pagesFooter/preguntas-frecuentes.php">Preguntas frecuentes</a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="footer_download">
            <div class="row">
              <div class="col-lg-6 col-lg-12">
                <h4 class="footer_title">Contacto</h4>
                <div class="footer_contact">
                  <p>
                    <span class="icn"><i class="bi bi-geo-alt"></i></span>
                    Av. Buenos Aires 771, Puente Piedra 15118, Perú
                  </p>
                  <p>
                    <span class="icn"><i class="bi bi-telephone"></i></span>
                    +51 970 815 826, +51 928 104 181
                  </p>
                  <p>
                    <span class="icn"><i class="bi bi-envelope"></i></span>
                    Katyaimportaciones2025@gmail.com
                  </p>
                  <p>
                    <a href="https://www.facebook.com/people/Importaciones-Katya/100092336872945/"
                      style="text-decoration: none; color: black;"><span class="icn"><i
                          class="bi bi-facebook"></i></span>
                      Importaciones Katya</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <script src="../../js/bootstrap.bundle.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    document.addEventListener('click', function(event) {
      if (event.target.classList.contains('password-toggle')) {
        const inputId = event.target.dataset.input;
        const inputField = document.getElementById(inputId);
        const icon = event.target;

        if (inputField.type === 'password') {
          inputField.type = 'text';
          icon.classList.remove('bi-eye-slash');
          icon.classList.add('bi-eye');
        } else {
          inputField.type = 'password';
          icon.classList.remove('bi-eye');
          icon.classList.add('bi-eye-slash');
        }
      }
    });
  </script>

</body>

</html>
