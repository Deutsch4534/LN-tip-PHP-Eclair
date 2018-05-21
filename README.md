# LN-tip-PHP-Eclair
A simple way to accept tips via the Lightning Network on your website. 

<img src="https://i.imgur.com/0mOEgTf.gif" width="240">

## Credit ##
Kudos to [robclark56](https://github.com/robclark56) for the original [LightningTip-PHP](https://github.com/robclark56/lightningtip-PHP/blob/master/README.md).

## Requirements ##
* one [Eclair](https://github.com/ACINQ/eclair) instance
* a webserver that supports [PHP](http://www.php.net/) and [curl](https://curl.haxx.se/)
## Why PHP? ##
Using PHP improves portability and removes the need for a separate executable running as a service.
## Prepare Eclair ##
`api` block in `eclair.config` should be enabled so PHP backend can issue local JsonRPC queries to Eclair.
  
## Prepare Web Server ##
Your webserver will need to have the _php-curl_ package installed. The most straightforward way:

```
$ sudo apt install apache2
$ sudo apt install php-pear php-fpm php-dev php-zip php-curl php-xmlrpc php-gd php-mysql php-mbstring php-xml libapache2-mod-php
```


## How to install ##
* Download the [sources](https://github.com/btcontract/LN-tip-PHP-Eclair), and unzip.
* From the _frontend_ folder: Upload these files to your webserver:
  * index.php
  * lightningTip.js
  * lightningTip.css
  * lightningTip_light.css (Optional)
* Edit the _CHANGE ME_ section of `index.php`.
* Edit the _CHANGE ME_ section of `lightningTip.js`.
* Copy the contents of the head tag from `index.php` into the head section of the HTML file you want to show LightningTip in. The div below the head tag is LightningTip itself. Paste it into any place in the already edited HTML file on your server.


There is a light theme available. If you want to use it **add** this to the head tag of your HTML file:

```
<link rel="stylesheet" href="lightningTip_light.css">
```

**Do not use on XHTML** sites. That causes some weird scaling issues.

That's it! The only things you need to take care of is keeping the Eclair node and web server online.


