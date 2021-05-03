#!/bin/sh

php artisan key:generate
php artisan optimize
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
# php artisan swoole:http start
