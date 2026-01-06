import requests
import urllib.parse


class Address:
    def __init__(self, nome, cep, logradouro, numero, bairro, complemento, cidade, uf):
        if nome in ("SEM-VELORIO", "SEM LABORATORIO"):
            self.nome = None
            self.cep = None
            self.logradouro = None
            self.numero = None
            self.bairro = None
            self.complemento = None
            self.cidade = None
            self.uf = None
        else:
            self.nome = nome
            # Convert cep to string and pad with zeros
            self.cep = str(cep).replace(' ', '').zfill(8) if cep else None
            self.logradouro = logradouro
            self.numero = numero
            self.bairro = bairro
            self.complemento = complemento
            self.cidade = cidade
            self.uf = uf

    def to_string(self):
        if self.nome is None:
            return None
        
        formatted_cep = str(self.cep).zfill(8)
        formatted_cep = f"{formatted_cep[:5]}-{formatted_cep[5:]}"
        
        address = f"{str(self.logradouro).upper()} - Nº {str(self.numero).strip()}"
        if self.complemento:
            address += f" - {str(self.complemento).upper()}"
        address += f" - {str(self.bairro).upper()} - {formatted_cep}"
        
        return address

    def geocoding(self):
        formatted_cep = f"{str(self.cep)[:5]}-{str(self.cep)[5:]}"
        numero = str(self.numero).replace(' ', '')
        query = f"{numero} {self.logradouro}, {self.bairro}, {self.cidade}, {self.uf} {formatted_cep}, Brazil"
        q = urllib.parse.quote(query)
        url = f"https://geocode.search.hereapi.com/v1/geocode?q={q}&apiKey=aJOrzbvwdAB3Rc2SbC8ilup4wxJgtf3voDBuln2y598"
        
        try:
            response = requests.get(url, timeout=10)
            response.raise_for_status()
            data = response.json()
            return data["items"][0]["position"]
        except Exception as e:
            raise Exception(f"Erro ao geocodificar endereço '{query}': {e}")
