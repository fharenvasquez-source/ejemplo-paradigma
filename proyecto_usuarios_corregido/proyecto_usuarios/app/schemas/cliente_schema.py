from pydantic import BaseModel


class ClienteBase(BaseModel):
    """Campos comunes de Cliente (sin contraseña, porque esta nunca
    debe viajar de vuelta al front en las respuestas)."""
    nombre: str
    correo: str
    direccion: str = ""
    telefono: str = ""


class ClienteCreate(ClienteBase):
    """Para registrar un cliente nuevo. Aqui si se incluye
    la contraseña, porque el cliente la debe enviar al registrarse."""
    contrasena: str


class ClienteUpdate(BaseModel):
    """Para actualizar, Todo opcional, incluyendo la
    contraseña por si el cliente quiere cambiarla."""
    nombre: str | None = None
    correo: str | None = None
    direccion: str | None = None
    telefono: str | None = None
    contrasena: str | None = None


class ClienteResponse(ClienteBase):
    """Lo que el servidor devuelve. Incluye 'id' pero nunca la
    contraseña y asi, aunque el repositorio la tenga guardada
    internamente jamás se filtra hacia afuera en las respuestas."""
    id: int


class LoginRequest(BaseModel):
    """Body que se envia al endpoint de login."""
    correo: str
    contrasena: str


class LoginResponse(BaseModel):
    """Lo que se devuelve tras un login exitoso: los datos básicos
    del cliente y su id, que el front (JS) guardara para usarlo
    en las siguientes peticiones (carrito, compras, etc.)."""
    id: int
    nombre: str
    correo: str