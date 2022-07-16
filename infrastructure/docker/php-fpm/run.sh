php-fpm &
sleep 30
supervisord --nodaemon --configuration=/etc/supervisord.conf