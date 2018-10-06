<?php
/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

if ($database->count("locations") == 0 || $database->count("categories") == 0) {
    header('Location: app.php?page=items&msg=noloccat');
    die();
}

$item = [
    'itemid' => '',
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

if (empty($VARS['id']) || !$database->has('items', ['itemid' => $VARS['id']])) {
    header('Location: app.php?page=items&msg=invalid_itemid');
    die();
}
$item = $database->get(
        'items', [
    '[>]categories' => [
        'catid' => 'catid'
    ],
    '[>]locations' => [
        'locid' => 'locid'
    ]
        ], [
    'itemid',
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
        ]);
?>

<div class="card">
    <div class="card-body">
        <div class="card-title d-flex flex-wrap justify-content-between">
                <h3><a href="app.php?page=items" class="text-body"><i class="fas fa-arrow-left"></i></a> <?php echo $item['name']; ?></h3>
            <div>
                <a href="app.php?page=edititem&id=<?php echo $item['itemid']; ?>&source=item" class="btn btn-primary"><i class="fas fa-edit"></i> <?php $Strings->get('Edit Item'); ?></a>
                <a href="app.php?page=editimages&id=<?php echo $item['itemid']; ?>&source=item" class="btn btn-info"><i class="fas fa-images"></i> <?php $Strings->get('Edit Images'); ?></a>
            </div>
        </div>

        <div class="d-flex justify-content-around flex-wrap">
            <div class="list-group-item h5 mb-2">
                <i class="fas fa-archive"></i> <?php
                $Strings->get("category");
                echo ": " . $item['catname'];
                ?>
            </div>
            <div class="list-group-item h5 mb-2">
                <i class="fas fa-map-marker"></i> <?php
                $Strings->get("location");
                echo ": " . $item['locname'];
                ?>
            </div>
            <div class="list-group-item h5 mb-2">
                <i class="fas fa-hashtag"></i> <?php
                $Strings->get("quantity");
                echo ": " . $item['qty'];
                ?>
            </div>
            <div class="list-group-item h5 mb-2">
                <i class="fas fa-money-bill"></i> <?php
                $Strings->get("Item cost");
                echo ": " . $item['cost'];
                ?>
            </div>
            <div class="list-group-item h5 mb-2">
                <i class="fas fa-shopping-cart"></i> <?php
                $Strings->get("Sale price");
                echo ": " . $item['price'];
                ?>
            </div>
            <?php
            if (!is_null($item['userid']) && is_numeric($item['userid'])) {
                ?>
                <div class="list-group-item h5 mb-2">
                    <i class="fas fa-user"></i> <?php
                    $Strings->get("assigned to");
                    echo ": " . (new User($item['userid']))->getName();
                    ?>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="row mt-4 mx-2 mb-4">
            <div class="col-12 col-sm-6 col-md-4">
                <h5><i class="fas fa-info"></i> <?php $Strings->get('Description'); ?></h5>
                <div>
                    <?php echo strip_tags($item['text1']); ?>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <h5><i class="fas fa-sticky-note"></i> <?php $Strings->get('Notes'); ?></h5>
                <div>
                    <?php echo strip_tags($item['text2']); ?>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <h5><i class="fas fa-comments"></i> <?php $Strings->get('Comments'); ?></h5>
                <div>
                    <?php echo strip_tags($item['text3']); ?>
                </div>
            </div>
        </div>

        <hr />

        <div class="row mt-4">
            <?php
            $images = $database->select('images', ['imageid', 'imagename', 'primary'], ['itemid' => $VARS['id']]);
            foreach ($images as $i) {
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3">
                    <div class="card m-2">
                        <img class="card-img" src="image.php?i=<?php echo $i['imagename']; ?>" alt="<?php echo $i['imagename']; ?>">
                        <div class="card-img-overlay text-right">
                            <?php
                            if ($i['primary']) {
                                ?>
                                <span class="badge badge-success p-2"><i class="fas fa-star"></i></span>
                                    <?php
                                }
                                ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>