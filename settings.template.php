<?php

// Whether to show debugging data in output.
// DO NOT SET TO TRUE IN PRODUCTION!!!
define("DEBUG", false);

// Database connection settings
// See http://medoo.in/api/new for info
define("DB_TYPE", "mysql");
define("DB_NAME", "app");
define("DB_SERVER", "localhost");
define("DB_USER", "app");
define("DB_PASS", "");
define("DB_CHARSET", "utf8");

// Name of the app.
define("SITE_TITLE", "Web App Template");

// Which pages to show the app icon on:
// index, app, both, none
define("SHOW_ICON", "both");
// Where to put the icon: top or menu
// Overridden to 'menu' if MENU_BAR_STYLE is 'fixed'.
define("ICON_POSITION", "menu");
// App menu bar style: fixed or static
define("MENU_BAR_STYLE", "fixed");

// URL of the Business Portal API endpoint
define("PORTAL_API", "http://localhost/accounthub/api.php");
// URL of the Portal home page
define("PORTAL_URL", "http://localhost/accounthub/home.php");
// Business Portal API Key
define("PORTAL_KEY", "123");

// For supported values, see http://php.net/manual/en/timezones.php
define("TIMEZONE", "America/Denver");

// Base URL for site links.
define('URL', 'http://localhost/app');

// Use reCAPTCHA on login screen
// https://www.google.com/recaptcha/
define("RECAPTCHA_ENABLED", FALSE);
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// See lang folder for language options
define('LANGUAGE', "en_us");

//////////////////////////////////////////////////////////////
//  /!\       Warning: Changing these values may       /!\  //
//  /!\  violate the terms of your license agreement!  /!\  //
//////////////////////////////////////////////////////////////
define("LICENSE_TEXT", "<b>Free Software: MIT License</b>");
define("COPYRIGHT_NAME", "Netsyms Technologies");
//////////////////////////////////////////////////////////////