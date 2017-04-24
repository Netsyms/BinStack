<?php

/**
 * Authentication and account functions
 */
use Base32\Base32;
use OTPHP\TOTP;
use LdapTools\LdapManager;
use LdapTools\Connection\ADResponseCodes;

////////////////////////////////////////////////////////////////////////////////
//                           Account handling                                 //
////////////////////////////////////////////////////////////////////////////////

/**
 * Add a user to the system.  /!\ Assumes input is OK /!\
 * @param string $username Username, saved in lowercase.
 * @param string $password Password, will be hashed before saving.
 * @param string $realname User's real legal name
 * @param string $email User's email address.
 * @return int The new user's ID number in the database.
 */
function adduser($username, $password, $realname, $email = null, $phone1 = "", $phone2 = "") {
    global $database;
    $database->debug()->insert('accounts', [
        'username' => strtolower($username),
        'password' => (is_null($password) ? null : encryptPassword($password)),
        'realname' => $realname,
        'email' => $email,
        'phone1' => $phone1,
        'phone2' => $phone2,
        'acctstatus' => 1
    ]);

    return $database->id();
}

/**
 * Get where a user's account actually is.
 * @param string $username
 * @return string "LDAP", "LOCAL", "LDAP_ONLY", or "NONE".
 */
function account_location($username, $password) {
    global $database;
    $user_exists = user_exists($username);
    if (!$user_exists && !LDAP_ENABLED) {
        return false;
    }
    if ($user_exists) {
        $userinfo = $database->select('accounts', ['password'], ['username' => $username])[0];
        // if password empty, it's an LDAP user
        if (is_empty($userinfo['password']) && LDAP_ENABLED) {
            return "LDAP";
        } else if (is_empty($userinfo['password']) && !LDAP_ENABLED) {
            return "NONE";
        } else {
            return "LOCAL";
        }
    } else {
        if (user_exists_ldap($username, $password)) {
            return "LDAP_ONLY";
        } else {
            return "NONE";
        }
    }
}

/**
 * Checks the given credentials against the database.
 * @param string $username
 * @param string $password
 * @return boolean True if OK, else false
 */
function authenticate_user($username, $password) {
    global $database;
    global $ldap_config;
    if (is_empty($username) || is_empty($password)) {
        return false;
    }
    $loc = account_location($username, $password);
    if ($loc == "NONE") {
        return false;
    } else if ($loc == "LOCAL") {
        $hash = $database->select('accounts', ['password'], ['username' => $username, "LIMIT" => 1])[0]['password'];
        return (comparePassword($password, $hash));
    } else if ($loc == "LDAP") {
        return authenticate_user_ldap($username, $password);
    } else if ($loc == "LDAP_ONLY") {
        if (authenticate_user_ldap($username, $password) === TRUE) {
            try {
                $user = (new LdapManager($ldap_config))->getRepository('user')->findOneByUsername($username);
                var_dump($user);
                adduser($user->getUsername(), null, $user->getName(), ($user->hasEmailAddress() ? $user->getEmailAddress() : null));
                return true;
            } catch (Exception $e) {
                sendError("LDAP error: " . $e->getMessage());
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Check if a username exists in the local database.
 * @param String $username
 */
function user_exists($username) {
    global $database;
    return $database->has('accounts', ['username' => $username, "LIMIT" => QUERY_LIMIT]);
}

/**
 * Get the account status: NORMAL, TERMINATED, LOCKED_OR_DISABLED,
 * CHANGE_PASSWORD, or ALERT_ON_ACCESS
 * @global $database $database
 * @param string $username
 * @return string
 */
function get_account_status($username) {
    global $database;
    $loc = account_location($username);
    if ($loc == "LOCAL") {
        $statuscode = $database->select('accounts', [
                    '[>]acctstatus' => [
                        'acctstatus' => 'statusid'
                    ]
                        ], [
                    'accounts.acctstatus',
                    'acctstatus.statuscode'
                        ], [
                    'username' => $username,
                    "LIMIT" => 1
                        ]
                )[0]['statuscode'];
        return $statuscode;
    } else if ($loc == "LDAP") {
        // TODO: Read actual account status from AD servers
        return "NORMAL";
    } else {
        // account isn't setup properly
        return "LOCKED_OR_DISABLED";
    }
}

////////////////////////////////////////////////////////////////////////////////
//                              Login handling                                //
////////////////////////////////////////////////////////////////////////////////

/**
 * Setup $_SESSION values to log in a user
 * @param string $username
 */
function doLoginUser($username, $password) {
    global $database;
    $userinfo = $database->select('accounts', ['email', 'uid', 'realname'], ['username' => $username])[0];
    $_SESSION['username'] = $username;
    $_SESSION['uid'] = $userinfo['uid'];
    $_SESSION['email'] = $userinfo['email'];
    $_SESSION['realname'] = $userinfo['realname'];
    $_SESSION['password'] = $password; // needed for things like EWS
    $_SESSION['loggedin'] = true;
}

/**
 * Send an alert email to the system admin
 * 
 * Used when an account with the status ALERT_ON_ACCESS logs in
 * @param String $username the account username
 */
function sendLoginAlertEmail($username) {
    // TODO: add email code
}

////////////////////////////////////////////////////////////////////////////////
//                              LDAP handling                                 //
////////////////////////////////////////////////////////////////////////////////

/**
 * Checks the given credentials against the LDAP server.
 * @param string $username
 * @param string $password
 * @return mixed True if OK, else false or the error code from the server
 */
function authenticate_user_ldap($username, $password) {
    global $ldap_config;
    if (is_empty($username) || is_empty($password)) {
        return false;
    }
    $ldapManager = new LdapManager($ldap_config);
    $msg = "";
    $code = 0;
    if ($ldapManager->authenticate($username, $password, $msg, $code)) {
        return true;
    } else {
        return $code;
    }
}

/**
 * Check if a username exists on the LDAP server.
 * @global type $ldap_config
 * @param type $username
 * @return boolean true if yes, else false
 */
function user_exists_ldap($username, $password) {
    global $ldap_config;
    $ldap = new LdapManager($ldap_config);
    if (!$ldap->authenticate($username, $password, $message, $code)) {
        switch ($code) {
            case ADResponseCodes::ACCOUNT_INVALID:
                return false;
            case ADResponseCodes::ACCOUNT_CREDENTIALS_INVALID:
                return true;
            case ADResponseCodes::ACCOUNT_RESTRICTIONS:
                return true;
            case ADResponseCodes::ACCOUNT_RESTRICTIONS_TIME:
                return true;
            case ADResponseCodes::ACCOUNT_RESTRICTIONS_DEVICE:
                return true;
            case ADResponseCodes::ACCOUNT_PASSWORD_EXPIRED:
                return true;
            case ADResponseCodes::ACCOUNT_DISABLED:
                return true;
            case ADResponseCodes::ACCOUNT_CONTEXT_IDS:
                return true;
            case ADResponseCodes::ACCOUNT_EXPIRED:
                return false;
            case ADResponseCodes::ACCOUNT_PASSWORD_MUST_CHANGE:
                return true;
            case ADResponseCodes::ACCOUNT_LOCKED:
                return true;
            default:
                return false;
        }
    }
    return true;
}

////////////////////////////////////////////////////////////////////////////////
//                          2-factor authentication                           //
////////////////////////////////////////////////////////////////////////////////

/**
 * Check if a user has TOTP setup
 * @global $database $database
 * @param string $username
 * @return boolean true if TOTP secret exists, else false
 */
function userHasTOTP($username) {
    global $database;
    $secret = $database->select('accounts', 'authsecret', ['username' => $username])[0];
    if (is_empty($secret)) {
        return false;
    }
    return true;
}

/**
 * Generate a TOTP secret for the given user.
 * @param string $username
 * @return string OTP provisioning URI (for generating a QR code)
 */
function newTOTP($username) {
    global $database;
    $secret = random_bytes(20);
    $encoded_secret = Base32::encode($secret);
    $userdata = $database->select('accounts', ['email', 'authsecret', 'realname'], ['username' => $username])[0];
    $totp = new TOTP((is_null($userdata['email']) ? $userdata['realname'] : $userdata['email']), $encoded_secret);
    $totp->setIssuer(SYSTEM_NAME);
    return $totp->getProvisioningUri();
}

/**
 * Save a TOTP secret for the user.
 * @global $database $database
 * @param string $username
 * @param string $secret
 */
function saveTOTP($username, $secret) {
    global $database;
    $database->update('accounts', ['authsecret' => $secret], ['username' => $username]);
}

/**
 * Verify a TOTP multiauth code
 * @global $database
 * @param string $username
 * @param int $code
 * @return boolean true if it's legit, else false
 */
function verifyTOTP($username, $code) {
    global $database;
    $userdata = $database->select('accounts', ['email', 'authsecret'], ['username' => $username])[0];
    if (is_empty($userdata['authsecret'])) {
        return false;
    }
    $totp = new TOTP(null, $userdata['authsecret']);
    return $totp->verify($code);
}
