FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install --legacy-peer-deps
COPY . .
RUN npm run prod

FROM dunglas/frankenphp:php8.3

RUN install-php-extensions \
    gd \
    intl \
    zip \
    pdo_mysql \
    opcache \
    exif \
    bcmath

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
COPY auth.json /root/.composer/auth.json
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
COPY --from=assets /app/public/js public/js
COPY --from=assets /app/public/mix-manifest.json public/mix-manifest.json

RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && php artisan view:clear \
    && php artisan storage:link || true

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

COPY Caddyfile /etc/caddy/Caddyfile

ENV SERVER_NAME=":80"

EXPOSE 80
