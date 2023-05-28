FROM php:8.2-fpm-alpine

COPY ./entrypoint.sh /usr/bin/genesis-entrypoint

RUN chmod 0755 /usr/bin/genesis-entrypoint \
    && apk --no-cache update \
    && apk add --no-cache --virtual git \
    && chmod -R a+rwX /usr/local/etc/php/conf.d

#RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
#RUN apk add --update linux-headers
#RUN pecl install xdebug-3.2.1
#RUN docker-php-ext-enable xdebug
#RUN apk del -f .build-deps

ENV XDEBUG ${XDEBUG}

CMD ["genesis-entrypoint", "php-fpm"]
