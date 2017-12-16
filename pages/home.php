<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
        <form action="app.php" method="get">
            <input type="hidden" name="page" value="items" />
            <div class="input-group">
                <input type="text" class="form-control" name="q" id="quicklookup_box" placeholder="<?php lang("search"); ?>"/>
                <div class="input-group-btn">
                    <?php
                    if ($_SESSION['mobile']) {
                        ?>
                        <span class="btn btn-default" onclick="scancode('#quicklookup_box');">
                            <i class="fa fa-barcode fa-fw"></i>
                        </span>
                    <?php } ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<br />
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4 col-md-offset-2">
        <div class="panel panel-blue-grey">
            <div class="panel-heading"><div class="panel-title"><?php lang("total items") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-cubes"></i> <?php echo $database->count('items'); ?></h1>
            </div>
            <div class="panel-footer">
                <a href="app.php?page=items" class="black-text"><i class="fa fa-arrow-right"></i> <?php lang("view items"); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <?php
        $lowcnt = $database->count('items', ["AND" => ["qty[<]want", "want[>]" => 0]]);
        ?>
        <div class="panel panel-<?php echo ($lowcnt > 0 ? "orange" : "green"); ?>">
            <div class="panel-heading"><div class="panel-title"><?php lang("understocked items") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-tachometer"></i> <?php echo $lowcnt; ?></h1>
            </div>
            <div class="panel-footer">
                <a href="app.php?page=items&filter=stock" class="black-text"><i class="fa fa-arrow-right"></i> <?php lang("view understocked"); ?></a>
            </div>
        </div>
    </div>
</div>