from pydantic import BaseModel


class ProductoBase(BaseModel):
    """Campos comunes de producto"""
    nombre: str
    descripcion: str = ""
    precio: float
    stock: int = 0
    categoria_id: int    # Relacion: a que categoria pertenece
    proveedor_id: int    # Relacion: de que proveedor viene


class ProductoCreate(ProductoBase):
    """Para crear un producto nuevo """
    pass


class ProductoUpdate(BaseModel):
    """Para actualizar, todo opcional."""
    nombre: str | None = None
    descripcion: str | None = None
    precio: float | None = None
    stock: int | None = None
    categoria_id: int | None = None
    proveedor_id: int | None = None


class ProductoResponse(ProductoBase):
    """Lo que el servidor devuelve, incluyendo el id."""
    id: int