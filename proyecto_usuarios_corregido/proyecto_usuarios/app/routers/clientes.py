import hashlib
from fastapi import APIRouter, HTTPException
from app.database import clientes_repo
from app.schemas.cliente_schema import (
    ClienteCreate, ClienteUpdate, ClienteResponse,
    LoginRequest, LoginResponse,
)

router = APIRouter(prefix="/clientes", tags=["Clientes"])


def _hash_password(contrasena: str) -> str:
    """Convierte la contraseña en texto plano a un hash
      asi aunque alguien abra el archivo clientes.json,
    nunca ve la contraseña solo  hash.
"""
    return hashlib.sha256(contrasena.encode("utf-8")).hexdigest()


@router.get("/", response_model=list[ClienteResponse])
def listar_clientes():
    """GET /clientes/ -> Lista todos los clientes (sin contraseñas,
    porque ClienteResponse no incluye ese campo)."""
    return clientes_repo.leer_todos()


@router.get("/{cliente_id}", response_model=ClienteResponse)
def obtener_cliente(cliente_id: int):
    """GET /clientes/{id} -> Devuelve un cliente especifico."""
    cliente = clientes_repo.obtener_por_id(cliente_id)
    if not cliente:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    return cliente


@router.post("/", response_model=ClienteResponse, status_code=201)
def registrar_cliente(cliente: ClienteCreate):
    """POST /clientes/ -> Registra un cliente nuevo
    Antes de guardar, reemplaza la contraseña en texto plano
    por su versión hasheada"""
    datos = cliente.model_dump()
    datos["contrasena"] = _hash_password(datos["contrasena"])
    return clientes_repo.agregar(datos)


@router.put("/{cliente_id}", response_model=ClienteResponse)
def actualizar_cliente(cliente_id: int, cliente: ClienteUpdate):
    """PUT /clientes/{id} -> Actualiza solo los campos enviados
    Si viene una contraseña nueva, también se hashea antes de guardar."""
    cambios = cliente.model_dump(exclude_unset=True)
    if "contrasena" in cambios:
        cambios["contrasena"] = _hash_password(cambios["contrasena"])

    actualizado = clientes_repo.actualizar(cliente_id, cambios)
    if not actualizado:
        raise HTTPException(status_code=404, detail="Cliente no encontrado")
    return actualizado


@router.delete("/{cliente_id}", status_code=204)
def eliminar_cliente(cliente_id: int):
    """DELETE /clientes/{id} -> Elimina un cliente."""
    if not clientes_repo.eliminar(cliente_id):
        raise HTTPException(status_code=404, detail="Cliente no encontrado")


@router.post("/login", response_model=LoginResponse)
def login(credenciales: LoginRequest):
    """POST /clientes/login -> Login simple
    busca al cliente por correo, hashea la contraseña recibida
    y la compara contra la guardada. Si coincide devuelve el id
    del cliente (que el front usara como identificador de sesion)."""
    todos = clientes_repo.leer_todos()
    cliente = next((c for c in todos if c["correo"] == credenciales.correo), None)

    if not cliente or cliente["contrasena"] != _hash_password(credenciales.contrasena):
        # Mensaje generico, no decimos si fallo el correo
        # o la contraseña, por seguridad 
        raise HTTPException(status_code=401, detail="Correo o contraseña incorrectos")

    return cliente