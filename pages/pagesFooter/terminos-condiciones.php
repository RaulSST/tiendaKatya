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
  <title>Términos y Condiciones</title>
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

  <div id="containTermsCondic" class="container">

    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <a href="terminos-condiciones.php">Términos y Condiciones</a>
      </div>
    </div>

    <div id="divContainTermsCondic">

      <div>
        <h2>Términos y Condiciones</h2>
        <p>Bienvenido a <strong>Importaciones Katya</strong>, tu tienda online de productos de la vida cotidiana. Al
          utilizar nuestro sitio web, aceptas los siguientes términos y condiciones. Si no estás de acuerdo con alguno
          de
          estos términos, te recomendamos que no utilices nuestros servicios.</p>
      </div>

      <div>
        <h4>1. Aceptación de los Términos</h4>
        <p>Al acceder y utilizar este sitio web, aceptas cumplir con estos Términos y Condiciones, y con nuestra
          Política
          de Privacidad. Si no aceptas estos términos, no utilices nuestro sitio.</p>
      </div>

      <div>
        <h4>2. Registro y Cuenta de Usuario</h4>
        <p>Para realizar compras en nuestro sitio, es necesario que te registres con un nombre de usuario y una
          contraseña. Te comprometes a proporcionar información exacta y actualizada durante el proceso de registro.</p>
        <p>Es tu responsabilidad mantener la confidencialidad de tus credenciales y notificar cualquier uso no
          autorizado
          de tu cuenta.</p>
      </div>

      <div>
        <h4>3. Métodos de Pago</h4>
        <p>En <strong>Importaciones Katya</strong> aceptamos los siguientes métodos de pago:</p>
        <ul>
          <li>Tarjetas de crédito y débito (Visa, MasterCard, etc.)</li>
          <li>Transferencias bancarias</li>
          <li>Pagos mediante plataformas de pago en línea (MercadoPago, PayPal, etc.)</li>
        </ul>
        <p>El pago debe realizarse en su totalidad antes de procesar cualquier pedido.</p>
      </div>

      <div>
        <h4>4. Precios y Disponibilidad</h4>
        <p>Los precios de los productos están indicados en nuestra página web y son finales, salvo error tipográfico.
          Nos
          reservamos el derecho de modificar los precios sin previo aviso.</p>
        <p>La disponibilidad de productos puede variar. Si un producto no está disponible después de realizar un pedido,
          te notificaremos y te ofreceremos un reembolso o una opción alternativa.</p>
      </div>

      <div>
        <h4>5. Envíos y Entregas</h4>
        <p>Los envíos se realizan dentro de Perú. El costo del envío será calculado en el momento de la compra,
          dependiendo de la ubicación y el tamaño del pedido.</p>
        <p>Los plazos de entrega varían según la zona, pero generalmente oscilan entre 3 a 7 días hábiles. No nos
          hacemos
          responsables de retrasos causados por la empresa de mensajería o eventos fuera de nuestro control.</p>
      </div>

      <div>
        <h4>6. Devoluciones y Reembolsos</h4>
        <p>Si no estás satisfecho con tu compra, puedes solicitar una devolución dentro de los 10 días hábiles
          siguientes
          a la recepción del producto, siempre que el producto esté en su estado original y no haya sido utilizado.</p>
        <p>Los costos de envío para devoluciones correrán a cargo del cliente, salvo que el producto esté defectuoso o
          haya sido enviado por error.</p>
      </div>

      <div>
        <h4>7. Uso del Sitio Web</h4>
        <p>Te comprometes a utilizar el sitio web únicamente para fines legales y en cumplimiento de estos Términos y
          Condiciones. No está permitido:</p>
        <ul>
          <li>Interferir o alterar el funcionamiento del sitio web.</li>
          <li>Realizar compras fraudulentas o no autorizadas.</li>
          <li>Utilizar métodos automatizados para acceder o interactuar con el sitio.</li>
        </ul>
      </div>

      <div>
        <h4>8. Propiedad Intelectual</h4>
        <p>Todo el contenido de este sitio web, incluidos textos, imágenes, logotipos y gráficos, es propiedad de
          <strong>Importaciones Katya</strong> o de los licenciantes de la empresa. Queda prohibida la reproducción,
          distribución o modificación de este contenido sin autorización expresa.
        </p>
      </div>

      <div>
        <h4>9. Responsabilidad</h4>
        <p><strong>Importaciones Katya</strong> no será responsable por daños directos, indirectos o incidentales
          derivados del uso de nuestros productos o de la utilización del sitio web.</p>
      </div>

      <div>
        <h4>10. Modificaciones</h4>
        <p>Nos reservamos el derecho de modificar estos Términos y Condiciones en cualquier momento, sin previo aviso.
          Las
          modificaciones entrarán en vigor inmediatamente después de su publicación en el sitio web.</p>
      </div>

      <div>
        <h4>11. Legislación Aplicable</h4>
        <p>Este contrato se regirá e interpretará de acuerdo con las leyes de Perú. Cualquier disputa relacionada con
          estos Términos y Condiciones será resuelta por los tribunales competentes de Perú.</p>
      </div>

      <div>
        <h4>12. Contacto</h4>
        <p>Si tienes alguna pregunta sobre estos Términos y Condiciones, no dudes en contactarnos a través de nuestro
          correo electrónico: <strong>contacto@importacioneskatya.pe</strong>.</p>
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