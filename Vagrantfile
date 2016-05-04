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
    v.memory = 2048
  end
  config.vm.box = "driv"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 443, host: 8081
  config.vm.synced_folder ".", "/vagrant", :mount_options => ['dmode=774','fmode=775']
  config.vm.provision "fix-no-tty", type: "shell" do |s|
      s.privileged = false
      s.inline = "sudo sed -i '/tty/!s/mesg n/tty -s \\&\\& mesg n/' /root/.profile"
  end
  config.vm.provision "shell", inline: <<-SHELL
    usermod -a -G vagrant www-data
    sudo php5enmod mcrypt
    sudo a2enmod rewrite
    sudo php /vagrant/setup/setup.php
    sudo chmod -R 777 /var/log
    sudo chmod 777 /phpsendmail
  SHELL
  config.vm.provision "shell", inline: "service apache2 restart", run: "always"

  # Temporary code
  # Bind 80/443 to 8080/8081 after booting/provisioning/reloading
  # We should also check for a forward_port_80 config for the site
  if Vagrant.has_plugin?("vagrant-triggers")
    config.trigger.after [:provision, :up, :reload] do
      system('echo "
      rdr pass on lo0 inet proto tcp from any to 127.0.0.1 port 80 -> 127.0.0.1 port 8080
      rdr pass on lo0 inet proto tcp from any to 127.0.0.1 port 443 -> 127.0.0.1 port 8081
      " | sudo pfctl -f - > /dev/null 2>&1; echo "==> Fowarding Ports: 80 -> 8080, 443 -> 8081"')
    end

    config.trigger.after [:halt, :destroy] do
      system("sudo pfctl -f /etc/pf.conf > /dev/null 2>&1; echo '==> Removing Port Forwarding'")
    end
  end

end