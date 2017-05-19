<?php
require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$catdata = [
    'catid' => '',
    'catname' => ''];

$editing = false;

if (!is_empty($VARS['id'])) {
    if ($database->has('categories', ['catid' => $VARS['id']])) {
        $editing = true;
        $catdata = $database->select(
                        'categories', [
                    'catid',
                    'catname'
                        ], [
                    'catid' => $VARS['id']
                ])[0];
    } else {
        // cat id is invalid, redirect to a page that won't cause an error when pressing Save
        header('Location: app.php?page=editcat');
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
                    <i class="fa fa-pencil-square-o"></i> <?php lang2("editing category", ['cat' => "<span id=\"name_title\">" . htmlspecialchars($catdata['catname']) . "</span>"]); ?>
                    <?php
                } else {
                    ?>
                    <i class="fa fa-pencil-square-o"></i> <?php lang("adding category"); ?>
                    <?php
                }
                ?>
            </h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="name"><i class="fa fa-archive"></i> <?php lang("name"); ?></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Foo Bar" required="required" value="<?php echo htmlspecialchars($catdata['catname']); ?>" />
            </div>
        </div>

        <input type="hidden" name="catid" value="<?php echo htmlspecialchars($VARS['id']); ?>" />
        <input type="hidden" name="action" value="editcat" />
        <input type="hidden" name="source" value="categories" />

        <div class="panel-footer">
            <button type="submit" class="btn btn-success"><i class="fa fa-floppy-o"></i> <?php lang("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deletecat&source=categories&catid=<?php echo htmlspecialchars($VARS['id']); ?>" style="margin-top: 8px;" class="btn btn-danger btn-xs pull-right"><i class="fa fa-times"></i> <?php lang('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>