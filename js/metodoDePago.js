// Script para manejar la selección del método e pago
const metodoTarjeta = document.getElementById("metodoTarjeta");
const metodoEfectivo = document.getElementById("metodoEfectivo");
const contenidoMetodo = document.getElementById("contenidoMetodo");
const metodosPagoDiv = document.getElementById('metodosPago'); // Referencia al div que contiene los métodos de pago

// Variables globales para los elementos de Stripe
let cardNumber, cardExpiry, cardCvc;
let stripe, elements;

// Inicializar Stripe una sola vez
stripe = Stripe('pk_stripe');
elements = stripe.elements();

function limpiarSeleccion() {
  metodoTarjeta.classList.remove("seleccionado");
  metodoEfectivo.classList.remove("seleccionado");
  contenidoMetodo.innerHTML = ""; // Limpiar contenido
}

// Estilos personalizados para aumentar el tamaño del texto
let style = {
  base: {
    fontSize: '16px',
  },
  invalid: {
    color: '#e10a15',
  },
};

// Crear los campos de tarjeta con el estilo aplicado
cardNumber = elements.create('cardNumber', { style });
cardExpiry = elements.create('cardExpiry', { style });
cardCvc = elements.create('cardCvc', { style });

let nuevaTarjeta;
let tarjetaGuardada;

let errorMetodoPago = document.getElementById("error-metodoPago");

metodoTarjeta.addEventListener("click", () => {
  limpiarSeleccion();
  metodoTarjeta.classList.add("seleccionado");

  contenidoMetodo.innerHTML = `
       
        <div class="form-field" style="margin-top: 8px;">
          <label>
            <input type="radio" name="tarjetaSeleccionada" id="nuevaTarjeta">
            <i class="bi bi-plus-circle" style="margin: 0 6px;"></i> <span>Introducir tarjeta</span>
          </label>
        </div>

        <div id="formularioStripe" style="display:none;">
          <div class="form-row">
            <div class="form-field" style="flex: 1.5;">
              <label for="nameTarjeta">Nombre en la tarjeta <span style="color:#fd3d57;">*</span></label>
              <input type="text" id="nameTarjeta" name="nameTarjeta" class="inputPago" />
              <div id="error-nameTarjeta" class="errorTarjeta"></div>
            </div>
            <div class="form-field" style="flex: 1;">
              <label for="card-number">Número de tarjeta <span style="color:#fd3d57;">*</span></label>
              <div id="card-number" class="inputPago"></div>
              <div id="error-cardNumber" class="errorTarjeta"></div>
            </div>
            <div class="form-field" style="flex: 1;">
              <label for="card-expiry">Fecha de Caducidad <span style="color:#fd3d57;">*</span></label>
              <div id="card-expiry" class="inputPago"></div>
              <div id="error-cardExpiry" class="errorTarjeta"></div>
            </div>
            <div class="form-field" style="flex: 0.5;">
              <label for="card-cvc">CVC <span style="color:#fd3d57;">*</span></label>
              <div id="card-cvc" class="inputPago"></div>
              <div id="error-cardCvc" class="errorTarjeta"></div>
            </div>
          </div>
          <div id="error-stripe" class="error-message"></div>
        </div>
      `;

  contenidoMetodo.classList.remove("visible");

  setTimeout(() => {
    contenidoMetodo.classList.add("visible");
    contenidoMetodo.scrollIntoView({ behavior: 'smooth' });
  }, 10);

  // Montar los listeners para los radio buttons
  const radioNuevaTarjeta = document.getElementById("nuevaTarjeta");
  const formularioStripe = document.getElementById("formularioStripe");

  radioNuevaTarjeta.addEventListener("change", () => {
    if (radioNuevaTarjeta.checked) {
      formularioStripe.style.display = "block";

      cardNumber.mount('#card-number');
      cardExpiry.mount('#card-expiry');
      cardCvc.mount('#card-cvc');

    }
  });



  nuevaTarjeta = document.getElementById('nuevaTarjeta');


  // Eliminar el mensaje de error al hacer clic en este método
  errorMetodoPago.remove();

});

metodoEfectivo.addEventListener("click", () => {
  limpiarSeleccion();
  metodoEfectivo.classList.add("seleccionado");
  contenidoMetodo.innerHTML = `
        <p>Puedes pagar en efectivo a nuestro mensajero cuando reciba el pedido en su puerta.</p>
      `;

  contenidoMetodo.classList.remove("visible");

  setTimeout(() => {
    contenidoMetodo.classList.add("visible");
    contenidoMetodo.scrollIntoView({ behavior: 'smooth' });
  }, 10);

  errorMetodoPago.remove();

});

// Escuchar los eventos 'change' en los Elementos de Stripe (COLOCAR AQUÍ)
cardNumber.on('change', (event) => {
  const errorCardNumberEl = document.getElementById('error-cardNumber');
  if (event.error) {
    errorCardNumberEl.textContent = event.error.message;
  } else {
    errorCardNumberEl.textContent = ''; // Limpiar el error si el campo es válido
  }
});

cardExpiry.on('change', (event) => {
  const errorCardExpiryEl = document.getElementById('error-cardExpiry');
  if (event.error) {
    errorCardExpiryEl.textContent = event.error.message;
  } else {
    errorCardExpiryEl.textContent = ''; // Limpiar el error si el campo es válido
  }
});

cardCvc.on('change', (event) => {
  const errorCardCvcEl = document.getElementById('error-cardCvc');
  if (event.error) {
    errorCardCvcEl.textContent = event.error.message;
  } else {
    errorCardCvcEl.textContent = ''; // Limpiar el error si el campo es válido
  }
});

submitButton.addEventListener('click', async (event) => {
  event.preventDefault();

  let valid = true;
  let metodoPagoSeleccionado = false;

  // Limpiar errores anteriores (AHORA INCLUYE TODOS LOS MENSAJES DE ERROR)
  document.querySelectorAll('.error-message').forEach(e => e.remove());
  document.querySelectorAll('.errorTarjeta').forEach(e => e.textContent = ''); // Limpiar errores de tarjeta al intentar enviar

  // Volver a obtener referencias a los divs de error (porque el contenido es dinámico)
  const errorNameEl = document.getElementById('error-nameTarjeta');
  const errorCardNumberEl = document.getElementById('error-cardNumber');
  const errorCardExpiryEl = document.getElementById('error-cardExpiry');
  const errorCardCvcEl = document.getElementById('error-cardCvc');
  const errorStripe = document.getElementById('error-stripe');
  const errorMetodoPago = document.getElementById("error-metodoPago");


  // Validar campos obligatorios en el form principal
  document.querySelectorAll('#formCheckout input.campoOblig').forEach(input => {
    if (input.value.trim() === "") {
      valid = false;
      input.style.border = '2px solid red';
      const error = document.createElement('div');
      error.className = 'error-message';
      error.style.color = 'red';
      error.textContent = 'Este campo es obligatorio.';
      input.after(error);
    } else {
      input.style.border = '1px solid #ccc';
    }
  });


  // Validación de correo electrónico
  const correoInput = document.getElementById('correo');
  if (correoInput) {
    const email = correoInput.value;
    const emailPattern = /^[a-zA-Z0-9._-]+@gmail\.com$/;
    const errorElement = correoInput.nextElementSibling;
    if (email === "") {
      valid = false;
      correoInput.style.border = '2px solid red';
      if (!errorElement || !errorElement.classList.contains('error-message')) {
        correoInput.insertAdjacentHTML('afterend', '<div class="error-message" style="color:red;">Campo obligatorio</div>');
      }
    } else if (!emailPattern.test(email)) {
      valid = false;
      correoInput.style.border = '2px solid red';
      if (!errorElement || !errorElement.classList.contains('error-message')) {
        correoInput.insertAdjacentHTML('afterend', '<div class="error-message" style="color:red;">Correo electrónico inválido. Debe ser una dirección de gmail.com.</div>');
      }
    } else {
      correoInput.style.border = '1px solid #ccc';
      if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.remove();
      }
    }
  }


  // Validacion de Select Departamento
  const departamento = document.getElementById('departamento');

  let departamentoError = departamento.nextElementSibling;
  while (departamentoError && departamentoError.classList.contains('error-message')) {
    departamentoError.remove();
    departamentoError = departamento.nextElementSibling;
  }
  if (departamento && departamento.value === "") {
    valid = false;
    departamento.insertAdjacentHTML('afterend', '<div class="error-message" style="color:red;">Por favor, seleccione un departamento.</div>');
  }


  // Validacion de Select Distrito
  const distrito = document.getElementById('distrito');

  let distritoError = distrito.nextElementSibling;
  while (distritoError && distritoError.classList.contains('error-message')) {
    distritoError.remove();
    distritoError = distrito.nextElementSibling;
  }
  if (distrito && distrito.value === "") {
    valid = false;
    distrito.insertAdjacentHTML('afterend', '<div class="error-message" style="color:red;">Por favor, seleccione un distrito.</div>');
  }



  // Validación de selección de método de pago
  if (metodoTarjeta.classList.contains('seleccionado') || metodoEfectivo.classList.contains('seleccionado')) {
    metodoPagoSeleccionado = true;
    if (errorMetodoPago) errorMetodoPago.textContent = '';
  } else {
    valid = false;
    if (errorMetodoPago) {
      errorMetodoPago.textContent = 'Por favor, seleccione un método de pago.';
      errorMetodoPago.style.color = "red";
    }
    return;
  }


  if (!valid) {
    return; // No continuar si hay errores
  }


  let nameTarjeta = {}; // Inicializar como objeto vacío por defecto

  if (nuevaTarjeta && nuevaTarjeta.checked) {
    const nameField = document.getElementById('nameTarjeta');

    if (nameField.value.trim() === '') {
      valid = false;

      errorNameEl.textContent = 'Este campo es obligatorio.';
    }
    else {
      nameTarjeta = { name: nameField.value }; // Crear el objeto aquí mismo
    }
  }


  try {
    let paymentSuccessful = false;
    let paymentData;

    // Lógica de pago con Stripe (si aplica)
    if (nuevaTarjeta && nuevaTarjeta.checked) {
     const responseStripe = await fetch('../../php/create-checkout-session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(nameTarjeta)
      });

      const dataStripe = await responseStripe.json();

      if (dataStripe.error) {
        if (errorStripe) errorStripe.textContent = dataStripe.error;
        return;
      } else {
        const result = await stripe.confirmCardPayment(dataStripe.clientSecret, {
          payment_method: {
            card: cardNumber,
            billing_details: { name: nameTarjeta.name }
          }
        });


        if (result.error) {

          const code = result.error.code;
          const message = result.error.message;

          switch (code) {
            case 'incomplete_number':
            case 'invalid_number':
              console.log(message)
              if (errorCardNumberEl) errorCardNumberEl.textContent = message;
              break;
            case 'incomplete_expiry':
            case 'invalid_expiry_month':
            case 'invalid_expiry_year':
              console.log(message)
              if (errorCardExpiryEl) errorCardExpiryEl.textContent = message;
              break;
            case 'incomplete_cvc':
            case 'invalid_cvc':
              console.log(message)
              if (errorCardCvcEl) errorCardCvcEl.textContent = message;
              break;
            case 'card_declined':
              console.log(message)
              if (errorStripe) errorStripe.textContent = message;
              break;
            default:
              if (errorStripe) errorStripe.textContent = message;
              break;
          }
          return; // No continuar si hay error
        }

        if (result.paymentIntent.status === 'succeeded') {
          paymentSuccessful = true;
          paymentData = { paymentMethod: 'stripe', paymentIntentId: result.paymentIntent.id }; // Opcional: guardar info del pago
        }
      }
    }  else if (metodoEfectivo && metodoEfectivo.classList.contains('seleccionado')) {
      paymentSuccessful = true;
      paymentData = { paymentMethod: 'cash_on_delivery' };
    }

    if (paymentSuccessful) {
      // Obtener los datos del carrito para enviar al servidor
      const cartResponse = await fetch('../../php/getCart.php?getFullDetails=true'); // Necesitamos más detalles del carrito
      const cartData = await cartResponse.json();

      if (cartData.success && cartData.cartDetails && cartData.cartDetails.length > 0) {
        const orderData = {
          // Asumiendo que tienes el usuario_id en alguna variable JavaScript (ej: userId)
          usuario_id: null, // Aquí debes obtener el ID del usuario logueado 
          total: cartData.total,
          productos: cartData.cartDetails.map(item => ({
            producto_id: item.id,
            cantidad: item.cantidad,
            precio: item.precio
          })), // <-- Corregida la coma aquí
          payment: paymentData, // Incluir información del pago
          shippingAddress: { // Recoger la dirección del formulario
            nombre: document.getElementById('nombre').value,
            apellidos: document.getElementById('apellidos').value,
            direccion: document.getElementById('direccion').value,
            departamento: document.getElementById('departamento').value,
            distrito: document.getElementById('distrito').value,
            codigoPostal: document.getElementById('codigoPostal').value,
            correo: document.getElementById('correo').value,
            telefono: document.getElementById('telefono').value
          }
        };
        console.log("Valor del departamento enviado:", orderData.shippingAddress.departamento);
        // Realizar la petición AJAX para guardar el pedido
        const saveOrderResponse = await fetch('../../php/saveOrder.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(orderData)
        });

        const saveOrderResult = await saveOrderResponse.json();

        if (saveOrderResult.success) {
          window.location.href = 'success.php';
          // Opcional: Limpiar el carrito 
        } else {
          console.error("Error al guardar el pedido:", saveOrderResult.message);
          if (errorStripe) errorStripe.textContent = "Error al procesar el pedido. Por favor, inténtalo de nuevo.";
        }
      } else {
        console.error("No hay productos en el carrito o no se pudieron obtener los detalles.");
        if (errorStripe) errorStripe.textContent = "El carrito está vacío o hubo un error al obtener los detalles.";
      }
    }

  } catch (error) {
    console.error("Error general:", error);
    if (errorStripe) {
      errorStripe.textContent = "Error al procesar el pago, inténtalo nuevamente.";
    }
  }


});