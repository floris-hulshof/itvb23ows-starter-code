#FROM php:8.2-cli
#COPY . /usr/src/myapp
#WORKDIR /usr/src/myapp
#CMD [ "php", "./index.php" ]
#
# # Use an official PHP runtime as a parent image
# FROM php:latest
#
# # Install MySQL extensions
# RUN docker-php-ext-install mysqli pdo_mysql
#
# # Make port 80 available to the world outside this container
# EXPOSE 80
#
# # Define environment variable
# ENV NAME World
#
# # Run php when the container launches
# CMD ["php", "-S", "0.0.0.0:80", "-t", "/app"]


FROM php:8.0-apache
RUN docker-php-ext-install mysqli pdo_mysql && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y