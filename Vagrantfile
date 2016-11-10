# -*- mode: ruby -*-
# vi: set ft=ruby :

system("
    port_forwarding_arg=''
    if [ #{ARGV[0]} = 'up' ] && [ #{ARGV[1]} = 'vm2' ]; then
      port_forwarding_arg='--port_forwarding'
    fi

    if [ #{ARGV[0]} = 'up' ]; then
        echo 'Setting up /etc/hosts'
        SCRIPT=setup/setup-host.php
        [[ -f $SCRIPT ]] && php \"$SCRIPT\" --before ${port_forwarding_arg}
        SCRIPT=../setup/setup-host.php
        [[ -f $SCRIPT ]] && php \"$SCRIPT\" --before ${port_forwarding_arg}
    fi
")

Vagrant.configure(2) do |config|

  # Shared
  config.ssh.username = 'vagrant'
  config.ssh.password = 'vagrant'
  config.vm.provider "virtualbox" do |v|
    v.memory = 1024
  end
  config.vm.box = "driv02"
  config.vm.box_url = "http://drivdi-2200.rask17.raskesider.no/vagrant/driv02.box"


  # vm1 - Private network. Default
  config.vm.define "vm1", primary: true do |vm1|
    vm1.vm.network "private_network", ip: "192.168.33.10"
    config.vm.synced_folder ".", "/vagrant", :nfs => { :mount_options => ["dmode=774","fmode=775"] }
  end

  # vm2 - Port forwarding.
  config.vm.define "vm2", autostart: false do |vm2|
    vm2.vm.network "forwarded_port", guest: 80, host: 8080
    vm2.vm.network "forwarded_port", guest: 443, host: 8081
    config.vm.synced_folder ".", "/vagrant", :mount_options => ['dmode=774','fmode=775']
  end


  # Shared
  config.vm.provision "fix-no-tty", type: "shell" do |s|
      s.privileged = false
      s.inline = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
  end

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

  # Setup PHP
  config.vm.provision "shell", privileged: false, run: "always", inline: "php /vagrant/setup/class-setup-php.php provision"

  # Restart apache
  config.vm.provision "shell", inline: "service apache2 restart", run: "always"

end
