FROM php:8.1-cli-alpine

# Системные зависимости
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    $PHPIZE_DEPS

# PHP-расширения: pdo_mysql для работы с БД, intl для i18n, zip для composer
RUN docker-php-ext-install -j$(nproc) pdo_mysql zip intl bcmath

# Composer ставим напрямую с getcomposer.org - не зависим от Docker Hub
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer --version=2.6.6 \
 && composer --version

WORKDIR /app

# composer.json/lock - кешируем слой зависимостей
COPY composer.json composer.lock* /app/
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction || true

COPY . /app

RUN composer dump-autoload --optimize --no-dev --no-interaction || true \
    && chmod +x /app/yii 2>/dev/null || true

# Защита от Windows CRLF в shell-скриптах. На Windows git с дефолтным
# core.autocrlf=true переводит LF в CRLF при клоне, и Linux в контейнере
# не может выполнить #!/bin/sh\r ("not found" - это про интерпретатор).
# sed убирает \r из всех .sh файлов и делает их исполняемыми.
RUN find /app -type f -name "*.sh" -exec sed -i 's/\r$//' {} \; \
 && find /app -type f -name "*.sh" -exec chmod +x {} \; \
 && sed -i 's/\r$//' /app/yii \
 && chmod +x /app/yii

EXPOSE 8080

# Встроенный PHP веб-сервер Yii раздаёт из /app/web
# entrypoint.sh делает миграции и seed перед запуском
CMD ["/bin/sh", "-c", "/app/entrypoint.sh"]
