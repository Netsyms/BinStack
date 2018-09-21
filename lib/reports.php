<?php

/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */


// Detect if loaded by the user or by PHP
if (count(get_included_files()) == 1) {
    define("LOADED", true);
} else {
    define("LOADED", false);
}

require_once __DIR__ . "/../required.php";

// Allow access with a download code, for mobile app and stuff
$date = date("Y-m-d H:i:s");
if (isset($VARS['code']) && LOADED) {
    if (!$database->has('report_access_codes', ["AND" => ['code' => $VARS['code'], 'expires[>]' => $date]])) {
        dieifnotloggedin();
    }
} else {
    dieifnotloggedin();
}

// Delete old DB entries
$database->delete('report_access_codes', ['expires[<=]' => $date]);

if (LOADED) {
    if (isset($VARS['type']) && isset($VARS['format'])) {
        generateReport($VARS['type'], $VARS['format']);
        die();
    } else {
        $Strings->get("invalid parameters");
        die();
    }
}

/**
 * Get a 2d array of the items in the database.
 * @global type $database
 * @param array $filter Medoo WHERE clause.
 * @return string
 */
function getItemReport($filter = []): Report {
    global $database, $Strings;
    $items = $database->select(
            "items", [
        "[>]locations" => ["locid"],
        "[>]categories" => ["catid"]
            ], [
        "itemid",
        "name",
        "catname",
        "locname",
        "code1",
        "code2",
        "qty",
        "want",
        "userid",
        "text1",
        "text2",
        "text3",
        "cost",
        "price"
            ], $filter
    );
    $report = new Report($Strings->get("Items", false));
    $report->setHeader([
        $Strings->get("itemid", false),
        $Strings->get("name", false),
        $Strings->get("category", false),
        $Strings->get("location", false),
        $Strings->get("code 1", false),
        $Strings->get("code 2", false),
        $Strings->get("quantity", false),
        $Strings->get("want", false),
        $Strings->get("Cost", false),
        $Strings->get("Price", false),
        $Strings->get("assigned to", false),
        $Strings->get("Description", false),
        $Strings->get("Notes", false),
        $Strings->get("Comments", false)
    ]);
    for ($i = 0; $i < count($items); $i++) {
        $user = "";
        if (!is_null($items[$i]["userid"])) {
            $u = new User($items[$i]["userid"]);
            $user = $u->getName() . " (" . $u->getUsername() . ')';
        }
        $report->addDataRow([
            $items[$i]["itemid"],
            $items[$i]["name"],
            $items[$i]["catname"],
            $items[$i]["locname"],
            $items[$i]["code1"],
            $items[$i]["code2"],
            $items[$i]["qty"],
            $items[$i]["want"],
            $items[$i]["cost"],
            $items[$i]["price"],
            $user,
            $items[$i]["text1"],
            $items[$i]["text2"],
            $items[$i]["text3"]
        ]);
    }
    return $report;
}

function getCategoryReport(): Report {
    global $database, $Strings;
    $cats = $database->select('categories', [
        'catid',
        'catname'
    ]);
    $report = new Report($Strings->get("Categories", false));
    $report->setHeader([$Strings->get("id", false), $Strings->get("category", false), $Strings->get("item count", false)]);
    for ($i = 0; $i < count($cats); $i++) {
        $itemcount = $database->count('items', ['catid' => $cats[$i]['catid']]);
        $report->addDataRow([
            $cats[$i]["catid"],
            $cats[$i]["catname"],
            $itemcount . ""
        ]);
    }
    return $report;
}

function getLocationReport(): Report {
    global $database, $Strings;
    $locs = $database->select('locations', [
        'locid',
        'locname',
        'loccode',
        'locinfo'
    ]);
    $report = new Report($Strings->get("Locations", false));
    $report->setHeader([$Strings->get("id", false), $Strings->get("location", false), $Strings->get("code", false), $Strings->get("item count", false), $Strings->get("Description", false)]);
    for ($i = 0; $i < count($locs); $i++) {
        $itemcount = $database->count('items', ['locid' => $locs[$i]['locid']]);
        $report->addDataRow([
            $locs[$i]["locid"],
            $locs[$i]["locname"],
            $locs[$i]["loccode"],
            $itemcount . "",
            $locs[$i]["locinfo"]
        ]);
    }
    return $report;
}

function getReport($type): Report {
        switch ($type) {
        case "item":
            return getItemReport();
            break;
        case "category":
            return getCategoryReport();
            break;
        case "location":
            return getLocationReport();
            break;
        case "itemstock":
            return getItemReport(["AND" => ["qty[<]want", "want[>]" => 0]]);
            break;
        default:
            return new Report("error", ["ERROR"], ["Invalid report type."]);
    }
}

function generateReport($type, $format) {
    $report = getReport($type);
    $report->output($format);
}