<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * This file contains global settings and utility functions.
 */
ob_start(); // allow sending headers after content
// Settings file
require __DIR__ . '/settings.php';

// Unicode, solves almost all stupid encoding problems
header('Content-Type: text/html; charset=utf-8');

// Strip PHP version
header('X-Powered-By: PHP');

// Security
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('X-Frame-Options: "DENY"');
header('Referrer-Policy: "no-referrer, strict-origin-when-cross-origin"');
$SECURE_NONCE = base64_encode(random_bytes(8));

$session_length = 60 * 60 * 1; // 1 hour
ini_set('session.gc_maxlifetime', $session_length);
session_set_cookie_params($session_length, "/", null, false, false);

session_start(); // stick some cookies in it
// renew session cookie
setcookie(session_name(), session_id(), time() + $session_length, "/", false, false);

if (isset($_SESSION['mobile']) && $_SESSION['mobile'] === TRUE) {
    header("Content-Security-Policy: "
            . "default-src 'self';"
            . "object-src 'none'; "
            . "img-src * data:; "
            . "media-src 'self'; "
            . "frame-src 'none'; "
            . "font-src 'self'; "
            . "connect-src *; "
            . "style-src 'self' 'unsafe-inline'; "
            . "script-src 'self' 'unsafe-inline'");
} else {
    header("Content-Security-Policy: "
            . "default-src 'self';"
            . "object-src 'none'; "
            . "img-src * data:; "
            . "media-src 'self'; "
            . "frame-src 'none'; "
            . "font-src 'self'; "
            . "connect-src *; "
            . "style-src 'self' 'nonce-$SECURE_NONCE'; "
            . "script-src 'self' 'nonce-$SECURE_NONCE'");
}

//
// Composer
require __DIR__ . '/vendor/autoload.php';

// List of alert messages
require __DIR__ . '/langs/messages.php';

$libs = glob(__DIR__ . "/lib/*.lib.php");
foreach ($libs as $lib) {
    require_once $lib;
}

$Strings = new Strings($SETTINGS['language']);

/**
 * Kill off the running process and spit out an error message
 * @param string $error error message
 */
function sendError($error) {
    global $SECURE_NONCE;
    die("<!DOCTYPE html>"
            . "<meta charset=\"UTF-8\">"
            . "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">"
            . "<title>Error</title>"
            . "<style nonce=\"" . $SECURE_NONCE . "\">"
            . "h1 {color: red; font-family: sans-serif; font-size: 20px; margin-bottom: 0px;} "
            . "h2 {font-family: sans-serif; font-size: 16px;} "
            . "p {font-family: monospace; font-size: 14px; width: 100%; wrap-style: break-word;} "
            . "i {font-size: 12px;}"
            . "</style>"
            . "<h1>A fatal application error has occurred.</h1>"
            . "<i>(This isn't your fault.)</i>"
            . "<h2>Details:</h2>"
            . "<p>" . htmlspecialchars($error) . "</p>");
}

date_default_timezone_set($SETTINGS['timezone']);

// Database settings
// Also inits database and stuff
use Medoo\Medoo;

$database;
try {
    $database = new Medoo([
        'database_type' => $SETTINGS['database']['type'],
        'database_name' => $SETTINGS['database']['name'],
        'server' => $SETTINGS['database']['server'],
        'username' => $SETTINGS['database']['user'],
        'password' => $SETTINGS['database']['password'],
        'charset' => $SETTINGS['database']['charset'],
        'option' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    ]);
} catch (Exception $ex) {
    //header('HTTP/1.1 500 Internal Server Error');
    sendError("Database error.  Try again later.  $ex");
}


if (!$SETTINGS['debug']) {
    error_reporting(0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}


$VARS;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $VARS = $_POST;
    define("GET", false);
} else {
    $VARS = $_GET;
    define("GET", true);
}

function dieifnotloggedin() {
    global $SETTINGS;
    if ($_SESSION['loggedin'] != true) {
        sendError("Session expired.  Please log out and log in again.");
        die();
    }
    if ((new User($_SESSION['uid']))->hasPermission("INV_VIEW") == FALSE) {
        die("You don't have permission to be here.");
    }
    $user = new User($_SESSION['uid']);
    foreach ($SETTINGS['permissions'] as $perm) {
        if (!$user->hasPermission($perm)) {
            session_destroy();
            die("You don't have permission to be here.");
        }
    }
}

function redirectIfNotLoggedIn() {
    global $SETTINGS;
    if ($_SESSION['loggedin'] !== TRUE) {
        header('Location: ' . $SETTINGS['url'] . '/index.php');
        die();
    }
    $user = new User($_SESSION['uid']);
    foreach ($SETTINGS['permissions'] as $perm) {
        if (!$user->hasPermission($perm)) {
            session_destroy();
            header('Location: ./index.php');
            die("You don't have permission to be here.");
        }
    }
}
