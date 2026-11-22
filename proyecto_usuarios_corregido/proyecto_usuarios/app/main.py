#PYTHON BACKEND: ORLIN MOISES MUNGUIA SALGADO - 20231003372
#JAVASCRIPT: Enzo Diddier Flores López - 20201900058
#PHP: Daniel Enrique Ortiz González - 20191003463

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from app.routers import categorias, proveedores, productos, clientes, carrito, compras, facturas, usuarios

app = FastAPI(title="API Tienda de Artículos Deportivos")

#Configuracion de CORS
# "*" permite que cualquier origen consuma la API.
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/")
def inicio():
    return {"mensaje": "API funcionando correctamente"}


app.include_router(categorias.router)
app.include_router(proveedores.router)
app.include_router(productos.router)
app.include_router(clientes.router)
app.include_router(carrito.router)
app.include_router(compras.router)
app.include_router(facturas.router)
app.include_router(usuarios.router)