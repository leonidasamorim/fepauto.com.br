FROM php:8.2-apache

# Extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# mod_rewrite para URLs limpas
RUN a2enmod rewrite

# Configuração básica do Apache
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/rallye.conf && \
    a2enconf rallye

WORKDIR /var/www/html
