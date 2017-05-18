<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$locdata = [
    'locid' => '',
    'locname' => '',
    'loccode' => '',
    'locinfo' => ''
];

$editing = false;

if (!is_empty($VARS['id'])) {
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
    <div class="panel panel-blue">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php
                if ($editing) {
                    ?>
                    <i class="fa fa-pencil-square-o"></i> <?php lang2("editing location", ['loc' => "<span id=\"name_title\">" . htmlspecialchars($locdata['locname']) . "</span>"]); ?>
                    <?php
                } else {
                    ?>
                    <i class="fa fa-pencil-square-o"></i> <?php lang("adding location"); ?>
                    <?php
                }
                ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="name"><i class="fa fa-map-marker"></i> Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Foo Bar" required="required" value="<?php echo htmlspecialchars($locdata['locname']); ?>" />
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label for="code"><i class="fa fa-barcode"></i> Code</label>
                        <input type="text" class="form-control" id="code" name="code" placeholder="123456789" value="<?php echo htmlspecialchars($locdata['loccode']); ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="info"><i class="fa fa-info"></i> Description</label>
                <textarea class="form-control" id="info" name="info"><?php echo htmlspecialchars($locdata['locinfo']); ?></textarea>
            </div>
        </div>

        <input type="hidden" name="locid" value="<?php echo htmlspecialchars($VARS['id']); ?>" />
        <input type="hidden" name="action" value="editloc" />
        <input type="hidden" name="source" value="locations" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> Save</button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deleteloc&source=locations&locid=<?php echo htmlspecialchars($VARS['id']); ?>" style="margin-top: 8px;" class="btn btn-danger btn-xs pull-right"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>