from pydantic import BaseModel


class ProveedorBase(BaseModel):
    """Campos comunes de Proveedor, laas demes clases heredan de aqui"""
    nombre: str
    contacto: str = ""      # Persona de contacto en la empresa proveedora
    telefono: str = ""


class ProveedorCreate(ProveedorBase):
    """Se usa cuando el cliente envia datos para crear un proveedor
    y no incluye 'id' porque lo genera el servidor."""
    pass


class ProveedorUpdate(BaseModel):
    """Para actualizar, todo opcional para permitir editar
    solo el campo que se necesite."""
    nombre: str | None = None
    contacto: str | None = None
    telefono: str | None = None


class ProveedorResponse(ProveedorBase):
    """Lo que el servidor devuelve, si incluye id."""
    id: int