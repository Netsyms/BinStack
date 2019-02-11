<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class User {

    private $uid = null;
    private $username;
    private $email;
    private $realname;
    private $has2fa = false;
    private $exists = false;

    public function __construct(int $uid, string $username = "") {
        // Check if user exists
        $resp = AccountHubApi::get("userexists", ["uid" => $uid]);
        if ($resp['status'] == "OK" && $resp['exists'] === true) {
            $this->exists = true;
        } else {
            $this->uid = $uid;
            $this->username = $username;
            $this->exists = false;
        }

        if ($this->exists) {
            // Get user info
            $resp = AccountHubApi::get("userinfo", ["uid" => $uid]);
            if ($resp['status'] == "OK") {
                $this->uid = $resp['data']['uid'] * 1;
                $this->username = $resp['data']['username'];
                $this->email = $resp['data']['email'];
                $this->realname = $resp['data']['name'];
            } else {
                sendError("Login server error: " . $resp['msg']);
            }
        }
    }

    public static function byUsername(string $username): User {
        $resp = AccountHubApi::get("userinfo", ["username" => $username]);
        if (!isset($resp['status'])) {
            sendError("Login server error: " . $resp);
        }
        if ($resp['status'] == "OK") {
            return new self($resp['data']['uid'] * 1);
        } else {
            return new self(-1, $username);
        }
    }

    public function exists(): bool {
        return $this->exists;
    }

    public function has2fa(): bool {
        if (!$this->exists) {
            return false;
        }

        $resp = AccountHubApi::get("hastotp", ['username' => $this->username]);
        if ($resp['status'] == "OK") {
            return $resp['otp'] == true;
        } else {
            return false;
        }
    }

    function getUsername() {
        return $this->username;
    }

    function getUID() {
        return $this->uid;
    }

    function getEmail() {
        return $this->email;
    }

    function getName() {
        return $this->realname;
    }

    /**
     * Check the given plaintext password against the stored hash.
     * @param string $password
     * @param bool $apppass Set to true to enforce app passwords when 2fa is on.
     * @return bool
     */
    function checkPassword(string $password, bool $apppass = false): bool {
        $resp = AccountHubApi::get("auth", ['username' => $this->username, 'password' => $password, 'apppass' => ($apppass ? "1" : "0")]);
        if ($resp['status'] == "OK") {
            return true;
        } else {
            return false;
        }
    }


    function check2fa(string $code): bool {
        if (!$this->has2fa) {
            return true;
        }

        $resp = AccountHubApi::get("verifytotp", ['username' => $this->username, 'code' => $code]);
        if ($resp['status'] == "OK") {
            return $resp['valid'];
        } else {
            return false;
        }
    }

    /**
     * Check if the given username has the given permission (or admin access)
     * @global $database $database
     * @param string $code
     * @return boolean TRUE if the user has the permission (or admin access), else FALSE
     */
    function hasPermission(string $code): bool {
        $resp = AccountHubApi::get("permission", ['username' => $this->username, 'code' => $code]);
        if ($resp['status'] == "OK") {
            return $resp['has_permission'];
        } else {
            return false;
        }
    }

    /**
     * Get the account status.
     * @return \AccountStatus
     */
    function getStatus(): AccountStatus {
        $resp = AccountHubApi::get("acctstatus", ['username' => $this->username]);
        if ($resp['status'] == "OK") {
            return AccountStatus::fromString($resp['account']);
        } else {
            return null;
        }
    }

    function sendAlertEmail(string $appname = null) {
        global $SETTINGS;
        if (is_null($appname)) {
            $appname = $SETTINGS['site_title'];
        }
        $resp = AccountHubApi::get("alertemail", ['username' => $this->username, 'appname' => $SETTINGS['site_title']]);

        if ($resp['status'] == "OK") {
            return true;
        } else {
            return $resp['msg'];
        }
    }

}

class AccountStatus {

    const NORMAL = 1;
    const LOCKED_OR_DISABLED = 2;
    const CHANGE_PASSWORD = 3;
    const TERMINATED = 4;
    const ALERT_ON_ACCESS = 5;

    private $status;

    public function __construct(int $status) {
        $this->status = $status;
    }

    public static function fromString(string $status): AccountStatus {
        switch ($status) {
            case "NORMAL":
                return new self(self::NORMAL);
            case "LOCKED_OR_DISABLED":
                return new self(self::LOCKED_OR_DISABLED);
            case "CHANGE_PASSWORD":
                return new self(self::CHANGE_PASSWORD);
            case "TERMINATED":
                return new self(self::TERMINATED);
            case "ALERT_ON_ACCESS":
                return new self(self::ALERT_ON_ACCESS);
            default:
                return new self(0);
        }
    }

    /**
     * Get the account status/state as an integer.
     * @return int
     */
    public function get(): int {
        return $this->status;
    }

    /**
     * Get the account status/state as a string representation.
     * @return string
     */
    public function getString(): string {
        switch ($this->status) {
            case self::NORMAL:
                return "NORMAL";
            case self::LOCKED_OR_DISABLED:
                return "LOCKED_OR_DISABLED";
            case self::CHANGE_PASSWORD:
                return "CHANGE_PASSWORD";
            case self::TERMINATED:
                return "TERMINATED";
            case self::ALERT_ON_ACCESS:
                return "ALERT_ON_ACCESS";
            default:
                return "OTHER_" . $this->status;
        }
    }

}
