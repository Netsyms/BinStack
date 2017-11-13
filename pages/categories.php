<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="btn-group mgn-btm-10px">
    <a href="app.php?page=editcat" class="btn btn-success"><i class="fa fa-plus"></i> <?php lang("new category"); ?></a>
</div>
<table id="cattable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th><?php lang('actions'); ?></th>
            <th><i class="fa fa-archive hidden-xs"></i> <?php lang('category'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cats = $database->select('categories', [
            'catid',
            'catname'
        ]);
        foreach ($cats as $cat) {
            ?>
            <tr>
                <td>
                    <a class="btn btn-blue btn-xs" href="app.php?page=editcat&id=<?php echo $cat['catid']; ?>"><i class="fa fa-pencil-square-o"></i> <?php lang("edit"); ?></a>
                </td>
                <td><?php echo $cat['catname']; ?></td>
            </tr>
            <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th><?php lang('actions'); ?></th>
            <th><i class="fa fa-archive"></i> <?php lang('category'); ?></th>
        </tr>
    </tfoot>
</table>