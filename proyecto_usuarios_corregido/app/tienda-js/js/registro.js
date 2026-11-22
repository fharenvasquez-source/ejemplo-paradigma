// Logica de la pantalla de registro de cliente, registro.html.

const formRegistro = document.getElementById("form-registro");
const mensajeRegistro = document.getElementById("mensaje-registro");

function mostrarMensajeRegistro(texto, esError = true) {
  mensajeRegistro.textContent = texto;
  mensajeRegistro.classList.remove("oculto");
  mensajeRegistro.classList.toggle("mensaje-error", esError);
  mensajeRegistro.classList.toggle("mensaje-exito", !esError);
}

formRegistro.addEventListener("submit", async (evento) => {
  evento.preventDefault();

  const datosCliente = {
    nombre: document.getElementById("nombre").value.trim(),
    correo: document.getElementById("correo").value.trim(),
    telefono: document.getElementById("telefono").value.trim(),
    direccion: document.getElementById("direccion").value.trim(),
    contrasena: document.getElementById("contrasena").value,
  };

  const botonEnviar = formRegistro.querySelector("button[type=submit]");
  botonEnviar.disabled = true;
  botonEnviar.textContent = "Creando cuenta...";

  try {
    const clienteCreado = await registrarCliente(datosCliente);

    guardarSesion(clienteCreado);

    mostrarMensajeRegistro("¡Cuenta creada! Iniciando sesión...", false);

    setTimeout(() => {
      window.location.href = "index.html";
    }, 800);
  } catch (error) {
    mostrarMensajeRegistro(error.message);
    botonEnviar.disabled = false;
    botonEnviar.textContent = "Crear cuenta";
  }
});
