#!/bin/bash

#################################################################
# ZPanelX Automated uninstall for CentOS 6.2                    #
# Created by : nickelj                                          #
# Current maintainer : Kevin Andrews (kandrews@zpanelcp.com)    #
# Licensed Under the GPL (http://www.gnu.org/licenses/gpl.html) #
# Version 1.0.0                                                 #
#################################################################
yum -y remove wget
yum -y remove chkconfig
yum -y remove do2unix
yum -y remove sudo
yum -y remove vim
yum -y remove make
yum -y remove zip
yum -y remove unzip
yum -y remove git
yum -y remove linux.so.2
yum -y remove libbz2.so.1
yum -y remove libdb-4.7.so
yum -y remove libgd.so.2
yum -y remove httpd
yum -y remove php
yum -y remove php-suhosin
yum -y remove php-devel
yum -y remove php-gd
yum -y remove php-mbstring
yum -y remove php-mcrypt
yum -y remove php-intl
yum -y remove php-imap
yum -y remove php-mysql
yum -y remove php-xml
yum -y remove php-xmlrpc
yum -y remove curl
yum -y remove curl-devel
yum -y remove perl-libwww-perl
yum -y remove libxml2
yum -y remove libxml2-devel
yum -y remove mysql-server
yum -y remove webalizer
yum -y remove gcc
yum -y remove gcc-c++
yum -y remove httpd-devel
yum -y remove at
yum -y remove mysql-devel
yum -y remove bzip2-devel
yum -y remove postfix
yum -y remove dovecot
yum -y remove dovecot-mysql
yum -y remove proftpd
yum -y remove proftpd-mysql
yum -y remove bind
yum -y remove bind-utils
yum -y remove bind-libs
yum -y install sendmail
yum -y install vsftpd
rm -rf /etc/zpanel
rm -rf /var/zpanel
rm -rf /etc/httpd
rm -rf /etc/named
rm -rf /etc/postfix
rm -rf /etc/dovecot
rm -rf /var/named
rm -rf /var/ftp
rm -rf /var/lib/mysql
rm /etc/my.cnf
rm /etc/named.conf
rm /etc/named.conf.rpmsave
rm /etc/proftpd.conf
rm /etc/proftpd.conf.rpmsave
rm /etc/proftpd.old
reboot