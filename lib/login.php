<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * Authentication and account functions.  Connects to an AccountHub instance.
 */

/**
 * Check the login server API for sanity
 * @return boolean true if OK, else false
 */
function checkLoginServer() {
    try {
        $client = new GuzzleHttp\Client();

        $response = $client
                ->request('POST', PORTAL_API, [
            'form_params' => [
                'key' => PORTAL_KEY,
                'action' => "ping"
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            return false;
        }

        $resp = json_decode($response->getBody(), TRUE);
        if ($resp['status'] == "OK") {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Checks if the given AccountHub API key is valid by attempting to
 * access the API with it.
 * @param String $key The API key to check
 * @return boolean TRUE if the key is valid, FALSE if invalid or something went wrong
 */
function checkAPIKey($key) {
    try {
        $client = new GuzzleHttp\Client();

        $response = $client
                ->request('POST', PORTAL_API, [
            'form_params' => [
                'key' => $key,
                'action' => "ping"
            ]
        ]);

        if ($response->getStatusCode() === 200) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////////////
//                           Account handling                                 //
////////////////////////////////////////////////////////////////////////////////

/**
 * Checks the given credentials against the API.
 * @param string $username
 * @param string $password
 * @return boolean True if OK, else false
 */
function authenticate_user($username, $password, &$errmsg) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "auth",
            'username' => $username,
            'password' => $password
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return true;
    } else {
        $errmsg = $resp['msg'];
        return false;
    }
}

/**
 * Check if a username exists.
 * @param String $username
 */
function user_exists($username) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "userexists",
            'username' => $username
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK" && $resp['exists'] === true) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if a UID exists.
 * @param String $uid
 */
function uid_exists($uid) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "userexists",
            'uid' => $uid
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK" && $resp['exists'] === true) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get the account status: NORMAL, TERMINATED, LOCKED_OR_DISABLED,
 * CHANGE_PASSWORD, or ALERT_ON_ACCESS
 * @param string $username
 * @return string
 */
function get_account_status($username) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "acctstatus",
            'username' => $username
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['account'];
    } else {
        return false;
    }
}

/**
 * Check if the given username has the given permission (or admin access)
 * @param string $username
 * @param string $permcode
 * @return boolean TRUE if the user has the permission (or admin access), else FALSE
 */
function account_has_permission($username, $permcode) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "permission",
            'username' => $username,
            'code' => $permcode
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['has_permission'];
    } else {
        return false;
    }
}

////////////////////////////////////////////////////////////////////////////////
//                              Login handling                                //
////////////////////////////////////////////////////////////////////////////////

/**
 * Setup $_SESSION values with user data and set loggedin flag to true
 * @param string $username
 */
function doLoginUser($username) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "userinfo",
            'username' => $username
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);

    if ($resp['status'] == "OK") {
        $userinfo = $resp['data'];
        session_regenerate_id(true);
        $newSession = session_id();
        session_write_close();
        session_id($newSession);
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['uid'] = $userinfo['uid'];
        $_SESSION['email'] = $userinfo['email'];
        $_SESSION['realname'] = $userinfo['name'];
        $_SESSION['loggedin'] = true;
        return true;
    } else {
        return false;
    }
}

function sendLoginAlertEmail($username) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "alertemail",
            'username' => $username,
            'appname' => SITE_TITLE
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        return "An unknown error occurred.";
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return true;
    } else {
        return $resp['msg'];
    }
}

function simLogin($username, $password) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "login",
            'username' => $username,
            'password' => $password
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return true;
    } else {
        return $resp['msg'];
    }
}

function verifyCaptcheck($session, $answer, $url) {
    $data = [
        'session_id' => $session,
        'answer_id' => $answer,
        'action' => "verify"
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resp = json_decode($result, TRUE);
    if (!$resp['result']) {
        return false;
    } else {
        return true;
    }
}

////////////////////////////////////////////////////////////////////////////////
//                          2-factor authentication                           //
////////////////////////////////////////////////////////////////////////////////

/**
 * Check if a user has TOTP setup
 * @param string $username
 * @return boolean true if TOTP secret exists, else false
 */
function userHasTOTP($username) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "hastotp",
            'username' => $username
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['otp'];
    } else {
        return false;
    }
}

/**
 * Verify a TOTP multiauth code
 * @global $database
 * @param string $username
 * @param int $code
 * @return boolean true if it's legit, else false
 */
function verifyTOTP($username, $code) {
    $client = new GuzzleHttp\Client();

    $response = $client
            ->request('POST', PORTAL_API, [
        'form_params' => [
            'key' => PORTAL_KEY,
            'action' => "verifytotp",
            'username' => $username,
            'code' => $code
        ]
    ]);

    if ($response->getStatusCode() > 299) {
        sendError("Login server error: " . $response->getBody());
    }

    $resp = json_decode($response->getBody(), TRUE);
    if ($resp['status'] == "OK") {
        return $resp['valid'];
    } else {
        return false;
    }
}
