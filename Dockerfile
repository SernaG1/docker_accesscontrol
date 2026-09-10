FROM composer:2 AS vendor

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/tmp/composer-cache \
	COMPOSER_CACHE_DIR=/tmp/composer-cache composer install \
	--no-interaction \
	--prefer-dist \
	--no-progress \
	--optimize-autoloader \
	--no-scripts \
	--ignore-platform-req=php \
	--no-dev

FROM node:22-alpine AS frontend

WORKDIR /var/www/html

COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci --prefer-offline

COPY resources ./resources
COPY public ./public
COPY vite.config.js .
RUN npm run build

FROM php:8.3-fpm-bookworm

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		libicu-dev \
		libonig-dev \
		libzip-dev \
	&& docker-php-ext-install -j"$(nproc)" \
		bcmath \
		intl \
		mbstring \
		opcache \
		pdo_mysql \
		zip \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=frontend /var/www/html/public/build ./public/build
COPY . .

RUN php artisan package:discover --ansi

COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

RUN chown -R www-data:www-data storage bootstrap/cache \
	&& chmod -R ug+rwx storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
