<?php

/**
 * Make things happen when buttons are pressed and forms submitted.
 */
require_once __DIR__ . "/required.php";

require_once __DIR__ . "/lib/login.php";
require_once __DIR__ . "/lib/userinfo.php";

dieifnotloggedin();

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

if ($VARS['action'] != "signout" && !account_has_permission($_SESSION['username'], "INV_EDIT")) {
    returnToSender("no_edit_permission");
}

switch ($VARS['action']) {
    case "edititem":
        $insert = true;
        if (is_empty($VARS['itemid'])) {
            $insert = true;
        } else {
            if ($database->has('items', ['itemid' => $VARS['itemid']])) {
                $insert = false;
            } else {
                returnToSender("invalid_itemid");
            }
        }
        if (is_empty($VARS['name'])) {
            returnToSender('missing_name');
        }
        if (!is_empty($VARS['catstr']) && is_empty($VARS['cat'])) {
            if ($database->count("categories", ["catname" => $VARS['catstr']]) == 1) {
                $VARS['cat'] = $database->get("categories", 'catid', ["catname" => $VARS['catstr']]);
            } else {
                returnToSender('use_the_drop_luke');
            }
        }
        if (!is_empty($VARS['locstr']) && is_empty($VARS['loc'])) {
            if ($database->count("locations", ["locname" => $VARS['locstr']]) == 1) {
                $VARS['loc'] = $database->get("locations", 'locid', ["locname" => $VARS['locstr']]);
            } else {
                returnToSender('use_the_drop_luke');
            }
        }
        if (is_empty($VARS['cat']) || is_empty($VARS['loc'])) {
            returnToSender('invalid_parameters');
        }
        if (is_empty($VARS['qty'])) {
            $VARS['qty'] = 1;
        } else if (!is_numeric($VARS['qty'])) {
            returnToSender('field_nan');
        }
        if (is_empty($VARS['want'])) {
            $VARS['want'] = 0;
        } else if (!is_numeric($VARS['want'])) {
            returnToSender('field_nan');
        }
        if (!$database->has('categories', ['catid' => $VARS['cat']])) {
            returnToSender('invalid_category');
        }
        if (!$database->has('locations', ['locid' => $VARS['loc']])) {
            returnToSender('invalid_location');
        }

        if (!is_empty($VARS['assignedto']) && user_exists($VARS['assignedto'])) {
            $userid = getUserByUsername($VARS['assignedto'])['uid'];
        } else {
            $userid = null;
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
            'userid' => $userid
        ];

        if ($insert) {
            $database->insert('items', $data);
        } else {
            $database->update('items', $data, ['itemid' => $VARS['itemid']]);
        }

        returnToSender("item_saved");
    case "editcat":
        $insert = true;
        if (is_empty($VARS['catid'])) {
            $insert = true;
        } else {
            if ($database->has('categories', ['catid' => $VARS['catid']])) {
                $insert = false;
            } else {
                returnToSender("invalid_catid");
            }
        }
        if (is_empty($VARS['name'])) {
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
        if (is_empty($VARS['locid'])) {
            $insert = true;
        } else {
            if ($database->has('locations', ['locid' => $VARS['locid']])) {
                $insert = false;
            } else {
                returnToSender("invalid_locid");
            }
        }
        if (is_empty($VARS['name'])) {
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
        exit(json_encode($database->select('categories', ['catid (id)', 'catname (name)'], ['catname[~]' => $VARS['q'], 'LIMIT' => 10])));
    case "autocomplete_location":
        exit(json_encode($database->select('locations', ['locid (id)', 'locname (name)'], ["OR" => ['locname[~]' => $VARS['q'], 'loccode' => $VARS['q']], 'LIMIT' => 10])));
    case "autocomplete_user":
        header("Content-Type: application/json");
        $client = new GuzzleHttp\Client();

        $response = $client
                ->request('POST', PORTAL_API, [
            'form_params' => [
                'key' => PORTAL_KEY,
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
    case "signout":
        session_destroy();
        header('Location: index.php');
        die("Logged out.");
}