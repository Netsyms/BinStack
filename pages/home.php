<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-blue-grey">
            <div class="panel-heading"><div class="panel-title"><?php lang("total items") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-cubes"></i> <?php echo $database->count('items'); ?></h1>
            </div>
            <div class="panel-footer">
                <a href="app.php?page=items" class="black-text"><i class="fa fa-arrow-right"></i> <?php lang("view items"); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <?php
        $lowcnt = $database->count('items', ["AND" => ["qty[<]want", "want[>]" => 0]]);
        ?>
        <div class="panel panel-<?php echo ($lowcnt > 0 ? "orange" : "green"); ?>">
            <div class="panel-heading"><div class="panel-title"><?php lang("understocked items") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-tachometer"></i> <?php echo $lowcnt; ?></h1>
            </div>
            <div class="panel-footer">
                <a href="app.php?page=items&filter=stock" class="black-text"><i class="fa fa-arrow-right"></i> <?php lang("view understocked"); ?></a>
            </div>
        </div>
    </div>
</div>