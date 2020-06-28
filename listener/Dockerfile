FROM robbrazier/php:7.2
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
RUN composer install
RUN composer dumpautoload
CMD [ "php", "./index.php" ]
