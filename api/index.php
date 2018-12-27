<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require __DIR__ . '/../required.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/apisettings.php';

$VARS = $_GET;
if ($_SERVER['REQUEST_METHOD'] != "GET") {
    $VARS = array_merge($VARS, $_POST);
}

$requestbody = file_get_contents('php://input');
$requestjson = json_decode($requestbody, TRUE);
if (json_last_error() == JSON_ERROR_NONE) {
    $VARS = array_merge($VARS, $requestjson);
}

// If we're not using the old api.php file, allow more flexible requests
if (strpos($_SERVER['REQUEST_URI'], "/api.php") === FALSE) {
    $route = explode("/", substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "api/") + 4));

    if (count($route) >= 1) {
        $VARS["action"] = $route[0];
    }
    if (count($route) >= 2 && strpos($route[1], "?") !== 0) {
        for ($i = 1; $i < count($route); $i++) {
            if (empty($route[$i]) || strpos($route[$i], "=") === false) {
                continue;
            }
            $key = explode("=", $route[$i], 2)[0];
            $val = explode("=", $route[$i], 2)[1];
            $VARS[$key] = $val;
        }
    }

    if (strpos($route[count($route) - 1], "?") === 0) {
        $morevars = explode("&", substr($route[count($route) - 1], 1));
        foreach ($morevars as $var) {
            $key = explode("=", $var, 2)[0];
            $val = explode("=", $var, 2)[1];
            $VARS[$key] = $val;
        }
    }
}

if (!authenticate()) {
    header('WWW-Authenticate: Basic realm="' . $SETTINGS['site_title'] . '"');
    header('HTTP/1.1 401 Unauthorized');
    die("401 Unauthorized: you need to supply valid credentials.");
}

if (empty($VARS['action'])) {
    http_response_code(404);
    die("404 No action specified");
}

if (!isset($APIS[$VARS['action']])) {
    http_response_code(404);
    die("404 Action not defined");
}

$APIACTION = $APIS[$VARS["action"]];

if (!file_exists(__DIR__ . "/actions/" . $APIACTION["load"])) {
    http_response_code(404);
    die("404 Action not found");
}

if (!empty($APIACTION["vars"])) {
    checkVars($APIACTION["vars"]);
}

require_once __DIR__ . "/actions/" . $APIACTION["load"];
