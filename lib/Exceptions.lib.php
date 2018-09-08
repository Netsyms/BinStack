<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class IncorrectPasswordException extends Exception {
    public function __construct(string $message = "Incorrect password.", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}