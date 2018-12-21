<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class AccountHubApi {

    public static function get(string $action, array $data = null, bool $throwex = false) {
        global $SETTINGS;

        $content = [
            "action" => $action,
            "key" => $SETTINGS['accounthub']['key']
        ];
        if (!is_null($data)) {
            $content = array_merge($content, $data);
        }
        $options = [
            'http' => [
                'method' => 'POST',
                'content' => json_encode($content),
                'header' => "Content-Type: application/json\r\n" .
                "Accept: application/json\r\n",
                "ignore_errors" => true
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($SETTINGS['accounthub']['api'], false, $context);
        $response = json_decode($result, true);
        if ($result === false || !AccountHubApi::checkHttpRespCode($http_response_header) || json_last_error() != JSON_ERROR_NONE) {
            if ($throwex) {
                throw new Exception($result);
            } else {
                sendError($result);
            }
        }
        return $response;
    }

    private static function checkHttpRespCode(array $headers): bool {
        foreach ($headers as $header) {
            if (preg_match("/HTTP\/[0-9]\.[0-9] [0-9]{3}.*/", $header)) {
                $respcode = explode(" ", $header)[1] * 1;
                if ($respcode >= 200 && $respcode < 300) {
                    return true;
                }
            }
        }
        return false;
    }

}
