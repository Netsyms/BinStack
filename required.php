<?php

/**
 * This file contains global settings and utility functions.
 */
ob_start(); // allow sending headers after content
// Unicode, solves almost all stupid encoding problems
header('Content-Type: text/html; charset=utf-8');

// l33t $ecurity h4x
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
$session_length = 60 * 60; // 1 hour
session_set_cookie_params($session_length, "/", null, false, true);

session_start(); // stick some cookies in it
// renew session cookie
setcookie(session_name(), session_id(), time() + $session_length);
//
// Composer
require __DIR__ . '/vendor/autoload.php';

// Settings file
require __DIR__ . '/settings.php';
// List of alert messages
require __DIR__ . '/lang/messages.php';
// text strings (i18n)
require __DIR__ . '/lang/' . LANGUAGE . ".php";

/**
 * Kill off the running process and spit out an error message
 * @param string $error error message
 */
function sendError($error) {
    die("<!DOCTYPE html><html><head><title>Error</title></head><body><h1 style='color: red; font-family: sans-serif; font-size:100%;'>" . htmlspecialchars($error) . "</h1></body></html>");
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

/**
 * I18N string getter.  If the key doesn't exist, outputs the key itself.
 * @param string $key I18N string key
 * @param boolean $echo whether to echo the result or return it (default echo)
 */
function lang($key, $echo = true) {
    if (array_key_exists($key, STRINGS)) {
        $str = STRINGS[$key];
    } else {
        $str = $key;
    }

    if ($echo) {
        echo $str;
    } else {
        return $str;
    }
}

/**
 * I18N string getter (with builder).    If the key doesn't exist, outputs the key itself.
 * @param string $key I18N string key
 * @param array $replace key-value array of replacements.
 * If the string value is "hello {abc}" and you give ["abc" => "123"], the
 * result will be "hello 123".
 * @param boolean $echo whether to echo the result or return it (default echo)
 */
function lang2($key, $replace, $echo = true) {
    if (array_key_exists($key, STRINGS)) {
        $str = STRINGS[$key];
    } else {
        $str = $key;
    }

    foreach ($replace as $find => $repl) {
        $str = str_replace("{" . $find . "}", $repl, $str);
    }

    if ($echo) {
        echo $str;
    } else {
        return $str;
    }
}

function dieifnotloggedin() {
    if ($_SESSION['loggedin'] != true) {
        sendError("Session expired.  Please log out and log in again.");
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
 * http://stackoverflow.com/a/20075147/2534036
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
        header('Location: ' . URL . '/index.php');
        die();
    }
}
