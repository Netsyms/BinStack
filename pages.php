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
    "items" => [
        "title" => "Items",
        "navbar" => true,
        "icon" => "fas fa-boxes",
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
        "title" => "Locations",
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
        "title" => "Categories",
        "navbar" => true,
        "icon" => "fas fa-pallet",
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
        "title" => "Edit item",
        "navbar" => false,
        "styles" => [
            "static/css/easy-autocomplete.min.css"
        ],
        "scripts" => [
            "static/js/jquery.easy-autocomplete.min.js",
            "static/js/edititem.js"
        ],
    ],
    "editimages" => [
        "title" => "Edit item images",
        "navbar" => false,
        "styles" => [
            "static/css/files.css"
        ],
        "scripts" => [
            "static/js/files.js"
        ]
    ],
    "editcat" => [
        "title" => "Edit category",
        "navbar" => false,
        "scripts" => [
            "static/js/editcat.js"
        ],
    ],
    "editloc" => [
        "title" => "Edit location",
        "navbar" => false,
        "scripts" => [
            "static/js/editloc.js"
        ],
    ],
    "export" => [
        "title" => "Reports/Export",
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
