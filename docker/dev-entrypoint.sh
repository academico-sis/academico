#!/bin/sh
# Prepares a fresh checkout so that `docker compose up` is the only step needed:
# creates .env, installs PHP dependencies, generates the app key and migrates the database.
set -e

cd /app

if [ ! -f .env ]; then
    echo "[dev-entrypoint] Creating .env from .env.example"
    cp .env.example .env
    # Keep the file editable from the host when the container runs as root
    chown --reference=.env.example .env
fi

echo "[dev-entrypoint] Installing PHP dependencies"
composer install --no-interaction --prefer-dist

if ! grep -q '^APP_KEY=..*' .env; then
    echo "[dev-entrypoint] Generating application key"
    php artisan key:generate --ansi
fi

if [ ! -e public/storage ]; then
    php artisan storage:link
fi

if [ "${AUTO_MIGRATE:-true}" = "true" ]; then
    echo "[dev-entrypoint] Running database migrations"
    php artisan migrate --force
fi

exec docker-php-entrypoint "$@"
