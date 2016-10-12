**Note for winodws users:** This project has been created for use on osx and linux systems. It should still work on a windows system, but might require adjustments to make it work.

## Installing

Before starting up, you need to verify that the following software are installed:

* Git
* Virtual Box
* Vagrant

Download, add the box and configure: 

`vagrant up`

The first time you run `vagrant up` Vagrant will download and add the box.

## Daily usage

Start:  

`vagrant up [vm2]`

SSH:  

`vagrant ssh [vm2]`

Destroy:  

`vagrant destroy -f`

The default VM enables [port forwarding](https://www.vagrantup.com/docs/networking/forwarded_ports.html) and .dev sites can be accessed using port 8080 or 8081(SSL)
When running `vm2`, the VM enables a [private network ](https://www.vagrantup.com/docs/networking/private_network.html) and .dev site URLs does not require a port number.

NOTE!
When running a private network (vm2) you may have to shut down any HTTP server running on port 80.

## Documentation 

See the [wiki](https://github.com/drivdigital/driv-vagrant/wiki) for more documentation.