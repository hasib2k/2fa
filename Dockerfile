# Minimal container for the 2FA.Online PHP clone.
#
# The app has no database, sessions, or file uploads — it's PHP-rendered
# HTML templates plus a client-side TOTP generator — so PHP's own
# built-in web server is sufficient; there's no need for Apache/nginx
# or a PHP-FPM setup.
FROM php:8.3-cli

WORKDIR /var/www/html
COPY . .

# Render (and most PaaS hosts) inject the port to listen on via $PORT.
ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:${PORT} -t /var/www/html
