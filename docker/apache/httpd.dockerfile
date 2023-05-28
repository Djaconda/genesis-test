FROM httpd:2.4-alpine

COPY ./etc/ /usr/local/apache2/conf/
COPY ./entrypoint.sh /usr/bin/genesis-entrypoint

RUN apk add --no-cache gettext \
    && chmod 0755 /usr/bin/genesis-entrypoint

CMD ["genesis-entrypoint", "httpd-foreground"]
