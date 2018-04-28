FROM php:7
RUN apt-get update -y && apt-get install -y openssl zip unzip git libcurl3-dev sqlite3 libsqlite3-dev libbz2-dev libxml2-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install bz2 pdo mbstring curl pdo_mysql tokenizer xml ctype json
WORKDIR /app/fszekfigyelo
COPY . /app/fszekfigyelo
RUN composer install
#RUN php artisan key:generate
RUN php artisan migrate

CMD php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181
