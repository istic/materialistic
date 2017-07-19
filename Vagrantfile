# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "debian/stretch64"
  # config.vm.box_url = "http://domain.com/path/to/above.box"
  config.ssh.forward_agent = true
  config.vm.provision :shell, :path => "etc/vagrant_provision.sh"
  if Vagrant::Util::Platform.windows?
  	config.vm.network :forwarded_port, host: 80, guest: 80
  end


  config.vm.network "private_network", ip: "192.168.33.21"

  web_hosts = [
        'materialistic.dev',
      ]

  config.hostsupdater.aliases = web_hosts
  config.vm.provision :hosts, :sync_hosts => true

  config.vm.provision :hosts do |provisioner|
    # provisioner.add_host "192.168.0.1", web_hosts
    provisioner.add_host "192.168.33.21", web_hosts
  end


  config.vm.synced_folder ".", "/vagrant", disabled: true
  # Windows doesn't support NFS, but it's better for everyone else.
  if Vagrant::Util::Platform.windows?
    config.vm.synced_folder ".", "/vagrant/", id: "vagrant-root", type: "virtualbox"
  else
    config.vm.synced_folder ".", "/vagrant/", id: "vagrant-root", type: "nfs"
  end


end
