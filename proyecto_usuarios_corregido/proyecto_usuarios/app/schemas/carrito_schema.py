from pydantic import BaseModel


class ItemCarritoInput(BaseModel):
    """Lo que el cliente envia para agregar un producto al carrito."""
    producto_id: int
    cantidad: int = 1


class ItemCarritoDetalle(BaseModel):
    """Lo que se devuelve por cada item del carrito, ya con la
    información del producto resuelta (nombre, precio) y el
    subtotal calculado (precio * cantidad)."""
    producto_id: int
    nombre: str
    precio: float
    cantidad: int
    subtotal: float


class CarritoResponse(BaseModel):
    """Respuesta completa del carrito: la lista de items con detalle,
    mas el total general (suma de todos los subtotales)."""
    cliente_id: int
    items: list[ItemCarritoDetalle]
    total: float