import json
import os
from typing import List, Dict, Any


class JSONRepository:
    """
    Repositorio para persistencia en archivos JSON.

    aqui encapsulamos la logica para leer en el disco

    """

    def __init__(self, archivo: str):
        # Guarda la ruta completa del archivo JSON dentro de la carpeta "data/"
        self._ruta = os.path.join("data", archivo)
        self._asegurar_archivo()

    def _asegurar_archivo(self):
        """este metodose ejecuta al crear el repositorio: si la carpeta 'data/' o el
        archivo JSON no existen aun, se crean vacios ([])
        para evitar errores de 'archivo no encontrado' al iniciar."""
        os.makedirs(os.path.dirname(self._ruta), exist_ok=True)
        if not os.path.exists(self._ruta):
            with open(self._ruta, "w", encoding="utf-8") as f:
                json.dump([], f)

    def leer_todos(self) -> List[Dict[str, Any]]:
        """Abre el JSON y devuelve todos los registros como
        una lista de diccionarios, se usa como base para las demas
        operaciones (buscar, actualizar y eliminar)."""
        with open(self._ruta, "r", encoding="utf-8") as f:
            return json.load(f)

    def guardar_todos(self, datos: List[Dict[str, Any]]) -> None:
        """Sobrescribe el archivo JSON completo con la lista 'datos'.
        indent=4 -> lo deja legible para si se abre el archivo.
        ensure_ascii=False -> permite guardar tildes/ñ correctamente."""
        with open(self._ruta, "w", encoding="utf-8") as f:
            json.dump(datos, f, indent=4, ensure_ascii=False)

    def obtener_por_id(self, id: int) -> Dict[str, Any] | None:
        """Busca un registro por su id.
        Recorre la lista y devuelve el primero que coincida,
        o None si no existe ninguno con ese id."""
        return next((d for d in self.leer_todos() if d["id"] == id), None)

    def agregar(self, item: Dict[str, Any]) -> Dict[str, Any]:
        """Crea un nuevo registro:
        1. Lee todo lo que existe actualmente.
        2. Le asigna un id nuevo automatico (_siguiente_id).
        3. Lo agrega a la lista y guarda el archivo completo.
        4. Devuelve el item ya con su id, para que el router lo
           regrese como respuesta al cliente."""
        datos = self.leer_todos()
        item["id"] = self._siguiente_id(datos)
        datos.append(item)
        self.guardar_todos(datos)
        return item

    def actualizar(self, id: int, cambios: Dict[str, Any]) -> Dict[str, Any] | None:
        """Busca el registro por id y actualiza solo los campos que
        vienen en 'cambios' (los demás quedan igual, gracias a
        exclude_unset=True que se usa en el router).
        Devuelve el registro actualizado, o None si no se encontro."""
        datos = self.leer_todos()
        for d in datos:
            if d["id"] == id:
                d.update(cambios)
                self.guardar_todos(datos)
                return d
        return None

    def eliminar(self, id: int) -> bool:
        """Elimina un registro filtrando la lista (se queda con todos
        los que no tengan ese id). Si el tamaño de la lista no cambio,
        significa que no existia ese id ->retorna False."""
        datos = self.leer_todos()
        nuevos = list(filter(lambda d: d["id"] != id, datos))
        if len(nuevos) == len(datos):
            return False
        self.guardar_todos(nuevos)
        return True

    def _siguiente_id(self, datos: List[Dict[str, Any]]) -> int:
        """Calcula el proximo id autoincremental:
        toma el id más alto existente y le suma 1.
        Si la lista está vacia (default=0), el primer id será 1."""
        return max((d["id"] for d in datos), default=0) + 1



categorias_repo = JSONRepository("categorias.json")
proveedores_repo = JSONRepository("proveedores.json")
productos_repo = JSONRepository("productos.json")
usuarios_repo = JSONRepository("usuarios.json")
clientes_repo = JSONRepository("clientes.json")
compras_repo = JSONRepository("compras.json")
facturas_repo = JSONRepository("facturas.json")
carritos_repo = JSONRepository("carritos.json")
compras_repo = JSONRepository("compras.json")
facturas_repo = JSONRepository("facturas.json")