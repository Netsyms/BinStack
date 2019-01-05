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

$form = new FormBuilder("", "fas fa-edit");

if ($editing) {
    $form->setTitle($Strings->build("editing category", ['cat' => "<span id=\"name_title\">" . htmlentities($catdata['catname']) . "</span>"], false));
} else {
    $form->setTitle($Strings->get("Adding new category", false));
}
$form->addInput("name", htmlentities($catdata['catname']), "text", true, "name", null, $Strings->get("name", false), "fas fa-archive", 12);

$form->addHiddenInput("catid", isset($VARS['id']) ? htmlspecialchars($VARS['id']) : "");
$form->addHiddenInput("action", "editcat");
$form->addHiddenInput("source", "categories");

$form->addButton($Strings->get("save", false), "fas fa-save", null, "submit");

if ($editing) {
    $form->addButton($Strings->get("delete", false), "fas fa-times", "action.php?action=deletecat&source=categories&catid=" . htmlspecialchars($VARS['id']), "", null, null, "", "btn btn-danger ml-auto");
}

$form->generate();