<?php

/**
 * Make things happen when buttons are pressed and forms submitted.
 */

require_once __DIR__ . "/required.php";

dieifnotloggedin();

function returnToSender($msg, $arg = "") {
    global $VARS;
    if ($arg == "") {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=" . $msg);
    } else {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=$msg&arg=$arg");
    }
    die();
}

switch ($VARS['action']) {
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
}