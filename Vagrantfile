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

end

# Load additional Vagrant file in the site directory
site_dirs = Dir.glob("*.dev")

site_dirs.each do |site_dir|
  file = File.expand_path(site_dir + '/Vagrantfile.site')
  load File.expand_path(file) if File.exists?(file)
end
