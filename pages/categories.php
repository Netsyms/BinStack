<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group">
    <a href="app.php?page=editcat" class="btn btn-success"><i class="fas fa-plus"></i> <?php $Strings->get("New Category"); ?></a>
</div>
<table id="cattable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-pallet hidden-sm"></i> <?php $Strings->get('category'); ?></th>
            <th data-priority="2"><i class="fas fa-hashtag hidden-sm"></i> <?php $Strings->get('item count'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cats = $database->select('categories', [
            'catid',
            'catname'
        ]);
        foreach ($cats as $cat) {
            $itemcount = $database->count('items', ['catid' => $cat['catid']]);
            ?>
            <tr>
                <td></td>
                <td>
                    <a class="btn btn-primary btn-sm" href="app.php?page=editcat&id=<?php echo $cat['catid']; ?>"><i class="fas fa-edit"></i> <?php $Strings->get("edit"); ?></a>
                </td>
                <td><?php echo $cat['catname']; ?></td>
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
            <th data-priority="1"><i class="fas fa-pallet hidden-sm"></i> <?php $Strings->get('category'); ?></th>
            <th data-priority="2"><i class="fas fa-hashtag hidden-sm"></i> <?php $Strings->get('item count'); ?></th>
        </tr>
    </tfoot>
</table>