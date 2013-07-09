/* Update SQL for Windows ZPanel 10.0.2 to 10.0.3 */
USE `zpanel_core`;

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.0.3' WHERE  `so_name_vc` = 'dbversion';
ALTER TABLE  `x_accounts` ADD  `ac_catorder_vc` VARCHAR(255) DEFAULT NULL AFTER  `ac_passsalt_vc`;

/* Drop the redunent x_mysql table */
DROP TABLE IF EXISTS `zpanel_core`.`x_mysql`;