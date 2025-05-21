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

<!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Importaciones Katya</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesPago.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Futura&amp;family=Roboto&amp;family=Helvetica&amp;family=Garamond&amp;family=Caslon&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Lobster&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
  <meta name="description" content="Importaciones Katya">
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

        <div class="cart-icon">
          <a href="../pagesAccount/account-lista-deseos.php" class="icon">
            <i class="bi bi-heart"></i><span id="wishlist-count">0</span>
          </a>
        </div>

        <div class="cart-icon">
          <a href="cart.php" class="icon">
            <i class="bi bi-cart"></i><span id="cart-count">0</span>
          </a>
        </div>

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
        <img src="img/turespaña.jpg" class="logo-turespaña" alt="Turespaña">
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

  <div id="divContainPago" class="container">
    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <a href="cart.php" class="active">Carro de compras</a>
        <i class="bi bi-chevron-right"></i>
        <a href="checkout.php">Proceso de compra</a>
      </div>
    </div>

    <div id="containerPago">
      <div class="divFormPago" id="formPagoCheckout">
        <div id="formCheckout">
          <h6>Finaliza tu compra</h6>
          <div class="form-row">
            <div class="form-field">
              <label for="nombre">Nombre <span style="color:#fd3d57;">*</span></label>
              <input type="text" id="nombre" name="nombre" class="inputPago campoOblig">
            </div>
            <div class="form-field">
              <label for="apellidos">Apellidos <span style="color:#fd3d57;">*</span></label>
              <input type="text" id="apellidos" name="apellidos" class="inputPago campoOblig">
            </div>
          </div>

          <div class="form-group">
            <label for="direccion">Dirección de Domicilio <span style="color:#fd3d57;">*</span></label>
            <input type="text" id="direccion" name="direccion" class="inputPago campoOblig">
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Departamento <span style="color:#fd3d57;">*</span></label>
              <select id="departamento" name="departamento">
                <option value="">Seleccione un departamento</option>
                <option value="Lima">Lima</option>
                <option value="Callao">Callao</option>
              </select>
            </div>
            <div class="form-field">
              <label>Distrito <span style="color:#fd3d57;">*</span></label>
              <select id="distrito" name="distrito">
                <option value="">Seleccione un distrito</option>
              </select>
            </div>
            <div class="form-field">
              <label for="codigoPostal">Código Postal <span style="color:#fd3d57;">*</span></label>
              <input type="text" id="codigoPostal" name="codigoPostal" class="inputPago campoOblig">
            </div>
          </div>

          <div class="form-row">
            <div class="form-field" style="flex: 2;">
              <label for="correo">Dirección de correo electrónico <span style="color:#fd3d57;">*</span></label>
              <input type="email" id="correo" name="correo" class="inputPago">
            </div>
            <div class="form-field">
              <label for="telefono">Número de teléfono <span style="color:#fd3d57;">*</span></label>
              <input type="number" id="telefono" name="telefono" class="inputPago campoOblig">
            </div>
          </div>

          <h6 style="margin-top: 40px;">Seleccione el método de pago</h6>

          <div class="form-row" id="metodosPago">
            <div class="form-field" style="flex: 1;" id="metodoTarjeta">
              <img src="../../img/cardCreditDebit.png" alt="tarjetaCreditoDebito">
              <p>Tarjeta de crédito/débito</p>
            </div>
            <div class="form-field" style="flex: 1;" id="metodoEfectivo">
              <img src="../../img/pagoEfectivo.png" alt="pagoEfectivo">
              <p>Pago contra entrega</p>
            </div>
          </div>
          <div id="error-metodoPago"></div>

          <div id="contenidoMetodo" style="margin-top: 20px;"></div>
        </div>
      </div>

      <div class="order-summary" id="summaryCheckout">
        <h6>Su Pedido</h6>
        <table id="tableCheckout">
          <tr class="title-orderSummary">
            <td><strong>Producto</strong></td>
            <td style="text-align: center;"><strong>Cantidad</strong></td>
            <td><strong>Precio</strong></td>
          </tr>
          <tr class="subtotal">
            <td colspan="2"><strong>Subtotal</strong></td>
            <td id="checkout-subtotal"></td>
          </tr>
          <tr class="envio">
            <td colspan="2"><strong>Envío</strong></td>
            <td id="checkout-envio">GRATIS</td>
          </tr>
          <tr class="total">
            <td colspan="2"><strong>Total</strong></td>
            <td id="checkout-total"></td>
          </tr>
        </table>

        <div class="terms">
          <label class="terms-label">
            <input type="checkbox" required>
            <span>Acepta nuestros <a href="../pagesFooter/terminos-condiciones.php">términos y
                condiciones</a></span>
          </label>
        </div>

        <button id="submitButton" class="btnPago">REALIZAR PEDIDO</button>
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
                <p>Importaciones Katya es tu tienda de confianza en productos para el hogar, moda y
                  mucho más.<br>
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      const tableCheckout = $('#tableCheckout tbody');

      $.ajax({
        url: '../../php/getCart.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
          // Limpiar las filas de productos existentes, excepto las de subtotal, envío y total
          tableCheckout.find('tr:not(.title-orderSummary):not(.subtotal):not(.envio):not(.total)').remove();

          if (response.cart && response.cart.length > 0) {
            response.cart.forEach(product => {
              const row = $('<tr>').addClass('producto');
              const nameCell = $('<td>').text(product.nombre);
              const quantityCell = $('<td>').text(`x${product.cantidad}`).css('text-align', 'center');
              const priceCell = $('<td>').text(`S/${product.precio}`);

              row.append(nameCell, quantityCell, priceCell);
              tableCheckout.find('.subtotal').before(row); // Insertar antes del subtotal
            });

            $('#checkout-subtotal').text(`S/${response.subtotal}`);
            $('#checkout-total').text(`S/${response.total}`);
            $('#cart-count').text(response.totalProductos);
          } else {
            const emptyRow = $('<tr>').append($('<td colspan="3">').text('No hay productos en el carrito.'));
            tableCheckout.prepend(emptyRow); // Insertar al inicio de la tabla
            $('#checkout-subtotal').text('S/0.00');
            $('#checkout-total').text('S/0.00');
            $('#cart-count').text(0);
          }
        },
        error: function () {
          alert("Hubo un error al cargar el resumen del pedido.");
        }
      });
    });
  </script>
  <script src="https://js.stripe.com/v3/"></script>
  <script src="../../js/metodoDePago.js"></script>
  <script src="../../js/navScroll.js"></script>
  <script src="../../js/selectDepartamDistrito.js"></script>
  <script src="../../js/bootstrap.bundle.js"></script>

</body>

</html>