<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*
 * This file demonstrates creating a form with the FormBuilder class.
 */

$form = new FormBuilder("Sample Form", "fas fa-code", "", "GET");

$form->setID("sampleform");

$form->addHiddenInput("page", "form");

$form->addInput("name", "John", "text", true, null, null, "Your name", "fas fa-user", 6, 5, 20, "John(ny)?|Steve", "Invalid name, please enter John, Johnny, or Steve.");
$form->addInput("location", "", "select", true, null, ["1" => "Here", "2" => "There"], "Location", "fas fa-map-marker");
$form->addInput("textbox", "Hello world", "textarea", true, null, null, "Text area", "fas fa-font");
$form->addInput("box", "1", "checkbox", true, null, null, "I agree to the terms of service");

$form->addButton("Submit", "fas fa-save", null, "submit", "savebtn");

$form->generate();