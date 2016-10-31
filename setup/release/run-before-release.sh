#!/usr/bin/env bash

# Empty logs, history etc.
sudo apt-get clean
cat /dev/null > ~/.zsh_history
cat /dev/null > ~/.mysql_history
cat /dev/null > ~/.wget-hsts
sudo bash -c 'echo > /var/log/apache2/error.log'
sudo bash -c 'echo > /var/log/apache2/access.log'
sudo bash -c 'echo > /var/log/php-errors.log'
sudo rm -f /.db-installed
mongo tideways --eval "db.dropDatabase()"

# Remove default sites
for file in /etc/apache2/sites-available/*.conf
do
  if ! [ ${file} == "/etc/apache2/sites-available/000-default.conf" ] && ! [ ${file} == "/etc/apache2/sites-available/default-ssl.conf" ]
  then
    filename=`basename ${file}`
    sitename=${filename%.conf}
    sudo a2dissite ${sitename}
    rm ${file}
  fi
done
#sudo a2dissite box.dev
#sudo a2dissite logs.dev
#sudo a2dissite mail.dev
#sudo a2dissite phpmyadmin.dev
#sudo a2dissite profiler.dev
#rm /etc/apache2/sites-available/box.dev.conf
#rm /etc/apache2/sites-available/logs.dev.conf
#rm /etc/apache2/sites-available/mail.dev.conf
#rm /etc/apache2/sites-available/phpmyadmin.dev.conf
#rm /etc/apache2/sites-available/profiler.dev.conf

# Check that the current PHP version is 7
php_version_first_line=$(php -v | head -n 1)
php_version=$(echo ${php_version_first_line} | sed 's/^.*[^0-9]\([0-9]*\.[0-9]*\.[0-9]*\).*$/\1/')
if [[ ! ${php_version} =~ ^7 ]]
then
  printf "\nWARNING!\n"
  printf "The box should be released with PHP 7 installed\n"
  printf "use: \n"
  printf "  source phpswitch 7\n"
fi

# Check that Apache is using PHP 7
if [ ! -f "/etc/apache2/mods-enabled/php7.load" ]
then
  printf "\nWARNING!\n"
  printf "Apache should be enabled with PHP 7\n"
  printf "use: \n"
  printf "  source phpswitch 7\n"
fi
