import os
from dotenv import load_dotenv, find_dotenv

# Carregando o arquivo .env
load_dotenv(find_dotenv())

# PARÂMETROS DO .ENV
parameters = [
    'BASE_URL'
]

class Dotenv:
    def __init__(self):
        self.parameters = parameters

    def get_all(self):
        return {param: os.getenv(param) for param in self.parameters}

    def get(self, key, default=None):
        return os.getenv(key, default)

    def get_bool(self, key, default=False):
        value = self.get(key)

        if value is None:
            return default

        return value.lower() == 'true'