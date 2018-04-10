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

use League\Csv\Writer;
use League\Csv\HTMLConverter;
use odsPhpGenerator\ods;
use odsPhpGenerator\odsTable;
use odsPhpGenerator\odsTableRow;
use odsPhpGenerator\odsTableColumn;
use odsPhpGenerator\odsTableCellString;
use odsPhpGenerator\odsStyleTableColumn;
use odsPhpGenerator\odsStyleTableCell;

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
        lang("invalid parameters");
        die();
    }
}

/**
 * Get a 2d array of the items in the database.
 * @global type $database
 * @param array $filter Medoo WHERE clause.
 * @return string
 */
function getItemReport($filter = []) {
    global $database;
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
    $header = [
        lang("itemid", false),
        lang("name", false),
        lang("category", false),
        lang("location", false),
        lang("code 1", false),
        lang("code 2", false),
        lang("quantity", false),
        lang("want", false),
        lang("cost", false),
        lang("price", false),
        lang("assigned to", false),
        lang("description", false),
        lang("notes", false),
        lang("comments", false)
    ];
    $out = [$header];
    for ($i = 0; $i < count($items); $i++) {
        $user = "";
        if (!is_null($items[$i]["userid"])) {
            require_once __DIR__ . "/userinfo.php";
            $u = getUserByID($items[$i]["userid"]);
            $user = $u['name'] . " (" . $u['username'] . ')';
        }
        $out[] = [
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
        ];
    }
    return $out;
}

function getCategoryReport() {
    global $database;
    $cats = $database->select('categories', [
        'catid',
        'catname'
    ]);
    $header = [lang("id", false), lang("category", false), lang("item count", false)];
    $out = [$header];
    for ($i = 0; $i < count($cats); $i++) {
        $itemcount = $database->count('items', ['catid' => $cats[$i]['catid']]);
        $out[] = [
            $cats[$i]["catid"],
            $cats[$i]["catname"],
            $itemcount . ""
        ];
    }
    return $out;
}

function getLocationReport() {
    global $database;
    $locs = $database->select('locations', [
        'locid',
        'locname',
        'loccode',
        'locinfo'
    ]);
    $header = [lang("id", false), lang("location", false), lang("code", false), lang("item count", false), lang("description", false)];
    $out = [$header];
    for ($i = 0; $i < count($locs); $i++) {
        $itemcount = $database->count('items', ['locid' => $locs[$i]['locid']]);
        $out[] = [
            $locs[$i]["locid"],
            $locs[$i]["locname"],
            $locs[$i]["loccode"],
            $itemcount . "",
            $locs[$i]["locinfo"]
        ];
    }
    return $out;
}

function getReportData($type) {
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
            return [["error"]];
    }
}

function dataToCSV($data, $name = "report") {
    $csv = Writer::createFromString('');
    $csv->insertAll($data);
    header('Content-type: text/csv');
    header('Content-Disposition: attachment; filename="' . $name . "_" . date("Y-m-d_Hi") . ".csv" . '"');
    echo $csv;
    die();
}

function dataToODS($data, $name = "report") {
    $ods = new ods();
    $styleColumn = new odsStyleTableColumn();
    $styleColumn->setUseOptimalColumnWidth(true);
    $headerstyle = new odsStyleTableCell();
    $headerstyle->setFontWeight("bold");
    $table = new odsTable($name);

    for ($i = 0; $i < count($data[0]); $i++) {
        $table->addTableColumn(new odsTableColumn($styleColumn));
    }

    $rowid = 0;
    foreach ($data as $datarow) {
        $row = new odsTableRow();
        foreach ($datarow as $cell) {
            if ($rowid == 0) {
                $row->addCell(new odsTableCellString($cell, $headerstyle));
            } else {
                $row->addCell(new odsTableCellString($cell));
            }
        }
        $table->addRow($row);
        $rowid++;
    }
    $ods->addTable($table);
    $ods->downloadOdsFile($name . "_" . date("Y-m-d_Hi") . ".ods");
}

function dataToHTML($data, $name = "report") {
    global $SECURE_NONCE;
    // HTML exporter doesn't like null values
    for ($i = 0; $i < count($data); $i++) {
        for ($j = 0; $j < count($data[$i]); $j++) {
            if (is_null($data[$i][$j])) {
                $data[$i][$j] = '';
            }
        }
    }
    header('Content-type: text/html');
    $converter = new HTMLConverter();
    $out = "<!DOCTYPE html>\n"
            . "<meta charset=\"utf-8\">\n"
            . "<meta name=\"viewport\" content=\"width=device-width\">\n"
            . "<title>" . $name . "_" . date("Y-m-d_Hi") . "</title>\n"
            . <<<STYLE
<style nonce="$SECURE_NONCE">
    .table-csv-data {
        border-collapse: collapse;
    }
    .table-csv-data tr:first-child {
        font-weight: bold;
    }
    .table-csv-data tr td {
        border: 1px solid black;
    }
</style>
STYLE
            . $converter->convert($data);
    echo $out;
}

function generateReport($type, $format) {
    $data = getReportData($type);
    switch ($format) {
        case "ods":
            dataToODS($data, $type);
            break;
        case "html":
            dataToHTML($data, $type);
            break;
        case "csv":
        default:
            echo dataToCSV($data, $type);
            break;
    }
}
