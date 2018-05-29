FROM php:7.1-fpm

###########################################################################
# Install requirements
###########################################################################

RUN apt-get update -yqq \
  && apt-get install -y --no-install-recommends apt-utils \
  && apt-get install git libpq-dev zip unzip -yqq \
  && apt-get install libpng-dev wget openssl -y

#install php extensions
RUN docker-php-ext-install zip \
  && docker-php-ext-install gd

#install pgsql extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo_pgsql

#install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN mkdir /var/www/.composer && chown -R www-data:www-data /var/www/.composer

RUN curl --silent --show-error https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

###########################################################################
# PHPUnit path
###########################################################################

RUN echo "" >> ~/.bashrc && \
    echo 'export PATH="/var/www/vendor/bin:$PATH"' >> ~/.bashrc

# Set default work directory
RUN chown -R www-data:www-data /var/www

RUN usermod -u 1000 www-data
USER www-data