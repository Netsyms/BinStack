<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/required.php";

require_once __DIR__ . "/lib/login.php";

// if we're logged in, we don't need to be here.
if ($_SESSION['loggedin']) {
    header('Location: app.php');
}

/* Authenticate user */
$userpass_ok = false;
$multiauth = false;
if (checkLoginServer()) {
    if ($VARS['progress'] == "1") {
        if (!CAPTCHA_ENABLED || (CAPTCHA_ENABLED && verifyCaptcheck($VARS['captcheck_session_code'], $VARS['captcheck_selected_answer'], CAPTCHA_SERVER . "/api.php"))) {
            $errmsg = "";
            if (authenticate_user($VARS['username'], $VARS['password'], $errmsg)) {
                switch (get_account_status($VARS['username'])) {
                    case "LOCKED_OR_DISABLED":
                        $alert = lang("account locked", false);
                        break;
                    case "TERMINATED":
                        $alert = lang("account terminated", false);
                        break;
                    case "CHANGE_PASSWORD":
                        $alert = lang("password expired", false);
                    case "NORMAL":
                        $userpass_ok = true;
                        break;
                    case "ALERT_ON_ACCESS":
                        sendLoginAlertEmail($VARS['username']);
                        $userpass_ok = true;
                        break;
                }
                if ($userpass_ok) {
                    $_SESSION['passok'] = true; // stop logins using only username and authcode
                    if (userHasTOTP($VARS['username'])) {
                        $multiauth = true;
                    } else {
                        doLoginUser($VARS['username'], $VARS['password']);
                        header('Location: app.php');
                        die("Logged in, go to app.php");
                    }
                }
            } else {
                if (!is_empty($errmsg)) {
                    $alert = lang2("login server error", ['arg' => $errmsg], false);
                } else {
                    $alert = lang("login incorrect", false);
                }
            }
        } else {
            $alert = lang("captcha error", false);
        }
    } else if ($VARS['progress'] == "2") {
        if ($_SESSION['passok'] !== true) {
            // stop logins using only username and authcode
            sendError("Password integrity check failed!");
        }
        if (verifyTOTP($VARS['username'], $VARS['authcode'])) {
            if (doLoginUser($VARS['username'])) {
                header('Location: app.php');
                die("Logged in, go to app.php");
            } else {
                $alert = lang("login server user data error", false);
            }
        } else {
            $alert = lang("2fa incorrect", false);
        }
    }
} else {
    $alert = lang("login server unavailable", false);
}
header("Link: <static/fonts/Roboto.css>; rel=preload; as=style", false);
header("Link: <static/css/bootstrap.min.css>; rel=preload; as=style", false);
header("Link: <static/css/material-color/material-color.min.css>; rel=preload; as=style", false);
header("Link: <static/css/index.css>; rel=preload; as=style", false);
header("Link: <static/js/jquery-3.3.1.min.js>; rel=preload; as=script", false);
header("Link: <static/js/bootstrap.min.js>; rel=preload; as=script", false);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo SITE_TITLE; ?></title>

        <link rel="icon" href="static/img/logo.svg">

        <link href="static/css/bootstrap.min.css" rel="stylesheet">
        <link href="static/css/material-color/material-color.min.css" rel="stylesheet">
        <link href="static/css/index.css" rel="stylesheet">
        <?php if (CAPTCHA_ENABLED) { ?>
            <script src="<?php echo CAPTCHA_SERVER ?>/captcheck.dist.js"></script>
        <?php } ?>
    </head>
    <body>
        <div class="row justify-content-center">
            <div class="col-auto">
                <img class="banner-image" src="static/img/logo.svg" />
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="card col-11 col-xs-11 col-sm-8 col-md-6 col-lg-4">
                <div class="card-body">
                    <h5 class="card-title"><?php lang("sign in"); ?></h5>
                    <form action="" method="POST">
                        <?php
                        if (!is_empty($alert)) {
                            ?>
                            <div class="alert alert-danger">
                                <i class="fa fa-fw fa-exclamation-triangle"></i> <?php echo $alert; ?>
                            </div>
                            <?php
                        }

                        if ($multiauth != true) {
                            ?>
                            <input type="text" class="form-control" name="username" placeholder="<?php lang("username"); ?>" required="required" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus /><br />
                            <input type="password" class="form-control" name="password" placeholder="<?php lang("password"); ?>" required="required" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" /><br />
                            <?php if (CAPTCHA_ENABLED) { ?>
                                <div class="captcheck_container" data-stylenonce="<?php echo $SECURE_NONCE; ?>"></div>
                                <br />
                            <?php } ?>
                            <input type="hidden" name="progress" value="1" />
                            <?php
                        } else if ($multiauth) {
                            ?>
                            <div class="alert alert-info">
                                <?php lang("2fa prompt"); ?>
                            </div>
                            <input type="text" class="form-control" name="authcode" placeholder="<?php lang("authcode"); ?>" required="required" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus /><br />
                            <input type="hidden" name="progress" value="2" />
                            <input type="hidden" name="username" value="<?php echo $VARS['username']; ?>" />
                            <?php
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">
                            <?php lang("continue"); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer">
            <?php echo FOOTER_TEXT; ?><br />
            Copyright &copy; <?php echo date('Y'); ?> <?php echo COPYRIGHT_NAME; ?>
        </div>
    </div>
    <script src="static/js/jquery-3.3.1.min.js"></script>
    <script src="static/js/bootstrap.min.js"></script>
</body>
</html>
