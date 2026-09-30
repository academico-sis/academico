# Stage 1: Build frontend assets
FROM node:22-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY app ./app
RUN npm run build

# Stage 2: Install PHP dependencies
FROM composer:latest AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize

# Stage 3: PHP runtime shared by the development and production images
FROM dunglas/frankenphp:1-php8.5 AS base

WORKDIR /app

RUN apt-get update && apt-get install -y \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    xml \
    opcache \
    intl \
    zip

# Stage 4: Development image (used by docker-compose.yml)
# The source code is bind-mounted at runtime; dependencies are installed by the entrypoint.
FROM base AS dev

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY docker/dev-entrypoint.sh /usr/local/bin/dev-entrypoint
RUN chmod +x /usr/local/bin/dev-entrypoint

EXPOSE 80

ENTRYPOINT ["dev-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

# Stage 5: Production image (default target)
FROM base AS production

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN php artisan package:discover --ansi \
    && php artisan view:clear \
    && php artisan storage:link || true

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 80 443

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
