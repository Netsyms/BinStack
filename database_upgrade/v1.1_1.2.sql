/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
ALTER TABLE `items`
ADD COLUMN `cost` DECIMAL(10,2) NULL DEFAULT NULL AFTER `userid`,
ADD COLUMN `price` DECIMAL(10,2) NULL DEFAULT NULL AFTER `cost`;
