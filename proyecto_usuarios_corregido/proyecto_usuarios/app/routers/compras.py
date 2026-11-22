from datetime import datetime
from fastapi import APIRouter, HTTPException
from app.database import compras_repo, productos_repo, proveedores_repo
from app.schemas.compra_schema import CompraCreate, CompraResponse

router = APIRouter(prefix="/compras", tags=["Compras"])


@router.get("/", response_model=list[CompraResponse])
def listar_compras():
    """GET /compras/ -> Lista el historial completo de compras."""
    return compras_repo.leer_todos()


@router.get("/{compra_id}", response_model=CompraResponse)
def obtener_compra(compra_id: int):
    """GET /compras/{id} -> Devuelve una compra especifica."""
    compra = compras_repo.obtener_por_id(compra_id)
    if not compra:
        raise HTTPException(status_code=404, detail="Compra no encontrada")
    return compra


@router.post("/", response_model=CompraResponse, status_code=201)
def registrar_compra(compra: CompraCreate):
    """POST /compras/ -> Registra una compra a un proveedor y
    aumenta automaticamente el stock de cada producto comprado. """
    if not proveedores_repo.obtener_por_id(compra.proveedor_id):
        raise HTTPException(status_code=404, detail="Proveedor no encontrado")

    if not compra.items:
        raise HTTPException(status_code=422, detail="La compra debe tener al menos un producto")

    items_guardados = []
    total = 0.0

    for item in compra.items:
        producto = productos_repo.obtener_por_id(item.producto_id)
        if not producto:
            raise HTTPException(
                status_code=404,
                detail=f"El producto con id {item.producto_id} no existe"
            )

        # --- Aumenta el stock del producto sumando la cantidad comprada ---
        nuevo_stock = producto["stock"] + item.cantidad
        productos_repo.actualizar(producto["id"], {"stock": nuevo_stock})

        subtotal = round(item.precio_unitario * item.cantidad, 2)
        total += subtotal
        items_guardados.append({
            "producto_id": producto["id"],
            "nombre": producto["nombre"],
            "cantidad": item.cantidad,
            "precio_unitario": item.precio_unitario,
            "subtotal": subtotal,
        })

    nueva_compra = {
        "proveedor_id": compra.proveedor_id,
        "fecha": datetime.now().isoformat(timespec="seconds"),
        "items": items_guardados,
        "total": round(total, 2),
    }
    return compras_repo.agregar(nueva_compra)