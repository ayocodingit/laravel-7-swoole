FROM alpine:3.11

LABEL Maintainer="Firman Ayocoding <ayocodingit@gmail.com>"

ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub
# make sure you can use HTTPS
RUN apk --update-cache add ca-certificates

RUN echo "https://dl.bintray.com/php-alpine/v3.11/php-7.4" >> /etc/apk/repositories

# Install packages
RUN apk --no-cache add php7 \
    nano \
    php7-opcache \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-iconv \
    php7-intl \
    php7-mbstring \
    php7-pdo_mysql \
    php7-pdo_sqlite \
    php7-pdo_pgsql \
    php7-openssl \
    php7-session \
    php7-zip \
    php7-zlib \
    php7-sqlite3 \
    php7-gd \
    php7-phar \
    php7-redis \
    php7-json \
    php7-xml \
    php7-xmlreader \
    php7-sockets \
    php7-sodium \
    php7-pcntl \
    php7-posix \
    php7-iconv \
    php7-swoole \
    supervisor

# https://github.com/codecasts/php-alpine/issues/21
RUN ln -s /usr/bin/php7 /usr/bin/php

# Configure nginx
# COPY docker-config/nginx.conf /etc/nginx/nginx.conf

# Remove default server definition
# RUN rm /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
# COPY docker-config/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY docker-config/php.ini /etc/php7/conf.d/custom.ini

# Configure supervisord
COPY docker-config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup document root
RUN mkdir -p /var/www/html

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R nobody.nobody /var/www/html && \
    chown -R nobody.nobody /run

# Switch to use a non-root user from here on
USER nobody
# Add application
WORKDIR /var/www/html
COPY --chown=nobody . /var/www/html/
RUN chmod +x docker-config/docker-entrypoint.sh
# Install composer from the official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Run composer install to install the dependencies
RUN composer install

ARG DOCKER_APP
ENV DOCKER_APP $DOCKER_APP

ARG OCTANE_WORKER
ENV OCTANE_WORKER $OCTANE_WORKER

ARG OCTANE_SERVER
ENV OCTANE_SERVER $OCTANE_SERVER
# Expose the port nginx is reachable on
EXPOSE 8080
# Let supervisord start nginx & php-fpm
ENTRYPOINT [ "docker-config/docker-entrypoint.sh" ]
