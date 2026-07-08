# Use the official PHP image with Apache web server
FROM php:8.2-apache

# Install the MySQLi extension required for database connection
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy your local PHP files into the container's web directory
COPY . /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80
