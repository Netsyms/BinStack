<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";

if ($VARS['action'] !== "signout") {
    dieifnotloggedin();
}

/**
 * Redirects back to the page ID in $_POST/$_GET['source'] with the given message ID.
 * The message will be displayed by the app.
 * @param string $msg message ID (see lang/messages.php)
 * @param string $arg If set, replaces "{arg}" in the message string when displayed to the user.
 */
function returnToSender($msg, $arg = "") {
    global $VARS;
    if ($arg == "") {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=" . $msg);
    } else {
        header("Location: app.php?page=" . urlencode($VARS['source']) . "&msg=$msg&arg=$arg");
    }
    die();
}

if ($VARS['action'] != "signout" && !(new User($_SESSION['uid']))->hasPermission("INV_EDIT")) {
    returnToSender("no_edit_permission");
}

switch ($VARS['action']) {
    case "edititem":
        $insert = true;
        if (empty($VARS['itemid'])) {
            $insert = true;
        } else {
            if ($database->has('items', ['itemid' => $VARS['itemid']])) {
                $insert = false;
            } else {
                returnToSender("invalid_itemid");
            }
        }
        if (empty($VARS['name'])) {
            returnToSender('missing_name');
        }
        if (!empty($VARS['catstr']) && empty($VARS['cat'])) {
            if ($database->count("categories", ["catname" => $VARS['catstr']]) == 1) {
                $VARS['cat'] = $database->get("categories", 'catid', ["catname" => $VARS['catstr']]);
            } else {
                returnToSender('use_the_drop_luke');
            }
        }
        if (!empty($VARS['locstr']) && empty($VARS['loc'])) {
            if ($database->count("locations", ["locname" => $VARS['locstr']]) == 1) {
                $VARS['loc'] = $database->get("locations", 'locid', ["locname" => $VARS['locstr']]);
            } else {
                returnToSender('use_the_drop_luke');
            }
        }
        if (empty($VARS['cat']) || empty($VARS['loc'])) {
            returnToSender('invalid_parameters');
        }
        if (empty($VARS['qty'])) {
            $VARS['qty'] = 1;
        } else if (!is_numeric($VARS['qty'])) {
            returnToSender('field_nan');
        }
        if (empty($VARS['want'])) {
            $VARS['want'] = 0;
        } else if (!is_numeric($VARS['want'])) {
            returnToSender('field_nan');
        }
        if (empty($VARS['cost'])) {
            $VARS['cost'] = null;
        } else if (!is_numeric($VARS['cost'])) {
            returnToSender('field_nan');
        }
        if (empty($VARS['price'])) {
            $VARS['price'] = null;
        } else if (!is_numeric($VARS['price'])) {
            returnToSender('field_nan');
        }
        if (!$database->has('categories', ['catid' => $VARS['cat']])) {
            returnToSender('invalid_category');
        }
        if (!$database->has('locations', ['locid' => $VARS['loc']])) {
            returnToSender('invalid_location');
        }

        $userid = null;
        if (!empty($VARS['assignedto'])) {
            $assigneduser = User::byUsername($VARS['assignedto']);
            if ($assigneduser->exists()) {
                $userid = $assigneduser->getUID();
            }
        }

        $data = [
            'name' => $VARS['name'],
            'code1' => $VARS['code1'],
            'code2' => $VARS['code2'],
            'text1' => $VARS['text1'],
            'text2' => $VARS['text2'],
            'text3' => $VARS['text3'],
            'catid' => $VARS['cat'],
            'locid' => $VARS['loc'],
            'qty' => $VARS['qty'],
            'want' => $VARS['want'],
            'cost' => $VARS['cost'],
            'price' => $VARS['price'],
            'userid' => $userid
        ];

        if ($insert) {
            $database->insert('items', $data);
        } else {
            $database->update('items', $data, ['itemid' => $VARS['itemid']]);
        }

        if ($VARS['source'] == "item") {
            returnToSender("item_saved", "&id=" . $VARS['itemid']);
        }
        returnToSender("item_saved");
    case "editcat":
        $insert = true;
        if (empty($VARS['catid'])) {
            $insert = true;
        } else {
            if ($database->has('categories', ['catid' => $VARS['catid']])) {
                $insert = false;
            } else {
                returnToSender("invalid_catid");
            }
        }
        if (empty($VARS['name'])) {
            returnToSender('invalid_parameters');
        }

        $data = [
            'catname' => $VARS['name']
        ];

        if ($insert) {
            $database->insert('categories', $data);
        } else {
            $database->update('categories', $data, ['catid' => $VARS['catid']]);
        }

        returnToSender("category_saved");
    case "editloc":
        $insert = true;
        if (empty($VARS['locid'])) {
            $insert = true;
        } else {
            if ($database->has('locations', ['locid' => $VARS['locid']])) {
                $insert = false;
            } else {
                returnToSender("invalid_locid");
            }
        }
        if (empty($VARS['name'])) {
            returnToSender('invalid_parameters');
        }

        $data = [
            'locname' => $VARS['name'],
            'loccode' => $VARS['code'],
            'locinfo' => $VARS['info']
        ];

        if ($insert) {
            $database->insert('locations', $data);
        } else {
            $database->update('locations', $data, ['locid' => $VARS['locid']]);
        }

        returnToSender("location_saved");
    case "deleteitem":
        if ($database->has('items', ['itemid' => $VARS['itemid']])) {
            $database->delete('items', ['itemid' => $VARS['itemid']]);
            returnToSender("item_deleted");
        }
        returnToSender("invalid_parameters");
    case "deletecat":
        if ($database->has('categories', ['catid' => $VARS['catid']])) {
            if ($database->has('items', ['catid' => $VARS['catid']])) {
                returnToSender("category_in_use");
            }
            $database->delete('categories', ['catid' => $VARS['catid']]);
            returnToSender("category_deleted");
        }
        returnToSender("invalid_parameters");
    case "deleteloc":
        if ($database->has('locations', ['locid' => $VARS['locid']])) {
            if ($database->has('items', ['locid' => $VARS['locid']])) {
                returnToSender("location_in_use");
            }
            $database->delete('locations', ['locid' => $VARS['locid']]);
            returnToSender("location_deleted");
        }
        returnToSender("invalid_parameters");
    case "autocomplete_category":
        header("Content-Type: application/json");
        $q = (empty($VARS['q']) ? "" : $VARS['q']);
        exit(json_encode($database->select('categories', ['catid (id)', 'catname (name)'], ['catname[~]' => $q, 'LIMIT' => 10])));
    case "autocomplete_location":
        header("Content-Type: application/json");
        $q = (empty($VARS['q']) ? "" : $VARS['q']);
        exit(json_encode($database->select('locations', ['locid (id)', 'locname (name)'], ["OR" => ['locname[~]' => $VARS['q'], 'loccode' => $VARS['q']], 'LIMIT' => 10])));
    case "autocomplete_user":
        header("Content-Type: application/json");
        $client = new GuzzleHttp\Client();

        $response = $client
                ->request('POST', $SETTINGS['accounthub']['api'], [
            'form_params' => [
                'key' => $SETTINGS['accounthub']['key'],
                'action' => "usersearch",
                'search' => $VARS['q']
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            exit("[]");
        }

        $resp = json_decode($response->getBody(), TRUE);
        if ($resp['status'] == "OK") {
            exit(json_encode($resp['result']));
        } else {
            exit("[]");
        }
        break;
    case "imageupload":
        $destpath = $SETTINGS['file_upload_path'];
        if (!is_writable($destpath)) {
            returnToSender("unwritable_folder", "&id=$VARS[itemid]");
        }

        if (empty($VARS['itemid']) || !$database->has('items', ['itemid' => $VARS['itemid']])) {
            returnToSender("invalid_itemid", "&id=$VARS[itemid]");
        }

        $files = [];
        foreach ($_FILES['files'] as $key => $all) {
            foreach ($all as $i => $val) {
                $files[$i][$key] = $val;
            }
        }

        $errors = [];
        foreach ($files as $f) {
            if ($f['error'] !== UPLOAD_ERR_OK) {
                $err = "could not be uploaded.";
                switch ($f['error']) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $err = "is too big.";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $err = "could not be saved to disk.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $err = "was not actually sent.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $err = "was only partially sent.";
                        break;
                    default:
                        $err = "could not be uploaded.";
                }
                $errors[] = htmlentities($f['name']) . " $err";
                continue;
            }

            if (filesize($f['tmp_name']) > 11) {
                $imagetype = exif_imagetype($f['tmp_name']);
            } else {
                $imagetype = false;
            }

            switch ($imagetype) {
                case IMAGETYPE_JPEG:
                case IMAGETYPE_GIF:
                case IMAGETYPE_PNG:
                case IMAGETYPE_WEBP:
                    $imagevalid = true;
                    break;
                default:
                    $imagevalid = false;
            }

            if (!$imagevalid) {
                $errors[] = htmlentities($f['name']) . " is not a supported image type (JPEG, GIF, PNG, WEBP).";
                continue;
            }

            $filename = basename($f['name']);
            $filename = preg_replace("/[^a-z0-9\._\-]/", "_", strtolower($filename));
            $n = 1;
            if (file_exists($destpath . "/" . $filename)) {
                while (file_exists($destpath . '/' . $n . '_' . $filename)) {
                    $n++;
                }
                $filename = $n . '_' . $filename;
            }

            $finalpath = $destpath . "/" . $filename;

            if (move_uploaded_file($f['tmp_name'], $finalpath)) {
                $primary = false;
                if (!$database->has('images', ['AND' => ['itemid' => $VARS['itemid'], 'primary' => true]])) {
                    $primary = true;
                }
                $database->insert('images', ['itemid' => $VARS['itemid'], 'imagename' => $filename, 'primary' => $primary]);
            } else {
                $errors[] = htmlentities($f['name']) . " could not be uploaded.";
            }
        }

        if (count($errors) > 0) {
            returnToSender("upload_warning", implode("<br>", $errors) . "&id=$VARS[itemid]");
        }
        returnToSender("upload_success", "&id=$VARS[itemid]");
        break;
    case "promoteimage":
        if (empty($VARS['itemid']) || !$database->has('items', ['itemid' => $VARS['itemid']])) {
            returnToSender("invalid_itemid", "&id=$VARS[itemid]");
        }
        if (empty($VARS['imageid']) || !$database->has('images', ['AND' => ['itemid' => $VARS['itemid'], 'imageid' => $VARS['imageid']]])) {
            returnToSender("invalid_imageid", "&id=$VARS[itemid]");
        }

        $database->update('images', ['primary' => false], ['itemid' => $VARS['itemid']]);
        $database->update('images', ['primary' => true], ['AND' => ['itemid' => $VARS['itemid'], 'imageid' => $VARS['imageid']]]);
        returnToSender("image_promoted", "&id=$VARS[itemid]");
        break;
    case "deleteimage":
        if (empty($VARS['itemid']) || !$database->has('items', ['itemid' => $VARS['itemid']])) {
            returnToSender("invalid_itemid", "&id=$VARS[itemid]");
        }
        if (empty($VARS['imageid']) || !$database->has('images', ['AND' => ['itemid' => $VARS['itemid'], 'imageid' => $VARS['imageid']]])) {
            returnToSender("invalid_imageid", "&id=$VARS[itemid]");
        }

        $imagename = $database->get('images', 'imagename', ['imageid' => $VARS['imageid']]);
        if ($database->count('images', ['imagename' => $imagename]) <= 1) {
            unlink($SETTINGS['file_upload_path'] . "/" . $imagename);
        }
        $database->delete('images', ['AND' => ['itemid' => $VARS['itemid'], 'imageid' => $VARS['imageid']]]);

        if (!$database->has('images', ['AND' => ['itemid' => $VARS['itemid'], 'primary' => true]])) {
            $database->update('images', ['primary' => true], ['itemid' => $VARS['itemid'], 'LIMIT' => 1]);
        }

        returnToSender("image_deleted", "&id=$VARS[itemid]");
    case "signout":
        session_destroy();
        header('Location: index.php?logout=1');
        die("Logged out.");
}
