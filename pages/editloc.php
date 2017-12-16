<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$locdata = [
    'locid' => '',
    'locname' => '',
    'loccode' => '',
    'locinfo' => ''
];

$editing = false;

if (!is_empty($VARS['id'])) {
    if ($database->has('locations', ['locid' => $VARS['id']])) {
        $editing = true;
        $locdata = $database->select(
                        'locations', [
                    'locid',
                    'locname',
                    'loccode',
                    'locinfo'
                        ], [
                    'locid' => $VARS['id']
                ])[0];
    } else {
        // cat id is invalid, redirect to a page that won't cause an error when pressing Save
        header('Location: app.php?page=editloc');
    }
}
?>

<form role="form" action="action.php" method="POST">
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php
                if ($editing) {
                    ?>
                    <i class="fa fa-pencil-square-o"></i> <?php lang2("editing location", ['loc' => "<span id=\"name_title\">" . htmlspecialchars($locdata['locname']) . "</span>"]); ?>
                    <?php
                } else {
                    ?>
                    <i class="fa fa-pencil-square-o"></i> <?php lang("adding location"); ?>
                    <?php
                }
                ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fa fa-map-marker"></i> <?php lang("name"); ?></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="<?php lang("placeholder location name"); ?>" required="required" value="<?php echo htmlspecialchars($locdata['locname']); ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="code"><i class="fa fa-barcode"></i> <?php lang("code"); ?></label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="123456789" value="<?php echo htmlspecialchars($locdata['loccode']); ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="info"><i class="fa fa-info"></i> <?php lang("description"); ?></label>
                <textarea class="form-control" id="info" name="info"><?php echo htmlspecialchars($locdata['locinfo']); ?></textarea>
            </div>
        </div>

        <input type="hidden" name="locid" value="<?php echo htmlspecialchars($VARS['id']); ?>" />
        <input type="hidden" name="action" value="editloc" />
        <input type="hidden" name="source" value="locations" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deleteloc&source=locations&locid=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-danger btn-xs pull-right mgn-top-8px"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>