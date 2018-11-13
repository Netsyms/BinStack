/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
ALTER TABLE `items`
ADD COLUMN `cost` DECIMAL(10,2) NULL DEFAULT NULL AFTER `userid`,
ADD COLUMN `price` DECIMAL(10,2) NULL DEFAULT NULL AFTER `cost`;\

ALTER TABLE `items`
DROP PRIMARY KEY,
ADD PRIMARY KEY (`itemid`);


CREATE TABLE IF NOT EXISTS `images` (
  `imageid` INT(11) NOT NULL AUTO_INCREMENT,
  `itemid` INT(11) NOT NULL,
  `imagename` VARCHAR(255) NOT NULL,
  `primary` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`imageid`, `itemid`),
  UNIQUE INDEX `imageid_UNIQUE` (`imageid` ASC),
  INDEX `fk_images_items1_idx` (`itemid` ASC),
  CONSTRAINT `fk_images_items1`
    FOREIGN KEY (`itemid`)
    REFERENCES `inventory`.`items` (`itemid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `images` ADD COLUMN `primary` TINYINT(1) NOT NULL DEFAULT 0 AFTER `imagename`;