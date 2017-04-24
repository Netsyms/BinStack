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

define("SITE_TITLE", "Web App Template");

// Used to identify the system in OTP and other places
define("SYSTEM_NAME", "Web App Template");

// For supported values, see http://php.net/manual/en/timezones.php
define("TIMEZONE", "America/Denver");

// Base URL for site links.
define('URL', 'http://localhost:8000/');

// See lang folder for language options
define('LANGUAGE', "en_us");

// Minimum length for new passwords
// The system checks new passwords against the 500 worst passwords and rejects
// any matches.
// If you want to have additional password requirements, go edit action.php.
// However, all that does is encourage people to use the infamous 
// "post-it password manager".  See also https://xkcd.com/936/ and
// http://stackoverflow.com/a/34166252/2534036 for reasons why forcing passwords
// like CaPs45$% is not actually a great idea.
// Encourage users to use 2-factor auth whenever possible.
define("MIN_PASSWORD_LENGTH", 8);

//////////////////////////////////////////////////////////////
//  /!\       Warning: Changing these values may       /!\  //
//  /!\  violate the terms of your license agreement!  /!\  //
//////////////////////////////////////////////////////////////
define("LICENSE_TEXT", "<b>Free Software: MIT License</b>");
define("COPYRIGHT_NAME", "Netsyms Technologies");
//////////////////////////////////////////////////////////////