import importlib
import os

def register_routes(app):
    folder = os.path.dirname(__file__)
    files = sorted(
        os.path.join(folder, name)
        for name in os.listdir(folder)
        if name.endswith(".py")
        and name not in {"__init__.py", "_autoloader.py"}
    )

    for arquivo in files:
        module_name = os.path.splitext(os.path.basename(arquivo))[0]
        module = importlib.import_module(f"routes.{module_name}")
        blueprint = getattr(module, "bp", None)
        if blueprint is None:
            raise RuntimeError(f"Route module {module.__name__} does not define a blueprint.")

        app.register_blueprint(blueprint)