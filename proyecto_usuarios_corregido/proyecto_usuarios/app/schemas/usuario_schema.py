from pydantic import BaseModel


class UsuarioBase(BaseModel):
    """Campos comunes de Usuario (administrador) sin contraseña,
    porque esta nunca debe viajar de vuelta al front."""
    nombre: str
    correo: str
    rol: str = "administrador"


class UsuarioCreate(UsuarioBase):
    """Para registrar un usuario nuevo. Incluye la contraseña
    porque el usuario la manda al crearse."""
    contrasena: str


class UsuarioUpdate(BaseModel):
    """Para actualizar, opcional todo"""
    nombre: str | None = None
    correo: str | None = None
    rol: str | None = None
    contrasena: str | None = None


class UsuarioResponse(UsuarioBase):
    """Lo que el servidor devuelve, incluye el id pero NUNCA la contraseñaa"""
    id: int


class LoginRequest(BaseModel):
    """Body que se envia al endpoint de login."""
    correo: str
    contrasena: str


class LoginResponse(BaseModel):
    """Lo que se devuelve tras un login exitoso."""
    id: int
    nombre: str
    correo: str
    rol: str