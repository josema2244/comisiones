FROM node:22 AS angular
WORKDIR /app
COPY frontend/package*.json ./
RUN npm ci
COPY frontend/ ./
RUN npm run build

FROM php:8.2-apache-bookworm

RUN a2enmod rewrite \
 && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

ENV PORT=80
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

COPY --from=angular /app/dist/frontend/browser/ /var/www/html/
COPY backend/ /var/www/html/backend/

RUN printf 'RewriteEngine On\nRewriteRule ^api/ backend/index.php [L]\n' > /var/www/html/.htaccess

ENV DB_FILE=/var/www/data/comisiones.db
RUN php /var/www/html/backend/crear_sqlite.php /var/www/data/comisiones.db \
 && chown -R www-data:www-data /var/www/data

EXPOSE 80