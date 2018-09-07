<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

// Fill these in with valid credentials for an account with NORMAL status
$valid_user = "";
$valid_pass = "";

require __DIR__ . "/../required.php";
error_reporting(E_ALL);
ini_set('display_errors', 'On');
header("Content-Type: text/plain");

// Test invalid user responses

$user = new User(784587254);
if ($user->exists()) {
    echo "FAIL: Invalid user ID marked as existing\n";
} else {
    echo "OK\n";
}
if ($user->getUID() != 784587254) {
    echo "FAIL: Invalid user has mismatched UID\n";
} else {
    echo "OK\n";
}

$user = User::byUsername("r9483yt8934t");
if ($user->exists()) {
    echo "FAIL: Invalid username marked as existing\n";
} else {
    echo "OK\n";
}

if ($user->checkPassword("gbirg4wre") != false) {
    echo "FAIL: Invalid user and invalid password allowed\n";
} else {
    echo "OK\n";
}

if ($user->has2fa() != false) {
    echo "FAIL: Invalid user has 2fa\n";
} else {
    echo "OK\n";
}

if ($user->getUsername() != "r9483yt8934t") {
    echo "FAIL: Invalid user has mismatched username\n";
} else {
    echo "OK\n";
}

if ($user->getStatus()->get() != 0) {
    echo "FAIL: Invalid user has real account status\n";
} else {
    echo "OK\n";
}

if ($user->getStatus()->getString() != "OTHER_0") {
    echo "FAIL: Invalid user has wrong account status string\n";
} else {
    echo "OK\n";
}

// Test valid user responses

$user = User::byUsername($valid_user);
if (!$user->exists()) {
    echo "FAIL: Valid user does not exist\n";
} else {
    echo "OK\n";
}

if ($user->checkPassword($valid_pass) !== true) {
    echo "FAIL: Valid user and password not allowed\n";
} else {
    echo "OK\n";
}

if ($user->getUsername() != $valid_user) {
    echo "FAIL: Valid user has mismatched username\n";
} else {
    echo "OK\n";
}

if ($user->getStatus()->getString() != "NORMAL") {
    echo "FAIL: Valid user has wrong account status string\n";
} else {
    echo "OK\n";
}

exit("ALL OK");