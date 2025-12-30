FROM php:7.2-apache

# 1. Install PDO MySQL
RUN docker-php-ext-install pdo_mysql

# 2. Enable Apache rewrite and proxy modules

# 3. Fix: Ensure Apache knows how to handle PHP files
# Sometimes older images need explicit Type handlers
RUN echo "<FilesMatch \.php$>\n\
    SetHandler application/x-httpd-php\n\
</FilesMatch>" >> /etc/apache2/apache2.conf
FROM php:7.2-apache

# Install PDO MySQL
RUN docker-php-ext-install pdo_mysql

# 1. Force the Global Apache Timeout to 5 seconds
# This tells Apache: "If I don't send data for 5s, kill the request."

# 2. Set PHP Execution time to be MUCH longer than Apache
# This creates the "Conflict" that causes a 504/500 error


WORKDIR /var/www/html
COPY . /var/www/html/
COPY php.ini /usr/local/etc/php/conf.d/php.ini
RUN a2enmod rewrite proxy proxy_fcgi headers
RUN service apache2 restart