import hashlib
from fastapi import APIRouter, HTTPException
from app.database import usuarios_repo
from app.schemas.usuario_schema import (
    UsuarioCreate, UsuarioUpdate, UsuarioResponse,
    LoginRequest, LoginResponse,
)

router = APIRouter(prefix="/usuarios", tags=["Usuarios"])


def _hash_password(contrasena: str) -> str:
    """Convierte la contraseña en texto plano a un hash
    Es la misma logica que en clientes.py, asi que aqui tambien queda
    documentado como función pura reutilizable."""
    return hashlib.sha256(contrasena.encode("utf-8")).hexdigest()


@router.get("/", response_model=list[UsuarioResponse])
def listar_usuarios():
    """GET /usuarios/ -> Lista todos los administradores, pero no sus contraseñas."""
    return usuarios_repo.leer_todos()


@router.get("/{usuario_id}", response_model=UsuarioResponse)
def obtener_usuario(usuario_id: int):
    """GET /usuarios/{id} -> Devuelve un usuario especifico."""
    usuario = usuarios_repo.obtener_por_id(usuario_id)
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return usuario


@router.post("/", response_model=UsuarioResponse, status_code=201)
def crear_usuario(usuario: UsuarioCreate):
    """POST /usuarios/ -> Registra un administrador nuevo.
    Antes de guardar, reemplaza la contraseña en texto plano
    por su version hasheada."""
    datos = usuario.model_dump()
    datos["contrasena"] = _hash_password(datos["contrasena"])
    return usuarios_repo.agregar(datos)


@router.put("/{usuario_id}", response_model=UsuarioResponse)
def actualizar_usuario(usuario_id: int, usuario: UsuarioUpdate):
    """PUT /usuarios/{id} -> Actualiza solo los campos enviados
    si viene una contraseña nueva tambien se hashea antes de guardar."""
    cambios = usuario.model_dump(exclude_unset=True)
    if "contrasena" in cambios:
        cambios["contrasena"] = _hash_password(cambios["contrasena"])

    actualizado = usuarios_repo.actualizar(usuario_id, cambios)
    if not actualizado:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return actualizado


@router.delete("/{usuario_id}", status_code=204)
def eliminar_usuario(usuario_id: int):
    """DELETE /usuarios/{id} -> Elimina un usuario."""
    if not usuarios_repo.eliminar(usuario_id):
        raise HTTPException(status_code=404, detail="Usuario no encontrado")


@router.post("/login", response_model=LoginResponse)
def login(credenciales: LoginRequest):
    """POST /usuarios/login -> Login simple del administrador,
    busca por correo, hashea la contraseña recibida y la compara
    contra la guardada u si coincide, devuelve los datos del usuario
    (el portal en PHP guardara el id para saber quien esta logueado)."""
    todos = usuarios_repo.leer_todos()
    usuario = next((u for u in todos if u["correo"] == credenciales.correo), None)

    if not usuario or usuario["contrasena"] != _hash_password(credenciales.contrasena):
        raise HTTPException(status_code=401, detail="Correo o contraseña incorrectos")

    return usuario