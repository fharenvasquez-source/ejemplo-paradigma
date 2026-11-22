from fastapi import APIRouter, HTTPException
from app.database import proveedores_repo
from app.schemas.proveedor_schema import ProveedorCreate, ProveedorUpdate, ProveedorResponse

router = APIRouter(prefix="/proveedores", tags=["Proveedores"])


@router.get("/", response_model=list[ProveedorResponse])
def listar_proveedores():
    """GET /proveedores/ -> Lista todos los proveedores registrados."""
    return proveedores_repo.leer_todos()


@router.get("/{proveedor_id}", response_model=ProveedorResponse)
def obtener_proveedor(proveedor_id: int):
    """GET /proveedores/{id} -> Devuelve un proveedor especifico."""
    proveedor = proveedores_repo.obtener_por_id(proveedor_id)
    if not proveedor:
        raise HTTPException(status_code=404, detail="Proveedor no encontrado")
    return proveedor


@router.post("/", response_model=ProveedorResponse, status_code=201)
def crear_proveedor(proveedor: ProveedorCreate):
    """POST /proveedores/ -> Crea un proveedor nuevo."""
    return proveedores_repo.agregar(proveedor.model_dump())


@router.put("/{proveedor_id}", response_model=ProveedorResponse)
def actualizar_proveedor(proveedor_id: int, proveedor: ProveedorUpdate):
    """PUT /proveedores/{id} -> Actualiza solo los campos enviados."""
    cambios = proveedor.model_dump(exclude_unset=True)
    actualizado = proveedores_repo.actualizar(proveedor_id, cambios)
    if not actualizado:
        raise HTTPException(status_code=404, detail="Proveedor no encontrado")
    return actualizado


@router.delete("/{proveedor_id}", status_code=204)
def eliminar_proveedor(proveedor_id: int):
    """DELETE /proveedores/{id} -> Elimina un proveedor."""
    if not proveedores_repo.eliminar(proveedor_id):
        raise HTTPException(status_code=404, detail="Proveedor no encontrado")