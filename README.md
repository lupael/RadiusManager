# Radius Manager

Lupael

(c) iNternet For Education i4E

# Overview

The  purpose  of  this  project  is  to  provide  an  Administration  and  End  User  GUI  interface  for  FreeRadius  entries  into  the  MySQL  Database.

## Installation for UBUNTU 18.04.XX

# Login as ROOT

sudo su

# Install GIT, CURL, WGET and ZIP

apt-get install -y git curl wget zip

# Remove php 5 and apache2 if previously installed

sudo apt-get purge php5-fpm apache2

sudo apt-get --purge autoremove

# PHP 7 (Needed for Laravel 5.7)

apt-get install -y software-properties-common

add-apt-repository -y ppa:ondrej/php

apt-get update

apt-get install -y php7.2 php7.2-fpm php-mysql php7.2-mysql php7.2-mbstring php-gettext php-doctrine-dbal php7.2-xml php7.2-zip php7.2-curl

sudo -- sh -c "echo 'cgi.fix_pathinfo=0' >> /etc/php/7.2/fpm/php.ini"

sudo -- sh -c "echo 'cgi.fix_pathinfo=0' >> /etc/php/7.2/cli/php.ini"

sudo service php7.2-fpm restart

# Install Composer for Laravel

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install MySQL Server

apt-get install mysql-server

/etc/init.d/mysql start
mysql_secure_installation

# Install NGINX Server

apt-get install nginx

# Install FreeRadius 3.0

apt-get install -y freeradius

service freeradius start

apt-get install -y freeradius-mysql

service freeradius stop

ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/sql

ln -s /etc/freeradius/3.0/sites-available/dynamic-clients /etc/freeradius/3.0/sites-enabled/dynamic-clients

sh /etc/freeradius/3.0/certs/bootstrap

chown -R freerad:freerad /etc/freeradius/3.0/certs

# Create MySQL Database and User for Application

mysql -uroot -p

<< ENTER YOUR MYSQL ROOT PASSWORD WHEN PROMPT >>

CREATE DATABASE radius;
GRANT ALL ON radius.* TO radius@localhost IDENTIFIED BY "radpass";
exit

# Clone Radius Manager from Github 

cd /var/www/html

git clone -b "master" https://github.com/lupael/RadiusManager.git 

chown www-data:www-data -R RadiusManager

cd RadiusManager

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan radius:install

service freeradius start

# Set NGINX to point to Application

php artisan nginx:install

# Restart NGINX to Apply Config

service nginx restart

# Radius Clean up script

php artisan radius:cleanup

Setup Laravel cron to run every min this will run Radius Cleanup daily midnight and this will clean the logs older then 90 days. 

`* * * * * nginx php /var/www/html/RadiusManager/artisan schedule:run`

