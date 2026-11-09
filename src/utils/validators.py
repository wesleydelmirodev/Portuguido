import re
from flask import request

def validate_fields(required_fields):
    for field in required_fields:
        value = request.form.get(field, '')
        if not value.strip():
            return False, {
                "success": False,
                "status": 400,
                "message": f"O campo '{field}' é obrigatório."
            }

    return True, None

def validate_length(field_name, value, min_length=None, max_length=None):
    if min_length is not None and len(value) < min_length:
        return False, {
            "success": False,
            "status": 400,
            "message": f"O campo '{field_name}' deve ter pelo menos {min_length} caracteres."
        }

    if max_length is not None and len(value) > max_length:
        return False, {
            "success": False,
            "status": 400,
            "message": f"O campo '{field_name}' deve ter no máximo {max_length} caracteres."
        }

    return True, None

def validate_email(email, max_length=None, domain=None):
    pattern = r"^[\w.+-]+@[\w-]+\.[\w.-]+$"

    if max_length is not None and len(email) > max_length:
        return False, {
            "success": False,
            "status": 400,
            "message": f"O email deve ter no máximo {max_length} caracteres."
        }

    if not re.fullmatch(pattern, email):
        return False, {
            "success": False,
            "status": 400,
            "message": f"O email '{email}' não é válido."
        }

    if domain and email.split("@")[-1].lower() != domain.lower():
        return False, {
            "success": False,
            "status": 400,
            "message": f"O email deve pertencer ao domínio {domain}."
        }

    return True, None