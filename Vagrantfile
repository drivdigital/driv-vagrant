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

  ##############################################################################
  ## Shared                                                                   ##
  ##############################################################################
  config.vm.provider "virtualbox" do |v|
    v.memory = 2048
  end
  config.vm.box = "driv02"
  config.vm.box_url = "http://drivdi-2200.rask17.raskesider.no/vagrant/driv02.box"


  ##############################################################################
  ##  vm1 - Default. With port forwarding.                                    ##
  ##############################################################################
  config.vm.define "vm1", primary: true do |vm1|
    vm1.vm.network "forwarded_port", guest: 80, host: 8080
    vm1.vm.network "forwarded_port", guest: 443, host: 8081
  end

  ##############################################################################
  ##  vm2 - Private network. No port forwarding.                              ##
  ##############################################################################
  config.vm.define "vm2", autostart: false do |vm2|
    vm2.vm.network "private_network", ip: "192.168.33.10"
  end


  ##############################################################################
  ## Shared                                                                   ##
  ##############################################################################
  config.vm.synced_folder ".", "/vagrant", :mount_options => ['dmode=774','fmode=775']
  config.vm.provision "fix-no-tty", type: "shell" do |s|
      s.privileged = false
      s.inline = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
  end

  # Setup PHP
  config.vm.provision "shell", privileged: false, run: "always", inline: "php /vagrant/setup/class-setup-php.php provision"

  # Start Mailcatcher
  config.vm.provision "shell", privileged: false, run: "always", inline: <<-SHELL
    /home/vagrant/.rbenv/shims/mailcatcher
  SHELL

  # Setup
  config.vm.provision "shell", inline: <<-SHELL
    usermod -a -G vagrant www-data
    sudo php /vagrant/setup/setup.php
    sudo chmod -R 777 /var/log
    sudo chmod 777 /phpsendmail
  SHELL

  # Restart apache
  config.vm.provision "shell", inline: "service apache2 restart", run: "always"

end