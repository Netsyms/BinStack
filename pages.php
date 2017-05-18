<?php

// List of pages and metadata
define("PAGES", [
    "home" => [
        "title" => "home",
        "navbar" => true,
        "icon" => "home"
    ],
    "items" => [
        "title" => "items",
        "navbar" => true,
        "icon" => "cubes",
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
        "icon" => "map-marker",
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
        "icon" => "archive",
        "styles" => [
            "static/css/datatables.min.css"
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
    "404" => [
        "title" => "404 error"
    ]
]);
