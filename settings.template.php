<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

// Settings for the app.
// Copy to settings.php and customize.

$SETTINGS = [
    // Whether to output debugging info like PHP notices, warnings,
    // and stacktraces.
    // Turning this on in production is a security risk and can sometimes break
    // things, such as JSON output where extra content is not expected.
    "debug" => false,
    // Database connection settings
    // See http://medoo.in/api/new for info
    "database" => [
        "type" => "mysql",
        "name" => "app",
        "server" => "localhost",
        "user" => "app",
        "password" => "",
        "charset" => "utf8"
    ],
    // Name of the app.
    "site_title" => "Web App Template",
    // Settings for connecting to the AccountHub server.
    "accounthub" => [
        // URL for the API endpoint
        "api" => "http://localhost/accounthub/api/",
        // URL of the home page
        "home" => "http://localhost/accounthub/home.php",
        // API key
        "key" => "123"
    ],
    // For supported values, see http://php.net/manual/en/timezones.php
    "timezone" => "America/Denver",
    // Language to use for localization. See langs folder to add a language.
    "language" => "en",
    // Shown in the footer of all the pages.
    "footer_text" => "",
    // Also shown in the footer, but with "Copyright <current_year>" in front.
    "copyright" => "Netsyms Technologies",
    // Base URL for building links relative to the location of the app.
    // Only used when there's no good context for the path.
    // The default is almost definitely fine.
    "url" => "."
];
