// api.js
// Funciones reutilizables para hablar con el backend FastAPI.
// Cada función hace una cosa: pedir datos y devolver JSON ya parseado.
// y asi el resto del codigo (productos.js, carrito.js, etc.) no necesita
// saber nada de fetch, headers, ni manejo de errores repetido.

/**
 * Wrapper generico sobre fetch que:
 * - arma la URL completa a partir de API_BASE_URL
 * - lanza un Error legible si la respuesta no fue exitosa (status >= 400)
 * - devuelve directamente el JSON de la respuesta
 */
async function apiRequest(endpoint, options = {}) {
  const url = `${API_BASE_URL}${endpoint}`;

  let response;
  try {
    response = await fetch(url, {
      headers: { "Content-Type": "application/json" },
      ...options,
    });
  } catch (error) {
    // Esto ocurre si el backend no está corriendo, o hay un problema de red/CORS.
    throw new Error(
      "No se pudo conectar con el servidor. ¿Está corriendo el backend en " +
        API_BASE_URL +
        "?"
    );
  }


  let data = null;
  try {
    data = await response.json();
  } catch (_) {
    // Respuesta vacia o no-JSON; lo dejamos en null.
  }

  if (!response.ok) {
    const mensaje = extraerMensajeError(data, response.status, endpoint);
    throw new Error(mensaje);
  }

  return data;
}

/**
 * FastAPI puede mandar el error de dos formas distintas:
 * - Un string simple
 * - Una lista de errores de validación (422):
 * Esta función arma un mensaje legible en cualquiera de los dos casos.
 */
function extraerMensajeError(data, status, endpoint) {
  if (!data || !data.detail) {
    return `Error ${status} al consultar ${endpoint}`;
  }

  if (typeof data.detail === "string") {
    return data.detail;
  }

  if (Array.isArray(data.detail)) {
    return data.detail
      .map((err) => {
        const campo = Array.isArray(err.loc) ? err.loc.at(-1) : "campo";
        return `${campo}: ${err.msg}`;
      })
      .join(" · ");
  }

  return `Error ${status} al consultar ${endpoint}`;
}

// ---- Productos ----

function obtenerProductos() {
  return apiRequest("/productos/");
}

function obtenerProductoPorId(id) {
  return apiRequest(`/productos/${id}`);
}

function obtenerProductosPorCategoria(categoriaId) {
  return apiRequest(`/productos/categoria/${categoriaId}`);
}

// ---- Categorías ----

function obtenerCategorias() {
  return apiRequest("/categorias/");
}

// ---- Clientes (autenticación) ----

/**
 * Inicia sesión de un cliente.
 * Devuelve el id del cliente si las credenciales son correctas.
 */
function loginCliente(correo, contrasena) {
  return apiRequest("/clientes/login", {
    method: "POST",
    body: JSON.stringify({ correo, contrasena }),
  });
}

/**
 * Registra un nuevo cliente
 * datosCliente = { nombre, correo, direccion, telefono, contrasena }
 */
function registrarCliente(datosCliente) {
  return apiRequest("/clientes/", {
    method: "POST",
    body: JSON.stringify(datosCliente),
  });
}

function obtenerClientePorId(id) {
  return apiRequest(`/clientes/${id}`);
}

// ---- Carrito de compras ----

function obtenerCarrito(clienteId) {
  return apiRequest(`/carrito/${clienteId}`);
}

function agregarItemCarrito(clienteId, productoId, cantidad) {
  return apiRequest(`/carrito/${clienteId}/items`, {
    method: "POST",
    body: JSON.stringify({ producto_id: productoId, cantidad }),
  });
}

function actualizarItemCarrito(clienteId, productoId, cantidad) {
  return apiRequest(`/carrito/${clienteId}/items/${productoId}`, {
    method: "PUT",
    body: JSON.stringify({ producto_id: Number(productoId), cantidad }),
  });
}

function eliminarItemCarrito(clienteId, productoId) {
  return apiRequest(`/carrito/${clienteId}/items/${productoId}`, {
    method: "DELETE",
  });
}

function vaciarCarrito(clienteId) {
  return apiRequest(`/carrito/${clienteId}`, {
    method: "DELETE",
  });
}

// ---- Facturas ----

function generarFactura(clienteId, entregaDomicilio) {
  return apiRequest("/facturas/", {
    method: "POST",
    body: JSON.stringify({
      cliente_id: clienteId,
      entrega_domicilio: entregaDomicilio,
    }),
  });
}

function obtenerFacturasCliente(clienteId) {
  return apiRequest(`/facturas/cliente/${clienteId}`);
}
