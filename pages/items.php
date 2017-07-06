<?php
require_once __DIR__ . '/../required.php';
require_once __DIR__ . "/../lib/userinfo.php";

redirectifnotloggedin();
?>

<div class="btn-group" style="margin-bottom: 10px;">
    <a href="app.php?page=edititem" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("new item"); ?></a>
</div>
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
            <th data-priority="3"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('assigned to'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php /*
        $items = $database->select('items', [
            '[>]categories' => ['catid' => 'catid'],
            '[>]locations' => ['locid' => 'locid']
                ], [
            'itemid',
            'name',
            'catname',
            'locname',
            'loccode',
            'code1',
            'code2',
            'qty',
            'userid'
        ], ["LIMIT" => 100]);
        $usercache = [];
        foreach ($items as $item) {
            if (is_null($item['userid'])) {
                $user = "";
            } else {
                if (!isset($usercache[$item['userid']])) {
                    $usercache[$item['userid']] = getUserByID($item['userid']);
                }
                $user = $usercache[$item['userid']]['name'];
            }
            ?>
            <tr>
                <td></td>
                <td>
                    <a class="btn btn-blue btn-xs" href="app.php?page=edititem&id=<?php echo $item['itemid']; ?>"><i class="fa fa-pencil-square-o"></i> <?php lang("edit"); ?></a>
                </td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['catname']; ?></td>
                <td><?php echo $item['locname'] . " (" . $item['loccode'] . ")"; ?></td>
                <td><?php echo $item['code1']; ?></td>
                <td><?php echo $item['code2']; ?></td>
                <td><?php echo $item['qty']; ?></td>
                <td><?php echo $user; ?></td>
            </tr>
            <?php
        } */
        ?>
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
            <th data-priority="3"><i class="fa fa-fw fa-user hidden-xs"></i> <?php lang('assigned to'); ?></th>
        </tr>
    </tfoot>
</table>