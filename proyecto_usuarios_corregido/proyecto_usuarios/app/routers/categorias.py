from fastapi import APIRouter, HTTPException
from app.database import categorias_repo
from app.schemas.categoria_schema import CategoriaCreate, CategoriaUpdate, CategoriaResponse

#APIRouter agrupa todos los endpoints de "categorias" bajo el mismo
# prefijo de URL (/categorias) y los etiqueta en la documentacion
#de Swagger (/docs) bajo el tag "Categorias".
router = APIRouter(prefix="/categorias", tags=["Categorías"])


@router.get("/", response_model=list[CategoriaResponse])
def listar_categorias():
    """GET /categorias/  -> Devuelve todaslas categorias registradas."""
    return categorias_repo.leer_todos()


@router.get("/{categoria_id}", response_model=CategoriaResponse)
def obtener_categoria(categoria_id: int):
    """GET /categorias/{id}  -> Devuelve una categoria especifica
    ssi no existe responde con error 404 """
    categoria = categorias_repo.obtener_por_id(categoria_id)
    if not categoria:
        raise HTTPException(status_code=404, detail="Categoría no encontrada")
    return categoria


@router.post("/", response_model=CategoriaResponse, status_code=201)
def crear_categoria(categoria: CategoriaCreate):
    """POST /categorias/  -> Crea una categoria nueva
    FastAPI valida automaticamente que el body cumpla con
    CategoriaCreate (nombre obligatorio) antes de llegar hastaaqui
    status_code=201 = 'Created', el estandar HTTP para creaciones exitosas"""
    return categorias_repo.agregar(categoria.model_dump())
    #model_dump()


@router.put("/{categoria_id}", response_model=CategoriaResponse)
def actualizar_categoria(categoria_id: int, categoria: CategoriaUpdate):
    """PUT /categorias/{id}  -> Actualiza una categoria existente"""
    # exclude_unset=True: solo incluye en el diccionario los campos que
    # el cliente si envio, ignorando los que quedaron en None por defecto
    # Asi se evita borrar accidentalmente datos que no se querian cambiar.
    cambios = categoria.model_dump(exclude_unset=True)
    actualizada = categorias_repo.actualizar(categoria_id, cambios)
    if not actualizada:
        raise HTTPException(status_code=404, detail="Categoría no encontrada")
    return actualizada


@router.delete("/{categoria_id}", status_code=204)
def eliminar_categoria(categoria_id: int):
    """DELETE /categorias/{id}  -> Elimina una categoría.
    status_code=204 = 'No Content', el estándar para eliminaciones
    exitosas (no se devuelve ningún body en la respuesta)."""
    if not categorias_repo.eliminar(categoria_id):
        raise HTTPException(status_code=404, detail="Categoría no encontrada")