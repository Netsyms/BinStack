<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class Notifications {

    /**
     * Add a new notification.
     * @global $database
     * @param User $user
     * @param string $title
     * @param string $content
     * @param string $timestamp If left empty, the current date and time will be used.
     * @param string $url
     * @param bool $sensitive If true, the notification is marked as containing sensitive content, and the $content might be hidden on lockscreens and other non-secure places.
     * @return int The newly-created notification ID.
     * @throws Exception
     */
    public static function add(User $user, string $title, string $content, string $timestamp = "", string $url = "", bool $sensitive = false): int {
        global $Strings;
        if ($user->exists()) {
            if (empty($title) || empty($content)) {
                throw new Exception($Strings->get("invalid parameters", false));
            }

            $timestamp = date("Y-m-d H:i:s");
            if (!empty($timestamp)) {
                $timestamp = date("Y-m-d H:i:s", strtotime($timestamp));
            }

            $client = new GuzzleHttp\Client();

            $response = $client
                    ->request('POST', PORTAL_API, [
                'form_params' => [
                    'key' => PORTAL_KEY,
                    'action' => "addnotification",
                    'uid' => $user->getUID(),
                    'title' => $title,
                    'content' => $content,
                    'timestamp' => $timestamp,
                    'url' => $url,
                    'sensitive' => $sensitive
                ]
            ]);

            if ($response->getStatusCode() > 299) {
                sendError("Login server error: " . $response->getBody());
            }

            $resp = json_decode($response->getBody(), TRUE);
            if ($resp['status'] == "OK") {
                return $resp['id'] * 1;
            } else {
                return false;
            }
        }
        throw new Exception($Strings->get("user does not exist", false));
    }

}
