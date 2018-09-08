<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

if ($database->count("locations") == 0 || $database->count("categories") == 0) {
    header('Location: app.php?page=items&msg=noloccat');
    die();
}

$itemdata = [
    'name' => '',
    'catid' => '',
    'catname' => '',
    'locid' => '',
    'locname' => '',
    'loccode' => '',
    'code1' => '',
    'code2' => '',
    'text1' => '',
    'text2' => '',
    'text3' => '',
    'qty' => 1,
    'want' => 0,
    'cost' => 0.0,
    'price' => 0.0,
    'userid' => ''];

$editing = false;
$cloning = false;

if (!empty($VARS['id'])) {
    if ($database->has('items', ['itemid' => $VARS['id']])) {
        $editing = true;
        if (isset($VARS['clone']) && $VARS['clone'] == 1) {
            $cloning = true;
        }
        $itemdata = $database->select(
                        'items', [
                    '[>]categories' => [
                        'catid' => 'catid'
                    ],
                    '[>]locations' => [
                        'locid' => 'locid'
                    ]
                        ], [
                    'name',
                    'code1',
                    'code2',
                    'text1',
                    'text2',
                    'text3',
                    'items.catid',
                    'catname',
                    'items.locid',
                    'locname',
                    'loccode',
                    'qty',
                    'want',
                    'cost',
                    'price',
                    'userid'
                        ], [
                    'itemid' => $VARS['id']
                ])[0];
    } else {
        // item id is invalid, redirect to a page that won't cause an error when pressing Save
        header('Location: app.php?page=edititem');
        die();
    }
}
?>

<form role="form" action="action.php" method="POST">
    <div class="card border-green">
        <h3 class="card-header text-green">
            <?php
            if ($cloning) {
                ?>
                <i class="fas fa-edit"></i> <?php $Strings->build("cloning item", ['oitem' => htmlspecialchars($itemdata['name']), 'nitem' => "<span id=\"name_title\">" . htmlspecialchars($itemdata['name']) . "</span>"]); ?>
                <?php
            } else if ($editing) {
                ?>
                <i class="fas fa-edit"></i> <?php $Strings->build("editing item", ['item' => "<span id=\"name_title\">" . htmlspecialchars($itemdata['name']) . "</span>"]); ?>
                <?php
            } else {
                ?>
                <i class="fas fa-edit"></i> <?php $Strings->get("Adding Item"); ?>
                <?php
            }
            ?>
        </h3>
        <div class="card-body">
            <div class="form-group">
                <label for="name"><i class="fas fa-cube"></i> <?php $Strings->get("name"); ?></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="<?php $Strings->get("placeholder item name"); ?>" required="required" value="<?php echo htmlspecialchars($itemdata['name']); ?>" />
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="cat"><i class="fas fa-archive"></i> <?php $Strings->get("category"); ?></label>
                        <input type="text" name="catstr" class="form-control" id="cat" placeholder="<?php $Strings->get("placeholder category name"); ?>" value="<?php echo htmlspecialchars($itemdata['catname']); ?>" />
                        <input type="hidden" id="realcat" name="cat" value="<?php echo $itemdata['catid']; ?>" required="required" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="loc"><i class="fas fa-map-marker"></i> <?php $Strings->get("location"); ?></label>
                        <input type="text" name="locstr" class="form-control" id="loc" placeholder="<?php $Strings->get("placeholder location name"); ?>" value="<?php echo htmlspecialchars($itemdata['locname']); ?>" />
                        <input type="hidden" id="realloc" name="loc" value="<?php echo $itemdata['locid']; ?>" required="required" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="code1"><i class="fas fa-barcode"></i> <?php $Strings->get("code 1"); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="code1" name="code1" placeholder="123456789" value="<?php echo htmlspecialchars($itemdata['code1']); ?>" />
                            <span class="input-group-btn mobile-app-show">
                                <button type="button" class="btn btn-default" onclick="scancode('#code1'); return false;"><i class="fa fa-fw fa-barcode"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="code2"><i class="fas fa-qrcode"></i> <?php $Strings->get("code 2"); ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="code2" name="code2" placeholder="qwerty123" value="<?php echo htmlspecialchars($itemdata['code2']); ?>" />
                            <span class="input-group-btn mobile-app-show">
                                <button type="button" class="btn btn-default" onclick="scancode('#code2'); return false;"><i class="fa fa-fw fa-barcode"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="qty"><i class="fas fa-hashtag"></i> <?php $Strings->get('quantity'); ?></label>
                        <input type="number" class="form-control" id="qty" name="qty" placeholder="1" value="<?php echo $itemdata['qty']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="want"><i class="fas fa-hashtag"></i> <?php $Strings->get('minwant'); ?></label>
                        <input type="number" class="form-control" id="want" name="want" placeholder="1" value="<?php echo $itemdata['want']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="assignedto"><i class="fas fa-user"></i> <?php $Strings->get('assigned to'); ?></label>
                        <input type="text" class="form-control" id="assignedto" name="assignedto" placeholder="<?php $Strings->get('nobody'); ?>" value="<?php
                        if (!empty($itemdata['userid'])) {
                            $user = new User($itemdata['userid']);
                            if ($user->exists()) {
                                echo $user->getUsername();
                            }
                        }
                        ?>" />
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="cost"><i class="far fa-money-bill-alt"></i> <?php $Strings->get('Item cost'); ?></label>
                        <input type="number" class="form-control" id="cost" name="cost" placeholder="0.00" step="0.01" value="<?php echo $itemdata['cost']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="price"><i class="fas fa-shopping-cart"></i> <?php $Strings->get('Sale price'); ?></label>
                        <input type="number" class="form-control" id="price" name="price" placeholder="0.00" step="0.01" value="<?php echo $itemdata['price']; ?>" />
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="info1"><i class="fas fa-info"></i> <?php $Strings->get("Description"); ?></label>
                        <textarea class="form-control" id="info1" name="text1"><?php echo htmlspecialchars($itemdata['text1']); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="info2"><i class="fas fa-sticky-note"></i> <?php $Strings->get("Notes"); ?></label>
                        <textarea class="form-control" id="info2" name="text2"><?php echo htmlspecialchars($itemdata['text2']); ?></textarea>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-6">
                    <div class="form-group">
                        <label for="info3"><i class="fas fa-comments"></i> <?php $Strings->get("Comments"); ?></label>
                        <textarea class="form-control" id="info3" name="text3"><?php echo htmlspecialchars($itemdata['text3']); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="itemid" value="<?php
        if ($editing && !$cloning) {
            echo htmlspecialchars($VARS['id']);
        }
        ?>" />
        <input type="hidden" name="action" value="edititem" />
        <input type="hidden" name="source" value="items" />

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success mr-auto"><i class="fas fa-save"></i> <?php $Strings->get("save"); ?></button>
            <?php
            if ($editing && !$cloning) {
                ?>
                <a href="action.php?action=deleteitem&source=items&itemid=<?php echo htmlspecialchars($VARS['id']); ?>" class="btn btn-danger ml-auto"><i class="fas fa-times"></i> <?php $Strings->get('delete'); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</form>