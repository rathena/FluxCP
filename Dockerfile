FROM davidsiaw/alpine-nginx-php

ADD . /var/www

ENV REMOTE_ADDR=rathena \
    BASE_PATH="" \
    RO_SERVER_TITLE=Lunacy \
    RO_SERVER_NAME=Lunacy \
    DATABASE_HOST=rathena \
    DATABASE_USER=ragnarok \
    DATABASE_PASS=ragnarok \
    DATABASE_NAME=ragnarok \
    LOG_DATABASE_HOST=rathena \
    LOG_DATABASE_USER=ragnarok \
    LOG_DATABASE_PASS=ragnarok \
    LOG_DATABASE_NAME=ragnarok \
    LOGIN_SERVER_HOST=rathena \
    CHAR_SERVER_HOST=rathena \
    MAP_SERVER_HOST=rathena 

RUN apk update && apk add php5-pdo_mysql php5-gd php5-ctype 

CMD ["sh", "/var/www/run.sh"]