<?php
include_once("../../php/verificarSesion.php"); 
include_once('../../config/routes.php');

$currentPage = $_SERVER['REQUEST_URI'];

if (array_key_exists($currentPage, $rutasProtegidas) && !$isLoggedIn) {
    header("Location: /pages/pagesAutentication/login.php");
    exit();
}

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

?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Importaciones Katya</title>
  <link rel="stylesheet" href="../../index.css">
  <link rel="stylesheet" href="../../css/pagesPago.css">
  <link rel="stylesheet" href="../../css/bootstrap.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <meta name="description" content="Importaciones Katya">
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
    <!-- Navbar para computadoras -->
    <nav id="navDesktop" class="container">
      <ul class="navbar-nav">
        <li class="inicio"><a href="../../index.php?category_id=0">Inicio</a></li>
        <?php foreach ($categories as $category): ?>
          <li><a href="../../index.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nombre']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </nav>

  <div id="divContainPago" class="container">
    <div id="containerBreadcrumbs">
      <div class="breadcrumbs">
        <a href="../../index.php"><i class="bi-house-door"></i></a>
        <i class="bi bi-chevron-right"></i>
        <a href="cart.php">Carro de compras</a>
      </div>
    </div>

    <div id="containerPago">
      <div class="divFormPago" id="formPagoCart">

        <form id="formCart" action="checkout.php" method="POST">
          <table class="tableFormCart">
            <thead>
              <tr class="encabezadoFilaProducto">
                <th colspan="2">Producto</th>
                <th>Cantidad</th>
                <th>Precio Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </form>

      </div>

      <div class="order-summary" id="summaryCart">
        <table id="tableCart">
          <tr class="resumenPedido">
            <td colspan="2"><strong>RESUMEN DEL PEDIDO</strong></td>
          </tr>
          <tr class="subtotal">
            <td>Subtotal</td>
            <td id="subtotal">S/0.00</td>
          </tr>
          <tr class="envio">
            <td>Envío</td>
            <td>GRATIS</td>
          </tr>
          <tr class="total">
            <td><strong>Total</strong></td>
            <td id="total"><strong>S/0.00</strong></td>
          </tr>
        </table>
        <button id="submitButton" class="btnPago">PROCEDE A PAGAR</button>
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
  <script src="../../js/bootstrap.bundle.js"></script>

  <script>
    $(document).ready(function () {

      // Actualizar contadores de carrito y lista de deseos al cargar la página
      updateWishlistCounter();
      updateCartCounter();
      
      $.ajax({
        url: '../../php/getCart.php',
        method: 'GET',  
        dataType: 'json',
        success: function (response) {
          if (response.cart && response.cart.length > 0) {
            let tableBody = $('#formCart tbody');
            tableBody.empty();

            response.cart.forEach(product => {
              let productRow = `
                <tr class="filaProducto" data-id="${product.id}">
                  <td class="producto-img">
                    <img src="../../img/productos/${product.imagen}" alt="${product.nombre}">
                  </td>
                  <td class="producto-info">
                    <p class="producto-nombre"><a href="#">${product.nombre}</a></p>
                    <p class="producto-precio">S/${product.precio}</p>
                    <input type="hidden" name="precioUnitario" value="${product.precio}">
                  </td>
                  <td class="producto-cantidad">
                    <div class="cantidad-container">
                      <button type="button" class="btnCantidad">-</button>
                      <span class="cantidad">${product.cantidad}</span>
                      <button type="button" class="btnCantidad">+</button>
                      <input type="hidden" name="cantidad" value="${product.cantidad}">
                    </div>
                  </td>
                  <td class="producto-precioTotal">
                    <p class="precioTotal">S/${(product.precio * product.cantidad).toFixed(2)}</p>
                    <input type="hidden" name="precioTotal" value="${(product.precio * product.cantidad).toFixed(2)}">
                  </td>
                  <td class="producto-eliminar">
                    <button type="button" class="btnEliminar" data-id="${product.id}">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              `;
              tableBody.append(productRow);
            });


            calcularTotales();
          } else {
            $('#containerPago').html('<p>No hay productos en el carrito.</p>');
          }
        },
        error: function () {
          alert("Hubo un error al cargar el carrito");
        }
      });


      /* Eliminar Producto de la cesta */
      $(document).on('click', '.btnEliminar', function () {
        const productId = $(this).data('id');
        $.ajax({
          url: '../../php/removeFromCart.php',
          type: 'POST',
          data: { id: productId },
          success: function (response) {
            if (response.success) {
              location.reload();
            } else {
              alert("Error al eliminar el producto");
            }
          },
          error: function () {
            alert("Error al procesar la solicitud");
          }
        });
      });


      $(document).on('click', '.btnCantidad', function () {
        const cambio = $(this).text() === '+' ? 1 : -1;
        actualizarCantidad(this, cambio);
      });

      function actualizarCantidad(button, cambio) {
        
        const fila = button.closest("tr");
        const cantidadSpan = fila.querySelector(".cantidad");
        const cantidadInput = fila.querySelector("input[name='cantidad']");
        const precioUnitario = parseFloat(fila.querySelector("input[name='precioUnitario']").value);
        const precioTotalSpan = fila.querySelector(".precioTotal");
        const precioTotalInput = fila.querySelector("input[name='precioTotal']");
        const idProducto = fila.dataset.id;

        let cantidad = parseInt(cantidadSpan.textContent) + cambio;
        if (cantidad < 1) cantidad = 1;

        cantidadSpan.textContent = cantidad;
        cantidadInput.value = cantidad;

        const precioTotal = cantidad * precioUnitario;
        precioTotalSpan.textContent = `S/${precioTotal.toFixed(2)}`;
        precioTotalInput.value = precioTotal.toFixed(2);

        // Actualizamos el total y contador en tiempo real (Frontend)
        calcularTotales();
        actualizarContadorCarrito();

        // Enviamos los datos al servidor para actualizar la sesión
        fetch('../../php/updateCart.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `id=${encodeURIComponent(idProducto)}&cantidad=${encodeURIComponent(cantidad)}`
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Actualizamos el subtotal y total con los nuevos valores de la sesión
              $('#subtotal').text(`S/${data.subtotal}`);
              $('#total').text(`S/${data.total}`);
            } else {
              console.error("Error al actualizar el carrito:", data.message);
            }
          })
          .catch(error => {
            console.error("Error de red:", error);
          });

      }

      // Función para actualizar el total de la suma de los precios
      function calcularTotales() {
        let subtotal = 0;

        document.querySelectorAll(".filaProducto").forEach(fila => {
          const precioTotal = parseFloat(fila.querySelector("input[name='precioTotal']").value);
          subtotal += precioTotal;
        });

        document.getElementById("subtotal").textContent = `S/${subtotal.toFixed(2)}`;
        document.getElementById("total").textContent = `S/${subtotal.toFixed(2)}`;
      }

      // Función para actualizar contador del carrito
      function actualizarContadorCarrito() {
        let totalCantidad = 0;

        document.querySelectorAll('.filaProducto').forEach(fila => {
          const cantidad = parseInt(fila.querySelector('.cantidad').textContent);
          totalCantidad += cantidad;
        });

        // Actualizamos el contador en el ícono del carrito en tiempo real
        $('#cart-count').text(totalCantidad);
      }

     
      // Actualizar contador del carrito al cargar la página
      function updateCartCounter() {
        $.ajax({
          url: '../../php/getCartCount.php', // Ahora apunta al archivo de contador
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            let data = typeof response === 'string' ? JSON.parse(response) : response;

            if (data.success && data.count !== undefined) {
              $('#cart-count').text(data.count);
            } else {
              $('#cart-count').text(0);
              console.error('Error al obtener el contador del carrito al cargar la página');
            }
          },
          error: function() {
            console.error("Hubo un error al obtener el contador del carrito al cargar la página.");
          }
        });
      }

      // Actualizar contador de lista de deseos al cargar la página
      function updateWishlistCounter() {
        $.ajax({
          url: '../../php/getWishlistCount.php',
          method: 'GET',
          dataType: 'json',
          success: function(response) {
            let data = typeof response === 'string' ? JSON.parse(response) : response;

            if (data.success && data.count !== undefined) {
              $('#wishlist-count').text(data.count);
            } else {
              console.error('Error al obtener el contador de la lista de deseos');
            }
          },
          error: function() {
            console.error("Hubo un error al obtener el contador de la lista de deseos");
          }
        });
      }



      $('#submitButton').click(function () {
        window.location.href = 'checkout.php';
      });

    });
  </script>
  <script src="../../js/bootstrap.bundle.js"></script>
</body>

</html>
