<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

$SETTINGS = [
    "debug" => false,
    "database" => [
        "type" => "mysql",
        "name" => "app",
        "server" => "localhost",
        "user" => "app",
        "password" => "",
        "charset" => "utf8"
    ],
    "site_title" => "Web App Template",
    "accounthub" => [
        "api" => "http://localhost/accounthub/api/",
        "home" => "http://localhost/accounthub/home.php",
        "key" => "123"
    ],
    "timezone" => "America/Denver",
    "captcha" => [
        "enabled" => false,
        "server" => "https://captcheck.netsyms.com"
    ],
    "language" => "en",
    "footer_text" => "",
    "copyright" => "Netsyms Technologies",
    "url" => "."
];