from modules.Dotenv import Dotenv
dotenv = Dotenv()

class Database:
    def __init__(self):
        pass    

    def generate_uri(self):
        database_name = dotenv.get("DATABASE_NAME")
        database_password = dotenv.get("DATABASE_PASSWORD")
        database_host = dotenv.get("DATABASE_HOST")
        database_port = dotenv.get("DATABASE_PORT")

        return f"mysql://root:{database_password}@{database_host}:{database_port}/{database_name}"

    def connect(self):
        pass

    def query(self):
        pass

    def disconnect(self):
        pass