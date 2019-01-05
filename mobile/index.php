<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/*
 * Mobile app API
 */

require __DIR__ . "/../required.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Allow ping check without authentication
if ($VARS['action'] == "ping") {
    exit(json_encode(["status" => "OK"]));
}

function mobile_enabled() {
    $resp = AccountHubApi::get("mobileenabled");
    if ($resp['status'] == "OK" && $resp['mobile'] === TRUE) {
        return true;
    } else {
        return false;
    }
}

function mobile_valid($username, $code) {
    try {
        $resp = AccountHubApi::get("mobilevalid", ["code" => $code, "username" => $username], true);

        if ($resp['status'] == "OK" && $resp['valid'] === TRUE) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $ex) {
        return false;
    }
}

if (mobile_enabled() !== TRUE) {
    exit(json_encode(["status" => "ERROR", "msg" => $Strings->get("mobile login disabled", false)]));
}

// Make sure we have a username and access key
if (empty($VARS['username']) || empty($VARS['key'])) {
    http_response_code(401);
    die(json_encode(["status" => "ERROR", "msg" => "Missing username and/or access key."]));
}

// Make sure the username and key are actually legit
if (!mobile_valid($VARS['username'], $VARS['key'])) {
    engageRateLimit();
    http_response_code(401);
    die(json_encode(["status" => "ERROR", "msg" => "Invalid username and/or access key."]));
}

// Process the action
switch ($VARS['action']) {
    case "start_session":
        // Do a web login.
        $user = User::byUsername($VARS['username']);
        if ($user->exists()) {
            if ($user->getStatus()->getString() == "NORMAL") {
                if ($user->checkPassword($VARS['password'])) {
                    foreach ($SETTINGS['permissions'] as $perm) {
                        if (!$user->hasPermission($perm)) {
                            exit(json_encode(["status" => "ERROR", "msg" => $Strings->get("no permission", false)]));
                        }
                    }
                    Session::start($user);
                    $_SESSION['mobile'] = true;
                    exit(json_encode(["status" => "OK"]));
                }
            }
        }
        exit(json_encode(["status" => "ERROR", "msg" => $Strings->get("login incorrect", false)]));
    default:
        http_response_code(404);
        die(json_encode(["status" => "ERROR", "msg" => "The requested action is not available."]));
}
