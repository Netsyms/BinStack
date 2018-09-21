<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

use League\Csv\Writer;
use League\Csv\HTMLConverter;
use odsPhpGenerator\ods;
use odsPhpGenerator\odsTable;
use odsPhpGenerator\odsTableRow;
use odsPhpGenerator\odsTableColumn;
use odsPhpGenerator\odsTableCellString;
use odsPhpGenerator\odsStyleTableColumn;
use odsPhpGenerator\odsStyleTableCell;

class Report {

    private $title = "";
    private $header = [];
    private $data = [];

    public function __construct(string $title = "", array $header = [], array $data = []) {
        $this->title = $title;
        $this->header = $header;
        $this->data = $data;
    }

    public function setHeader(array $header) {
        $this->header = $header;
    }

    public function addDataRow(array $columns) {
        $this->data[] = $columns;
    }

    public function getHeader(): array {
        return $this->header;
    }

    public function getData(): array {
        return $this->data;
    }

    public function output(string $format) {
        switch ($format) {
            case "ods":
                $this->toODS();
                break;
            case "html":
                $this->toHTML();
                break;
            case "csv":
            default:
                $this->toCSV();
                break;
        }
    }

    private function toODS() {
        $ods = new ods();
        $styleColumn = new odsStyleTableColumn();
        $styleColumn->setUseOptimalColumnWidth(true);
        $headerstyle = new odsStyleTableCell();
        $headerstyle->setFontWeight("bold");
        $table = new odsTable($this->title);

        for ($i = 0; $i < count($this->header); $i++) {
            $table->addTableColumn(new odsTableColumn($styleColumn));
        }

        $row = new odsTableRow();
        foreach ($this->header as $cell) {
            $row->addCell(new odsTableCellString($cell, $headerstyle));
        }
        $table->addRow($row);

        foreach ($this->data as $cols) {
            $row = new odsTableRow();
            foreach ($cols as $cell) {
                $row->addCell(new odsTableCellString($cell));
            }
            $table->addRow($row);
        }
        $ods->addTable($table);
        // The @ is a workaround to silence the tempnam notice,
        // which breaks the file.  This is apparently the intended behavior:
        // https://bugs.php.net/bug.php?id=69489
        @$ods->downloadOdsFile($this->title . "_" . date("Y-m-d_Hi") . ".ods");
    }

    private function toHTML() {
        global $SECURE_NONCE;
        $data = array_merge([$this->header], $this->data);
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
                . "<title>" . $this->title . "_" . date("Y-m-d_Hi") . "</title>\n"
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

    private function toCSV() {
        $csv = Writer::createFromString('');
        $data = array_merge([$this->header], $this->data);
        $csv->insertAll($data);
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->title . "_" . date("Y-m-d_Hi") . ".csv" . '"');
        echo $csv;
    }

}
