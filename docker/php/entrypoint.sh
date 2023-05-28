#!/bin/sh
set -e

if [ "$XDEBUG" = 1 ]; then
    echo "zend_extension=xdebug" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
else
    echo "" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
fi

exec "$@"
