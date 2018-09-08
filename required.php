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

$captcha_server = (CAPTCHA_ENABLED === true ? preg_replace("/http(s)?:\/\//", "", CAPTCHA_SERVER) : "");
if ($_SESSION['mobile'] === TRUE) {
    header("Content-Security-Policy: "
            . "default-src 'self';"
            . "object-src 'none'; "
            . "img-src * data:; "
            . "media-src 'self'; "
            . "frame-src 'none'; "
            . "font-src 'self'; "
            . "connect-src *; "
            . "style-src 'self' 'unsafe-inline' $captcha_server; "
            . "script-src 'self' 'unsafe-inline' $captcha_server");
} else {
    header("Content-Security-Policy: "
            . "default-src 'self';"
            . "object-src 'none'; "
            . "img-src * data:; "
            . "media-src 'self'; "
            . "frame-src 'none'; "
            . "font-src 'self'; "
            . "connect-src *; "
            . "style-src 'self' 'nonce-$SECURE_NONCE' $captcha_server; "
            . "script-src 'self' 'nonce-$SECURE_NONCE' $captcha_server");
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

$Strings = new Strings(LANGUAGE);

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

date_default_timezone_set(TIMEZONE);

// Database settings
// Also inits database and stuff
use Medoo\Medoo;

$database;
try {
    $database = new Medoo([
        'database_type' => DB_TYPE,
        'database_name' => DB_NAME,
        'server' => DB_SERVER,
        'username' => DB_USER,
        'password' => DB_PASS,
        'charset' => DB_CHARSET
    ]);
} catch (Exception $ex) {
    //header('HTTP/1.1 500 Internal Server Error');
    sendError("Database error.  Try again later.  $ex");
}


if (!DEBUG) {
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

/**
 * Checks if a string or whatever is empty.
 * @param $str The thingy to check
 * @return boolean True if it's empty or whatever.
 */
function is_empty($str) {
    return (is_null($str) || !isset($str) || $str == '');
}


function dieifnotloggedin() {
    if ($_SESSION['loggedin'] != true) {
        sendError("Session expired.  Please log out and log in again.");
        die();
    }
    if ((new User($_SESSION['uid']))->hasPermission("INV_VIEW") == FALSE) {
        die("You don't have permission to be here.");
    }
}

/**
 * Check if the previous database action had a problem.
 * @param array $specials int=>string array with special response messages for SQL errors
 */
function checkDBError($specials = []) {
    global $database;
    $errors = $database->error();
    if (!is_null($errors[1])) {
        foreach ($specials as $code => $text) {
            if ($errors[1] == $code) {
                sendError($text);
            }
        }
        sendError("A database error occurred:<br /><code>" . $errors[2] . "</code>");
    }
}

/*
 * http://stackoverflow.com/a/20075147
 */
if (!function_exists('base_url')) {

    function base_url($atRoot = FALSE, $atCore = FALSE, $parse = FALSE) {
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), NULL, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf($tmplt, $http, $hostname, $end);
        } else
            $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path']))
                if ($base_url['path'] == '/')
                    $base_url['path'] = '';
        }

        return $base_url;
    }

}

function redirectIfNotLoggedIn() {
    if ($_SESSION['loggedin'] !== TRUE) {
        header('Location: ./index.php');
        die();
    }
    if ((new User($_SESSION['uid']))->hasPermission("INV_VIEW") == FALSE) {
        header('Location: ./index.php?permissionerror');
        die("You don't have permission to be here.");
    }
}
