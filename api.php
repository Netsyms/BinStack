<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


/**
 * Simple JSON API to allow other apps to access data from this app.
 * 
 * Requests can be sent via either GET or POST requests.  POST is recommended
 * as it has a lower chance of being logged on the server, exposing unencrypted
 * user passwords.
 */
require __DIR__ . '/required.php';
require_once __DIR__ . '/lib/login.php';
require_once __DIR__ . '/lib/userinfo.php';
header("Content-Type: application/json");

$username = $VARS['username'];
$password = $VARS['password'];
if (user_exists($username) !== true || authenticate_user($username, $password, $errmsg) !== true) {
    header("HTTP/1.1 403 Unauthorized");
    die("\"403 Unauthorized\"");
}
$userinfo = getUserByUsername($username);

// query max results
$max = 20;
if (preg_match("/^[0-9]+$/", $VARS['max']) === 1 && $VARS['max'] <= 1000) {
    $max = (int) $VARS['max'];
}

switch ($VARS['action']) {
    case "ping":
        $out = ["status" => "OK", "maxresults" => $max, "pong" => true];
        exit(json_encode($out));
    default:
        header("HTTP/1.1 400 Bad Request");
        die("\"400 Bad Request\"");
}