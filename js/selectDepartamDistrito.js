// Script para manejar la selección de departamento y distrito
const distritosPorDepartamento = {
    "Lima": [
      "Miraflores", "San Isidro", "Surco", "Comas", "San Juan de Lurigancho",
      "San Martín de Porres", "San Miguel", "Ate", "Los Olivos", "Villa El Salvador",
      "Villa María del Triunfo", "Pueblo Libre", "Jesús María", "Lince", "Chorrillos",
      "Barranco", "Breña", "El Agustino", "La Molina", "La Victoria"
    ],
    "Callao": [
      "Callao", "Bellavista", "La Perla", "La Punta", "Carmen de La Legua Reynoso",
      "Ventanilla", "Mi Perú"
    ]
  };

  const departamentoSelect = document.getElementById("departamento");
  const distritoSelect = document.getElementById("distrito");

  // Habilitar el select de distrito solo si se selecciona un departamento
  departamentoSelect.addEventListener("change", () => {
    const depto = departamentoSelect.value;
    distritoSelect.innerHTML = '<option value="">Seleccione un distrito</option>';
    if (depto) {
      distritoSelect.disabled = false; // Habilitar el select de distrito
      distritoSelect.style.pointerEvents = 'auto'; // Habilitar interacciones
      distritoSelect.style.opacity = 1; // Hacer visible el select
      if (distritosPorDepartamento[depto]) {
        distritosPorDepartamento[depto].forEach(distrito => {
          const option = document.createElement("option");
          option.value = distrito;
          option.textContent = distrito;
          distritoSelect.appendChild(option);
        });
      }
    } else {
      distritoSelect.disabled = true; // Deshabilitar el select de distrito si no hay departamento seleccionado
      distritoSelect.style.pointerEvents = 'none'; // Deshabilitar interacciones
      distritoSelect.style.opacity = 0.6; // Hacerlo ver menos accesible
    }
  });

  // Cambiar el tamaño del select de departamento al hacer foco
  departamentoSelect.addEventListener("focus", function () {
    this.setAttribute("size", 3); // Mostrar 4 opciones al hacer foco
  });

  // Restaurar tamaño normal después de seleccionar
  departamentoSelect.addEventListener("change", function () {
    this.setAttribute("size", 1); // Cerrar el desplegable al seleccionar
    this.blur(); // Quitar el foco
  });

  // Restaurar tamaño normal al quitar el foco
  departamentoSelect.addEventListener("blur", function () {
    this.setAttribute("size", 1); // Cerrar el desplegable si pierde el foco
  });

  // Cambiar el tamaño del select de distrito al hacer foco
  distritoSelect.addEventListener("focus", function () {
    if (departamentoSelect.value) {
      this.setAttribute("size", 4); // Mostrar 4 opciones al hacer foco
    }
  });

  // Restaurar tamaño normal después de seleccionar
  distritoSelect.addEventListener("change", function () {
    this.setAttribute("size", 1); // Cerrar el desplegable al seleccionar
    this.blur(); // Quitar el foco
  });

  // Restaurar tamaño normal al quitar el foco
  distritoSelect.addEventListener("blur", function () {
    this.setAttribute("size", 1); // Cerrar el desplegable si pierde el foco
  });
