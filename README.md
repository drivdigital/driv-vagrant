**Note for winodws users:** This project has been created for use on osx and linux systems. It should still work on a windows system, but might require adjustments to make it work.

## Installing

`vagrant up`

This will download and add the box. 

Manual install:

`vagrant box add driv02 http://drivdi-2200.rask17.raskesider.no/vagrant/driv02.box`

## Daily usage

Start:
`vagrant up [vm2]`

SSH:
`vagrant ssh [vm2]`

Destroy:

`vagrant destroy -f`

The default VM enables port forwarding and .dev sites can be accessed using port 8080 or 8081(SSL)
When running `vm2`, the VM will enable a private network and .dev site URLs does not require port number.

NOTE!
When running a Vagrant private network you may have to shut down any HTTP server running on port 80.

## Documentation 

See the [wiki](https://github.com/drivdigital/driv-vagrant/wiki) for documentation.