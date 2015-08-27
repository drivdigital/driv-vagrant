# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "driv"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.synced_folder ".", "/vagrant", :mount_options => ['dmode=774','fmode=775']
  config.vm.provision "shell", inline: <<-SHELL
    usermod -a -G vagrant www-data
    git config core.fileMode false
    sudo php /vagrant/config/setup.php
  SHELL
end
