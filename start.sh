#!/bin/bash
set -e

# Configure port for Render (Render sets $PORT dynamically, defaults to 80 if unset)
APACHE_PORT="${PORT:-80}"
echo ">> Configuring Apache to listen on port ${APACHE_PORT}..."
sed -i "s/Listen 80/Listen ${APACHE_PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${APACHE_PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Start Python Flask Backend in the background on internal port 5000
export BACKEND_PORT=5000
echo ">> Starting Python Flask Backend on 127.0.0.1:5000..."
python3 backend/app.py &

# Wait up to 10 seconds for Python Flask to be ready
echo ">> Waiting for Python Flask backend to start..."
for i in {1..20}; do
    if curl -s http://127.0.0.1:5000/api/health > /dev/null 2>&1; then
        echo ">> Python Flask backend is ready and healthy!"
        break
    fi
    sleep 0.5
done

# Start Apache in the foreground to keep the container running
echo ">> Starting Apache Web Server on port ${APACHE_PORT}..."
exec apache2-foreground
