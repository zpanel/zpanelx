/* Update SQL for Ubuntu 12.04 ZPanel 10.1.0 to 10.1.1 */
USE `zpanel_core`;

/* VERSION SPECIFIC UPDATE SQL STATEMENTS */
ALTER TABLE x_vhosts ADD vh_soaserial_vc CHAR(10) DEFAULT "AAAAMMDDSS";

/* Update the ZPanel database version number */
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '10.1.1' WHERE  `so_name_vc` = 'dbversion';

/* Removal of Password Protect directories module */
DELETE FROM `zpanel_core`.`x_modules` WHERE `x_modules`.`mo_name_vc` = 'Protect Directories';

/* Add repo_browser */
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (48,2,'Repo Browser',101,'repo_browser','user','Repo Browser allows you to manage your custom module repositories.', 0, 'true', '', '');