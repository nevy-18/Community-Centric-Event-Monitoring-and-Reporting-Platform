import os
import sys
from pathlib import Path

# Add project root to sys.path so backend module can be imported anywhere
PROJECT_ROOT = Path(__file__).resolve().parent.parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.insert(0, str(PROJECT_ROOT))

from flask import Flask, jsonify
from flask_cors import CORS
from datetime import datetime

from backend.config import Config
from backend.routes.auth_routes import auth_bp
from backend.routes.chapters_routes import chapters_bp
from backend.routes.accounts_routes import accounts_bp
from backend.routes.proposals_routes import proposals_bp
from backend.routes.reports_routes import reports_bp
from backend.routes.poster_requests_routes import poster_requests_bp
from backend.routes.notifications_routes import notifications_bp
from backend.routes.dashboard_routes import dashboard_bp
from backend.routes.system_routes import system_bp

def create_app(config_class=Config):
    app = Flask(__name__)
    app.config.from_object(config_class)

    # Enable Cross-Origin Resource Sharing (CORS) with credentials
    CORS(app, supports_credentials=True, resources={r"/api/*": {"origins": "*"}})

    # Register blueprints
    app.register_blueprint(auth_bp)
    app.register_blueprint(chapters_bp)
    app.register_blueprint(accounts_bp)
    app.register_blueprint(proposals_bp)
    app.register_blueprint(reports_bp)
    app.register_blueprint(poster_requests_bp)
    app.register_blueprint(notifications_bp)
    app.register_blueprint(dashboard_bp)
    app.register_blueprint(system_bp)

    @app.route('/api/health', methods=['GET'])
    def health_check():
        return jsonify({
            'status': 'healthy',
            'service': 'Youth on the Rock Python Backend API',
            'version': '1.0.0',
            'database': Config.DB_NAME,
            'timestamp': datetime.now().isoformat()
        })

    @app.errorhandler(404)
    def not_found(e):
        return jsonify({
            'status': 'error',
            'message': 'Requested API endpoint not found.'
        }), 404

    @app.errorhandler(500)
    def internal_error(e):
        return jsonify({
            'status': 'error',
            'message': 'An internal server error occurred in Python backend.'
        }), 500

    return app

app = create_app()

if __name__ == '__main__':
    port = Config.PORT
    print("============================================================")
    print(f">> YTR Python Backend running on http://127.0.0.1:{port}")
    print(f">> Connected to Database: {Config.DB_NAME} on {Config.DB_HOST}:{Config.DB_PORT}")
    print("============================================================")
    app.run(host='0.0.0.0', port=port, debug=True)
