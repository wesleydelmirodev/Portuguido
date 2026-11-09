# Flask
from flask import Flask
from flask_wtf.csrf import CSRFProtect
from flask_talisman import Talisman

# Módulos
from modules.Dotenv import Dotenv
from modules.Database import Database
from database import db

# Outros
from datetime import timedelta
from extensions import limiter

database = Database()
dotenv = Dotenv()

# Inicializando a aplicação Flask
app = Flask(__name__, template_folder="../public", static_folder="../public/static")
app.config["SQLALCHEMY_DATABASE_URI"] = database.generate_uri()

db.init_app(app)

# Variáveis de configuração do ambiente
Debug = dotenv.get_bool('DEBUG', False)
Https = dotenv.get_bool('HTTPS', True)
SecretKey = dotenv.get('SECRET_KEY')

if not SecretKey:
    raise RuntimeError("SECRET_KEY não configurada.")

# Configurações da aplicação
app.config.update(
    SECRET_KEY = SecretKey,
    DEBUG = Debug,
    TESTING = False,
    SESSION_COOKIE_HTTPONLY = True,
    SESSION_COOKIE_SECURE = Https,
    SESSION_COOKIE_SAMESITE = "Lax",
    PERMANENT_SESSION_LIFETIME = timedelta(minutes=30),
    MAX_CONTENT_LENGTH = 5 * 1024 * 1024
)

csrf = CSRFProtect(app) # Aplicando proteção CSRF a toda a aplicação
limiter.init_app(app)   # Aplicando o rate limiting a toda a aplicação

# Configurações de segurança com Flask-Talisman
talisman = Talisman(
    app,
    force_https = Https,
    force_https_permanent = True,
    strict_transport_security = Https,
    strict_transport_security_max_age = 31536000,
    strict_transport_security_include_subdomains = True,
    strict_transport_security_preload = False,
    session_cookie_secure = Https,
    session_cookie_http_only = True,
    frame_options = "DENY",
    referrer_policy = "strict-origin-when-cross-origin",
    content_security_policy = {
        "default-src": "'self'",
        "script-src": "'self'",
        "style-src": ["'self'", "https://fonts.googleapis.com"],
        "img-src": ["'self'", "data:"],
        "font-src": ["'self'", "https://fonts.gstatic.com"],
        "connect-src": "'self'",
        "object-src": "'none'",
        "base-uri": "'self'",
        "form-action": "'self'",
        "frame-ancestors": "'none'",
    }
)

# Importando as rotas da aplicação
from routes._autoloader import register_routes
register_routes(app)

if __name__ == '__main__':
    with app.app_context():
        db.create_all()

    app.run(host='127.0.0.1', port=5000, debug=Debug)