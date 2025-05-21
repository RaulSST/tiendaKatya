<?php

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

$categories = getAllCategories();

mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Cuenta</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesAutentication.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Futura&family=Roboto&family=Helvetica&family=Garamond&family=Caslon&display=swap"
    rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
</head>

<body>

  <header id="header">
    <div class="container">
      <img src="../../img/logoKatya.png" class="logoTienda" alt="Spain by Train">

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

        <a href="../pagesAccount/account.php" class="icon">
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

      <img src="img/logoKatya.png" class="logoTienda" alt="Spain by Train">

      <div class="grupo-final">
        <img src="img/turespana.jpg" class="logo-turespana" alt="Turespaña">
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
              <a class="nav-link active" href="#">DESTINOS</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">EXPERIENCIAS</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">SPAINRAILPASS</a>
            </li>
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

  <div id="containFormRegistro" class="container">

    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <a href="login.php" class="active">Acceso</a>
        <i class="bi bi-chevron-right"></i>
        <p>Registro</p>
      </div>
    </div>

    <form action="../../php/enviar_codigo.php" method="POST" id="formRegistro">
      <h1 class="tituloForm">Crear cuenta</h1>

      <label for="name" class="labelForm">Nombre <span style="color:#fd3d57;">*</span></label>
      <input type="text" id="name" name="name" class="inputForm" required>

      <label for="new-email" class="labelForm">Correo Electronico <span style="color:#fd3d57;">*</span></label>
      <input type="email" id="new-email" name="new-email"
        class="inputForm" placeholder="example@gmail.com" required>
      <small id="email-feedback" style="margin-top: 5px; display: block; color:red;"></small>

      <label for="new-password" class="labelForm">Contraseña <span style="color:#fd3d57;">*</span></label>
      <div style="position: relative;">
        <input type="password" id="new-password" name="new-password" class="inputForm" required>
        <i class="bi bi-eye-slash password-toggle" data-input="new-password"
          style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
      </div>
      <small id="feedback" style="margin-top: 5px; display: block;"></small>

      <label for="confirm-password" class="labelForm">Confirmar Contraseña <span
          style="color:#fd3d57;">*</span></label>
      <div style="position: relative;">
        <input type="password" id="confirm-password" name="confirm-password" class="inputForm" required>
        <i class="bi bi-eye-slash password-toggle" data-input="confirm-password"
          style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></i>
      </div>
      <small id="confirm-feedback" style="margin-top: 5px; display: block;"></small>



      <div class="terms">
        <label class="terms-label">
          <input type="checkbox" required>
          <span>Acepta nuestros <a href="../pagesFooter/terminos-condiciones.php">términos y
              condiciones</a></span>
        </label>
      </div>

      <button type="submit" class="btnAutenticacion">REGISTRARSE</button>

      <p class="parrafoForm">¿Ya tienes una cuenta? <a href="login.php" id="btnAcceso">Inicia Sesión</a></p>

      <div id="error-message">
        Por favor, corrige los errores en el formulario.
      </div>
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
                <p>Importaciones Katya es tu tienda de confianza en productos para el hogar, moda y mucho
                  más.<br>
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
                <a href="../pagesAccount/account-order-history.php">Lista de pedidos</a>
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

  <script>
    function togglePassword(inputId, iconId) {
      const pass = document.getElementById(inputId);
      const icon = document.getElementById(iconId);

      if (pass.type === "password") {
        pass.type = "text";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      } else {
        pass.type = "password";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      }
    }

    // Función de validación de la contraseña
    function validatePassword() {
      const passwordInput = document.getElementById("new-password");
      const feedback = document.getElementById("feedback");
      const confirmPasswordInput = document.getElementById("confirm-password");
      const confirmFeedback = document.getElementById("confirm-feedback");

      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;

      // Validación de la contraseña
      if (password.length < 8) {
        feedback.textContent = "Debe tener al menos 8 caracteres.";
        feedback.style.color = "red";
      } else if (!/[A-Z]/.test(password)) {
        feedback.textContent = "Debe incluir una letra mayúscula.";
        feedback.style.color = "red";
      } else if (!/[a-z]/.test(password)) {
        feedback.textContent = "Debe incluir una letra minúscula.";
        feedback.style.color = "red";
      } else if (!/\d/.test(password)) {
        feedback.textContent = "Debe incluir al menos un número.";
        feedback.style.color = "red";
      } else {
        feedback.textContent = "Contraseña segura";
        feedback.style.color = "green";
      }

      // Validación de confirmación de contraseña
      if (confirmPassword !== "" && password !== confirmPassword) {
        confirmFeedback.textContent = "Las contraseñas no coinciden.";
        confirmFeedback.style.color = "red";
      } else if (confirmPassword === password && confirmPassword !== "") {
        confirmFeedback.textContent = "Las contraseñas coinciden";
        confirmFeedback.style.color = "green";
      } else {
        confirmFeedback.textContent = "";
      }
    }

    // Añadir event listeners
    document.getElementById("new-password").addEventListener("input", validatePassword);
    document.getElementById("confirm-password").addEventListener("input", validatePassword);

    // Event listener para mostrar/ocultar contraseña
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

    document.getElementById('formRegistro').addEventListener('submit', function(event) {
      // Obtener el contenedor de errores
      const errorMessage = document.getElementById("error-message");
      let hasError = false; // Creamos una variable para rastrear si hay algún error

      const emailInput = document.getElementById("new-email");
      const emailFeedback = document.getElementById("email-feedback");
      const email = emailInput.value.trim();

      // Validar si el correo termina en @gmail.com (no distingue mayúsculas)
      if (!email.toLowerCase().endsWith("@gmail.com")) {
        event.preventDefault(); // Detiene el envío del formulario
        emailFeedback.textContent = "El correo debe terminar en @gmail.com.";
        errorMessage.style.display = 'block';
        hasError = true; // Marcamos que hay un error
      } else {
        emailFeedback.textContent = "";
      }

      // Primero, realizamos la validación
      const passwordFeedback = document.getElementById("feedback").textContent;
      const confirmFeedback = document.getElementById("confirm-feedback").textContent;

      // Verificamos si hay algún mensaje de error en las contraseñas
      if (passwordFeedback.includes("Debe") || confirmFeedback.includes("no coinciden")) {
        event.preventDefault();
        errorMessage.style.display = 'block';
        hasError = true; // Marcamos que hay un error
      }

      // Si no hubo ningún error, ocultamos el mensaje de error general
      if (!hasError) {
        errorMessage.style.display = 'none';
      }
    });
  </script>
  <script src="../../js/navScroll.js"></script>
  <script src="../../js/bootstrap.bundle.js"></script>

</body>

</html>