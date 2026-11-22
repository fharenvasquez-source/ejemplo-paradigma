from datetime import datetime
from fastapi import APIRouter, HTTPException
from app.database import facturas_repo, carritos_repo, productos_repo, clientes_repo
from app.schemas.factura_schema import FacturaCreate, FacturaResponse

router = APIRouter(prefix="/facturas", tags=["Facturas"])

# Tasa de Impuesto Sobre Ventas (ISV)
TASA_IMPUESTO = 0.15


def _generar_numero(id: int) -> str:
    """genera un numero de factura con formato 'F-00001' a partir
    del id autoincremental, para que se vea
    como un numero de factura real."""
    return f"F-{id:05d}"


@router.get("/", response_model=list[FacturaResponse])
def listar_facturas():
    """GET /facturas/ -> Lista todas las facturas generadas."""
    return facturas_repo.leer_todos()


@router.get("/cliente/{cliente_id}", response_model=list[FacturaResponse])
def listar_facturas_por_cliente(cliente_id: int):
    """GET /facturas/cliente/{id} -> Historial de compras de un
    cliente especifico para que la tienda virtual muestre
    'mis pedidos'."""
    todas = facturas_repo.leer_todos()
    return list(filter(lambda f: f["cliente_id"] == cliente_id, todas))

@router.get("/{factura_id}", response_model=FacturaResponse)
def obtener_factura(factura_id: int):
    """GET /facturas/{id} -> Devuelve una factura específica."""
    factura = facturas_repo.obtener_por_id(factura_id)
    if not factura:
        raise HTTPException(status_code=404, detail="Factura no encontrada")
    return factura


@router.post("/", response_model=FacturaResponse, status_code=201)
def generar_factura(datos: FacturaCreate):
    """POST /facturas/ -> Genera una factura a partir del carrito
    actual del cliente, una compra simulada, aqui:
    1. se verifica que el carrito no este vacio.
    2. Verifica que haya stock de cada producto.
    3. Descuenta el stock vendido.
    4. Calcula subtotal, impuesto y total.
    5. Se vacia el carrito, porque ya se 'compró' todo lo que tenia.
    """
    cliente = clientes_repo.obtener_por_id(datos.cliente_id)
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")

    carritos = carritos_repo.leer_todos()
    carrito = next((c for c in carritos if c["cliente_id"] == datos.cliente_id), None)
    if not carrito or not carrito["items"]:
        raise HTTPException(status_code=400, detail="El carrito está vacío")

    # --- Primero se valida todo el stock, antes de modificar nada ---
    # se evita descontar el stock de unos productos y luego fallar
    for item in carrito["items"]:
        producto = productos_repo.obtener_por_id(item["producto_id"])
        if not producto:
            raise HTTPException(
                status_code=404,
                detail=f"El producto con id {item['producto_id']} ya no existe"
            )
        if producto["stock"] < item["cantidad"]:
            raise HTTPException(
                status_code=400,
                detail=f"Stock insuficiente de '{producto['nombre']}' "
                       f"(disponible: {producto['stock']}, solicitado: {item['cantidad']})"
            )

    # --- Ya validado ahora si se descuenta el stock y arma el detalle ---
    items_factura = []
    subtotal = 0.0
    for item in carrito["items"]:
        producto = productos_repo.obtener_por_id(item["producto_id"])
        nuevo_stock = producto["stock"] - item["cantidad"]
        productos_repo.actualizar(producto["id"], {"stock": nuevo_stock})

        subtotal_item = round(producto["precio"] * item["cantidad"], 2)
        subtotal += subtotal_item
        items_factura.append({
            "producto_id": producto["id"],
            "nombre": producto["nombre"],
            "cantidad": item["cantidad"],
            "precio_unitario": producto["precio"],
            "subtotal": subtotal_item,
        })

    subtotal = round(subtotal, 2)
    impuesto = round(subtotal * TASA_IMPUESTO, 2)
    total = round(subtotal + impuesto, 2)
    direccion = datos.direccion_entrega or cliente.get("direccion", "")

    nueva_factura = {
        "numero": "",  # Se completa despues de saber el id autogenerado
        "fecha": datetime.now().isoformat(timespec="seconds"),
        "cliente_id": datos.cliente_id,
        "items": items_factura,
        "subtotal": subtotal,
        "impuesto": impuesto,
        "total": total,
        "entrega_domicilio": datos.entrega_domicilio,
        "direccion_entrega": direccion,
    }
    creada = facturas_repo.agregar(nueva_factura)

    # Ahora que ya existe el id, genera el numero de factura y lo guarda
    numero = _generar_numero(creada["id"])
    creada = facturas_repo.actualizar(creada["id"], {"numero": numero})

    # --- Vaci el carrito, ya que su contenido se convirtio en factura ---
    carritos_repo.actualizar(carrito["id"], {"items": []})

    return creada