from flask import Blueprint, render_template
bp = Blueprint("views", __name__)

@bp.route('/')
def index():
    return render_template('index.html')

@bp.route('/mural')
def mural():
    return render_template('mural.html')    

@bp.route('/anuario')
def anuario():
    return render_template('anuario.html')

@bp.route('/auth')
def auth():
    return render_template('auth.html')