IceMarkt
========================

Welcome to IceMarkt, an open source marketing platform built on symfony2

#Requirements

##To use the virtual machine
* Vagrant
* VirtualBox

#Setup

In a terminal, go to the root folder of your copy of IceMarkt and enter:

`vagrant up`

This may take a couple of minutes.

Once this is complete. Ssh into your vagrant box with:

`vagrant ssh`

Then enter the following commands:

```
cd /var/www
composer install
chmod a+x setupdb.sh
./setupdb.sh
```

