<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require_once __DIR__ . "/required.php";

$base = FILE_UPLOAD_PATH . "/";
if (isset($_GET['i'])) {
    $file = $_GET['i'];
    $filepath = $base . $file;
    if (!file_exists($filepath) || is_dir($filepath)) {
        http_response_code(404);
        die("404 File Not Found");
    }
    if (strpos(realpath($filepath), FILE_UPLOAD_PATH) !== 0) {
        http_response_code(404);
        die("404 File Not Found");
    }
} else {
    http_response_code(404);
    die("404 File Not Found");
}

if (filesize($filepath) > 11) {
    $imagetype = exif_imagetype($filepath);
} else {
    $imagetype = false;
}

switch ($imagetype) {
    case IMAGETYPE_JPEG:
        $mimetype = "image/jpeg";
        break;
    case IMAGETYPE_GIF:
        $mimetype = "image/gif";
        break;
    case IMAGETYPE_PNG:
        $mimetype = "image/png";
        break;
    case IMAGETYPE_WEBP:
        $mimetype = "image/webp";
        break;
    default:
        $mimetype = "application/octet-stream";
}

header("Content-Type: $mimetype");
header('Content-Length: ' . filesize($filepath));
header("X-Content-Type-Options: nosniff");
$seconds_to_cache = 60 * 60 * 12; // 12 hours
$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
header("Expires: $ts");
header("Pragma: cache");
header("Cache-Control: max-age=$seconds_to_cache");

ob_end_flush();

readfile($filepath);
