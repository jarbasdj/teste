# FROM debian:buster-slim

# # System deps
# RUN apt-get update \
#     && DEBIAN_FRONTEND=noninteractive apt-get upgrade -yq \
#     && DEBIAN_FRONTEND=noninteractive apt-get install -yq --no-install-recommends \
#     software-properties-common \
#     systemd-sysv \
#     htop \
#     nfs-common \
#     binutils \
#     systemd \
#     make \
#     apt-utils \
#     apt-transport-https \
#     ca-certificates \
#     locales \
#     && apt-get update \
#     && DEBIAN_FRONTEND=noninteractive apt-get install -yq \
#     libfreetype6-dev \
#     libicu-dev \
#     libssl1.1 \
#     libmcrypt-dev \
#     libedit-dev \
#     libedit2 \
#     libxslt1-dev \
#     gnupg \
#     redis-tools \
#     mariadb-client \
#     git \
#     vim \
#     wget \
#     curl \
#     lynx \
#     psmisc \
#     unzip \
#     tar \
#     # cron \
#     bash-completion \
#     nginx \
#     # varnish \
#     nodejs \
#     npm \
#     libnode-dev \
#     node-gyp \
#     supervisor \
#     && apt-get clean

# RUN apt -y autoremove \
#     && apt-get clean

# RUN mkdir -p /var/run/

# RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -
# RUN echo "deb https://packages.sury.org/php/ buster main" | tee /etc/apt/sources.list.d/php.list
# RUN apt update

# # PHP deps
# RUN DEBIAN_FRONTEND=noninteractive apt-get install -yq --no-install-recommends php7.3 \
#     php7.3-ctype \
#     php7.3-gd \
#     php7.3-dom \
#     php7.3-simplexml \
#     php7.3-iconv \
#     php7.3-intl \
#     php7.3-xsl \
#     php7.3-mbstring \
#     php7.3-zip \
#     php7.3-soap \
#     php7.3-xml \
#     php7.3-fpm \
#     php-xdebug \
#     php7.3-bcmath \
#     php7.3-bz2 \
#     php7.3-cli \
#     php7.3-common \
#     php7.3-curl \
#     php7.3-dev \
#     php7.3-enchant \
#     php7.3-json \
#     php7.3-mysql \
#     php7.3-opcache \
#     php7.3-tidy \
#     php7.3-iconv \
#     php-redis \
#     php-pear

# COPY ./config/php/php.ini /etc/php/7.3/fpm/php.ini
# COPY ./config/system/rc.local /etc/rc.local

# # Xdebug config
# COPY ./config/php/xdebug.ini /etc/php/7.3/mods-available/xdebug.ini

# COPY ./config/nginx/site.conf /etc/nginx/sites-available/default
# COPY ./config/nginx/mime.types /etc/nginx/mime.types

# COPY ./config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# COPY ./entrypoint.sh /entrypoint.sh

# RUN chmod +x /entrypoint.sh

# RUN chmod +x /usr/bin/*

# RUN chown -Rhf www-data:www-data /var/www/html \
#     && usermod -u 1000 www-data \
#     && chsh -s /bin/bash www-data

# WORKDIR /var/www/html

# ENTRYPOINT ["/entrypoint.sh"]


FROM php:7.4-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN a2enmod rewrite

COPY ./config/php/php.ini $PHP_INI_DIR/conf.d/

COPY ./config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./config/apache/site.conf /etc/apache2/sites-available
COPY ./config/php/xdebug.ini /etc/php/7.3/mods-available/xdebug.ini
COPY ./entrypoint.sh /entrypoint.sh

WORKDIR /var/www/html