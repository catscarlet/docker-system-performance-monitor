#!/bin/bash

cd /root/docker-system-performance-monitor/
tar zxf PHPMailer-5.2.22.tar.gz -C model/
echo '* *    * * *   root    php /root/docker-system-performance-monitor/monitor.php >> /proc/1/fd/1' >> /etc/crontab
