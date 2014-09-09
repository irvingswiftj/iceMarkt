#!/bin/bash

cd $(dirname "$BASH_SOURCE");

SUCCESS=0                      # All good programmers use Constants
domain=icemarkt.dev             # Change this to meet your needs
needle=www.$domain             # Fortunately padding & comments are ignored
hostline="192.168.56.101 $domain www.$domain"
filename=/etc/hosts

# Determine if the line already exists in /etc/hosts
grep -q "$needle" "$filename"  # -q is for quiet. Shhh...

# Grep's return error code can then be checked. No error=success
if [ $? -eq $SUCCESS ]
then
  echo "$needle found in $filename"
else
  echo "$needle not found in $filename"
  # If the line wasn't found, add it using an echo append >>
  sudo bash -c "echo \"$hostline\" >> \"$filename\""
  echo "$hostline added to $filename"
fi

vagrant up;

vagrant ssh -c "cd /var/www && composer update"
vagrant ssh -c "cd /var/www && php bin/console doctrine:database:drop --force"
vagrant ssh -c "cd /var/www && php bin/console doctrine:database:create"
vagrant ssh -c "cd /var/www && php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/MailRecipient"
vagrant ssh -c "cd /var/www && php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/EmailProfile"
vagrant ssh -c "cd /var/www && php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/SpreadSheet"
vagrant ssh -c "cd /var/www && php bin/console doctrine:generate:entities IceMarkt/Bundle/MainBundle/Entity/EmailTemplate"
vagrant ssh -c "cd /var/www && php bin/console doctrine:schema:update --force"
