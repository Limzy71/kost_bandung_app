# Build stage: frontend assets
FROM node:20-alpine AS node
WORKDIR /app

COPY package.json ./
RUN npm install

COPY . .
RUN npm run build

# Runtime stage
FROM serversideup/php:8.4-fpm-nginx
WORKDIR /var/www/html

ENV AUTORUN_ENABLED=true \
    HEALTHCHECK_PATH=/up \
    PORT=8080 \
    NGINX_HTTP_PORT=8080

# Composer dependencies (no-scripts: artisan runs later after full app copy)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# Frontend build + application code
COPY --from=node --chown=www-data:www-data /app/public/build ./public/build
COPY --chown=www-data:www-data . .

# Laravel package discovery + storage dirs for fresh Railway volume
COPY docker/entrypoint.d/20-ensure-storage.sh /etc/entrypoint.d/20-ensure-storage.sh
RUN php artisan package:discover --ansi \
    && chmod +x /etc/entrypoint.d/20-ensure-storage.sh \
    && chown -R www-data:www-data /var/www/html

EXPOSE 8080
