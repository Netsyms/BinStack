<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require_once __DIR__ . "/required.php";

// if we're logged in, we don't need to be here.
if (!empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && !isset($_GET['permissionerror'])) {
    header('Location: app.php');
    die();
}

/**
 * Show a simple HTML page with a line of text and a button.  Matches the UI of
 * the AccountHub login flow.
 *
 * @global type $SETTINGS
 * @global type $SECURE_NONCE
 * @global type $Strings
 * @param string $title Text to show, passed through i18n
 * @param string $button Button text, passed through i18n
 * @param string $url URL for the button
 */
function showHTML(string $title, string $button, string $url) {
    global $SETTINGS, $SECURE_NONCE, $Strings;
    ?>
    <!DOCTYPE html>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $SETTINGS['site_title']; ?></title>

    <link rel="icon" href="static/img/logo.svg">

    <link href="static/css/bootstrap.min.css" rel="stylesheet">
    <style nonce="<?php echo $SECURE_NONCE; ?>">
        .display-5 {
            font-size: 2.5rem;
            font-weight: 300;
            line-height: 1.2;
        }

        .banner-image {
            max-height: 100px;
            margin: 2em auto;
            border: 1px solid grey;
            border-radius: 15%;
        }
    </style>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-12 text-center">
                <img class="banner-image" src="./static/img/logo.svg" />
            </div>

            <div class="col-12 text-center">
                <h1 class="display-5 mb-4"><?php $Strings->get($title); ?></h1>
            </div>

            <div class="col-12 col-sm-8 col-lg-6">
                <div class="card mt-4">
                    <div class="card-body">
                        <a href="<?php echo $url; ?>" class="btn btn-primary btn-block"><?php $Strings->get($button); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

if (!empty($_GET['logout'])) {
    showHTML("You have been logged out.", "Log in again", "./index.php");
    die();
}
if (empty($_SESSION["login_code"])) {
    $redirecttologin = true;
} else {
    try {
        $uidinfo = AccountHubApi::get("checkloginkey", ["code" => $_SESSION["login_code"]]);
        if ($uidinfo["status"] == "ERROR") {
            throw new Exception();
        }
        if (is_numeric($uidinfo['uid'])) {
            $user = new User($uidinfo['uid'] * 1);
            foreach ($SETTINGS['permissions'] as $perm) {
                if (!$user->hasPermission($perm)) {
                    showHTML("no access permission", "sign out", "./action.php?action=signout");
                    die();
                }
            }
            Session::start($user);
            $_SESSION["login_code"] = null;
            header('Location: app.php');
            showHTML("Logged in", "Continue", "./app.php");
            die();
        } else {
            throw new Exception();
        }
    } catch (Exception $ex) {
        $redirecttologin = true;
    }
}

if ($redirecttologin) {
    try {
        $urlbase = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . (($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) ? ":" . $_SERVER['SERVER_PORT'] : "");
        $iconurl = $urlbase . str_replace("index.php", "", $_SERVER["REQUEST_URI"]) . "static/img/logo.svg";
        $codedata = AccountHubApi::get("getloginkey", ["appname" => $SETTINGS["site_title"], "appicon" => $iconurl]);

        if ($codedata['status'] != "OK") {
            throw new Exception($Strings->get("login server unavailable", false));
        }

        $redirecturl = $urlbase . $_SERVER['REQUEST_URI'];

        $_SESSION["login_code"] = $codedata["code"];

        $locationurl = $codedata["loginurl"] . "?code=" . htmlentities($codedata["code"]) . "&redirect=" . htmlentities($redirecturl);
        header("Location: $locationurl");
        showHTML("Continue", "Continue", $locationurl);
        die();
    } catch (Exception $ex) {
        sendError($ex->getMessage());
    }
}
