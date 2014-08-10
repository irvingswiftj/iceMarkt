IceMarkt
========================

Welcome to IceMarkt, an open source marketing platform built on Symfony2

N.B. This is still very much a work in progress, the more contributors the better!

#Requirements

##To use the virtual machine
* Vagrant
* VirtualBox

#Setup

Add this entry to your hosts file (/etc/hosts on unix based machines):

`192.168.56.101  icemarkt.dev`

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

Open your browser and go to: icemarkt.dev

Hopefully it rendered a page!

#Testing

Unit tests coming soon

#Known Issues

As I'm currently using NFS to map a shared folder to the virtual machine, it won't work out of the box with windows


#Credits
- [irvingswiftj](https://github.com/:irvingswiftj)
- [All Contributors](https://github.com/irvingswiftj/icemarkt/contributors)

#License
The BSD License. Please see [License File](https://github.com/irvingswiftj/iceMarkt/blob/master/LICENSE) for more information.