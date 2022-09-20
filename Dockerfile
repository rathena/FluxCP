FROM trafex/php-nginx:2.6.0

USER root
RUN apk add --no-cache php81-pdo php81-pdo_mysql php81-gd php81-tidy

COPY --chown=nobody . /var/www/html/fluxcp

USER nobody
