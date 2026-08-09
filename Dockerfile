# Stage 1: Composer dependencies
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader

# Stage 2: Frontend assets
FROM node:20-alpine AS node
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
COPY --from=composer /app/vendor ./vendor
RUN npm run build

# Stage 3: Runtime
FROM serversideup/php:8.4-fpm-nginx
WORKDIR /var/www/html

ENV AUTORUN_ENABLED=true \
    HEALTHCHECK_PATH=/up \
    PORT=8080 \
    NGINX_HTTP_PORT=8080

COPY composer.json composer.lock ./
COPY --from=composer --chown=www-data:www-data /app/vendor ./vendor
COPY --from=node --chown=www-data:www-data /app/public/build ./public/build
COPY --chown=www-data:www-data . .

COPY docker/entrypoint.d/20-ensure-storage.sh /etc/entrypoint.d/20-ensure-storage.sh
RUN mkdir -p storage/app/public storage/app/private storage/app/verification_docs storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /etc/entrypoint.d/20-ensure-storage.sh \
    && COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data /var/www/html

EXPOSE 8080
