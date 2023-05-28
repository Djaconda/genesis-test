ARG PROJECT_NAME
FROM composer:2 AS composer
FROM php:${PROJECT_NAME}

COPY ./entrypoint.sh /usr/bin/genesis-entrypoint
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

ARG USER_ID
ARG GROUP_ID
ARG USER_NAME
ARG GROUP_NAME
ARG FULL_NAME
ARG EMAIL

RUN chmod 0755 /usr/bin/genesis-entrypoint \
    && apk add --no-cache git bash

CMD ["genesis-entrypoint"]
