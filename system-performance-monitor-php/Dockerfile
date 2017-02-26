FROM php:7.0.14-cli

MAINTAINER catscarlet

COPY etc/ /etc/
RUN /bin/sh -c 'apt-get update && apt-get install -y cron --no-install-recommends'

RUN /bin/sh -c 'apt-get install -y sysstat --no-install-recommends'
COPY files/ /root/docker-system-performance-monitor/
RUN /bin/sh -c 'chmod a+x /root/docker-system-performance-monitor/init.sh && sync && /root/docker-system-performance-monitor/init.sh'

CMD ["cron", "-f"]
