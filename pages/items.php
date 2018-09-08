<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=edititem" class="btn btn-success"><i class="fa fa-plus"></i> <?php $Strings->get("New Item"); ?></a>
</div>
<?php if (isset($_GET['filter']) && $_GET['filter'] == 'stock') { ?>
    <script nonce="<?php echo $SECURE_NONCE; ?>">var filter = "stock";</script>
    <div class="alert alert-blue-grey"><i class="fa fa-filter fa-fw"></i> <?php $Strings->get("only showing understocked"); ?> &nbsp; <a href="app.php?page=items" class="btn btn-sm btn-blue-grey"><?php $Strings->get("show all items"); ?></a></div>
    <?php
} else {
    echo "<script nonce=\"$SECURE_NONCE\">var filter = null;</script>\n";
}
?>
<table id="itemtable" class="table table-bordered table-hover table-sm">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-box d-none d-md-inline"></i> <?php $Strings->get('name'); ?></th>
            <th data-priority="7"><i class="fas fa-fw fa-pallet d-none d-md-inline"></i> <?php $Strings->get('category'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-map-marker d-none d-md-inline"></i> <?php $Strings->get('location'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-barcode d-none d-md-inline"></i> <?php $Strings->get('code 1'); ?></th>
            <th data-priority="5"><i class="fas fa-fw fa-qrcode d-none d-md-inline"></i> <?php $Strings->get('code 2'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-hashtag d-none d-md-inline"></i> <?php $Strings->get('qty'); ?></th>
            <th data-priority="6"><i class="fas fa-fw fa-hashtag d-none d-md-inline"></i> <?php $Strings->get('want'); ?></th>
            <th data-priority="8"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php $Strings->get('assigned to'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php $Strings->get('actions'); ?></th>
            <th data-priority="1"><i class="fas fa-fw fa-box d-none d-md-inline"></i> <?php $Strings->get('name'); ?></th>
            <th data-priority="7"><i class="fas fa-fw fa-pallet d-none d-md-inline"></i> <?php $Strings->get('category'); ?></th>
            <th data-priority="4"><i class="fas fa-fw fa-map-marker d-none d-md-inline"></i> <?php $Strings->get('location'); ?></th>
            <th data-priority="2"><i class="fas fa-fw fa-barcode d-none d-md-inline"></i> <?php $Strings->get('code 1'); ?></th>
            <th data-priority="5"><i class="fas fa-fw fa-qrcode d-none d-md-inline"></i> <?php $Strings->get('code 2'); ?></th>
            <th data-priority="3"><i class="fas fa-fw fa-hashtag d-none d-md-inline"></i> <?php $Strings->get('qty'); ?></th>
            <th data-priority="6"><i class="fas fa-fw fa-hashtag d-none d-md-inline"></i> <?php $Strings->get('want'); ?></th>
            <th data-priority="8"><i class="fas fa-fw fa-user d-none d-md-inline"></i> <?php $Strings->get('assigned to'); ?></th>
        </tr>
    </tfoot>
</table>
<?php
if (!empty($VARS['q'])) {
    ?>
    <script nonce="<?php echo $SECURE_NONCE; ?>">
        var search_preload_content = "<?php echo $VARS['q']; ?>";
    </script>
    <?php
} else {
    ?>
    <script nonce="<?php echo $SECURE_NONCE; ?>">
        var search_preload_content = false;
    </script>
<?php } ?>
