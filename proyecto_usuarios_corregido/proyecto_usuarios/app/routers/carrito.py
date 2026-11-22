from fastapi import APIRouter, HTTPException
from app.database import carritos_repo, productos_repo, clientes_repo
from app.schemas.carrito_schema import ItemCarritoInput, CarritoResponse

router = APIRouter(prefix="/carrito", tags=["Carrito"])


def _validar_cliente(cliente_id: int):
    """Corta la ejecucion con 404 si el cliente no existe.
    se reutiliza en casi todos los endpoints de este router"""
    if not clientes_repo.obtener_por_id(cliente_id):
        raise HTTPException(status_code=404, detail="Cliente no encontrado")


def _obtener_o_crear_carrito(cliente_id: int) -> dict:
    """Busca el carrito del cliente, si nunca ha tenido uno, le crea uno vacio automaticamente.
    de esa forma el cliente nunca tiene que crear su carrito manualmente."""
    carritos = carritos_repo.leer_todos()
    carrito = next((c for c in carritos if c["cliente_id"] == cliente_id), None)
    if not carrito:
        carrito = carritos_repo.agregar({"cliente_id": cliente_id, "items": []})
    return carrito


def _construir_respuesta(carrito: dict) -> dict:
    """Recorre los items guardados (solo producto_id + cantidad) y
    arma la respuesta completa consultando el precio y nombre
    de cada producto, si un producto fue eliminado
    simplemente se omite del carrito."""
    items_detalle = []
    total = 0.0
    for item in carrito["items"]:
        producto = productos_repo.obtener_por_id(item["producto_id"])
        if not producto:
            continue
        subtotal = round(producto["precio"] * item["cantidad"], 2)
        total += subtotal
        items_detalle.append({
            "producto_id": producto["id"],
            "nombre": producto["nombre"],
            "precio": producto["precio"],
            "cantidad": item["cantidad"],
            "subtotal": subtotal,
        })
    return {
        "cliente_id": carrito["cliente_id"],
        "items": items_detalle,
        "total": round(total, 2),
    }


@router.get("/{cliente_id}", response_model=CarritoResponse)
def ver_carrito(cliente_id: int):
    """GET /carrito/{cliente_id} -> Muestra el carrito del cliente
    con los subtotales y total ya calculados."""
    _validar_cliente(cliente_id)
    carrito = _obtener_o_crear_carrito(cliente_id)
    return _construir_respuesta(carrito)


@router.post("/{cliente_id}/items", response_model=CarritoResponse, status_code=201)
def agregar_item(cliente_id: int, item: ItemCarritoInput):
    """POST /carrito/{cliente_id}/items -> Agrega un producto al carrito.
    Si el producto ya estaba en el carrito, suma la cantidad nueva a
    la existente en vez de duplicar la entrada."""
    _validar_cliente(cliente_id)

    producto = productos_repo.obtener_por_id(item.producto_id)
    if not producto:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    if item.cantidad <= 0:
        raise HTTPException(status_code=422, detail="La cantidad debe ser mayor a 0")

    carrito = _obtener_o_crear_carrito(cliente_id)
    existente = next((i for i in carrito["items"] if i["producto_id"] == item.producto_id), None)

    if existente:
        existente["cantidad"] += item.cantidad
    else:
        carrito["items"].append({"producto_id": item.producto_id, "cantidad": item.cantidad})

    carritos_repo.actualizar(carrito["id"], {"items": carrito["items"]})
    return _construir_respuesta(carrito)


@router.put("/{cliente_id}/items/{producto_id}", response_model=CarritoResponse)
def actualizar_cantidad(cliente_id: int, producto_id: int, item: ItemCarritoInput):
    """PUT /carrito/{cliente_id}/items/{producto_id} -> Reemplaza la
    cantidad de un producto ya existente en el carrito,a diferencia
    de POST, aqui no se suma, se establece el valor exacto"""
    _validar_cliente(cliente_id)
    carrito = _obtener_o_crear_carrito(cliente_id)

    existente = next((i for i in carrito["items"] if i["producto_id"] == producto_id), None)
    if not existente:
        raise HTTPException(status_code=404, detail="Ese producto no está en el carrito")

    if item.cantidad <= 0:
        # Si la nueva cantidad es 0 o negativa, se interpreta como
        # que el cliente quiere quitar el producto del carrito.
        carrito["items"] = [i for i in carrito["items"] if i["producto_id"] != producto_id]
    else:
        existente["cantidad"] = item.cantidad

    carritos_repo.actualizar(carrito["id"], {"items": carrito["items"]})
    return _construir_respuesta(carrito)


@router.delete("/{cliente_id}/items/{producto_id}", response_model=CarritoResponse)
def eliminar_item(cliente_id: int, producto_id: int):
    """DELETE /carrito/{cliente_id}/items/{producto_id} -> Quita un
    producto especifico del carrito."""
    _validar_cliente(cliente_id)
    carrito = _obtener_o_crear_carrito(cliente_id)

    nuevos_items = [i for i in carrito["items"] if i["producto_id"] != producto_id]
    if len(nuevos_items) == len(carrito["items"]):
        raise HTTPException(status_code=404, detail="Ese producto no esta en el carrito")

    carritos_repo.actualizar(carrito["id"], {"items": nuevos_items})
    carrito["items"] = nuevos_items
    return _construir_respuesta(carrito)


@router.delete("/{cliente_id}", status_code=204)
def vaciar_carrito(cliente_id: int):
    """DELETE /carrito/{cliente_id} -> Vacia el carrito
    por ejemplo, despues de completar una compra."""
    _validar_cliente(cliente_id)
    carrito = _obtener_o_crear_carrito(cliente_id)
    carritos_repo.actualizar(carrito["id"], {"items": []})