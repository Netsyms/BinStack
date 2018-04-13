BinStack
========

BinStack is the easy way to keep track of your business assets.
Never wander in search of the stapler again.

https://netsyms.biz/apps/binstack

Features
--------

**Asset Tracking**
Easily manage locations, tag numbers, assigned users, and other important item
properties.

**Fully Searchable**
Start typing in the search box to instantly filter hundreds of results down to
only a few. Sorting also built in.

**Autofill**
You don't need to remember category codes or usernames. Just start typing and
BinStack will figure out what you're looking for.

**Mobile-ready**
Take inventory or find what you need on the go. BinStack looks and works
great on modern smartphones and tablets.

Installing
----------

0. Follow the installation directions for [AccountHub](https://source.netsyms.com/Business/AccountHub), then download this app somewhere.
1. Copy `settings.template.php` to `settings.php`
2. Import `database.sql` into your database server
3. Edit `settings.php` and fill in your DB info
4. Set the location of the AccountHub API in `settings.php` (see "PORTAL_API") and enter an API key ("PORTAL_KEY")
5. Set the location of the AccountHub home page ("PORTAL_URL")
6. Set the URL of this app ("URL")
7. Run `composer install` (or `composer.phar install`) to install dependency libraries.
