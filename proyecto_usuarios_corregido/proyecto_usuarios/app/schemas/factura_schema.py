from pydantic import BaseModel


class FacturaCreate(BaseModel):
    """Body para generar una factura desde el carrito del cliente."""
    cliente_id: int
    entrega_domicilio: bool = False
    direccion_entrega: str = ""   # Si no se envía, se usa la del cliente


class ItemFacturaDetalle(BaseModel):
    """Detalle de cada producto vendido en la factura."""
    producto_id: int
    nombre: str
    cantidad: int
    precio_unitario: float
    subtotal: float


class FacturaResponse(BaseModel):
    """Respuesta completa de la factura"""
    id: int
    numero: str
    fecha: str
    cliente_id: int
    items: list[ItemFacturaDetalle]
    subtotal: float
    impuesto: float
    total: float
    entrega_domicilio: bool
    direccion_entrega: str