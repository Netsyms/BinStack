<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . "/required.php";

redirectIfNotLoggedIn();

require_once __DIR__ . "/pages.php";

$pageid = "home";
if (!is_empty($_GET['page'])) {
    $pg = strtolower($_GET['page']);
    $pg = preg_replace('/[^0-9a-z_]/', "", $pg);
    if (array_key_exists($pg, PAGES) && file_exists(__DIR__ . "/pages/" . $pg . ".php")) {
        $pageid = $pg;
    } else {
        $pageid = "404";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo SITE_TITLE; ?></title>

        <link href="static/css/bootstrap.min.css" rel="stylesheet">
        <link href="static/css/font-awesome.min.css" rel="stylesheet">
        <link href="static/css/material-color/material-color.css" rel="stylesheet">
        <link href="static/css/app.css" rel="stylesheet">
        <?php
        // custom page styles
        if (isset(PAGES[$pageid]['styles'])) {
            foreach (PAGES[$pageid]['styles'] as $style) {
                echo "<link href=\"$style\" rel=\"stylesheet\">\n";
            }
        }
        ?>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 col-sm-offset-3 col-md-offset-4 col-lg-offset-4">
                    <?php
                    if ((SHOW_ICON == "both" || SHOW_ICON == "app") && ICON_POSITION != "menu") {
                        if (MENU_BAR_STYLE != "fixed") {
                            ?>
                            <img class="img-responsive banner-image" src="static/img/logo.png" />
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <nav class="navbar navbar-inverse navbar-green navbar-<?php echo MENU_BAR_STYLE; ?>-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <?php
                        if (SHOW_ICON == "both" || SHOW_ICON == "app") {
                            if (MENU_BAR_STYLE == "fixed" || ICON_POSITION == "menu") {
                                $src = "static/img/logo.png";
                                if ($pageid != "home") {
                                    $src = "static/img/up-arrow-white.png";
                                }
                                ?>
                                <a class="navbar-brand" href="app.php">
                                    <img src="<?php echo $src; ?>" />
                                </a>
                                <?php
                            }
                        }
                        ?>
                        <a class="navbar-brand" href="app.php">
                            <?php
                            echo SITE_TITLE;
                            ?>
                        </a>
                    </div>

                    <div class="collapse navbar-collapse" id="navbar-collapse">
                        <ul class="nav navbar-nav">
                            <?php
                            $counter = 0;
                            $more = "";
                            $curpagefound = false;
                            foreach (PAGES as $id => $pg) {
                                if ($pg['navbar'] === TRUE) {
                                    $counter++;
                                    if ($counter > ($curpagefound ? 4 : 3) && $pageid != $id) {
                                        $item = '<a href="app.php?page=' . $id . '">';
                                        if (isset($pg['icon'])) {
                                            $item .= '<i class="fa fa-' . $pg['icon'] . ' fa-fw"></i>';
                                        }
                                        $item .= lang($pg['title'], false) . '</a>';
                                        echo '<li class="hidden-sm hidden-md">' . $item . "</li>";
                                        $more .= '<li>' . $item . "</li>";
                                    } else {
                                        if ($pageid == $id) {
                                            $curpagefound = true;
                                            ?>
                                            <li class="active">
                                                <?php
                                            } else {
                                                ?>
                                            <li>
                                            <?php } ?>
                                            <a href="app.php?page=<?php echo $id; ?>">
                                                <?php
                                                if (isset($pg['icon'])) {
                                                    ?>
                                                    <i class="fa fa-<?php echo $pg['icon']; ?> fa-fw"></i>
                                                <?php } ?>
                                                <?php lang($pg['title']) ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                }
                            }

                            if ($counter > 4) {
                                ?>
                                <li class="dropdown hidden-lg hidden-xs">
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-v fa-fw"></i> <?php lang("more"); ?></a>
                                    <ul class="dropdown-menu"><?php echo $more; ?></ul>
                                </li>
                            <?php } ?>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="<?php echo PORTAL_URL; ?>"><i class="fa fa-user fa-fw"></i> <span class="hidden-sm hidden-md"><?php echo $_SESSION['realname'] ?></span></a></li>
                            <li><a href="action.php?action=signout"><i class="fa fa-sign-out fa-fw"></i> <span class="hidden-sm hidden-md"><?php lang("sign out") ?></span></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <?php
            if (MENU_BAR_STYLE == "fixed") {
                ?>
                <div class="pad-75px"></div>
                <?php
            }
            ?>
            <?php
// Alert messages
            if (!is_empty($_GET['msg']) && array_key_exists($_GET['msg'], MESSAGES)) {
                // optional string generation argument
                if (is_empty($_GET['arg'])) {
                    $alertmsg = lang(MESSAGES[$_GET['msg']]['string'], false);
                } else {
                    $alertmsg = lang2(MESSAGES[$_GET['msg']]['string'], ["arg" => strip_tags($_GET['arg'])], false);
                }
                $alerttype = MESSAGES[$_GET['msg']]['type'];
                $alerticon = "square-o";
                switch (MESSAGES[$_GET['msg']]['type']) {
                    case "danger":
                        $alerticon = "times";
                        break;
                    case "warning":
                        $alerticon = "exclamation-triangle";
                        break;
                    case "info":
                        $alerticon = "info-circle";
                        break;
                    case "success":
                        $alerticon = "check";
                        break;
                }
                echo <<<END
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4 col-sm-offset-3 col-md-offset-4 col-lg-offset-4">
                    <div class="alert alert-dismissible alert-$alerttype">
                        <button type="button" class="close">&times;</button>
                        <i class="fa fa-$alerticon"></i> $alertmsg
                    </div>
                </div>
            </div>
END;
            }
            ?>
            <div>
                <?php
                include_once __DIR__ . '/pages/' . $pageid . ".php";
                ?>
            </div>
            <div class="footer">
                <?php echo FOOTER_TEXT; ?><br />
                Copyright &copy; <?php echo date('Y'); ?> <?php echo COPYRIGHT_NAME; ?>
            </div>
        </div>
        <script src="static/js/jquery-3.2.1.min.js"></script>
        <script src="static/js/bootstrap.min.js"></script>
        <script src="static/js/app.js"></script>
        <?php
        // custom page scripts
        if (isset(PAGES[$pageid]['scripts'])) {
            foreach (PAGES[$pageid]['scripts'] as $script) {
                echo "<script src=\"$script\"></script>\n";
            }
        }
        ?>
    </body>
</html>