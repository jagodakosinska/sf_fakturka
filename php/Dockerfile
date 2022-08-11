FROM php:8-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev librabbitmq-dev \
    && docker-php-ext-install intl opcache pdo pdo_pgsql

RUN pecl install apcu \
    pecl install xdebug \
    && docker-php-ext-enable xdebug\
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip


RUN apt-get install -y libxml2-dev libxslt1-dev python-dev libpng-dev librabbitmq-dev libssl-dev

RUN pecl install redis \
    && docker-php-ext-install xsl gd \
    && docker-php-ext-enable redis xsl 

RUN docker-php-source extract \
    && mkdir /usr/src/php/ext/amqp \
    && curl -L https://github.com/php-amqp/php-amqp/archive/master.tar.gz | tar -xzC /usr/src/php/ext/amqp --strip-components=1 \
    && docker-php-ext-install amqp \
    && docker-php-ext-enable amqp

WORKDIR /var/www/project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN git config --global user.email "jagoda.kosinska@sortica.pl" \ 
    && git config --global user.name "Jagoda"

RUN apt-get install sudo vim bash-completion


RUN useradd -rm -d /home/ubuntu  -s /bin/bash -g root -G sudo -u 1000 ubuntu
RUN echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
USER ubuntu


WORKDIR /var/www/project

RUN symfony server:ca:install