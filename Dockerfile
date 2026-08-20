# Container for the 2FA.Online PHP clone.
#
# Uses the official Apache+mod_php image rather than `php -S`: PHP's own
# manual states the built-in server is for development/testing only and
# should not be exposed on a public network. The app itself still needs
# nothing beyond a static file + PHP server — no database, sessions, or
# file uploads — so no further extensions/config are required.
FROM php:8.3-apache

WORKDIR /var/www/html
COPY . .

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Render (and most PaaS hosts) inject the port to listen on via $PORT.
ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["docker-entrypoint.sh"]
