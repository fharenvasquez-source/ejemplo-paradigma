from pydantic import BaseModel


class ItemCompraInput(BaseModel):
    """Lo que se envia por cada producto que se esta comprando
    al proveedor para reabastecer inventario."""
    producto_id: int
    cantidad: int
    precio_unitario: float


class CompraCreate(BaseModel):
    """Body para registrar una compra nueva."""
    proveedor_id: int
    items: list[ItemCompraInput]


class ItemCompraDetalle(BaseModel):
    """detalle de cada item ya resuelto con el nombre del producto
    y su subtotal, para la respuesta"""
    producto_id: int
    nombre: str
    cantidad: int
    precio_unitario: float
    subtotal: float


class CompraResponse(BaseModel):
    """Respuesta completa de una compra registrada."""
    id: int
    proveedor_id: int
    fecha: str
    items: list[ItemCompraDetalle]
    total: float