<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

// Whether to show debugging data in output.
// DO NOT SET TO TRUE IN PRODUCTION!!!
define("DEBUG", false);

// Database connection settings
// See http://medoo.in/api/new for info
define("DB_TYPE", "mysql");
define("DB_NAME", "inventory");
define("DB_SERVER", "localhost");
define("DB_USER", "inventory");
define("DB_PASS", "");
define("DB_CHARSET", "utf8");

// Name of the app.
define("SITE_TITLE", "BinStack");

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
define('URL', 'http://localhost/inventory');

// Use reCAPTCHA on login screen
// https://www.google.com/recaptcha/
define("RECAPTCHA_ENABLED", FALSE);
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// See lang folder for language options
define('LANGUAGE', "en_us");


define("FOOTER_TEXT", "");
define("COPYRIGHT_NAME", "Netsyms Technologies");
//////////////////////////////////////////////////////////////