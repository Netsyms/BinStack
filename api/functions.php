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
 * @global type $VARS
 * @global type $database
 * @return bool true if the request should continue, false if the request is bad
 */
function authenticate(): bool {
    global $VARS, $database;
    if (empty($VARS['key'])) {
        return false;
    } else {
        $key = $VARS['key'];
        if ($database->has('apikeys', ['key' => $key]) !== TRUE) {
            engageRateLimit();
            http_response_code(403);
            Log::insert(LogType::API_BAD_KEY, null, "Key: " . $key);
            return false;
        }
    }
    return true;
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
        $checkmethod = "is_$val";
        if ($checkmethod($VARS[$key]) !== true) {
            $ok[$key] = false;
        } else {
            $ok[$key] = true;
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
