<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "home",
        "navbar" => true,
        "icon" => "fas fa-home"
    ],
    "items" => [
        "title" => "items",
        "navbar" => true,
        "icon" => "fas fa-cubes",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/items.js"
        ],
    ],
    "locations" => [
        "title" => "locations",
        "navbar" => true,
        "icon" => "fas fa-map-marker",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/locations.js"
        ],
    ],
    "categories" => [
        "title" => "categories",
        "navbar" => true,
        "icon" => "fas fa-archive",
        "styles" => [
            "static/css/datatables.min.css",
            "static/css/tables.css"
        ],
        "scripts" => [
            "static/js/datatables.min.js",
            "static/js/categories.js"
        ],
    ],
    "edititem" => [
        "title" => "edit item",
        "navbar" => false,
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/edititem.js"
        ],
    ],
    "editcat" => [
        "title" => "edit category",
        "navbar" => false,
        "scripts" => [
            "static/js/editcat.js"
        ],
    ],
    "editloc" => [
        "title" => "edit location",
        "navbar" => false,
        "scripts" => [
            "static/js/editloc.js"
        ],
    ],
    "export" => [
        "title" => "report export",
        "navbar" => true,
        "icon" => "fas fa-download",
        "scripts" => [
            "static/js/export.js"
        ]
    ],
    "404" => [
        "title" => "404 error"
    ]
]);
