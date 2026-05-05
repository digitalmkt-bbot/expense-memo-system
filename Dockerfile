# =====================================================================
# Expense Memo Management System
# PHP 8.2 + Apache + auto-installer
# Optimized for Railway / Render / Fly.io / any Docker host
# =====================================================================
FROM php:8.2-apache

# ---- System packages & PHP extensions ----
RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev \
        libzip-dev \
        zip \
        unzip \
        default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring \
    && rm -rf /var/lib/apt/lists/*

# ---- Apache configuration ----
RUN a2enmod rewrite headers expires deflate

# Set Apache to listen on $PORT (Railway-friendly)
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

# Use index.flat.php as entrypoint (everything in DocumentRoot)
RUN sed -i 's|/var/www/html|/var/www/html|g' /etc/apache2/sites-available/000-default.conf
COPY deploy/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# ---- PHP configuration ----
COPY deploy/php.ini /usr/local/etc/php/conf.d/zz-app.ini

# ---- Working directory ----
WORKDIR /var/www/html

# ---- Copy application code ----
COPY . /var/www/html/

# ---- Use the flat index.php for Docker deployment ----
RUN cp /var/www/html/deploy/index.flat.php /var/www/html/index.php \
    && cp /var/www/html/deploy/htaccess.root /var/www/html/.htaccess \
    && for d in app config database deploy storage; do \
         echo 'Require all denied' > /var/www/html/$d/.htaccess; \
       done \
    # Allow public uploads to be served via storage/uploads
    && rm -f /var/www/html/storage/.htaccess \
    && echo 'Options -Indexes' > /var/www/html/storage/.htaccess

# ---- Permissions ----
RUN mkdir -p /var/www/html/storage/uploads /var/www/html/storage/backups \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage \
    && chmod +x /var/www/html/deploy/*.sh

# ---- Default ENV (override in Railway dashboard) ----
ENV PORT=8080 \
    APP_ENV=production \
    APP_DEBUG=false \
    AUTO_INSTALL=true

EXPOSE 8080

# ---- Entrypoint: auto-install then start Apache ----
ENTRYPOINT ["/bin/bash", "/var/www/html/deploy/entrypoint.sh"]
