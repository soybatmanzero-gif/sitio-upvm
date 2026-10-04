# PHP 8.3 con Apache
FROM php:8.3-apache

# Extensiones para conectarse a MySQL
RUN docker-php-ext-install mysqli pdo_mysql

# Copiamos el proyecto a la carpeta pública de Apache
COPY . /var/www/html/

# Apache escuchará en el puerto que Render indica (10000 por defecto)
ENV PORT=10000
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf \
 && sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Permiso de escritura para las imágenes de productos
RUN chown -R www-data:www-data /var/www/html/imagenes_productos

EXPOSE 10000
