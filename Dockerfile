FROM php:8.2-apache

# Install Python 3, pip, curl, and tools
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules required for rewrite and proxying API calls
RUN a2enmod rewrite proxy proxy_http headers

# Set working directory
WORKDIR /var/www/html

# Copy all project files into Apache document root
COPY . /var/www/html/

# Copy Apache VirtualHost configuration
COPY apache-render.conf /etc/apache2/sites-available/000-default.conf

# Install Python dependencies
RUN pip3 install --no-cache-dir --break-system-packages -r requirements.txt

# Ensure upload directories exist and are writable
RUN mkdir -p /var/www/html/assets/uploads/proposals \
             /var/www/html/assets/uploads/attendance \
             /var/www/html/assets/uploads/posters \
             /var/www/html/assets/uploads/signatures \
    && chmod -R 777 /var/www/html/assets/uploads

# Make start script executable
RUN chmod +x /var/www/html/start.sh

# Expose default HTTP port
EXPOSE 80

# Run entrypoint script to launch Python and Apache
CMD ["/var/www/html/start.sh"]
