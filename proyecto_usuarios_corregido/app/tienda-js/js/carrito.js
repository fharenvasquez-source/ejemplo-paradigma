// carrito.js
// Lógica de la pantalla de carrito (carrito.html).

const mensajeEstadoCarrito = document.getElementById("mensaje-estado-carrito");
const carritoContenido = document.getElementById("carrito-contenido");
const cuerpoTablaCarrito = document.getElementById("cuerpo-tabla-carrito");
const totalCarritoEl = document.getElementById("total-carrito");
const checkboxEntrega = document.getElementById("entrega-domicilio");
const botonVaciar = document.getElementById("boton-vaciar");
const botonGenerarFactura = document.getElementById("boton-generar-factura");
const mensajeFactura = document.getElementById("mensaje-factura");

const formatoMonedaCarrito = new Intl.NumberFormat("es-HN", {
  style: "currency",
  currency: "HNL",
});

// El carrito requiere sesion, si no hay cliente logueado, requerirSesion()
// ya redirige a login.html y aqui cortamos la ejecución.
const sesionActual = requerirSesion();

function mostrarEstadoCarrito(texto, esError = false) {
  mensajeEstadoCarrito.textContent = texto;
  mensajeEstadoCarrito.classList.remove("oculto");
  mensajeEstadoCarrito.classList.toggle("mensaje-error", esError);
  carritoContenido.classList.add("oculto");
}

function ocultarEstadoCarrito() {
  mensajeEstadoCarrito.classList.add("oculto");
  carritoContenido.classList.remove("oculto");
}

function escaparHtmlCarrito(texto) {
  const div = document.createElement("div");
  div.textContent = texto;
  return div.innerHTML;
}

function renderizarCarrito(carrito) {
  if (!carrito.items || carrito.items.length === 0) {
    mostrarEstadoCarrito("Tu carrito está vacío. Ve al catálogo y agrega productos.");
    return;
  }

  cuerpoTablaCarrito.innerHTML = carrito.items
    .map(
      (item) => `
      <tr data-producto-id="${item.producto_id}">
        <td>${escaparHtmlCarrito(item.nombre)}</td>
        <td>${formatoMonedaCarrito.format(item.precio)}</td>
        <td>
          <input
            type="number"
            min="1"
            value="${item.cantidad}"
            class="input-cantidad"
            data-producto-id="${item.producto_id}"
          />
        </td>
        <td>${formatoMonedaCarrito.format(item.subtotal)}</td>
        <td>
          <button class="boton-quitar" data-producto-id="${item.producto_id}" title="Quitar">✕</button>
        </td>
      </tr>
    `
    )
    .join("");

  totalCarritoEl.textContent = formatoMonedaCarrito.format(carrito.total);
  ocultarEstadoCarrito();
}

async function cargarCarrito() {
  mostrarEstadoCarrito("Cargando tu carrito...");
  try {
    const carrito = await obtenerCarrito(sesionActual.id);
    renderizarCarrito(carrito);
  } catch (error) {
    mostrarEstadoCarrito(error.message, true);
  }
}

// Cambiar cantidad de un producto 
cuerpoTablaCarrito.addEventListener("change", async (evento) => {
  const input = evento.target.closest(".input-cantidad");
  if (!input) return;

  const productoId = input.dataset.productoId;
  const nuevaCantidad = parseInt(input.value, 10);

  if (!nuevaCantidad || nuevaCantidad < 1) {
    alert("La cantidad debe ser al menos 1.");
    cargarCarrito();
    return;
  }

  try {
    await actualizarItemCarrito(sesionActual.id, productoId, nuevaCantidad);
    await cargarCarrito();
  } catch (error) {
    alert(error.message);
    cargarCarrito();
  }
});

// Quitar un producto del carrito.
cuerpoTablaCarrito.addEventListener("click", async (evento) => {
  const boton = evento.target.closest(".boton-quitar");
  if (!boton) return;

  const productoId = boton.dataset.productoId;

  try {
    await eliminarItemCarrito(sesionActual.id, productoId);
    await cargarCarrito();
  } catch (error) {
    alert(error.message);
  }
});

// Vaciar todo el carrito.
botonVaciar.addEventListener("click", async () => {
  if (!confirm("¿Vaciar todo el carrito?")) return;

  try {
    await vaciarCarrito(sesionActual.id);
    await cargarCarrito();
  } catch (error) {
    alert(error.message);
  }
});

// Generar factura.
function mostrarMensajeFactura(texto, esError = true) {
  mensajeFactura.textContent = texto;
  mensajeFactura.classList.remove("oculto");
  mensajeFactura.classList.toggle("mensaje-error", esError);
  mensajeFactura.classList.toggle("mensaje-exito", !esError);
}

botonGenerarFactura.addEventListener("click", async () => {
  const entregaDomicilio = checkboxEntrega.checked;

  botonGenerarFactura.disabled = true;
  botonGenerarFactura.textContent = "Generando...";

  try {
    const factura = await generarFactura(sesionActual.id, entregaDomicilio);

    mostrarMensajeFactura(
      `¡Factura ${factura.numero} generada! Total: ${formatoMonedaCarrito.format(factura.total)}`,
      false
    );

    // El backend vacia el carrito al generar la factura asi que se recarga
    // la vista para reflejar el carrito vacio.
    setTimeout(cargarCarrito, 1500);
  } catch (error) {
    mostrarMensajeFactura(error.message);
  } finally {
    botonGenerarFactura.disabled = false;
    botonGenerarFactura.textContent = "Generar factura";
  }
});

// Punto de entrada.
if (sesionActual) {
  cargarCarrito();
}
