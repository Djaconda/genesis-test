#!/bin/sh
set -e

envsubst '$$HOST_NAME $$PROJECT_NAME $$LISTEN_PORT $$EMAIL' < /usr/local/apache2/conf/httpd.conf.template > /usr/local/apache2/conf/httpd.conf

exec "$@"
