class Usuario:
    def __init__(self, id: int, nombre: str, correo: str):
        self.id = id
        self.nombre = nombre
        self.__correo = correo   #privado

    # Getter, leer el correo
    @property
    def correo(self):
        return self.__correo

    # Setter, valida antes de modificar el correo
    @correo.setter
    def correo(self, nuevo_correo: str):
        if "@" not in nuevo_correo:
            raise ValueError("Correo invalido")
        self.__correo = nuevo_correo

    def saludar(self) -> str:
        return f"Hola, soy {self.nombre}"