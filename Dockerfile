FROM php:8.0-apache

# # Installation des dépendances système
RUN apt-get update && apt-get install -y libicu-dev libpq-dev libzip-dev && rm -rf /var/lib/apt/lists/*

# # je prépare l'installation de intl
RUN docker-php-ext-configure intl

# # Installation des extensions PHP
RUN docker-php-ext-install intl \
                            pdo \
                            pdo_mysql