#!/usr/bin/env sh
# Sourced by the serversideup/php entrypoint before AUTORUN (50-laravel-automations.sh).
# Ensures a freshly mounted Railway volume at /var/www/html/storage has the
# required directory structure and is writable by the www-data process user.

rm -rf /var/www/html/public/storage

mkdir -p \
    /var/www/html/storage/app/public \
    /var/www/html/storage/app/private \
    /var/www/html/storage/app/verification_docs \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache
