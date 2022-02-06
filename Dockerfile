FROM webdevops/php-nginx:7.4-alpine

WORKDIR /app

ENV WEB_DOCUMENT_ROOT /app/public

ADD ./artisan \
    ./composer.json \
    ./composer.lock ./

RUN composer install --prefer-dist --no-ansi --no-interaction --no-progress --no-scripts

ADD ./server.php ./

ADD ./app ./app
ADD ./bootstrap ./bootstrap
ADD ./config ./config
ADD ./database ./database
ADD ./public ./public
ADD ./resources ./resources
ADD ./routes ./routes
ADD ./storage ./storage

RUN composer dump-autoload; \
    chmod -R 777 /app/storage /app/bootstrap
