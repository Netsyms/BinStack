<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class Session {

    public static function start(User $user) {
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['uid'] = $user->getUID();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['realname'] = $user->getName();
        $_SESSION['loggedin'] = true;
    }

}
