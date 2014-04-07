#!/bin/bash
apt-get update -q
export DEBIAN_FRONTEND=noninteractive

## Aquarion's piece of mind setup

apt-get install -q -y vim curl

## Apache Setup
apt-get install -q -y apache2 libapache2-mod-php5 php5-json php5-xdebug php5-mysqlnd
a2enmod rewrite
ln -fs /vagrant /var/www/materialistic
cp /vagrant/etc/apache_dev.conf /etc/apache2/sites-available/materialistic
a2ensite materialistic
a2dissite default
service apache2 restart

## Mysql Setup 
#debconf-set-selections <<< 'mysql-server mysql-server/root_password password $PASSWORD'
#debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password $PASSWORD'

export PASSWORD=important
apt-get install -q -y mysql-server-5.5 mysql-client-5.5
mysqladmin -u root password $PASSWORD
echo "create database materialistic;" | mysql -uroot -p$PASSWORD
echo "grant all on materialistic.* to webapp@localhost identified by 'webapp'" | mysql -uroot -p$PASSWORD

mysql -uwebapp -pwebapp materialistic < /vagrant/data/schema.sql

if [[ -e /vagrant/data/data.sql ]]; then
	mysql -uwebapp -pwebapp materialistic < /vagrant/data/data.sql
fi