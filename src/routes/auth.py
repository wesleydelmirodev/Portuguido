from flask import Blueprint, request, redirect, url_for
from extensions import limiter

from utils.validators import *
from modules.Dotenv import Dotenv
from werkzeug.security import generate_password_hash, check_password_hash

from google.oauth2 import id_token
from google.auth.transport import requests

from sqlalchemy import select
from sqlalchemy.exc import IntegrityError
from models import User, db

dotenv = Dotenv()
bp = Blueprint("auth", __name__)

institution_email_domain = dotenv.get("INSTITUTION_EMAIL_DOMAIN")
google_client_id = dotenv.get("GOOGLE_CLIENT_ID")

@bp.route('/login', methods=['POST'])
@limiter.limit("5 per minute", methods=["POST"])
def login():
    valid, response = validate_fields(['email', 'password'])
    if not valid:
        return response, 400

    # Resgatando os dados do formulário
    email = request.form['email']
    password = request.form['password']

    # Validando o email
    valid, response = validate_email(email, max_length=255, domain=institution_email_domain)
    if not valid:
        return response, 400

    # Verificando se o email está cadastrado
    query = select(User).where(User.email == email)
    user = db.session.scalar(query)

    if not user or not check_password_hash(user.password, password):
        return {
            "success": False,
            "status": 401,
            "message": "Email ou senha fornecidos estão incorretos."
        }, 401

    return redirect(url_for('views.index'))

@bp.route('/login/google', methods=['POST'])
@limiter.limit("5 per minute", methods=["POST"])
def google_login():
    valid, response = validate_fields(['token'])
    if not valid:
        return response, 400

    token = request.form.get('token')

    try:
        payload = id_token.verify_oauth2_token(token, requests.Request(), google_client_id)

    except ValueError:
        return {
            "success": False,
            "status": 401,
            "message": "O token enviado é inválido."
        }, 401

    google_id = payload['sub']
    email = payload['email']
    username = payload['name']
    email_verified = payload['email_verified']

    if not email_verified:
        return {
            "success": False,
            "status": 401,
            "message": "O e-mail não foi verificado pelo Google."
        }, 401

    # Verificando se o usuário já existe
    query = select(User).where(User.email == email)
    user = db.session.scalar(query)

    if user:
        if user.google_id == google_id:
            return redirect(url_for('views.index'))

        if user.google_id is None:
            return {
                "success": False,
                "status": 409,
                "message": "Já existe uma conta com este email. Entre nela pelo método original para vincular sua conta Google."
            }, 409

        return {
            "success": False,
            "status": 409,
            "message": "Esta conta já possui outro Google vinculado."
        }, 409

    # Criando a instância de um novo usuário
    new_user = User(username=username, email=email, google_id=google_id)

    try:
        # Tentando adicionar o novo usuário ao banco de dados
        db.session.add(new_user)
        db.session.commit()

    except IntegrityError as error:
        db.session.rollback()
        print(f"IntegrityError: {error.orig}")

        return {
            "success": False,
            "status": 409,
            "message": "Erro ao criar o usuário."
        }, 409

    return redirect(url_for('views.index'))

@bp.route('/signup', methods=['POST'])
@limiter.limit("3 per minute", methods=["POST"])
def signup():
    # Verificando se todos os campos obrigatórios estão presentes
    valid, response = validate_fields(['username', 'email', 'password'])
    if not valid:
        return response, 400

    # Resgatando os dados do formulário
    username = request.form['username']
    email = request.form['email']
    password = request.form['password']

    valid, response = validate_length('username', username, min_length=4, max_length=100)
    if not valid:
        return response, 400

    valid, response = validate_length('password', password, min_length=8, max_length=100)
    if not valid:
        return response, 400

    # Validando o email
    valid, response = validate_email(email, max_length=255, domain=institution_email_domain)
    if not valid:
        return response, 400

    # Verificando se o email já está em uso
    query = select(User).where(User.email == email)
    exists = db.session.scalar(query)

    if exists:
        return {
            "success": False,
            "status": 409,
            "message": f"O email '{email}' já está em uso."
        }, 409

    # Hashing da senha
    password_hash = generate_password_hash(password)

    # Criando a instância de um novo usuário
    new_user = User(username=username, email=email, password=password_hash)

    try:
        # Tentando adicionar o novo usuário ao banco de dados
        db.session.add(new_user)
        db.session.commit()

    except IntegrityError as error:
        db.session.rollback()
        print(f"IntegrityError: {error.orig}")

        return {
            "success": False,
            "status": 409,
            "message": "Erro ao criar o usuário."
        }, 409
    
    return redirect(url_for('views.index'))

@bp.route('/logout', methods=['POST'])
@limiter.limit("10 per minute", methods=["POST"])
def logout():
    return 'Logout'