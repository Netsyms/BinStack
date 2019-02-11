<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Build and send a simple JSON response.
 * @param string $msg A message
 * @param string $status "OK" or "ERROR"
 * @param array $data More JSON data
 */
function sendJsonResp(string $msg = null, string $status = "OK", array $data = null) {
    $resp = [];
    if (!is_null($data)) {
        $resp = $data;
    }
    if (!is_null($msg)) {
        $resp["msg"] = $msg;
    }
    $resp["status"] = $status;
    header("Content-Type: application/json");
    exit(json_encode($resp));
}

function exitWithJson(array $json) {
    header("Content-Type: application/json");
    exit(json_encode($json));
}

/**
 * Get the API key with most of the characters replaced with *s.
 * @global string $key
 * @return string
 */
function getCensoredKey() {
    global $key;
    $resp = $key;
    if (strlen($key) > 5) {
        for ($i = 2; $i < strlen($key) - 2; $i++) {
            $resp[$i] = "*";
        }
    }
    return $resp;
}

/**
 * Check if the request is allowed
 * @global array $VARS
 * @return bool true if the request should continue, false if the request is bad
 */
function authenticate(): bool {
    global $VARS;
    // HTTP basic auth
    if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
    } else if (!empty($VARS['username']) && !empty($VARS['password'])) {
        $username = $VARS['username'];
        $password = $VARS['password'];
    } else {
        return false;
    }
    $user = User::byUsername($username);
    if (!$user->exists()) {
        return false;
    }
    if ($user->checkPassword($password, true)) {
        return true;
    }
    return false;
}

/**
 * Get the User whose credentials were used to make the request.
 */
function getRequestUser(): User {
    global $VARS;
    if (!empty($_SERVER['PHP_AUTH_USER'])) {
        return User::byUsername($_SERVER['PHP_AUTH_USER']);
    } else {
        return User::byUsername($VARS['username']);
    }
}

function checkVars($vars, $or = false) {
    global $VARS;
    $ok = [];
    foreach ($vars as $key => $val) {
        if (strpos($key, "OR") === 0) {
            checkVars($vars[$key], true);
            continue;
        }

        // Only check type of optional variables if they're set, and don't
        // mark them as bad if they're not set
        if (strpos($key, " (optional)") !== false) {
            $key = str_replace(" (optional)", "", $key);
            if (empty($VARS[$key])) {
                continue;
            }
        } else {
            if (empty($VARS[$key])) {
                $ok[$key] = false;
                continue;
            }
        }

        if (strpos($val, "/") === 0) {
            // regex
            $ok[$key] = preg_match($val, $VARS[$key]) === 1;
        } else {
            $checkmethod = "is_$val";
            $ok[$key] = !($checkmethod($VARS[$key]) !== true);
        }
    }
    if ($or) {
        $success = false;
        $bad = "";
        foreach ($ok as $k => $v) {
            if ($v) {
                $success = true;
                break;
            } else {
                $bad = $k;
            }
        }
        if (!$success) {
            http_response_code(400);
            die("400 Bad request: variable $bad is missing or invalid");
        }
    } else {
        foreach ($ok as $key => $bool) {
            if (!$bool) {
                http_response_code(400);
                die("400 Bad request: variable $key is missing or invalid");
            }
        }
    }
}
