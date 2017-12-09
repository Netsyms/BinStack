<?php
require_once __DIR__ . '/../required.php';
require_once __DIR__ . "/../lib/userinfo.php";

redirectifnotloggedin();
?>

<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=edititem" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("new item"); ?></a>
</div>
<?php if ($_GET['filter'] == 'stock') { ?>
    <script nonce="<?php echo $SECURE_NONCE; ?>">var filter = "stock";</script>
    <div class="alert alert-blue-grey"><i class="fa fa-filter fa-fw"></i> <?php lang("only showing understocked"); ?> &nbsp; <a href="app.php?page=items" class="btn btn-sm btn-blue-grey"><?php lang("show all items"); ?></a></div>
    <?php
} else {
    echo "<script nonce=\"$SECURE_NONCE\">var filter = null;</script>\n";
}
?>
<table id="itemtable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-cube hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-archive hidden-xs"></i> <?php lang('category'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-map-marker hidden-xs"></i> <?php lang('location'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-barcode hidden-xs"></i> <?php lang('code 1'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-qrcode hidden-xs"></i> <?php lang('code 2'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-hashtag hidden-xs"></i> <?php lang('qty'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-hashtag hidden-xs"></i> <?php lang('want'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('assigned to'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
        <tr>
            <th data-priority="0"></th>
            <th data-priority="1"><?php lang('actions'); ?></th>
            <th data-priority="1"><i class="fa fa-fw fa-cube hidden-xs"></i> <?php lang('name'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-archive hidden-xs"></i> <?php lang('category'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-map-marker hidden-xs"></i> <?php lang('location'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-barcode hidden-xs"></i> <?php lang('code 1'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-qrcode hidden-xs"></i> <?php lang('code 2'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-hashtag hidden-xs"></i> <?php lang('qty'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-hashtag hidden-xs"></i> <?php lang('want'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('assigned to'); ?></th>
        </tr>
    </tfoot>
</table>
<?php
if (!is_empty($VARS['q'])) {
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
