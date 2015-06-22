FROM ubuntu

MAINTAINER Samuel ROZE <samuel.roze@gmail.com>

RUN apt-get update
RUN apt-get install -y git php5-cli php5-mysql php5-intl curl

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

ADD . /app
WORKDIR /app

RUN composer install -o --no-dev

CMD ["php", "-S", "0.0.0.0:80", "-t", "web"]
