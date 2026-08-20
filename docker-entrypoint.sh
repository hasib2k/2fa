#!/bin/sh
# Render (and most PaaS hosts) assign the listen port at runtime via $PORT,
# but Apache's config is static, so rewrite it to the real port before
# starting — the container can't know $PORT until it's actually running.
set -e

: "${PORT:=10000}"

sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/:80>/:${PORT}>/" /etc/apache2/sites-available/*.conf

# Allow .htaccess overrides
sed -ri "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
sed -ri "s/AllowOverride None/AllowOverride All/g" /etc/apache2/sites-available/*.conf

exec apache2-foreground
