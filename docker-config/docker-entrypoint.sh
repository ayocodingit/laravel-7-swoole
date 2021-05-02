#!/bin/sh

php artisan key:generate
php artisan optimize
php artisan swoole:http start
