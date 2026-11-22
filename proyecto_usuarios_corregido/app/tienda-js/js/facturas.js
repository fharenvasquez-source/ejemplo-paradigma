const mensajeEstadoFacturas = document.getElementById("mensaje-estado-facturas");
const listaFacturas = document.getElementById("lista-facturas");

const formatoMonedaFacturas = new Intl.NumberFormat("es-HN", {
  style: "currency",
  currency: "HNL",
});

const formatoFecha = new Intl.DateTimeFormat("es-HN", {
  dateStyle: "medium",
  timeStyle: "short",
});

// Esta pantalla tambien requiere sesion igual que el carrito.
const sesionFacturas = requerirSesion();

function mostrarEstadoFacturas(texto, esError = false) {
  mensajeEstadoFacturas.textContent = texto;
  mensajeEstadoFacturas.classList.remove("oculto");
  mensajeEstadoFacturas.classList.toggle("mensaje-error", esError);
  listaFacturas.classList.add("oculto");
}

function ocultarEstadoFacturas() {
  mensajeEstadoFacturas.classList.add("oculto");
  listaFacturas.classList.remove("oculto");
}

function escaparHtmlFacturas(texto) {
  const div = document.createElement("div");
  div.textContent = texto ?? "";
  return div.innerHTML;
}

function crearTarjetaFactura(factura) {
  const fecha = formatoFecha.format(new Date(factura.fecha));

  const filasItems = (factura.items || [])
    .map(
      (item) => `
      <tr>
        <td>${escaparHtmlFacturas(item.nombre)}</td>
        <td>${item.cantidad}</td>
        <td>${formatoMonedaFacturas.format(item.precio_unitario ?? item.precio)}</td>
        <td>${formatoMonedaFacturas.format(item.subtotal ?? item.precio_unitario * item.cantidad)}</td>
      </tr>
    `
    )
    .join("");

  return `
    <details class="tarjeta-factura">
      <summary class="tarjeta-factura__resumen">
        <span class="tarjeta-factura__numero">${escaparHtmlFacturas(factura.numero)}</span>
        <span class="tarjeta-factura__fecha">${fecha}</span>
        <span class="tarjeta-factura__total">${formatoMonedaFacturas.format(factura.total)}</span>
      </summary>

      <div class="tarjeta-factura__detalle">
        <table class="tabla-carrito">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>${filasItems}</tbody>
        </table>

        <div class="resumen-carrito">
          <div class="resumen-carrito__linea">
            <span>Subtotal</span>
            <span>${formatoMonedaFacturas.format(factura.subtotal)}</span>
          </div>
          <div class="resumen-carrito__linea">
            <span>ISV (15%)</span>
            <span>${formatoMonedaFacturas.format(factura.impuesto)}</span>
          </div>
          <div class="resumen-carrito__linea resumen-carrito__linea--total">
            <span>Total</span>
            <span>${formatoMonedaFacturas.format(factura.total)}</span>
          </div>
          <p class="texto-suave">
            ${factura.entrega_domicilio
              ? `Entrega a domicilio: ${escaparHtmlFacturas(factura.direccion_entrega)}`
              : "Retiro en tienda"}
          </p>
        </div>
      </div>
    </details>
  `;
}

function renderizarFacturas(facturas) {
  if (!facturas || facturas.length === 0) {
    mostrarEstadoFacturas("Todavía no tienes facturas. Cuando generes una compra, aparecerá aquí.");
    return;
  }

  // Mas recientes primero.
  const ordenadas = [...facturas].sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

  listaFacturas.innerHTML = ordenadas.map(crearTarjetaFactura).join("");
  ocultarEstadoFacturas();
}

async function cargarFacturas() {
  mostrarEstadoFacturas("Cargando tus facturas...");
  try {
    const facturas = await obtenerFacturasCliente(sesionFacturas.id);
    renderizarFacturas(facturas);
  } catch (error) {
    mostrarEstadoFacturas(error.message, true);
  }
}

if (sesionFacturas) {
  cargarFacturas();
}
