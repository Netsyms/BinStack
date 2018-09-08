<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$catdata = [
    'catid' => '',
    'catname' => ''];

$editing = false;

if (!empty($VARS['id'])) {
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
    <div class="card border-green">
        <h3 class="card-header text-green">
            <?php
            if ($editing) {
                ?>
                <i class="fas fa-edit"></i> <?php $Strings->build("editing category", ['cat' => "<span id=\"name_title\">" . htmlspecialchars($catdata['catname']) . "</span>"]); ?>
                <?php
            } else {
                ?>
                <i class="fas fa-edit"></i> <?php $Strings->get("Adding new category"); ?>
                <?php
            }
            ?>
        </h3>
        <div class="card-body">
            <div class="form-group">
                <label for="name"><i class="fas fa-archive"></i> <?php $Strings->get("name"); ?></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Foo Bar" required="required" value="<?php echo htmlspecialchars($catdata['catname']); ?>" />
            </div>
        </div>

        <input type="hidden" name="catid" value="<?php echo isset($VARS['id']) ? htmlspecialchars($VARS['id']) : ""; ?>" />
        <input type="hidden" name="action" value="editcat" />
        <input type="hidden" name="source" value="categories" />

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php $Strings->get("save"); ?></button>
            <?php
            if ($editing) {
                ?>
                <a href="action.php?action=deletecat&source=categories&catid=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-danger ml-auto"><i class="fas fa-times"></i> <?php $Strings->get('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>