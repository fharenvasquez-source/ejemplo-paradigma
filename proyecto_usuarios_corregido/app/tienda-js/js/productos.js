// productos.js

const contenedorProductos = document.getElementById("contenedor-productos");
const selectCategoria = document.getElementById("filtro-categoria");
const mensajeEstado = document.getElementById("mensaje-estado");

// Formateador de moneda en lempiras
const formatoMoneda = new Intl.NumberFormat("es-HN", {
  style: "currency",
  currency: "HNL",
});

/**
 * Muestra un mensaje de estado cargando, error o vacio y oculta la grilla.
 */
function mostrarEstado(texto, esError = false) {
  mensajeEstado.textContent = texto;
  mensajeEstado.classList.remove("oculto");
  mensajeEstado.classList.toggle("mensaje-error", esError);
  contenedorProductos.classList.add("oculto");
}

function ocultarEstado() {
  mensajeEstado.classList.add("oculto");
  contenedorProductos.classList.remove("oculto");
}

/**
 * Pinta la lista de productos en el DOM.
 * Recibe el arreglo de productos ya filtrado 
 */
function renderizarProductos(productos) {
  if (!productos || productos.length === 0) {
    mostrarEstado("No hay productos para mostrar en esta categoría.");
    return;
  }

  contenedorProductos.innerHTML = productos
    .map((producto) => crearTarjetaProducto(producto))
    .join("");

  ocultarEstado();
}

function crearTarjetaProducto(producto) {
  const sinStock = producto.stock <= 0;

  return `
    <article class="tarjeta-producto">
      <div class="tarjeta-producto__imagen-wrap">
        <img
          src="img/placeholder-producto.svg"
          alt="${escaparHtml(producto.nombre)}"
          class="tarjeta-producto__imagen"
        />
        ${sinStock ? '<span class="etiqueta etiqueta--agotado">Agotado</span>' : ""}
      </div>
      <div class="tarjeta-producto__cuerpo">
        <h3 class="tarjeta-producto__nombre">${escaparHtml(producto.nombre)}</h3>
        <p class="tarjeta-producto__descripcion">${escaparHtml(producto.descripcion || "")}</p>
        <div class="tarjeta-producto__pie">
          <span class="tarjeta-producto__precio">${formatoMoneda.format(producto.precio)}</span>
          <button
            class="boton boton--primario"
            data-id="${producto.id}"
            ${sinStock ? "disabled" : ""}
          >
            ${sinStock ? "Sin stock" : "Agregar"}
          </button>
        </div>
        <p class="tarjeta-producto__stock">Stock disponible: ${producto.stock}</p>
      </div>
    </article>
  `;
}

// Evita inyeccion de HTML si un nombre/descripción trae caracteres especiales.
function escaparHtml(texto) {
  const div = document.createElement("div");
  div.textContent = texto;
  return div.innerHTML;
}

/**
 * Llena el select de categorías con las categorias reales del backend.
 */
async function cargarCategorias() {
  try {
    const categorias = await obtenerCategorias();
    const opciones = categorias
      .map((cat) => `<option value="${cat.id}">${escaparHtml(cat.nombre)}</option>`)
      .join("");
    selectCategoria.insertAdjacentHTML("beforeend", opciones);
  } catch (error) {
    // Si fallan las categorias, el catalogo general igual debe funcionar
    // solo dejamos el filtro con la opción "Todas"
    console.error("No se pudieron cargar las categorías:", error.message);
  }
}

/**
 * Carga productos todos, o filtrados por categoría segun el valor de select
 */
async function cargarProductos() {
  mostrarEstado("Cargando productos...");

  const categoriaId = selectCategoria.value;

  try {
    const productos = categoriaId
      ? await obtenerProductosPorCategoria(categoriaId)
      : await obtenerProductos();

    renderizarProductos(productos);
  } catch (error) {
    mostrarEstado(error.message, true);
  }
}

// Delegacion de eventos, un solo listener para todos los botones Agregar,
// aunque las tarjetas se vuelvan a generar dinamicamente.
contenedorProductos.addEventListener("click", async (evento) => {
  const boton = evento.target.closest("button[data-id]");
  if (!boton) return;

  const sesion = obtenerSesion();
  if (!sesion) {
    if (confirm("Debes iniciar sesión para agregar productos al carrito. ¿Ir a iniciar sesión?")) {
      window.location.href = "login.html";
    }
    return;
  }

  const productoId = boton.dataset.id;
  const textoOriginal = boton.textContent;
  boton.disabled = true;
  boton.textContent = "Agregando...";

  try {
    await agregarItemCarrito(sesion.id, productoId, 1);
    boton.textContent = "¡Agregado!";
    setTimeout(() => {
      boton.textContent = textoOriginal;
      boton.disabled = false;
    }, 1200);
  } catch (error) {
    alert(error.message);
    boton.textContent = textoOriginal;
    boton.disabled = false;
  }
});

selectCategoria.addEventListener("change", cargarProductos);

// Punto de entrada de la pagina.
(async function iniciar() {
  await cargarCategorias();
  await cargarProductos();
})();
