VAGRANTFILE_API_VERSION = '2'

@script = <<SCRIPT
add-apt-repository ppa:ondrej/php
apt-get update
apt-get install -y git curl php7.2 php7.2-bcmath php7.2-bz2 php7.2-cli php7.2-curl php7.2-intl php7.2-json php7.2-mbstring php7.2-opcache php7.2-soap php7.2-sqlite3 php7.2-xml php7.2-xsl php7.2-zip

if [ -e /usr/local/bin/composer ]; then
    /usr/local/bin/composer self-update
else
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# Reset home directory of vagrant user
if ! grep -q "cd /app" /home/vagrant/.profile; then
    echo "cd /app" >> /home/vagrant/.profile
fi

cd /app
composer update

SCRIPT

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = 'ubuntu/xenial64'
  config.vm.synced_folder '.', '/app'
  config.vm.provision 'shell', inline: @script

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--name", "metisfw-databaseutils"]
  end
end
