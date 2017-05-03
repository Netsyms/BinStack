<?php
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
        if (!RECAPTCHA_ENABLED || (RECAPTCHA_ENABLED && verifyReCaptcha($VARS['g-recaptcha-response']))) {
            if (authenticate_user($VARS['username'], $VARS['password'])) {
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
                $alert = lang("login incorrect", false);
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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" contgreent="width=device-width, initial-scale=1">

        <title><?php echo SITE_TITLE; ?></title>

        <link href="static/css/bootstrap.min.css" rel="stylesheet">
        <link href="static/css/font-awesome.min.css" rel="stylesheet">
        <link href="static/css/app.css" rel="stylesheet">
        <?php if (RECAPTCHA_ENABLED) { ?>
            <script src='https://www.google.com/recaptcha/api.js'></script>
        <?php } ?>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 col-sm-offset-3 col-md-offset-4 col-lg-offset-4">
                    <div>
                        <?php
                        if (SHOW_ICON == "both" || SHOW_ICON == "index") {
                            ?>
                            <img class="img-responsive banner-image" src="static/img/logo.png" />
                        <?php } ?>
                    </div>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php lang("sign in"); ?></h3>
                        </div>
                        <div class="panel-body">
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
                                    <input type="text" class="form-control" name="username" placeholder="<?php lang("username"); ?>" required="required" autofocus /><br />
                                    <input type="password" class="form-control" name="password" placeholder="<?php lang("password"); ?>" required="required" /><br />
                                    <?php if (RECAPTCHA_ENABLED) { ?>
                                        <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SITE_KEY; ?>"></div>
                                        <br />
                                    <?php } ?>
                                    <input type="hidden" name="progress" value="1" />
                                    <?php
                                } else if ($multiauth) {
                                    ?>
                                    <div class="alert alert-info">
                                        <?php lang("2fa prompt"); ?>
                                    </div>
                                    <input type="text" class="form-control" name="authcode" placeholder="<?php lang("authcode"); ?>" required="required" autocomplete="off" autofocus /><br />
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
            </div>
            <div class="footer">
                <?php echo LICENSE_TEXT; ?><br />
                Copyright &copy; <?php echo date('Y'); ?> <?php echo COPYRIGHT_NAME; ?>
            </div>
        </div>
        <script src="static/js/jquery-3.2.1.min.js"></script>
        <script src="static/js/bootstrap.min.js"></script>
    </body>
</html>