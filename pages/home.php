<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row justify-content-center">
    <div class="col-12 col-md-6">
        <form action="app.php" method="get">
            <input type="hidden" name="page" value="items" />
            <div class="input-group">
                <input type="text" class="form-control" name="q" id="quicklookup_box" placeholder="<?php $Strings->get("Search"); ?>"/>
                <div class="input-group-append">
                    <?php
                    if (isset($_SESSION['mobile']) && $_SESSION['mobile']) {
                        ?>
                        <span class="btn btn-default" onclick="scancode('#quicklookup_box');">
                            <i class="fas fa-barcode fa-fw"></i>
                        </span>
                    <?php } ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<br />
<div class="card-deck">
    <div class="card bg-teal text-light">
        <div class="card-body">
            <h4 class="card-title"><?php $Strings->get("Total Items") ?></h4>
            <h1><i class="fas fa-fw fa-boxes"></i> <?php echo $database->count('items'); ?></h1>
        </div>
        <div class="card-footer">
            <a href="app.php?page=items" class="text-light"><i class="fas fa-arrow-right"></i> <?php $Strings->get("View Items"); ?></a>
        </div>
    </div>
    <?php
    $lowcnt = $database->count('items', ["AND" => ["qty[<]want", "want[>]" => 0]]);
    ?>
    <div class="card bg-<?php echo ($lowcnt > 0 ? "deep-orange" : "green"); ?> text-light">
        <div class="card-body">
            <h4 class="card-title"><?php $Strings->get("Understocked Items") ?></h4>
            <h1><i class="fas fa-fw fa-tachometer-alt"></i> <?php echo $lowcnt; ?></h1>
        </div>
        <div class="card-footer">
            <a href="app.php?page=items&filter=stock" class="text-light"><i class="fas fa-arrow-right"></i> <?php $Strings->get("View Understocked"); ?></a>
        </div>
    </div>
</div>
