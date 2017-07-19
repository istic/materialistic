#!/bin/bash
apt-get update -q
export DEBIAN_FRONTEND=noninteractive

## Aquarion's piece of mind setup

apt-get install -q -y vim curl

## Apache Setup
apt-get install -q -y apache2 libapache2-mod-php php-json php-xdebug php-mysqlnd
a2enmod rewrite
ln -fs /vagrant /var/www/materialistic
cp /vagrant/etc/apache_dev.conf /etc/apache2/sites-available/materialistic.conf
a2ensite materialistic
a2dissite default
service apache2 restart

## mariadb Setup 
#debconf-set-selections <<< 'mariadb-server mariadb-server/root_password password $PASSWORD'
#debconf-set-selections <<< 'mariadb-server mariadb-server/root_password_again password $PASSWORD'

export PASSWORD=important
apt-get install -q -y mariadb-server mariadb-client
mysqladmin -u root password $PASSWORD
echo "create database materialistic;" | mariadb -uroot -p$PASSWORD
echo "grant all on materialistic.* to webapp@localhost identified by 'webapp'" | mariadb -uroot -p$PASSWORD

mariadb -uwebapp -pwebapp materialistic < /vagrant/data/schema.sql

if [[ -e /vagrant/data/data.sql ]]; then
	mariadb -uwebapp -pwebapp materialistic < /vagrant/data/data.sql
fi

hostname materialistic