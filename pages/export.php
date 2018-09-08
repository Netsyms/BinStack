<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once __DIR__ . '/../required.php';

redirectifnotloggedin();
?>

<div class="card border-green">
    <form action="lib/reports.php" method="GET" target="_BLANK">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="type"><?php $Strings->get("report type"); ?></label>
                        <select name="type" class="form-control" required>
                            <option value="item"><?php $Strings->get("Items") ?></option>
                            <option value="category"><?php $Strings->get("Categories") ?></option>
                            <option value="location"><?php $Strings->get("Locations") ?></option>
                            <option value="itemstock"><?php $Strings->get("Understocked Items") ?></option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="form-group">
                        <label for="type"><?php $Strings->get("format"); ?></label>
                        <select name="format" class="form-control" required>
                            <option value="csv"><?php $Strings->get("csv file") ?></option>
                            <option value="ods"><?php $Strings->get("ods file") ?></option>
                            <option value="html"><?php $Strings->get("html file") ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $code = uniqid(rand(10000000, 99999999), true);
        $database->insert('report_access_codes', ['code' => $code, 'expires' => date("Y-m-d H:i:s", strtotime("+5 minutes"))]);
        ?>
        <input type="hidden" name="code" value="<?php echo $code; ?>" />

        <div class="card-footer d-flex">
            <button type="submit" class="btn btn-success ml-auto" id="genrptbtn"><i class="fas fa-download"></i> <?php $Strings->get("generate report"); ?></button>
        </div>
    </form>
</div>