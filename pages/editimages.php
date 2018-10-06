<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$images = [];

if (!empty($VARS['id']) && $database->has('items', ['itemid' => $VARS['id']])) {
    $images = $database->select('images', ['imageid', 'imagename', 'primary'], ['itemid' => $VARS['id']]);
} else {
    header('Location: app.php?page=items&msg=invalid_itemid');
    die();
}
?>

<div class="card border-green">
    <div class="card-header d-flex justify-content-between flex-wrap">
        <h3 class="text-green my-auto"><i class="fas fa-images"></i> <?php $Strings->build("Editing images for item", ['item' => "<span id=\"name_title\">" . htmlspecialchars($database->get('items', 'name', ['itemid' => $VARS['id']])) . "</span>"]); ?></h3>
        <div class="ml-auto my-auto">
            <form action="action.php" method="POST" enctype="multipart/form-data">
                <div class="input-group input-group-sm">
                    <input type="text" id="uploadstatus" class="form-control" readonly />
                    <div class="input-group-append">
                        <span class="btn btn-primary btn-file">
                            <i class="fas fa-folder-open"></i> <?php $Strings->get("Browse"); ?> <input id="fileupload" type="file" name="files[]" accept=".png,.jpg,.jpeg,.gif,.webp,image/png,image/jpeg,image/gif,image/webp" multiple required />
                        </span>
                        <button class="btn btn-success" type="submit"><i class="fas fa-cloud-upload-alt"></i> <?php $Strings->get("Upload"); ?></button>
                    </div>
                </div>
                <input type="hidden" name="action" value="imageupload" />
                <input type="hidden" name="source" value="editimages" />
                <input type="hidden" name="itemid" value="<?php echo htmlspecialchars($VARS['id']); ?>" />
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <?php
            foreach ($images as $i) {
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3">
                    <div class="card m-2">
                        <img class="card-img" src="image.php?i=<?php echo $i['imagename']; ?>" alt="<?php echo $i['imagename']; ?>">
                        <div class="card-img-overlay text-right">
                            <?php
                            if ($i['primary']) {
                                ?>
                                <span class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="bottom" title=""><i class="fas fa-check"></i> <?php $Strings->get("Promoted"); ?></span>
                                <?php
                            } else {
                                ?>
                                <form action="action.php" method="POST">
                                    <input type="hidden" name="action" value="promoteimage" />
                                    <input type="hidden" name="itemid" value="<?php echo $VARS['id']; ?>" />
                                    <input type="hidden" name="imageid" value="<?php echo $i['imageid']; ?>" />
                                    <input type="hidden" name="source" value="editimages" />
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-level-up-alt"></i> <?php $Strings->get("Promote"); ?></button>
                                </form>
                                <?php
                            }
                            ?>
                            <form action="action.php" method="POST">
                                <input type="hidden" name="action" value="deleteimage" />
                                <input type="hidden" name="itemid" value="<?php echo $VARS['id']; ?>" />
                                <input type="hidden" name="imageid" value="<?php echo $i['imageid']; ?>" />
                                <input type="hidden" name="source" value="editimages" />
                                <button type="submit" class="btn btn-danger btn-sm mt-1"><i class="fas fa-trash"></i> <?php $Strings->get("Delete"); ?></button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="card-footer d-flex">
        <?php
        $source = "edititem";
        if ($_GET['source'] === "item") {
            $source = "item";
        }
        ?>
        <a href="./app.php?page=<?php echo $source; ?>&id=<?php echo $_GET['id']; ?>" class="btn btn-success mr-auto"><i class="fas fa-arrow-left"></i> <?php $Strings->get("Back"); ?></a>
    </div>
</div>