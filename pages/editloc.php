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

if (!empty($VARS['id'])) {
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
    <div class="card border-green">
            <h3 class="card-header text-green">
                <?php
                if ($editing) {
                    ?>
                    <i class="fas fa-edit"></i> <?php $Strings->build("editing location", ['loc' => "<span id=\"name_title\">" . htmlspecialchars($locdata['locname']) . "</span>"]); ?>
                    <?php
                } else {
                    ?>
                    <i class="fas fa-edit"></i> <?php $Strings->get("Adding new location"); ?>
                    <?php
                }
                ?>
            </h3>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-map-marker"></i> <?php $Strings->get("name"); ?></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="<?php $Strings->get("placeholder location name"); ?>" required="required" value="<?php echo htmlspecialchars($locdata['locname']); ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="code"><i class="fas fa-barcode"></i> <?php $Strings->get("code"); ?></label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="123456789" value="<?php echo htmlspecialchars($locdata['loccode']); ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="info"><i class="fas fa-info"></i> <?php $Strings->get("Description"); ?></label>
                <textarea class="form-control" id="info" name="info"><?php echo htmlspecialchars($locdata['locinfo']); ?></textarea>
            </div>
        </div>

        <input type="hidden" name="locid" value="<?php echo isset($VARS['id']) ? htmlspecialchars($VARS['id']) : ""; ?>" />
        <input type="hidden" name="action" value="editloc" />
        <input type="hidden" name="source" value="locations" />

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php $Strings->get("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deleteloc&source=locations&locid=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-danger ml-auto"><i class="fas fa-times"></i> <?php $Strings->get('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>