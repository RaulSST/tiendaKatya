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
  <title>Política de Privacidad</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesFooter.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Futura&amp;family=Roboto&amp;family=Helvetica&amp;family=Garamond&amp;family=Caslon&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&amp;display=swap" rel="stylesheet">
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

    <!-- Navbar para computadoras -->
    <nav id="navDesktop" class="container">
      <ul class="navbar-nav">
        <li class="inicio"><a href="../../index.php?category_id=0">Inicio</a></li>
        <?php foreach ($categories as $category): ?>
          <li><a href="../../index.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <!-- Navbar para móvil -->
    <nav class="navbar navbar-mobile" id="navMobile">

      <img src="img/logoKatya.png" class="logoTienda" alt="Spain by Train">

      <div class="grupo-final">
        <img src="img/turespaña.jpg" class="logo-turespaña" alt="Turespaña">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"
          aria-controls="offcanvasMenu">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Offcanvas Menu -->
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

  <div id="containPolitPriv" class="container">

    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <a href="politica-privacidad.php">Política de Privacidad</a>
      </div>
    </div>


    <div id="divContainPolitPriv">

      <div>
        <h2>Política de Privacidad</h2>
        <p>En <strong>Importaciones Katya</strong>, nos comprometemos a proteger la privacidad de nuestros usuarios.
          Esta Política de Privacidad explica cómo recopilamos, utilizamos y protegemos tus datos personales cuando
          visitas
          nuestro sitio web y realizas compras en nuestra tienda en línea.</p>
      </div>

      <div>
        <h4>1. Responsable del Tratamiento</h4>
        <p>
          Razón social: Importaciones Katya<br>
          RUC: [En proceso de formalización]<br>
          Dirección: [Dirección física de la empresa]<br>
          Correo electrónico: contacto@importacioneskatya.pe</p>
      </div>

      <div>
        <h4>2. Datos que Recopilamos</h4>
        <p>Podemos recopilar los siguientes datos personales:</p>
        <ul>
          <li>Nombre completo</li>
          <li>Correo electrónico</li>
          <li>Número de teléfono</li>
          <li>Dirección de envío</li>
          <li>Datos de pago (procesados por plataformas seguras)</li>
          <li>Información de navegación mediante cookies</li>
        </ul>
      </div>

      <div>
        <h4>3. Finalidad del Tratamiento</h4>
        <p>Usamos tus datos personales para:</p>
        <ul>
          <li>Procesar pedidos y gestionar envíos</li>
          <li>Enviar confirmaciones y actualizaciones sobre tus compras</li>
          <li>Atender consultas</li>
          <li>Mejorar la experiencia en nuestro sitio web</li>
        </ul>
      </div>

      <div>
        <h4>4. Legitimación</h4>
        <p>El tratamiento de tus datos se basa en:</p>
        <ul>
          <li>Tu consentimiento al registrarte o realizar una compra</li>
          <li>El cumplimiento de una relación contractual (compra/venta)</li>
          <li>Obligaciones legales aplicables</li>
        </ul>
      </div>

      <div>
        <h4>5. Plazo de Conservación</h4>
        <p>Tus datos se conservarán mientras dure la relación comercial o mientras no solicites su eliminación. Algunos
          datos podrán mantenerse por un plazo legal adicional por razones fiscales o contables.</p>
      </div>

      <div>
        <h4>6. Seguridad de los Datos</h4>
        <p>Implementamos medidas técnicas y organizativas para proteger tus datos personales, evitando accesos no
          autorizados, pérdida o alteración de la información.</p>
      </div>

      <div>
        <h4>7. Cookies</h4>
        <p>Este sitio web utiliza cookies esenciales para el funcionamiento del sitio, como mantener tu sesión iniciada
          y
          recordar tu carrito de compras. No utilizamos cookies para fines de análisis o publicidad.</p>
      </div>

      <div>
        <h4>8. Transferencia de Datos</h4>
        <p>No compartimos tus datos con terceros, salvo cuando sea necesario para procesar tu compra (por ejemplo,
          pasarelas de pago o empresas de envío) o por requerimiento legal. No se realizan transferencias
          internacionales de
          datos sin tu consentimiento.</p>
      </div>

      <div>
        <h4>9. Derechos del Usuario</h4>
        <p>Puedes ejercer tus derechos conforme a la Ley N.º 29733 de Protección de Datos Personales:</p>
        <ul>
          <li><strong>Acceso</strong>: conocer qué datos tuyos tratamos.</li>
          <li><strong>Rectificación</strong>: corregir datos incorrectos o incompletos.</li>
          <li><strong>Cancelación</strong>: solicitar la eliminación de tus datos.</li>
          <li><strong>Oposición</strong>: oponerte al tratamiento en ciertos casos.</li>
        </ul>
        <p>Para ejercer estos derechos, contáctanos al correo: <strong>contacto@importacioneskatya.pe</strong></p>
      </div>

      <div>
        <h4>10. Cambios en la Política</h4>
        <p>Nos reservamos el derecho de modificar esta política para adaptarla a cambios legislativos o nuevas
          prácticas.
          Cualquier cambio será publicado en esta misma página.</p>
      </div>

      <div>
        <h4>11. Aceptación</h4>
        <p>El uso de nuestro sitio web implica la aceptación de esta Política de Privacidad.</p>
      </div>

    </div>
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
                <a href="../pagesAccount/account-order-history.php">Lista de pedidos</a>
                <a href="../pagesAccount/account-lista-deseos.php">Lista de deseos</a>
                <a href="../pagesAccount/account.php">Administrar cuenta</a>
              </div>
            </div>

            <div class="col-6">
              <div class="footer_menu">
                <h4 class="footer_title">Informacion</h4>
                <a href="terminos-condiciones.php">Términos y condiciones</a>
                <a href="politica-privacidad.php">Politica de privacidad</a>
                <a href="preguntas-frecuentes.php">Preguntas frecuentes</a>
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

  <script src="../../js/navScroll.js"></script>
  <script src="../../js/bootstrap.bundle.js"></script>

</body>

</html>