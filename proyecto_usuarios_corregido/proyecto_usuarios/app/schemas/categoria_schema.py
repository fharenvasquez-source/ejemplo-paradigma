from pydantic import BaseModel


class CategoriaBase(BaseModel):
    """Campos comunes que comparten todos los esquemas de categoria.
    Las demas clases heredan de esta para no repetir codigo."""
    nombre: str
    descripcion: str = ""  # Opcional: si no se envia queda como texto vacío


class CategoriaCreate(CategoriaBase):
    """Esquema que se usa cuando el cliente envia datos para crear
    una categoria nueva (POST). No incluye 'id' porque ese lo
    genera el servidor de forma automatica"""
    pass


class CategoriaUpdate(BaseModel):
    """Esquema para actualizar (PUT/PATCH) Todos los campos son
    opcionales, osea none por defecto porque el cliente puede querer
    cambiar solo el nombre, o solo la descripcion, no ambos."""
    nombre: str | None = None
    descripcion: str | None = None


class CategoriaResponse(CategoriaBase):
    """Esquema que se usa para las respuestas del servidor
    Si incluye id porque ya existe en la base de datos y el
    cliente necesita saberlo, por si la quiee editar despues."""
    id: int