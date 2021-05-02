#!/bin/sh

php artisan key:generate
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan swoole:http start
