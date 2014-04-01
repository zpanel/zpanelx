/* Update SQL for Ubuntu 12.04 ZPanel 10.1.0 to 10.1.1 */
USE `zpanel_core`;

/* Insert the files backup setting */
INSERT INTO `zpanel_core`.`x_settings` (`so_id_pk`, `so_name_vc`, `so_cleanname_vc`, `so_value_tx`, `so_defvalues_tx`, `so_desc_tx`, `so_module_vc`, `so_usereditable_en`) VALUES
(NULL, 'files_bu', 'Backup files', 'day', 'never|day|week|month', 'Frequence to backup files from the disk', 'Backup Config', 'true');
