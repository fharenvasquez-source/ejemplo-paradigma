from fastapi import APIRouter, HTTPException
from app.database import productos_repo, categorias_repo, proveedores_repo
from app.schemas.producto_schema import ProductoCreate, ProductoUpdate, ProductoResponse

router = APIRouter(prefix="/productos", tags=["Productos"])


def _validar_relaciones(categoria_id: int, proveedor_id: int):
    """Funcion auxiliar (no es un endpoint): verifica que la categoria
    y el proveedor referenciados realmente existan antes de guardar
    el productos y si no existen, corta la ejecución con un error 404.."""
    if not categorias_repo.obtener_por_id(categoria_id):
        raise HTTPException(status_code=404, detail="La categoria indicada no existe")
    if not proveedores_repo.obtener_por_id(proveedor_id):
        raise HTTPException(status_code=404, detail="El proveedor indicado no existe")


@router.get("/", response_model=list[ProductoResponse])
def listar_productos():
    """GET /productos/ -> Lista todos los productos."""
    return productos_repo.leer_todos()


@router.get("/{producto_id}", response_model=ProductoResponse)
def obtener_producto(producto_id: int):
    """GET /productos/{id} -> Devuelve un producto por id."""
    producto = productos_repo.obtener_por_id(producto_id)
    if not producto:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    return producto


@router.get("/categoria/{categoria_id}", response_model=list[ProductoResponse])
def listar_productos_por_categoria(categoria_id: int):
    """GET /productos/categoria/{id} -> Filtra productos por categoria."""
    todos = productos_repo.leer_todos()
    return list(filter(lambda p: p["categoria_id"] == categoria_id, todos))


@router.post("/", response_model=ProductoResponse, status_code=201)
def crear_producto(producto: ProductoCreate):
    """POST /productos/ -> Crea un producto nuevo, validando primero
    que la categoria y el proveedor referenciados existan."""
    _validar_relaciones(producto.categoria_id, producto.proveedor_id)
    return productos_repo.agregar(producto.model_dump())


@router.put("/{producto_id}", response_model=ProductoResponse)
def actualizar_producto(producto_id: int, producto: ProductoUpdate):
    """PUT /productos/{id} -> Actualiza solo los campos enviados.
    Si el cliente envia categoria_id o proveedor_id nuevos, tambien
    se validan antes de guardar."""
    cambios = producto.model_dump(exclude_unset=True)
    if "categoria_id" in cambios and not categorias_repo.obtener_por_id(cambios["categoria_id"]):
        raise HTTPException(status_code=404, detail="La categoría indicada no existe")
    if "proveedor_id" in cambios and not proveedores_repo.obtener_por_id(cambios["proveedor_id"]):
        raise HTTPException(status_code=404, detail="El proveedor indicado no existe")

    actualizado = productos_repo.actualizar(producto_id, cambios)
    if not actualizado:
        raise HTTPException(status_code=404, detail="Producto no encontrado")
    return actualizado


@router.delete("/{producto_id}", status_code=204)
def eliminar_producto(producto_id: int):
    """DELETE /productos/{id} -> Elimina un producto."""
    if not productos_repo.eliminar(producto_id):
        raise HTTPException(status_code=404, detail="Producto no encontrado")