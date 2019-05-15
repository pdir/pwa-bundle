PWA - Progressive Web App bundle for Contao 4
======================

[![Latest Stable Version](https://poser.pugx.org/pdir/pwa-bundle/v/stable)](https://packagist.org/packages/pdir/pwa-bundle)
[![Total Downloads](https://poser.pugx.org/pdir/pwa-bundle/downloads)](https://packagist.org/packages/pdir/pwa-bundle)
[![License](https://poser.pugx.org/pdir/pwa-bundle/license)](https://packagist.org/packages/pdir/pwa-bundle)

Features
-----

- Create and register manifest file for each page root

Screenshot
-----------

![Contao demo example](https://pdir.de/extensions/pwa/pwa-screen.png)
![Root page settings](https://pdir.de/extensions/pwa/pwa-settings.png)

System requirements
-------------------

* [Contao Standard](https://github.com/contao/standard-edition) 4.4 or higher or
* [Contao Managed](https://github.com/contao/managed-edition) 4.4 or higher

Installation & Configuration
----------------------------

via Contao Manager

  1. Search for "pwa" or "pwa-bundle" mark for install and apply changes
  2. Update Database
  3. Activate Manifest in Root Page settings

via console

  1. composer require pdir/pwa-bundle
  2. Update database

License
---------------
LGPL-3.0+


ToDo
---------------

- add service worker for caching
- add web push notifications via FCM for desktop user
- add push notifications via fcm for app user via flutter app 

History
---------------

- see [changelog](https://github.com/pdir/pwa-bundle/blob/master/changelog.md)
