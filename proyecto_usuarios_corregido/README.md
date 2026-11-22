# Proyecto - Sistema de Gestión y Tienda (Paradigmas)

Este proyecto corresponde a una aplicación web dividida en un panel de administración backend desarrollado en **PHP**, una API de servicios desarrollada en **FastAPI (Python)**, y una vista de cliente (tienda) desarrollada en **JavaScript estático**.

---

## Estructura del Directorio

```text
proyecto_usuarios/        # Panel con los archivos Python y FastAPI
├── app/                  # Panel de administración (PHP), vistas y tienda-js
│   ├── assets/           # Archivos CSS e imágenes del panel
│   ├── includes/         # Componentes reutilizables (header, footer, bootstrap)
│   ├── src/              # Lógica y scripts de soporte en PHP
│   └── tienda-js/        # Interfaz de la tienda de cliente (HTML/JS)
├── proyecto_usuarios/    # Panel con los archivos Python y FastAPI
│    ├── app/             # Carpeta donde se contiene los Archivos Python                  
│    │    ├── models/     # Modelos de datos y esquemas de la base de datos
│    │    ├── routers/    # Endpoints y rutas de la API de FastAPI 
│    │    └── schemas/    # Definición de estructuras de validación de datos (Pydantic)
│    ├── venv/            # Entorno virtual de Python
│    └── data/            # Almacenamiento de datos
```

## Requisitos Previos

* **PHP** (Versión 8.0 o superior recomendada).
* **Python** (Versión 3.10 o superior) con su entorno virtual configurado.
* **Cualquier navegador web moderno** (Google Chrome, Edge, Firefox, etc.).

---

## Instrucciones de Ejecución

Para garantizar que las rutas del servidor local y los enlaces de navegación entre la administración y la tienda funcionen sin errores (evitando errores 404), asegúrate de ubicarte en la carpeta raíz del proyecto (`proyecto_usuarios`) en tu terminal antes de ejecutar los servicios.

### 1. Iniciar el Servidor de Python (FastAPI)
Abre una terminal, activa tu entorno virtual e inicia el servidor Uvicorn apuntando al módulo de la aplicación:

```powershell
# En Windows (PowerShell)
.\venv\Scripts\activate
python -m uvicorn app.main:app --reload --port 8000
```

### 2. Iniciar el Servidor PHP
Abre una **segunda terminal**, ubícate en la raíz del proyecto y ejecuta el servidor integrado de PHP configurando `app` como la carpeta raíz del servidor (`-t app`):

```powershell
php -S localhost:8001 -t app
```

## Acceso a las Interfaces

Una vez levantados ambos servidores locales, puedes ingresar a las diferentes secciones desde tu navegador:

* **Panel de Administración (PHP):** [http://localhost:8001/login.php](http://localhost:8001/login.php) (o directamente en `http://localhost:8001/index.php`).
* **Vista de Cliente / Tienda (JS):** [http://localhost:8001/tienda-js/index.html](http://localhost:8001/tienda-js/index.html) (también accesible desde el enlace "Ver Tienda (Cliente)" en el menú superior del panel de administración).
* **API Backend (FastAPI / Swagger Docs):** [http://localhost:8000/docs](http://localhost:8000/docs)