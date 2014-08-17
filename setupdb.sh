#!/bin/bash

cd /var/www
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/MailRecipient
php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/EmailProfile
php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/SpreadSheet
php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/EmailTemplate
php bin/console doctrine:schema:update --force
