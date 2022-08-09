## redirect-server

This application is only used to redirect from a non-www domain to a www domain

It will take any domain that is pointing towards it and is visited using http and generate a ssl certificate and then enable you to visit that same domain with https and then get forwarded to https://www. domain

## installation

Please note all of this is on your own risk. Should run on a FRESH installation. Reference server is running 512Ram and 1 CPU.
```
apt-get update && apt-get install -y git apache2 php7.4 certbot
```
```
cd /var/www
```
```
rm * -R
```
```
git clone https://github.com/welrachid/redirect-server .
```

Create a new file config.live.php with your configs. Copy file from config.php to use as reference (Do not include the last bit that includes the config.live.php file)


## Config / Setup
This is built on a simple debian server with apache2 + php7.4 standard setup.

Check the config.php file to see what settings you can look at

You will need to add both run.php and cleanup.php to your cronjobs and run as root (They will issue certificate, delete certificate, restart apache2 and change enabled sites.)

Example (will run on each minute of each day. It will cleanup first, and then run and generate new certificates.)
```
echo '* * * * * root cd /var/www/cli/ && php cleanup.php && php run.php' > /etc/cron.d/redirect-server
```


```
a2enmod ssl
systemctl reload apache2
cd /var/www/cli/ && php cleanup.php && php run.php
a2dissite 000-default
a2ensite ssl non-ssl
systemctl reload apache2
```

## CLI
run.php looks into the domains directory of non-ssl public folder and finds all relevant domains to check if certificate already is issued. if not - then it attempts to issue new certificate.

cleanup.php does the reverse. Looks at the directory and tests if domains are still pointing correctly. If not they will be deleted and certbot delete will be issued.


## possible misuse
If people keep adding removing domains, you can get into a position where your ip-address is banned in a period of time. This is the reason there is a whitelist_ips array for whitelisted ips, so only your own ip address can add new domains.



## WHY
Many dns services offer to do a web-forward. Usually however this does not include SSL. The reasoning behind this is that since they are not hosting a webhotel for you, they cannot verify the domain with services like letsencrypt.

When using a whitelabel domain with SaaS they usually ask you to make CNAME for a subdomain (typically WWW.example.com). Some DNS providers offer ANAME, but not all.

When using whitelabel domains on subscription software usually the infrastructure is managed by the SaaS company. They do however not have access to your DNS and cannot change ip whenever they want.

Therefore you might need a place to e able to change from non-www.example.com to www.example.com with SSL.

