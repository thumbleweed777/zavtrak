FROM centos:centos6.7
MAINTAINER "Cristian Martínez" <cristianandres92@gmail.com>
RUN yum -y update
RUN yum -y install python-setuptools
RUN easy_install supervisor
RUN mkdir -p /var/log/supervisor
RUN yum -y install which
RUN yum install -y git
RUN yum install -y php
RUN yum install -y php-mysql
RUN yum install -y php-mbstring
RUN yum install -y vim
RUN yum install -y curl
RUN yum install -y wget
RUN yum install -y php-xml
RUN yum install -y httpd
ADD supervisord.conf /etc/supervisord.conf
ENV TERM=xterm
EXPOSE 22 80
CMD ["/usr/bin/supervisord"]
