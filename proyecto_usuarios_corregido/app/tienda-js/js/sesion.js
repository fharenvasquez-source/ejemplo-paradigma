// sesion.js
// No se usan tokens para inicio de sesion, es carga de fronted recordar quien inicio la sesion
// Usamos localStorage para que la sesion sobreviva a recargar la pagina

const CLAVE_SESION = "tienda_sesion_cliente";

/**
 * Guarda los datos del cliente autenticado.
 * datosCliente debe incluir al menos id, nombre 
 */
function guardarSesion(datosCliente) {
  localStorage.setItem(CLAVE_SESION, JSON.stringify(datosCliente));
}

/**
 * Devuelve los datos del cliente en sesion, o null si no hay nadie logueado.
 */
function obtenerSesion() {
  const datos = localStorage.getItem(CLAVE_SESION);
  return datos ? JSON.parse(datos) : null;
}

function cerrarSesion() {
  localStorage.removeItem(CLAVE_SESION);
}

/**
 * Exige que haya un cliente logueado, si no, redirige a login.html
 * y devuelve null
 */
function requerirSesion() {
  const sesion = obtenerSesion();
  if (!sesion) {
    window.location.href = "login.html";
    return null;
  }
  return sesion;
}

/**
 * Actualiza el encabezado (nav) segun si hay sesion activa o no.
 * Se llama en cada pagina que incluya el encabezado estandar.
 */
function actualizarEncabezadoSesion() {
  const enlaceSesion = document.getElementById("enlace-sesion");
  if (!enlaceSesion) return;

  const sesion = obtenerSesion();

  if (sesion) {
    enlaceSesion.textContent = `Hola, ${sesion.nombre} (salir)`;
    enlaceSesion.href = "#";
    enlaceSesion.onclick = (evento) => {
      evento.preventDefault();
      cerrarSesion();
      window.location.href = "index.html";
    };
  } else {
    enlaceSesion.textContent = "Iniciar sesión";
    enlaceSesion.href = "login.html";
    enlaceSesion.onclick = null;
  }
}

document.addEventListener("DOMContentLoaded", actualizarEncabezadoSesion);
