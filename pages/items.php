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
            <th data-priority="1"><i class="fa fa-fw fa-cube"></i> <?php lang('name'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-archive"></i> <?php lang('category'); ?></th>
            <th data-priority="2"><i class="fa fa-fw fa-map-marker"></i> <?php lang('location'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-barcode"></i> <?php lang('code 1'); ?></th>
            <th data-priority="4"><i class="fa fa-fw fa-qrcode"></i> <?php lang('code 2'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-hashtag"></i> <?php lang('qty'); ?></th>
            <th data-priority="3"><i class="fa fa-fw fa-user"></i> <?php lang('assigned to'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
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
            'text1',
            'text2',
            'text3',
            'qty',
            'userid'
        ]);
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
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th></th>
            <th><?php lang('actions'); ?></th>
            <th><i class="fa fa-fw fa-cube"></i> <?php lang('name'); ?></th>
            <th><i class="fa fa-fw fa-archive"></i> <?php lang('category'); ?></th>
            <th><i class="fa fa-fw fa-map-marker"></i> <?php lang('location'); ?></th>
            <th><i class="fa fa-fw fa-barcode"></i> <?php lang('code 1'); ?></th>
            <th><i class="fa fa-fw fa-qrcode"></i> <?php lang('code 2'); ?></th>
            <th><i class="fa fa-fw fa-hashtag"></i> <?php lang('qty'); ?></th>
            <th><i class="fa fa-fw fa-user"></i> <?php lang('assigned to'); ?></th>
        </tr>
    </tfoot>
</table>