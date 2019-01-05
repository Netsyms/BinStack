<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();

$locdata = [
    'locid' => '',
    'locname' => '',
    'loccode' => '',
    'locinfo' => ''
];

$editing = false;

if (!empty($VARS['id'])) {
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

$form = new FormBuilder("", "fas fa-edit");

if ($editing) {
    $form->setTitle($Strings->build("editing location", ['loc' => "<span id=\"name_title\">" . htmlentities($locdata['locname']) . "</span>"], false));
} else {
    $form->setTitle($Strings->get("Adding new location", false));
}
$form->addInput("name", htmlentities($locdata['locname']), "text", true, "name", null, $Strings->get("name", false), "fas fa-map-marker", 6);
$form->addInput("code", htmlentities($locdata['loccode']), "text", false, "code", null, $Strings->get("code", false), "fas fa-barcode", 6);
$form->addInput("info", htmlentities($locdata['locinfo']), "textarea", false, "info", null, $Strings->get("Description", false), "fas fa-info", 12);

$form->addHiddenInput("locid", isset($VARS['id']) ? htmlspecialchars($VARS['id']) : "");
$form->addHiddenInput("action", "editloc");
$form->addHiddenInput("source", "locations");

$form->addButton($Strings->get("save", false), "fas fa-save", null, "submit");

if ($editing) {
    $form->addButton($Strings->get("delete", false), "fas fa-times", "action.php?action=deleteloc&source=locations&locid=" . htmlspecialchars($VARS['id']), "", null, null, "", "btn btn-danger ml-auto");
}

$form->generate();