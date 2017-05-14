Business App Template
=====================

This is an empty (but fully functional) PHP application.  It is designed to 
integrate with Portal, an account management web interface.  Portal manages 
user credentials and account data, and is accessed by this app via [a simple API](http://docs.netsyms.com/docs/Portal/API%20Documentation/).

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/2aeadc6b65d545c4a4c2e77d286373fd)](https://www.codacy.com/app/Netsyms/BusinessAppTemplate?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Netsyms/BusinessAppTemplate&amp;utm_campaign=Badge_Grade)

Program Structure
-----------------

### Folders
* lang  
   Translations and alert messages.  
   The language file that is loaded depends on the value of `LANGUAGE` in `settings.php`.  
   Translate the values (but not the keys) in `en_us.php` into other languages and save in appropriately named files to add languages.
* lib  
   A good place to put helper functions that you don't want "in the way".
* pages  
   What it looks like.  If you go into `pages.php` and define a page with the name `foo`, there should be a `foo.php` in here.  
   The app checks before loading, so it will give a friendly 404 error if it doesn't find your page.  
   Woe to you if you delete `home.php` or `404.php`, as those are assumed to exist for fallback behavior.
* static  
   CSS, JS, fonts, images...
* vendor  
   If you don't know what this is about, or you don't have it, you need to read up on Composer.  Right now.

### Files
* settings.template.php  
   App configuration.  Copy to `settings.php` and customize.  Documented with inline comments.
* required.php  
   The "duct tape" that holds the app together.  Use `require_once __DIR__."/required.php"` at the top of every file.  
   It loads Composer dependencies, app settings, language data, and creates `$database` for accessing the database.  
   It also has some utility functions, including `dieifnotloggedin()`, `is_empty($var)`, and `lang('key')`.  
   Read through it to see what those functions do.
* action.php  
   A good place to post forms to.  By default it only handles logging out, but is easily expanded.
* api.php
   Similar to action.php, but designed for user/pass authenticated JSON responses.
* index.php  
   Login page and handler.  Hands off to `app.php` after authenticating user.  
   It includes 2fa support, by the way.
* app.php  
   Main app page after login.  Handles loading app pages and 404 errors.  
   Redirects to `index.php` if the user is not logged in.  
   Note: to show an alert message (success, error, whatever), set the GET argument `msg` to a message ID from `lang/messages.php`.
* pages.php  
   Define app pages/screens in an array.  The page ID/array key is assumed to exist as a file `pages/{key}.php`, or it will 404.  
   __Optional parameters:__  
      `'navbar' => true` will show the page as a button in the app menu bar.  
      `'icon' => '...'` will show an icon from FontAwesome in the menu bar.  Setting this to `home` will show the icon `fa-home`.  
      `'styles' => ["file.css"]` will inject the listed CSS files into the page header (after all other CSS, like Bootstrap).  
      `'scripts' => ["file.js"]` will inject the listed JavaScript files into the page footer (after jQuery and other builtin scripts).
* lang/messages.php  
   Array of alert messages.
   `"string"` is the language string for the message, `"type"` is one of `success`, `info`, `warning`, or `danger` (i.e. Bootstrap alert classes).
   Changing the type changes the icon and color of the alert box.
*lang/en_us.php  
   Language data for US English.
*lib/login.php  
   Functions for logging in users and stuff like that.  Most functions transparently makes requests to the Portal API and return the results.
*lib/userinfo.php  
   Functions for getting user data, like real names and managed employees.
*static/css/app.css  
   Custom styles for the app.  See the comments inside for instructions on theming the app.

License
-------

tl;dr: MIT license, but also don't use our name in ads and stuff.

Copyright (C) 2017 Netsyms Technologies.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL NETSYMS TECHNOLOGIES BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the name and other identifying marks of Netsyms Technologies shall not be used in advertising or otherwise to promote the sale, use or other dealings in this Software without prior written authorization from Netsyms Technologies.