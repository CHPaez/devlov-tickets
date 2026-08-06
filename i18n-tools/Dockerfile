FROM php:8.3-cli
RUN apt-get update && apt-get install -y libzip-dev libxml2-dev gettext \
    && docker-php-ext-install mysqli zip xml opcache
WORKDIR /app
COPY src.tar.gz /tmp/src.tar.gz
RUN mkdir -p /app && tar -xzf /tmp/src.tar.gz -C /app && rm /tmp/src.tar.gz
