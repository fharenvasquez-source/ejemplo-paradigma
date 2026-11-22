// login.js
// Lógica de la pantalla de login (login.html).

const formLogin = document.getElementById("form-login");
const mensajeLogin = document.getElementById("mensaje-login");

function mostrarMensajeLogin(texto, esError = true) {
  mensajeLogin.textContent = texto;
  mensajeLogin.classList.remove("oculto");
  mensajeLogin.classList.toggle("mensaje-error", esError);
  mensajeLogin.classList.toggle("mensaje-exito", !esError);
}

formLogin.addEventListener("submit", async (evento) => {
  evento.preventDefault();

  const correo = document.getElementById("correo").value.trim();
  const contrasena = document.getElementById("contrasena").value;

  const botonEnviar = formLogin.querySelector("button[type=submit]");
  botonEnviar.disabled = true;
  botonEnviar.textContent = "Ingresando...";

  try {
   
    const resultado = await loginCliente(correo, contrasena);


    let datosCliente = resultado;
    if (!datosCliente.nombre && datosCliente.id) {
      datosCliente = await obtenerClientePorId(datosCliente.id);
    }

    guardarSesion(datosCliente);

    mostrarMensajeLogin(`¡Bienvenido, ${datosCliente.nombre}!`, false);

    setTimeout(() => {
      window.location.href = "index.html";
    }, 800);
  } catch (error) {
    mostrarMensajeLogin(error.message);
    botonEnviar.disabled = false;
    botonEnviar.textContent = "Entrar";
  }
});
