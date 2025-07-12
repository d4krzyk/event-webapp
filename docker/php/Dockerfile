FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip curl libpq-dev cron \
    && docker-php-ext-install pdo_pgsql

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Skopiuj plik crontab do obrazu
COPY crontab /etc/cron.d/reminder-cron

# Ustaw odpowiednie uprawnienia i zainstaluj crontab
RUN chmod 0644 /etc/cron.d/reminder-cron && crontab /etc/cron.d/reminder-cron

# Uruchom crona na pierwszym planie
CMD ["cron", "-f"]
