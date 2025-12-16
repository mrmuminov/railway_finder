FROM php:8.2-cli-alpine

COPY . /app

ENTRYPOINT ["php", "/app/index.php"]