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
                <a style="color: black;" href="app.php?page=items"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view items'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-deep-orange">
            <div class="panel-heading"><div class="panel-title"><?php lang("locations") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-map-marker"></i> <?php echo $database->count('locations'); ?></h1>
            </div>
            <div class="panel-footer">
                <a style="color: black;" href="app.php?page=locations"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view locations'); ?></a>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-4">
        <div class="panel panel-blue">
            <div class="panel-heading"><div class="panel-title"><?php lang("categories") ?></div></div>
            <div class="panel-body">
                <h1><i class="fa fa-fw fa-archive"></i> <?php echo $database->count('categories'); ?></h1>
            </div>
            <div class="panel-footer">
                <a style="color: black;" href="app.php?page=categories"><i class="fa fa-arrow-right fa-fw"></i> <?php lang('view categories'); ?></a>
            </div>
        </div>
    </div>
</div>