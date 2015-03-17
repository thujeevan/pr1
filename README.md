# pr1
sample payment gateway for paypal, braintree


## Installation instructions

 1. Clone this repository, and go to project directory ( may be within webroot / or symlink )
 2. Install composer - `curl -sS https://getcomposer.org/installer | php`
 3. issue `php composer.phar install` to install vendor libs
 4. [install bower](http://bower.io/#install-bower) package manager, if not already
 5. issue `bower install` to update assets
 6. Create virtual host and update `/etc/hosts` ( mod rewrite to work properly )
 7. Create a db, run `src/db/schema.sql`, and update `src/conf.yml` properly
 8. go to the given server name in browser

Sample vhost config

```
<VirtualHost *:80>
        ServerAdmin webmaster@localhost
        ServerName pr1
        DocumentRoot /var/www/pr1/public
        DirectoryIndex index.php index.html

        <Directory /var/www/pr1/public>
                Options FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                Allow from all
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/error.log

        # Possible values include: debug, info, notice, warn, error, crit,
        # alert, emerg.
        LogLevel warn

        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

sample `/etc/hosts`

```
127.0.0.1       pr1
```

in browser `http://pr1`