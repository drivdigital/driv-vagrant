# -*- mode: ruby -*-
# vi: set ft=ruby :

system("
    if [ #{ARGV[0]} = 'up' ]; then
        echo 'Setting up /etc/hosts'
        SCRIPT=setup/setup-host.php
        [[ -f $SCRIPT ]] && php \"$SCRIPT\" --before
        SCRIPT=../setup/setup-host.php
        [[ -f $SCRIPT ]] && php \"$SCRIPT\" --before
    fi
")

Vagrant.configure(2) do |config|
  config.vm.provider "virtualbox" do |v|
    v.memory = 1024
  end
  config.vm.box = "driv"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.synced_folder ".", "/vagrant", :mount_options => ['dmode=774','fmode=775']
  config.vm.provision "shell", inline: <<-SHELL
    usermod -a -G vagrant www-data
    sudo php5enmod mcrypt
    sudo a2enmod rewrite
    sudo php /vagrant/setup/setup.php
  SHELL
end

system("
    if [ #{ARGV[0]} = 'up' ]; then
        echo 'Running post setup scripts'
        SCRIPT=setup/setup-host.php
        [[ -f $SCRIPT ]] && php \"$SCRIPT\" --after
        SCRIPT=../setup/setup-host.php
        [[ -f $SCRIPT ]] && php \"$SCRIPT\" --after
    fi
")
