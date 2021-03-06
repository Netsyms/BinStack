<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=editloc" class="btn btn-success"><i class="fa fa-plus"></i> <?php $Strings->get("new location"); ?></a>
</div>
<table id="loctable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-map-marker"></i> <?php $Strings->get('location'); ?></th>
            <th data-priority="2"><i class="fas fa-barcode"></i> <?php $Strings->get('code'); ?></th>
            <th data-priority="3"><i class="fas fa-hashtag"></i> <?php $Strings->get('item count'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $locs = $database->select('locations', [
            'locid',
            'locname',
            'loccode'
        ]);
        foreach ($locs as $loc) {
            $itemcount = $database->count('items', ['locid' => $loc['locid']]);
            ?>
            <tr>
                <td></td>
                <td>
                    <a class="btn btn-primary btn-sm" href="app.php?page=editloc&id=<?php echo $loc['locid']; ?>"><i class="fas fa-edit"></i> <?php $Strings->get("edit"); ?></a>
                </td>
                <td><?php echo $loc['locname']; ?></td>
                <td><?php echo $loc['loccode']; ?></td>
                <td><?php echo $itemcount; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-map-marker"></i> <?php $Strings->get('location'); ?></th>
            <th data-priority="2"><i class="fas fa-barcode"></i> <?php $Strings->get('code'); ?></th>
            <th data-priority="3"><i class="fas fa-hashtag"></i> <?php $Strings->get('item count'); ?></th>
        </tr>
    </tfoot>
</table>