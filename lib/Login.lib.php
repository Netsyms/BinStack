<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class Login {

    const BAD_USERPASS = 1;
    const BAD_2FA = 2;
    const ACCOUNT_DISABLED = 3;
    const LOGIN_OK = 4;

    public static function auth(string $username, string $password, string $twofa = ""): int {
        global $database;
        $username = strtolower($username);

        $user = User::byUsername($username);

        if (!$user->exists()) {
            return Login::BAD_USERPASS;
        }
        if (!$user->checkPassword($password)) {
            return Login::BAD_USERPASS;
        }

        if ($user->has2fa()) {
            if (!$user->check2fa($twofa)) {
                return Login::BAD_2FA;
            }
        }

        switch ($user->getStatus()->get()) {
            case AccountStatus::TERMINATED:
                return Login::BAD_USERPASS;
            case AccountStatus::LOCKED_OR_DISABLED:
                return Login::ACCOUNT_DISABLED;
            case AccountStatus::NORMAL:
            default:
                return Login::LOGIN_OK;
        }

        return Login::LOGIN_OK;
    }

    /**
     * Check the login server API for sanity
     * @return boolean true if OK, else false
     */
    public static function checkLoginServer() {
        try {
            $resp = AccountHubApi::get("ping");
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
            $resp = AccountHubApi::get("ping", null, true);
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

}
