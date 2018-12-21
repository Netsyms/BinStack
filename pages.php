<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "Home",
        "navbar" => true,
        "icon" => "fas fa-home"
    ],
    "404" => [
        "title" => "404 error"
    ],
    "form" => [
        "title" => "Form",
        "navbar" => true,
        "icon" => "fas fa-file-alt",
        "scripts" => [
            "static/js/form.js"
        ]
    ]
]);