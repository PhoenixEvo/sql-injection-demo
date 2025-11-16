FROM php:8.1-apache

# Enable mysqli extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable mod_rewrite for Apache (optional)
RUN a2enmod rewrite

