from flask import Blueprint
from flask_wtf.csrf import generate_csrf

bp = Blueprint("security", __name__)

@bp.route('/csrf-token', methods=['GET'])
def csrf_token():
    return {'csrf_token': generate_csrf()}