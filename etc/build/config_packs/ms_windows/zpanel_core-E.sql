/*
/*
* ZPanelX Database Schema
*/
/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE `zpanel_core` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `zpanel_core`;

/*Table structure for table `x_accounts` */

DROP TABLE IF EXISTS `x_accounts`;

CREATE TABLE `x_accounts` (
  `ac_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ac_user_vc` varchar(50) DEFAULT NULL,
  `ac_pass_vc` varchar(50) DEFAULT NULL,
  `ac_email_vc` varchar(250) DEFAULT NULL,
  `ac_reseller_fk` int(6) DEFAULT NULL,
  `ac_package_fk` int(6) DEFAULT NULL,
  `ac_group_fk` int(6) DEFAULT NULL,
  `ac_usertheme_vc` varchar(45) DEFAULT NULL,
  `ac_usercss_vc` varchar(45) DEFAULT NULL,
  `ac_enabled_in` int(1) DEFAULT '1',
  `ac_lastlogon_ts` int(30) DEFAULT NULL,
  `ac_notice_tx` text,
  `ac_resethash_tx` text,
  `ac_created_ts` int(30) DEFAULT NULL,
  `ac_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ac_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `x_accounts` */

insert  into `x_accounts`(`ac_id_pk`,`ac_user_vc`,`ac_pass_vc`,`ac_email_vc`,`ac_reseller_fk`,`ac_package_fk`,`ac_group_fk`,`ac_usertheme_vc`,`ac_usercss_vc`,`ac_enabled_in`,`ac_lastlogon_ts`,`ac_notice_tx`,`ac_resethash_tx`,`ac_created_ts`,`ac_deleted_ts`) values (1,'zadmin','5f4dcc3b5aa765d61d8327deb882cf99','zadmin@localhost',1,1,1,'zpanelx','default',1,0,'Welcome to your new ZPanel installation! You can remove this message from the Client Notice Manager module. This module allows you to notify your clients of service outages upgrades and new features etc :-)','',NULL,NULL);

/*Table structure for table `x_aliases` */

DROP TABLE IF EXISTS `x_aliases`;

CREATE TABLE `x_aliases` (
  `al_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `al_acc_fk` int(6) DEFAULT NULL,
  `al_address_vc` varchar(255) DEFAULT NULL,
  `al_destination_vc` varchar(255) DEFAULT NULL,
  `al_created_ts` int(30) DEFAULT NULL,
  `al_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`al_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_aliases` */

/*Table structure for table `x_bandwidth` */

DROP TABLE IF EXISTS `x_bandwidth`;

CREATE TABLE `x_bandwidth` (
  `bd_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `bd_acc_fk` int(6) DEFAULT NULL,
  `bd_month_in` int(6) DEFAULT NULL,
  `bd_transamount_bi` bigint(20) DEFAULT NULL,
  `bd_diskamount_bi` bigint(20) DEFAULT NULL,
  `bd_diskover_in` int(6) DEFAULT NULL,
  `bd_diskcheck_in` int(6) DEFAULT NULL,
  `bd_transover_in` int(6) DEFAULT NULL,
  `bd_transcheck_in` int(6) DEFAULT NULL,
  PRIMARY KEY (`bd_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_bandwidth` */

/*Table structure for table `x_cronjobs` */

DROP TABLE IF EXISTS `x_cronjobs`;

CREATE TABLE `x_cronjobs` (
  `ct_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ct_acc_fk` int(6) DEFAULT NULL,
  `ct_script_vc` varchar(255) DEFAULT NULL,
  `ct_timing_vc` varchar(255) DEFAULT NULL,
  `ct_fullpath_vc` varchar(255) DEFAULT NULL,
  `ct_description_tx` text,
  `ct_created_ts` int(30) DEFAULT NULL,
  `ct_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ct_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_cronjobs` */

/*Table structure for table `x_distlists` */

DROP TABLE IF EXISTS `x_distlists`;

CREATE TABLE `x_distlists` (
  `dl_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `dl_acc_fk` int(6) DEFAULT NULL,
  `dl_address_vc` varchar(255) DEFAULT NULL,
  `dl_created_ts` int(30) DEFAULT NULL,
  `dl_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`dl_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_distlists` */

/*Table structure for table `x_distlistusers` */

DROP TABLE IF EXISTS `x_distlistusers`;

CREATE TABLE `x_distlistusers` (
  `du_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `du_distlist_fk` int(6) DEFAULT NULL,
  `du_address_vc` varchar(255) DEFAULT NULL,
  `du_created_ts` int(30) DEFAULT NULL,
  `du_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`du_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_distlistusers` */

/*Table structure for table `x_dns` */

DROP TABLE IF EXISTS `x_dns`;

CREATE TABLE `x_dns` (
  `dn_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `dn_acc_fk` int(6) DEFAULT NULL,
  `dn_name_vc` varchar(255) DEFAULT NULL,
  `dn_vhost_fk` int(6) DEFAULT NULL,
  `dn_type_vc` varchar(50) DEFAULT NULL,
  `dn_host_vc` varchar(50) DEFAULT NULL,
  `dn_ttl_in` int(30) DEFAULT NULL,
  `dn_target_vc` varchar(50) DEFAULT NULL,
  `dn_texttarget_tx` text,
  `dn_priority_in` int(50) DEFAULT NULL,
  `dn_weight_in` int(50) DEFAULT NULL,
  `dn_port_in` int(50) DEFAULT NULL,
  `dn_created_ts` int(30) DEFAULT NULL,
  `dn_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`dn_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_dns` */

/*Table structure for table `x_faqs` */

DROP TABLE IF EXISTS `x_faqs`;

CREATE TABLE `x_faqs` (
  `fq_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fq_acc_fk` int(6) DEFAULT NULL,
  `fq_question_tx` text,
  `fq_answer_tx` text,
  `fq_global_in` int(1) DEFAULT NULL,
  `fq_created_ts` int(30) DEFAULT NULL,
  `fq_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`fq_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

/*Data for the table `x_faqs` */

insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (1,1,'How can I update my personal contact details?','From the control panel homepage please click on the \'My Account\' icon to enable you to update your personal details.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (2,1,'How do I change my password?','Your ZPanel and MySQL password can be easily changed using the \'Change Password\' icon on the control panel.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (3,1,'I don’t think one of the services(Apache, MySQL, FTP, etc) are running. Is there any easy way to check?','ZPanel comes with a service monitoring system that checks to make sure all the services are up and running, Simply go to your Control Panel Home and select the module called “Service Status”. From there you will be able to see if any of the services are down or up.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (4,1,'How can I set my domain to work with my ZPanel Account?','To setup up a domain with ZPanel first thing you need to do is go “Domains” and add your to the list. Next you need to set the Name Server on your Domain Registrar to match that of your host. This information can be obtained by contacting your host.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (7,1,'How can I create a MySQL Database?','To create a MySQL database simply go to the section of the panel called “Database Management” and select the module called “MySQL Databases” from here you will easily be able to add and manage databases on your account.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (8,1,'What is phpMyAdmin?','phpMyAdmin is an open source tool intended to handle the administration of MySQL databases. It can perform various tasks such as creating, modifying or deleting databases, tables, fields or rows or executing SQL statements',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (9,1,'How do I create FTP Accounts?','You can create FTP accounts by going to “FTP Accounts” from their you can add accounts and manage quotas and directories. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (10,1,'How to Password Protect Directories?','Go to Advanced and select the module “Password Protect Directories” From here you can generate .htaccess files to lock down directories on your site to only people with a login and password.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (11,1,'How do I create an E-Mail Account?','Go to the Mail section of ZPanel and select the module called “Mailboxes”, from here you can create E-Mail account for each domain setup on your account. You can also reset passwords to previously created accounts.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (12,1,'How do I create a Mail Alias?','Go to the Mail section of ZPanel and select the module called “Aliases”, from here you can create Alias E-Mail accounts for each previously created E-Mail account. All mail sent to the alias will be delivered to the master e-mail account.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (13,1,'How can I create a Mailing List?','Mailing lists can be setup by going to the Mail section of ZPanel and select the module called “Distribution Lists”, from here you can create Mailing lists by creating an E-mail Account. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (14,1,'How do I use Mail Forwards?','Go to the Mail section of ZPanel and select the module called “Forwards”, from here you can create E-Mail address on your domains that will forward to other E-Mail addresses that are on different servers like “G-Mail, Yahoo, and MSN”. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (15,1,'What are Subdomains?','A subdomain combines a unique identifier with a domain name to become essentially a \"domain within a domain.\" The unique identifier simply replaces the www in the web address. Yahoo!, for example, uses subdomains such as mail.yahoo.com and music.yahoo.com to reference its mail and music services, under the umbrella of www.yahoo.com. They can be created by using the Subdomain module in the Domains section. You can assign directories for each sub domain from the module.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (16,1,'How can I view how much Data I have used?','You can view how much data you have used by accessing the “Usage Viewer” under the Account Information section of ZPanel. It displays account information in different formats. It displays Data Usage, Domain Usage, Bandwidth Usage, MySQL Usage, and much more.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (17,1,'How can I access Webmail?','Go to the Mail section of ZPanel and select the module called “Webmail”, from here you can login to your E-Mail account and view and create messages. ',1,NULL,NULL);

/*Table structure for table `x_forwarders` */

DROP TABLE IF EXISTS `x_forwarders`;

CREATE TABLE `x_forwarders` (
  `fw_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `fw_acc_fk` int(6) DEFAULT NULL,
  `fw_address_vc` varchar(255) DEFAULT NULL,
  `fw_destination_vc` varchar(255) DEFAULT NULL,
  `fw_keepmessage_in` int(1) DEFAULT '1',
  `fw_created_ts` int(30) DEFAULT NULL,
  `fw_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`fw_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_forwarders` */

/*Table structure for table `x_ftpaccounts` */

DROP TABLE IF EXISTS `x_ftpaccounts`;

CREATE TABLE `x_ftpaccounts` (
  `ft_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ft_acc_fk` int(6) DEFAULT NULL,
  `ft_user_vc` varchar(20) DEFAULT NULL,
  `ft_directory_vc` varchar(255) DEFAULT NULL,
  `ft_access_vc` varchar(20) DEFAULT NULL,
  `ft_password_vc` varchar(50) DEFAULT NULL,
  `ft_created_ts` int(6) DEFAULT NULL,
  `ft_deleted_ts` int(6) DEFAULT NULL,
  PRIMARY KEY (`ft_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_ftpaccounts` */

/*Table structure for table `x_groups` */

DROP TABLE IF EXISTS `x_groups`;

CREATE TABLE `x_groups` (
  `ug_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ug_name_vc` varchar(20) DEFAULT NULL,
  `ug_notes_tx` text,
  `ug_reseller_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`ug_id_pk`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `x_groups` */

insert  into `x_groups`(`ug_id_pk`,`ug_name_vc`,`ug_notes_tx`,`ug_reseller_fk`) values (1,'Administrators','The main administration group, this group allows access to all areas of ZPanel.',1);
insert  into `x_groups`(`ug_id_pk`,`ug_name_vc`,`ug_notes_tx`,`ug_reseller_fk`) values (2,'Resellers','Resellers have the ability to manage, create and maintain user accounts within ZPanel.',1);
insert  into `x_groups`(`ug_id_pk`,`ug_name_vc`,`ug_notes_tx`,`ug_reseller_fk`) values (3,'Users','Users have basic access to ZPanel.',1);

/*Table structure for table `x_htaccess` */

DROP TABLE IF EXISTS `x_htaccess`;

CREATE TABLE `x_htaccess` (
  `ht_id_pk` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ht_acc_fk` int(6) DEFAULT NULL,
  `ht_user_vc` varchar(10) DEFAULT NULL,
  `ht_dir_vc` varchar(255) DEFAULT NULL,
  `ht_created_ts` int(30) DEFAULT NULL,
  `ht_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ht_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_htaccess` */

/*Table structure for table `x_logs` */

DROP TABLE IF EXISTS `x_logs`;

CREATE TABLE `x_logs` (
  `lg_id_pk` int(9) unsigned NOT NULL AUTO_INCREMENT,
  `lg_user_fk` int(6) NOT NULL DEFAULT '1',
  `lg_code_vc` varchar(10) DEFAULT NULL,
  `lg_module_vc` varchar(25) DEFAULT NULL,
  `lg_detail_tx` text,
  `lg_stack_tx` text,
  `lg_when_ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lg_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_logs` */

/*Table structure for table `x_mailboxes` */

DROP TABLE IF EXISTS `x_mailboxes`;

CREATE TABLE `x_mailboxes` (
  `mb_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mb_acc_fk` int(6) DEFAULT NULL,
  `mb_address_vc` varchar(255) DEFAULT NULL,
  `mb_enabled_in` int(1) DEFAULT '1',
  `mb_created_ts` int(30) DEFAULT NULL,
  `mb_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`mb_id_pk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `x_mailboxes` */

/*Table structure for table `x_modcats` */

DROP TABLE IF EXISTS `x_modcats`;

CREATE TABLE `x_modcats` (
  `mc_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mc_name_vc` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mc_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `x_modcats` */

insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (1,'Account Information');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (2,'Server Admin');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (3,'Advanced');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (4,'Database Management');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (5,'Domain Management');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (6,'Mail');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (7,'Reseller');
insert  into `x_modcats`(`mc_id_pk`,`mc_name_vc`) values (8,'File Management');

/*Table structure for table `x_modules` */

DROP TABLE IF EXISTS `x_modules`;

CREATE TABLE `x_modules` (
  `mo_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mo_category_fk` int(6) NOT NULL DEFAULT '1',
  `mo_name_vc` varchar(200) NOT NULL,
  `mo_version_in` int(10) DEFAULT NULL,
  `mo_folder_vc` varchar(255) DEFAULT NULL,
  `mo_type_en` enum('user','system','modadmin','lang') NOT NULL DEFAULT 'user',
  `mo_desc_tx` text,
  `mo_installed_ts` int(30) DEFAULT NULL,
  `mo_enabled_en` enum('true','false') NOT NULL DEFAULT 'true',
  `mo_updatever_vc` varchar(10) DEFAULT NULL,
  `mo_updateurl_tx` text,
  PRIMARY KEY (`mo_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

/*Data for the table `x_modules` */

insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (1,2,'PHPInfo',100,'phpinfo','user','PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extentsions and configuration details.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (3,2,'Shadowing',100,'shadowing','user','From here you can shadow any of your client\'s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (4,2,'ZPanel Config',100,'zpanelconfig','user','Changes made here affect the entire ZPanel configuration, please double check everything before saving changes.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (5,2,'ZPanel News',100,'news','user','Find out all the latest news and information from the ZPanel project.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (6,2,'Updates',100,'updates','user','Check to see if there are any available updates to your version of the ZPanel software.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (8,4,'phpMyAdmin',100,'phpmyadmin','user','phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (9,1,'My Account',100,'my_account','user','Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\r\n',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (10,6,'WebMail',100,'webmail','user','Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (11,1,'Change Password',100,'password_assistant','user','Change your current control panel password.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (12,3,'Backup',100,'backupmgr','user','The backup manager module enables you to backup your entire hosting account including all your MySQL&reg databases.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (14,3,'Service Status',100,'services','user','Here you can check the current status of our services and see what services are up and running and which are down and not.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (15,5,'Domains',100,'domains','user','This module enables you to add or configure domain web hosting on your account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (16,5,'Parked Domains',100,'parked_domains','user','Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (17,5,'Sub Domains',100,'sub_domains','user','This module enables you to add or configure domain web hosting on your account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (18,2,'Module Admin',100,'moduleadmin','user','Administer or configure modules registered with module admin',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (19,7,'Manage Clients',100,'manage_clients','user','The account manager enables you to view, update and create client accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (20,7,'Package Manager',100,'packages','user','Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (22,3,'Cron Manager',100,'cron','user','Here you can configure PHP scripts to run automatically at different time intervals.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (23,2,'phpSysInfo',100,'phpsysinfo','user','phpSysInfo is a web-based server hardware monitoring tool which enables you to see detailed hardware statistics of your server.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (24,4,'MySQL Database',100,'mysql_databases','user','MySQL&reg databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg databases.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (25,1,'Usage Viewer',100,'usage_viewer','user','The account usage screen enables you to see exactly what you are currently using on your hosting package.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (26,8,'FTP Accounts',100,'ftp_management','user','Using this module you can create FTP accounts which will enable you and any other accounts you create to have the ability to upload and manage files on your hosting space.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (27,3,'FAQ\'s',100,'faqs','user','Please find a list of the most common questons from users, if you are unable to find a solution to your problem below please then contact your hosting provider. Simply click on the FAQ below to view the solution.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (28,0,'Apache Config',100,'apache_admin','modadmin','This module enables you to configure Apache Vhost settings for your hosting accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (29,5,'DNS Manager',100,'dns_manager','user',NULL,0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (30,0,'DNS Config',100,'dns_admin','modadmin','This module enables you to configure DNS settings for the DNS Manager',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (31,7,'Manage Groups',100,'manage_groups','user','Manage user groups to enable greater control over module permission.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (32,6,'Mailboxes',100,'mailboxes','user','Using this module you have the ability to create IMAP and POP3 Mailboxes.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (33,6,'Forwards',100,'forwarders','user','Using this module you have the ability to create mail forwarders.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (34,6,'Distribution Lists',100,'distlists','user','This module enables you to create and manage email distribution groups.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (35,6,'Aliases',100,'aliases','user','Using this module you have the ability to create alias mailboxes to existing accounts.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (36,0,'Mail Config',100,'mail_admin','modadmin','This module enables you to configure your mail options',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (39,4,'MySQL Users',100,'mysql_users','user','MySQL&reg Users allows you to add users and permissions to your MySQL&reg databases.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (40,0,'FTP Config',100,'ftp_admin','modadmin','This module enables you to configure FTP settings for your hosting accounts.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (41,0,'Backup Config',100,'backup_admin','modadmin','This module enables you to configure Backup settings for your hosting accounts.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (42,7,'Client Notice Manager',100,'client_notices','user','Enables resellers to set global notices for their clients.',NULL,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (43,3,'Protect Directories',100,'htpasswd','user','This module enables you to configure .htaccess files and users to protect your web directories.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (46,7,'Theme Manager',100,'theme_manager','user','Enables the reseller to set themes configurations for their clients.',0,'true','','');
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (47,3,'Webalizer Stats',100,'webalizer_stats','user','You can view many statistics such as visitor infomation, bandwidth used, referal infomation and most viewed pages etc. Web stats are based on Domains and sub-domains so to view web stats for a particular domain or subdomain use the drop-down menu to select the domain or sub-domain you want to view web stats for.',0,'true','',NULL);

/*Table structure for table `x_mysql` */

DROP TABLE IF EXISTS `x_mysql`;

CREATE TABLE `x_mysql` (
  `my_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `my_acc_fk` int(6) DEFAULT NULL,
  `my_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `my_usedspace_bi` bigint(50) DEFAULT '0',
  `my_created_ts` int(30) DEFAULT NULL,
  `my_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`my_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql` */

/*Table structure for table `x_mysql_databases` */

DROP TABLE IF EXISTS `x_mysql_databases`;

CREATE TABLE `x_mysql_databases` (
  `my_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `my_acc_fk` int(6) DEFAULT NULL,
  `my_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `my_usedspace_bi` bigint(50) DEFAULT '0',
  `my_created_ts` int(30) DEFAULT NULL,
  `my_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`my_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql_databases` */

/*Table structure for table `x_mysql_dbmap` */

DROP TABLE IF EXISTS `x_mysql_dbmap`;

CREATE TABLE `x_mysql_dbmap` (
  `mm_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mm_acc_fk` int(6) DEFAULT NULL,
  `mm_user_fk` int(6) DEFAULT NULL,
  `mm_database_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`mm_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql_dbmap` */

/*Table structure for table `x_mysql_users` */

DROP TABLE IF EXISTS `x_mysql_users`;

CREATE TABLE `x_mysql_users` (
  `mu_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mu_acc_fk` int(6) DEFAULT NULL,
  `mu_name_vc` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mu_database_fk` int(6) DEFAULT NULL,
  `mu_access_vc` varchar(40) DEFAULT NULL,
  `mu_pass_vc` varchar(40) DEFAULT NULL,
  `mu_created_ts` int(30) DEFAULT NULL,
  `mu_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`mu_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_mysql_users` */

/*Table structure for table `x_packages` */

DROP TABLE IF EXISTS `x_packages`;

CREATE TABLE `x_packages` (
  `pk_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pk_name_vc` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `pk_reseller_fk` int(6) DEFAULT NULL,
  `pk_enablephp_in` int(1) DEFAULT '0',
  `pk_enablecgi_in` int(1) DEFAULT '0',
  `pk_created_ts` int(30) DEFAULT NULL,
  `pk_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`pk_id_pk`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `x_packages` */

insert  into `x_packages`(`pk_id_pk`,`pk_name_vc`,`pk_reseller_fk`,`pk_enablephp_in`,`pk_enablecgi_in`,`pk_created_ts`,`pk_deleted_ts`) values (1,'Administration',1,1,1,NULL,NULL);
insert  into `x_packages`(`pk_id_pk`,`pk_name_vc`,`pk_reseller_fk`,`pk_enablephp_in`,`pk_enablecgi_in`,`pk_created_ts`,`pk_deleted_ts`) values (2,'Demo',1,0,0,NULL,NULL);

/*Table structure for table `x_permissions` */

DROP TABLE IF EXISTS `x_permissions`;

CREATE TABLE `x_permissions` (
  `pe_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `pe_group_fk` int(6) DEFAULT NULL,
  `pe_module_fk` int(6) DEFAULT NULL,
  PRIMARY KEY (`pe_id_pk`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

/*Data for the table `x_permissions` */

insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (1,1,18);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (2,1,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (3,2,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (4,3,35);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (5,1,28);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (6,1,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (7,2,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (8,3,12);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (9,1,41);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (10,1,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (11,2,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (12,3,11);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (13,1,42);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (14,2,42);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (15,1,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (16,2,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (17,3,22);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (18,1,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (19,2,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (20,3,34);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (21,1,30);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (22,1,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (23,2,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (24,3,29);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (25,1,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (26,2,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (27,3,15);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (28,1,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (29,2,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (30,3,27);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (31,1,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (32,2,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (33,3,33);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (34,1,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (35,2,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (36,3,26);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (37,1,40);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (38,1,36);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (39,1,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (40,2,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (41,3,32);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (42,1,19);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (43,2,19);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (44,1,31);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (45,2,31);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (46,1,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (47,2,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (48,3,9);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (49,1,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (50,2,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (51,3,24);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (52,1,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (53,2,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (54,3,39);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (55,1,20);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (56,2,20);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (57,1,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (58,2,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (59,3,16);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (60,1,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (61,2,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (62,3,1);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (63,1,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (64,2,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (65,3,8);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (66,1,23);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (67,1,43);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (68,2,43);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (69,3,43);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (70,1,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (71,2,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (72,3,14);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (73,1,3);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (74,2,3);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (75,1,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (76,2,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (77,3,17);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (78,1,46);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (79,2,46);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (80,1,6);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (81,1,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (82,2,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (83,3,25);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (84,1,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (85,2,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (86,3,47);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (87,1,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (88,2,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (89,3,10);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (90,1,4);
insert  into `x_permissions`(`pe_id_pk`,`pe_group_fk`,`pe_module_fk`) values (91,1,5);

/*Table structure for table `x_profiles` */

DROP TABLE IF EXISTS `x_profiles`;

CREATE TABLE `x_profiles` (
  `ud_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `ud_user_fk` int(6) DEFAULT NULL,
  `ud_fullname_vc` varchar(100) DEFAULT NULL,
  `ud_language_vc` varchar(10) DEFAULT 'en',
  `ud_group_fk` int(6) DEFAULT NULL,
  `ud_package_fk` int(6) DEFAULT NULL,
  `ud_address_tx` text,
  `ud_postcode_vc` varchar(20) DEFAULT NULL,
  `ud_phone_vc` varchar(20) DEFAULT NULL,
  `ud_created_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ud_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `x_profiles` */

insert  into `x_profiles`(`ud_id_pk`,`ud_user_fk`,`ud_fullname_vc`,`ud_language_vc`,`ud_group_fk`,`ud_package_fk`,`ud_address_tx`,`ud_postcode_vc`,`ud_phone_vc`,`ud_created_ts`) values (1,1,'Default Zadmin','en',1,1,'1 Example Road, Ipswich, Suffolk','IP9 2HL','+44(1473) 000 000',0);

/*Table structure for table `x_quotas` */

DROP TABLE IF EXISTS `x_quotas`;

CREATE TABLE `x_quotas` (
  `qt_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `qt_package_fk` int(6) DEFAULT NULL,
  `qt_domains_in` int(6) DEFAULT '0',
  `qt_subdomains_in` int(6) DEFAULT '0',
  `qt_parkeddomains_in` int(6) DEFAULT '0',
  `qt_mailboxes_in` int(6) DEFAULT '0',
  `qt_fowarders_in` int(6) DEFAULT '0',
  `qt_distlists_in` int(6) DEFAULT '0',
  `qt_ftpaccounts_in` int(6) DEFAULT '0',
  `qt_mysql_in` int(6) DEFAULT '0',
  `qt_diskspace_bi` bigint(20) DEFAULT '0',
  `qt_bandwidth_bi` bigint(20) DEFAULT '0',
  `qt_bwenabled_in` int(1) DEFAULT '0',
  `qt_dlenabled_in` int(1) DEFAULT '0',
  `qt_totalbw_fk` int(30) DEFAULT NULL,
  `qt_minbw_fk` int(30) DEFAULT NULL,
  `qt_maxcon_fk` int(30) DEFAULT NULL,
  `qt_filesize_fk` int(30) DEFAULT NULL,
  `qt_filespeed_fk` int(30) DEFAULT NULL,
  `qt_filetype_vc` varchar(30) NOT NULL DEFAULT '*',
  `qt_modified_in` int(1) DEFAULT '0',
  PRIMARY KEY (`qt_id_pk`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `x_quotas` */

insert  into `x_quotas`(`qt_id_pk`,`qt_package_fk`,`qt_domains_in`,`qt_subdomains_in`,`qt_parkeddomains_in`,`qt_mailboxes_in`,`qt_fowarders_in`,`qt_distlists_in`,`qt_ftpaccounts_in`,`qt_mysql_in`,`qt_diskspace_bi`,`qt_bandwidth_bi`,`qt_bwenabled_in`,`qt_dlenabled_in`,`qt_totalbw_fk`,`qt_minbw_fk`,`qt_maxcon_fk`,`qt_filesize_fk`,`qt_filespeed_fk`,`qt_filetype_vc`,`qt_modified_in`) values (1,1,5,10,5,10,100,5,10,10,2048000000,10240000000,0,0,NULL,NULL,NULL,NULL,NULL,'*',1);

/*Table structure for table `x_settings` */

DROP TABLE IF EXISTS `x_settings`;

CREATE TABLE `x_settings` (
  `so_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `so_name_vc` varchar(50) DEFAULT NULL,
  `so_cleanname_vc` varchar(50) DEFAULT NULL,
  `so_value_tx` text,
  `so_defvalues_tx` text,
  `so_desc_tx` text,
  `so_module_vc` varchar(50) DEFAULT NULL,
  `so_usereditable_en` enum('true','false') DEFAULT 'false',
  PRIMARY KEY (`so_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;

/*Data for the table `x_settings` */

insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (6,'dbversion','ZPanel version','10.0.0',NULL,'Database Version','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (7,'zpanel_root','ZPanel root path','E:/zpanel/panel/',NULL,'Zpanel Web Root','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (8,'module_icons_pr','Icons per Row','10',NULL,'Set the number of icons to display before beginning a new line.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (10,'zpanel_df','Date Format','H:i jS M Y T',NULL,'Set the date format used by modules.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (13,'servicechk_to','Service Check Timeout','2',NULL,'Service Check Timeout','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (14,'root_drive','Root Drive','/',NULL,'The root drive where ZPanel is installed.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (16,'php_exer','PHP executable','php',NULL,'PHP Executable','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (17,'temp_dir','Temp Directory','C:/windows/temp/',NULL,'Global temp directory.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (18,'news_url','ZPanel News API URL','http://api.zpanelcp.com/latestnews.json',NULL,'Zpanel News URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (19,'update_url','ZPanel Update API URL','http://api.zpanelcp.com/latestversion.json',NULL,'Zpanel Update URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (21,'server_ip','Server IP Address','172.167.22.10',NULL,'If set this will use this manually entered server IP address which is the prefered method for use behind a firewall.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (22,'zip_exe','ZIP Exe','E:/zpanel/bin/7zip/7z.exe',NULL,'Path to the ZIP Executable','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (24,'disable_hostsen','Disable auto HOSTS file entry','false','true|false','Disable Host Entries','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (25,'latestzpversion','Cached version of latest zpanel version','10.0.0',NULL,'This is used for caching the latest version of ZPanel.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (26,'logmode','Debug logging mode','db','db|file|email','The default mode to log all errors in.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (27,'logfile','ZPanel Log file','E:/zpanel/logs/log.txt',NULL,'If loggging is set to \'file\' mode this is the path to the log file that is to be used by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (28,'apikey','XMWS API Key','ee8795c8c53bfdb3b2cc595186b68912',NULL,'The secret API key for the server.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (29,'email_from_address','From Address','zpanel@localhost',NULL,'The email address to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (30,'email_from_name','From Name','Control Panel',NULL,'The name to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (31,'email_smtp','Use SMTP','false','true|false','Use SMTP server to send emails from. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (32,'smtp_auth','Use AUTH','false','true|false','SMTP requires authentication. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (33,'smtp_server','SMTP Server','smtp.gmail.com',NULL,'The address of the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (34,'smtp_port','SMTP Port','465',NULL,'The port address of the SMTP server (usually 25)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (35,'smtp_username','SMTP User','youremail@gmail.com',NULL,'Username for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (36,'smtp_password','SMTP Pass','',NULL,'Password for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (37,'smtp_secure','SMTP Auth method','ssl','false|ssl|tls','If specified will attempt to use encryption to connect to the server, if \'false\' this is disabled. Available options: false, ssl, tls','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (38,'daemon_lastrun',NULL,'1330544970',NULL,'Timestamp of when the daemon last ran.',NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (39,'daemon_dayrun',NULL,'1330542304',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (40,'daemon_weekrun',NULL,'1330102353',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (41,'daemon_monthrun',NULL,'1328908442',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (42,'purge_bu','Purge Backups','true','true|false','Delete client backups after alloted time has elapsed to help save diskspace (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (43,'purge_date','Purge Date','30',NULL,'Time in days backups are safe from being deleted. After days have elapsed, older backups will be deleted on Daemon Day Run','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (44,'disk_bu','Disk Backups','true','true|false','Allow users to create and save backups of their home directories to disk. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (45,'schedule_bu','Daily Backups','true','true|false','Maks a daily backup of each clients data, including MySQL databases to their backup folder. Backups will still be created if Disk Backups are set to false. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (46,'ftp_db','FTP Database','zpanel_filezilla',NULL,'The name of the ftp server database','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (47,'ftp_php','FTP PHP','filezilla.php',NULL,'Name of PHP to include when adding FTP data.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (48,'ftp_service','FTP Service Name','FileZilla server.exe',NULL,'The name of the FTP service','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (49,'ftp_service_root','FTP Service Root','E:/zpanel/bin/filezilla/',NULL,'The path to the service executable if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (50,'ftp_config_file','FTP Config File','E:/zpanel/bin/filezilla/FileZilla Server.xml',NULL,'The path to the configuration file if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (51,'mailserver_db','Mailserver Database','zpanel_hmail',NULL,'The name of the mail server database','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (52,'hmailserver_et','Hmail Encryption Type','2',NULL,'Type of encryption uses for hMailServer passwords','Mail Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (53,'max_mail_size','Max Mailbox Size','200',NULL,'Maximum size in megabytes allowed for mailboxes. Default = 200','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (54,'mailserver_php','Mailserver PHP','hmail.php',NULL,'Name of PHP to include when adding mailbox data.','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (55,'remove_orphan','Remove Orphans','true','true|false','When domains are deleted, also delete all mailboxes for that domain when the daemon runs. (true/false)','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (56,'named_dir','Named Directory','E:/zpanel/configs/bind/etc/',NULL,'Path to the directory where named.conf is stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (57,'named_conf','Named Config','named.conf',NULL,'Named configuration file','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (58,'zone_dir','Zone Directory','E:/zpanel/configs/bind/zones/',NULL,'Path to where DNS zone files are stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (59,'refresh_ttl','SOA Refesh TTL','3600000',NULL,'Global refresh TTL.  Default = 21600 (6 hours)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (60,'retry_ttl','SOA Retry TTL','300',NULL,'Global retry TTL. Default = 3600 (1 hour)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (61,'expire_ttl','SOA Expire TTL','86400',NULL,'Global expire TTL. Default = 604800 (1 week)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (62,'minimum_ttl','SOA Minimum TTL','3600',NULL,'Global minimum TTL. Default = 86400 (1 day)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (63,'custom_ip','Allow Custom IP','true','true|false','Allow users to change IP settings in A records. If set to false, IP is locked to server IP setting in ZPanel Config','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (64,'bind_dir','Path to BIND Root','E:/zpanel/bin/bind/bin/',NULL,'Path to the root directory where BIND is installed.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (65,'bind_service','BIND Service Name','named',NULL,'Name of the BIND service','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (66,'allow_xfer','Allow Zone Transfers','any',NULL,'Setting to restrict zone transfers in setting: allow-transfer {}; Default = all','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (67,'allowed_types','Allowed Record Types','A AAAA CNAME MX TXT SRV SPF DKIM NS',NULL,'Types of records allowed seperated by a space. Default = A AAAA CNAME MX TXT SRV SPF NS','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (68,'bind_log','Bind Log','E:/zpanel/logs/bind/bind.log',NULL,'Path and name of the Bind Log','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (69,'hosted_dir','Vhosts Directory','E:/zpanel/hostdata/',NULL,'Virtual host directory','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (70,'disable_hostsen','Disable HOSTS file entries','false','true|false','Disable host entries','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (71,'apache_vhost','Apache VHOST Conf','E:/zpanel/configs/apache/httpd-vhosts.conf',NULL,'The full system patch and filename of the Apache VHOST configuration name.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (72,'php_handler','PHP Handler','AddType application/x-httpd-php .php3 .php',NULL,'The PHP Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (73,'cgi_handler','CGI Handler','ScriptAlias /cgi-bin/ \"/_cgi-bin/\"\r\n<location /cgi-bin>\r\nAddHandler cgi-script .cgi .pl\r\nOptions +ExecCGI -Indexes\r\n</location>',NULL,'The CGI Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (74,'global_vhcustom','Global VHost Entry',NULL,NULL,'Extra directives for all apache vhost\'s.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (75,'static_dir','Static Pages Directory','E:/zpanel/panel/etc/static/',NULL,'The ZPanel static directory, used for storing welcome pages etc. etc.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (76,'parking_path','Vhost Parking Path','E:/zpanel/panel/etc/static/parking/',NULL,'The path to the parking website, this will be used by all clients.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (78,'shared_domains','Shared Domains','no-ip,dydns',NULL,'Domains entered here can be shared across multiple accounts. Seperate domains with , example: no-ip,dydns','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (79,'upload_temp_dir','Upload Temp Directory','E:/zpanel/panel/etc/tmp/',NULL,'The path to the Apache Upload directory (with trailing slash)','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (80,'apache_port','Apache Port','80',NULL,'Apache service port','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (81,'dir_index','Directory Indexes','DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm',NULL,'Directory Index','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (82,'suhosin_value','Suhosin Value','php_admin_value suhosin.executor.func.blacklist \"passthru, show_source, shell_exec, system, pcntl_exec, popen, pclose, proc_open, proc_nice, proc_terminate, proc_get_status, proc_close, leak, apache_child_terminate, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, escapeshellcmd, escapeshellarg\"',NULL,'Suhosin configuration for virtual host  blacklisting commands','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (83,'openbase_seperator','Open Base Seperator',';',NULL,'Seperator flag used in open_base_directory setting','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (84,'openbase_temp','Open Base Temp Directory','c:/windows/temp',NULL,'Temp directory used in open_base_directory setting','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (85,'access_log_format','Access Log Format','combined','combined|common','Log format for the Apache access log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (86,'bandwidth_log_format','Bandwidth Log Format','common','combined|common','Log format for the Apache bandwidth log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (87,'global_zpcustom','Global ZPanel Entry',NULL,NULL,'Extra directives for Zpanel default vhost.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (88,'use_openbase','Use Open Base Dir','true','true|false','Enable openbase directory for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (89,'use_suhosin','Use Suhosin','true','true|false','Enable Suhosin for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (90,'zpanel_domain','ZPanel Domain','zpanel.ztest.com',NULL,'Domain that the control panel is installed under.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (91,'log_dir','Log Directory','E:/zpanel/logs/',NULL,'Root path to directory log folders','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (92,'apache_changed','Apache Changed','1330542288','true|false','If set, Apache Config daemon hook will write the vhost config file changes.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (94,'apache_allow_disabled','Allow Disabled','true','true|false','Allow webhosts to remain active even if a user has been disabled.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (95,'apache_budir','VHost Backup Dir','E:/zpanel/backups/apachebackups/',NULL,'Directory that vhost.conf backups are stored.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (96,'apache_purgebu','Purge Backups','true','true|false','Old backups are deleted after the date set in Puge Date','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (97,'apache_purge_date','Purge Date','7',NULL,'Time in days that vhost backups are safe from deletion','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (98,'apache_backup','VHost Backup','true','true|false','Backup vhost file before a new one is written','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (99,'zsudo','zsudo path','E:/zpanel/panel/bin/zsudo',NULL,'Path to the zsudo binary used by Apache to run system commands.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (100,'apache_restart','Apache Restart Cmd','-k restart -n \"Apache\"',NULL,'Command line arguments used after the restart service request when reloading Apache.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (101,'httpd_exe','Apache Binary','E:/zpanel/bin/apache/bin/httpd.exe',NULL,'Path to the Apache binary','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (102,'apache_sn','Apache Service Name','httpd',NULL,'Service name used to handle Apache service control','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (103,'daemon_exer',NULL,'E:/zpanel/panel/bin/daemon.php',NULL,'Path to the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (104,'daemon_timing',NULL,'0 * * * *',NULL,'Cron time for when to run the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (105,'cron_file','Cron File','E:/windows/system32/crontab',NULL,'Path to the user cr0n file','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (106,'htpasswd_exe','htpassword Exe','htpasswd',NULL,'Path to htpasswd.exe for protecting directories with .htaccess','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (107,'mysqldump_exe','MySQL Dump','E:/zpanel/bin/mysql/bin/mysqldump.exe',NULL,'Path to MySQL dump','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (108,'named_checkconf','Named CheckConfig','E:/zpanel/bin/bind/bin/named-checkconf.exe',NULL,'Path to named-checkconf bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (109,'named_checkzone','Named CheckZone','E:/zpanel/bin/bind/bin/named-checkzone.exe',NULL,'Path to named-checkzone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (110,'named_compilezone','Named CompileZone','E:/zpanel/bin/bind/bin/named-compilezone.exe',NULL,'	Path to named-compilezone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (111,'dns_hasupdates','DNS Updated',NULL,NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (112,'mailer_type','Mail method','mail','mail|smtp|sendmail','Method to use when sending emails out. (mail = PHP Mail())','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (113,'daemon_run_interval','Number of seconds between each daemon execution','300',NULL,'The total number of seconds between each daemon run (default 300 = 5 mins)','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (114,'debug_mode','ZPanel Debug Mode','dev','dev|prod','Whether or not to show PHP debug errors,warnings and notices','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (115,'password_minlength','Min Password Length','6',NULL,'Minimum length required for new passwords','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (116,'cron_reload','Cron Reload',NULL,NULL,'Cron reload command for apache user in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values ('named_conf_slave','Named Slave Config','named.conf.slave',NULL,'Named Slave Configuration File. Won\'t be output if blank.','DNS Config','true');
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '86400' WHERE  `x_settings`.`so_id_pk` =59;
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '3600' WHERE  `x_settings`.`so_id_pk` =60;
UPDATE  `zpanel_core`.`x_settings` SET  `so_value_tx` =  '3600000' WHERE  `x_settings`.`so_id_pk` =61;

/*Table structure for table `x_translations` */

DROP TABLE IF EXISTS `x_translations`;
CREATE TABLE IF NOT EXISTS `x_translations` (
  `tr_id_pk` int(11) NOT NULL AUTO_INCREMENT,
  `tr_en_tx` text,
  `tr_German_tx` text,
  `tr_French_tx` text,
  `tr_Afrikaans_tx` text,
  `tr_Acholi_tx` text,
  `tr_Albanian_tx` text,
  `tr_Arabic_tx` text,
  `tr_Bulgarian_tx` text,
  `tr_Cantonese_tx` text,
  `tr_Croatian_tx` text,
  `tr_Czech_tx` text,
  `tr_Danish_tx` text,
  `tr_Dutch_tx` text,
  `tr_Farsi_tx` text,
  `tr_FrenchCanadian_tx` text,
  `tr_Greek_tx` text,
  `tr_Hungarian_tx` text,
  `tr_Indonesian_tx` text,
  `tr_Italian_tx` text,
  `tr_Jakartanese_tx` text,
  `tr_Latvian_tx` text,
  `tr_Lithuanian_tx` text,
  `tr_Macedonian_tx` text,
  `tr_Malay_tx` text,
  `tr_Mandarin_tx` text,
  `tr_Norwegian_tx` text,
  `tr_Polish_tx` text,
  `tr_Portuguese_tx` text,
  `tr_Romanian_tx` text,
  `tr_Russian_tx` text,
  `tr_Serbian_tx` text,
  `tr_Shanghainese_tx` text,
  `tr_Slovak_tx` text,
  `tr_Slovenian_tx` text,
  `tr_Spanish_tx` text,
  `tr_Swedish_tx` text,
  `tr_Thai_tx` text,
  `tr_Turkish_tx` text,
  `tr_Ukrainian_tx` text,
  `tr_Vietnamese_tx` text,
  PRIMARY KEY (`tr_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=563 ;
INSERT INTO `x_translations` (`tr_id_pk`, `tr_en_tx`, `tr_German_tx`, `tr_French_tx`, `tr_Afrikaans_tx`, `tr_Acholi_tx`, `tr_Albanian_tx`, `tr_Arabic_tx`, `tr_Bulgarian_tx`, `tr_Cantonese_tx`, `tr_Croatian_tx`, `tr_Czech_tx`, `tr_Danish_tx`, `tr_Dutch_tx`, `tr_Farsi_tx`, `tr_FrenchCanadian_tx`, `tr_Greek_tx`, `tr_Hungarian_tx`, `tr_Indonesian_tx`, `tr_Italian_tx`, `tr_Jakartanese_tx`, `tr_Latvian_tx`, `tr_Lithuanian_tx`, `tr_Macedonian_tx`, `tr_Malay_tx`, `tr_Mandarin_tx`, `tr_Norwegian_tx`, `tr_Polish_tx`, `tr_Portuguese_tx`, `tr_Romanian_tx`, `tr_Russian_tx`, `tr_Serbian_tx`, `tr_Shanghainese_tx`, `tr_Slovak_tx`, `tr_Slovenian_tx`, `tr_Spanish_tx`, `tr_Swedish_tx`, `tr_Thai_tx`, `tr_Turkish_tx`, `tr_Ukrainian_tx`, `tr_Vietnamese_tx`) VALUES
(44, 'Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.', 'Webmail ist ein bequemer Weg f&#252;r Sie, Ihre E-Mail-Konten online zu &#252;berpr&#252;fen, ohne dass eine E-Mail-Client zu konfigurieren.', 'Le webmail est un outil pratique qui vous permet de consulter vos comptes e-mails en ligne sans avoir besoin de configurer un logiciel de messagerie.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Launch Webmail', 'Starten Sie WebMail', 'Ouvrir le webmail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extentsions and configuration details.', 'PHPInfo bietet Ihnen Informationen &#252;ber die PHP-Version auf dem System, sowie PHP installiert extentsions und Konfigurationsm&#246;glichkeiten.', 'PHPInfo fournit des informations tel que la version de PHP qui s''ex&#233;cute sur votre syst&#232;me, le d&#233;tail de sa configuration ainsi que les extensions install&#233;es.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'From here you can shadow any of your client''s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!', 'Von hier aus k&#246;nnen alle Ihre Kunden-Accounts k&#246;nnen Schatten, erm&#246;glicht Ihnen dies automatisch, wenn der Benutzer mit dem Sie remote helfen zu sehen, was sie sehen, anbieten zu k&#246;nnen login!', 'Vous pouvez vous substituer &#224; n''importe quel compte client et vous connecter &#224; son interface, ce qui vous permet d''interagir directement au c&#339;ur du probl&#232;me.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'My Account', 'Mein Konto', 'Mon compte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'Change Password', 'Kennwort &#228;ndern', 'Changer le mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'Shadowing', 'Schattenzugriff', 'Substitution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'ZPanel Config', 'ZPanel Konfiguration', 'Configuration de ZPanel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'ZPanel News', 'ZPanel Aktuelles', 'Actualit&#233; ZPanel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'Updates', 'Aktualisierung', 'Mises &#224; jour', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'Report Bug', 'Fehler melden', 'Signaler un bug', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'Account', 'Konto', 'Compte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'Module Admin', 'Modul Admin', 'Module d''administration ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'Backup', 'Backup', 'Sauvegarde', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'Network Tools', 'Netzwerk-Tools', 'Outils r&#233;seau', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 'Service Status', 'Dienstestatus', 'Statut du service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'PHPInfo', 'PHPInfo', 'PHPInfo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'phpMyAdmin', 'phpMyAdmin', 'phpMyAdmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 'Domains', 'Domains', 'Domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'Sub Domains', 'Sub Domains', 'Sous-domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'Parked Domains', 'geparkte Domains', 'Domaines r&#233;serv&#233;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 'Manage Clients', 'Kunden verwalten', 'G&#233;rer les clients', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 'Package Manager', 'Paket Manager', 'Gestion des packages', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'Server', 'Server', 'Serveur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 'Database', 'Datenbank', 'Base de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 'Advanced', 'Fortgeschritten', 'Avanc&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 'Mail', 'E-Mail', 'e-mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 'Reseller', 'Wiederverk&#228;ufer', 'Revendeur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 'Account Information', 'Account Informationen', 'Informations de compte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 'Server Admin', 'Server Admin', 'Administrateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 'Database Management', 'Datenbank-Verwaltung', 'Gestion de base de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 'Domain Management', 'Verwalten von Domains', 'Gestion de domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 'Find out all the latest news and information from the ZPanel project.', 'Aktuelle News und Informationen rund um ZPanel', 'D&#233;couvrez les toutes  derni&#232;res nouvelles et informations du projet ZPanel.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 'Check to see if there are any available updates to your version of the ZPanel software.', 'Auf Updates und Aktualisierungen Ihrer ZPanel-Version pr&#252;fen', 'V&#233;rifiez s''il n''y a pas des mises &#224; jour disponibles pour votre version de ZPanel.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 'If you have found a bug with ZPanel you can report it here.', 'Wenn Sie einen Fehler innerhalb ZPanel gefunden haben, k&#246;nnen Sie ihn hier melden.', 'Si vous avez trouv&#233; un bug avec ZPanel, vous pouvez le rapporter ici.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 'phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.', 'phpMyAdmin ist ein webbasiertes Werkzeug,mit dem Sie Ihre ZPanel MySQL Datenbanken einfach im Browser verwalten k&#246;nnen', 'phpMyAdmin est une interface qui vous permet de g&#233;rer vos bases de donn&#233;es MySQL via un navigateur.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.', 'Nachfolgend Ihre pers&#246;nlichen Daten. Wir bitten Sie, diese stets aktuell zu halten, damit eine problemlose Kontaktaufnahem unsererseits m&#246;glich ist.', 'Vos informations personnelles. Veuillez les tenir &#224; jour afin de pouvoir vous contacter &#224; propos de votre h&#233;bergement.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 'Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.', 'Webmail ist ein bequemer Weg f&#252;r Sie, Ihre E-Mail-Konten online zu &#252;berpr&#252;fen, ohne dass eine E-Mail-Client zu konfigurieren.', 'Le webmail est un outil pratique qui vous permet de consulter vos comptes e-mails en ligne sans avoir besoin de configurer un logiciel de messagerie.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 'Change your current control panel password.', '&#196;ndern Sie Ihr aktuelles Kennwort.', 'Changez votre mot de passe actuel.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 'The backup manager module enables you to backup your entire hosting account including all your MySQL&reg databases.', 'Mit dem Backup-Manager-Modul k&#246;nnen Sie eine Sicherungskopie Ihres gesamten Hosting-Accounts inklusive aller Ihrer MySQL&#174;-Datenbanken erstellen.', 'Le module de gestion de sauvegarde vous permet de sauvegarder tout votre compte d''h&#233;bergement (FTP, bases de donn&#233;es, e-mails...)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 'You can use the tools below to diagnose issues or to simply test connectivity to other servers or sites around the globe.', 'Die unten aufgef&#252;hrten Werkzeuge stehen zur detaillierten Diagnose oder zum einfachen Verbindungstest zu anderen Seiten und Servern zu Ihrer Verf&#252;gung.', 'Vous pouvez utiliser les outils ci-dessous pour diagnostiquer les probl&#232;mes ou tout simplement pour tester la connectivit&#233; &#224; d''autres serveurs ou des sites &#224; travers le monde.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 'Here you can check the current status of our services and see what services are up and running and which are down and not.', 'Hier k&#246;nnen Sie den aktuellen Status unserer Dienste kontrollieren und sehen, welche Dienste vorhanden sind und laufen.', 'V&#233;rifiez le statut actuel des services du serveur et voir lesquels sont en fonctionnement et ceux qui ne le sont plus.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 'This module enables you to add or configure domain web hosting on your account.', 'Dieses Modul erm&#246;glicht es Ihnen, innerhalb Ihres Kontos, Domains hinzuzuf&#252;gen oder zu konfigurieren.', 'Ce module vous permet d''ajouter ou configurer un domaine &#224; h&#233;berger sur votre compte.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 'Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!', 'Der Domain Parkplatz bezieht sich auf die Registrierung eines Internet-Domain-Namen ohne die Domain f&#252;r Dienste wie E-Mail oder einer Webseite zu betreiben. Wenn Sie Domains besitzten, die Sie nicht verwenden, dann k&#246;nnen Sie diese einfach parken!', 'La fonctionnalit&#233; des domaines gar&#233;s vous permet d''ajouter un domaine sans que celui-ci soit visible sur le web. Pratique pour r&#233;server un domaine qui ne sera pas utilis&#233; imm&#233;diatement.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 'This module enables you to add or configure domain web hosting on your account.', 'Dieses Modul erm&#246;glicht es Ihnen, innerhalb Ihres Kontos, Domains hinzuzuf&#252;gen oder zu konfigurieren.', 'Ce module vous permet d''ajouter ou configurer un domaine &#224; h&#233;berger sur votre compte.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 'Administer or configure modules registered with module admin', 'Mit dem Modul-Admin registrierte Module verwalten oderkonfigurieren ', 'Administrer ou configurer des modules install&#233;s avec le gestionnaire de modules.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 'The account manager enables you to view, update and create client accounts.', 'Der Account-Manager erm&#246;glicht es Ihnen, Ihre Kundenkonten zu verwalten.', 'Le gestionnaire de compte vous permet de visualiser, mettre &#224; jour et cr&#233;er des comptes clients.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 'Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.', 'Willkommen zum Paket-Manager. Dieses Modul erm&#246;glicht Ihnen das Erstellen und Verwalten von bestehenden Reseller-Paketen auf Ihrem ZPanel-Hosting-Account.', 'Utilisez cet outil pour g&#233;rer les packs clients revendeur.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 'Gives you access to your files with drag-and-drop, multiple file uploading, text editing, zip support.', 'Erm&#246;glicht den Zugriff auf Ihre Dateien mit Drag-and-drop, multiple Datei-Upload, Bearbeitungund Zip-Unterst&#252;tzung.', 'Cette fonction vous donne acc&#232;s &#224; vos fichiers par simple glisser-d&#233;poser, le t&#233;l&#233;chargement de fichiers multiples, l''&#233;dition de texte, le support zip.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 'Secure FTP Applet is a JAVA based FTP client component that runs within your web browser. It is designed to let non-technical users exchange data securely with an FTP server.', 'Secure FTP Applet ist eine Java-basierte FTP-Client-Komponente, die in Ihrem Web-Browser l&#228;uft. Sie wurde entwickelt, um nicht-technische Anwender den sicheren Datenaustausch mit einem einem FTP-Server zu erm&#246;glichen.', 'Transf&#233;rez et partagez des donn&#233;es en toute s&#233;curit&#233; avec l''applet FTP Secure de JAVA.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 'Full name', 'Vollst&#228;ndiger Name', 'Nom et pr&#233;nom', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 'Email Address', 'E-Mail Adresse', 'Adresse e-mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 'Phone Number', 'Telefonnummer', 'Num&#233;ro de t&#233;l&#233;phone', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 'Choose Language', 'Sprache w&#228;hlen', 'S&#233;lectionnez la langue', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 'Postal Address', 'Postanschrift', 'Adresse postale', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 'Postal Code', 'Postleitzahl', 'Code postal', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.', 'Nachfolgend Ihre pers&#246;nlichen Daten. Wir bitten Sie, diese stets aktuell zu halten, damit eine problemlose Kontaktaufnahem unsererseits m&#246;glich ist.', 'Vos informations personnelles. Veuillez les tenir &#224; jour afin de pouvoir vous contacter &#224; propos de votre h&#233;bergement.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 'Changes to your account settings have been saved successfully!', '&#196;nderungen an Ihren Konto-Einstellungen wurden erfolgreich gespeichert!', 'Modifications prises en compte avec succ&#232;s !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 'Update Account', 'Konto aktualisieren', 'Mettre &#224; jour mon compte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 'Enter your account details', 'Geben Sie Ihre Kontodaten ein:', 'Entrez vos informations de compte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 'Usage Viewer', 'Statistiken', 'Statistiques d''utilisation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 'Admin', 'Admin', 'Administrateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 'phpSysInfo', 'php SysINfo', 'phpSysInfo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 'Cron Manager', 'Cron Manager', 'Gestionnaire de t&#226;ches Cron', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 'FAQ''s', 'FAQ''s', 'FAQ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 'Protect Directories', 'Gesch&#252;tzte Verzeichnisse', 'Prot&#233;ger un r&#233;pertoire', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 'Webalizer Stats', 'Webalizer Statistiken', 'Statistiques Webalizer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 'MySQL Database', 'MySQL-Datenbanken', 'Base de donn&#233;es MySQL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 'MySQL Users', 'MySQL-Benutzer', 'Utilisateurs MySQL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(133, 'Domain', 'Domain', 'Domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 'DNS Manager', 'DNS-Manager', 'Gestionnaire DNS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 'Aliases', 'Aliase', 'Alias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 'Distribution Lists', 'Verteiler-Listen', 'Listes de distribution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 'Forwards', 'Weiterleitungen', 'Redirections', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(138, 'Mailboxes', 'Mailboxen', 'Bo&#238;tes mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(139, 'WebMail', 'WebMail', 'WebMail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 'Client Notice Manager', 'Kunden-Nachrichten-rnManager', 'Notification clients', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(141, 'Manage Groups', 'Gruppen verwalten', 'G&#233;rer les groupes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(142, 'Theme Manager', 'Themen verwalten', 'G&#233;rer les th&#232;mes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 'File', 'Datei', 'Fichier', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 'FTP Accounts', 'FTP-Konto', 'Comptes FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\r\n', 'Nachfolgend Ihre pers&#246;nlichen Daten. Wir bitten Sie, diese stets aktuell zu halten, damit eine problemlose Kontaktaufnahem unsererseits m&#246;glich ist.', 'Vos informations personnelles. Veuillez les tenir &#224; jour afin de pouvoir vous contacter &#224; propos de votre h&#233;bergement.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 'The account usage screen enables you to see exactly what you are currently using on your hosting package.', 'Ihr Konto&#252;bersicht zeigt Ihnen die Nutzungsstatistik Ihres Hostingpakets', 'Cette interface vous permet de visualiser les ressources utilis&#233;es et celles restantes sur votre h&#233;bergement.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 'phpSysInfo is a web-based server hardware monitoring tool which enables you to see detailed hardware statistics of your server.', 'phpSysInfo ist ein webbasiertes Hardware-Monitoring-Tool, welches detaillierte Hardware-Informationen zu Ihrem Server anzeigt.', 'phpSysInfo est un outil de surveillance hardware qui vous permet de voir les statistiques d&#233;taill&#233;es sur le mat&#233;riel de votre serveur via une page web.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 'Changes made here affect the entire ZPanel configuration, please double check everything before saving changes.', 'Hier vorgenommene &#196;nderungen beeinflussen die gesamte ZPanel-Konfiguration. Bitte f&#252;hren Sie &#196;nderungen sehr sorgf&#228;ltig durch ! ', 'Ces changements peuvent avoir un impact sur la configuration de ZPanel, v&#233;rifiez tout avant de sauvegarder.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(149, 'Here you can configure PHP scripts to run automatically at different time intervals.', 'Konfigurieren Sie hier PHP-Skripte, welche in bestimmten Zeit-Intervallen ablaufen', 'Ici vous pouvez configurer des scripts PHP pour qu''ils s&#8217;ex&#233;cutent automatiquement &#224; des intervalles de temps diff&#233;rents.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 'Please find a list of the most common questons from users, if you are unable to find a solution to your problem below please then contact your hosting provider. Simply click on the FAQ below to view the solution.', 'Hier finden Sie eine Liste der h&#228;ufigsten Fragen von Benutzern.rnFalls Sie keine L&#246;sung f&#252;r Ihr Problem finden, dann kontaktieren Sie Ihren Hosting-Anbieter. Klicken Sie einfach unten auf FAQ um m&#246;gliche Antworten anzuzeigen.', 'Voici les questions fr&#233;quemment pos&#233;es par les utilisateurs. Cliquez sur une question pour voir la r&#233;ponse. Si vous ne trouvez pas la r&#233;ponse &#224; votre probl&#232;me, contactez le support technique.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(151, 'This module enables you to configure .htaccess files and users to protect your web directories.', 'Mit diesem Modul erstellen Sie .htaccess-Dateien und Benutzer, um Verzeichnisse zu sch&#252;tzen.', 'Ce module vous permet de prot&#233;ger un r&#233;pertoire, donnant ainsi l''acc&#232;s &#224; des utilisateurs cibl&#233;s (pr&#233;c&#233;demment cr&#233;&#233;s)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(152, 'You can view many statistics such as visitor infomation, bandwidth used, referal infomation and most viewed pages etc. Web stats are based on Domains and sub-domains so to view web stats for a particular domain or subdomain use the drop-down menu to select the domain or sub-domain you want to view web stats for.', 'Hier werden viele Statistiken wie Besucherinformationen, genutzte Bandbreite, Referal Infomationen und die am meisten aufgerufenen Seiten usw. angezeigt. Webstatistiken basieren  auf Domains und Sub-Domains. Verwenden Sie also das Dropdown-Men&#252; zur Auswahl der gew&#252;nschtenb Domain oder Sub-Domain.', 'S&#233;lectionnez le domaine / sous-domaine pour consulter les statistiques compl&#232;tes.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(153, 'MySQL&reg databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg databases.', 'MySQL&#174;-Datenbanken werden von vuielen PHP-Anwendungen wie z.B. Foren oder Online-Shops genutzt.rnHier k&#246;nnen Sie MySQL&#174;-Datenbanken erstellen und verwalten.', 'G&#233;rez vos bases de donn&#233;es MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(154, 'MySQL&reg Users allows you to add users and permissions to your MySQL&reg databases.', 'Hier k&#246;nnem Sie MySQL&#174;-Benutzer anlegen und die Zugriffsrechte der jeweiligen MySQL&#174;-Datenbank anpassen.', 'G&#233;rez vos utilisateurs MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 'Using this module you have the ability to create alias mailboxes to existing accounts.', 'Mit diesem Modul k&#246;nnen Sie Mailboxen erstellen und verwalten.', 'G&#233;rez vos alias d''adresses mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(156, 'This module enables you to create and manage email distribution groups.', 'Mit diesem Modul k&#246;nnen Sie E-Mail-Verteiler erstellen und verwalten.', 'G&#233;rez vos listes de distribution.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(157, 'Using this module you have the ability to create mail forwarders.', 'Mit diesem Modul k&#246;nnen Sie E-Mail-Weiterleitungen erstellen und verwalten.', 'G&#233;rez vos redirections mail.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(158, 'Using this module you have the ability to create IMAP and POP3 Mailboxes.', 'Mit diesem Modul k&#246;nnen Sie MIMAP und POP3-Mailboxen erstellen und verwalten.', 'G&#233;rez vos adresses mail.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(159, 'Enables resellers to set global notices for their clients.', 'Erm&#246;glicht es Wiederverk&#228;ufern alle Kunden auf einmal zu benachrichtigen.', 'G&#233;rez le message affich&#233; aux clients.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(160, 'Manage user groups to enable greater control over module permission.', 'Benutzer-Gruppen erm&#246;glichen eine gr&#246;&#223;ere Kontrolle &#252;ber die Modul-Berechtigungen', 'G&#233;rez les groupes d''utilisateurs (pratique pour les modules).', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(161, 'Enables the reseller to set themes configurations for their clients.', 'Erm&#246;glicht es dem Wiederverk&#228;ufer seinen Kunden Themes zuzuteilen.', 'G&#233;rez vos th&#232;mes clients (ZPanel)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(162, 'File Management', 'Datei-Manager', 'Gestion des fichiers', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(163, 'Using this module you can create FTP accounts which will enable you and any other accounts you create to have the ability to upload and manage files on your hosting space.', 'Mit diesm Modul k&#246;nne Sie FTP-Konten erstellen, die dann, je nach Berechtigung, Daten auf Ihrem Webspace laden und verwalten k&#246;nnen. ', 'G&#233;rez vos comptes FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(164, 'Main Page', 'Hauptseite', 'Page d''ccueil', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(165, 'Log off ZPanel', 'von ZPanel abmelden', 'D&#233;connexion du panel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(166, 'Server time is', 'Server-Zeit ist', 'Il est actuellement ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(167, 'Home', 'Home', 'Accueil', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(168, 'Log Out', 'Abmelden', 'D&#233;connexion', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(169, 'Username', 'Benutzername', 'Nom d''utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 'Update your contact email address', 'Mail-Kontaktadresse &#228;ndern', 'Veuillez mettre &#224; jour votre adresse mail de contact.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(171, 'Package name', 'Paketnamr', 'Nom du pack', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(172, 'Account type', 'Kontotyp', 'Type de compte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 'Last Logon', 'Letzte Anmeldung', 'Derni&#232;re connexion', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(174, 'Disk Quota', 'Speicherbegrenzung', 'Quota du disque', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(175, 'Bandwidth Quota', 'Bandbreitenbegrenzung', 'Quota de bande passante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(176, 'Used', 'benutzt', 'Utilis&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(177, 'Max', 'max', 'Maximum', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(178, 'Sub-domains', 'Sub-Domains', 'Sous-domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(179, 'MySQL&reg; databases', 'MySQL&#174;-Datenbanken', 'Base de donn&#233;es MySQL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(180, 'Email Accounts', 'E-Mail-Konten', 'Comptes mails', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(181, 'Email Forwarders', 'E-Mail-Weiterleitungen', 'Redirections de mails', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(182, 'Server Information', 'Server-Informationen', 'Informations sur le serveur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(183, 'Your IP', 'Ihre IP', 'Votre IP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(184, 'Server IP', 'Server-IP', 'IP du serveur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(185, 'Server OS', 'SERVER-OS', 'OS du serveur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(186, 'Apache Version', 'Apache-Version', 'Version d''Apache', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(187, 'PHP Version', 'PHP-Version', 'Version de PHP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(188, 'MySQL Version', 'MySQL&#174;-Version', 'Version de MySQL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(189, 'ZPanel Version', 'ZPanel-Version', 'Version du Panel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(190, 'Server uptime', 'Server-Laufzeit', 'Serveur ON depuis ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(191, 'Domain Information', 'Domain-Information', 'Informations du domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(192, 'Administration Modules', 'Administrations-Module', 'Administration des modules', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(193, 'Apache Config', 'Apache-Konfigurationm', 'Configuration d''Apache', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `x_translations` (`tr_id_pk`, `tr_en_tx`, `tr_German_tx`, `tr_French_tx`, `tr_Afrikaans_tx`, `tr_Acholi_tx`, `tr_Albanian_tx`, `tr_Arabic_tx`, `tr_Bulgarian_tx`, `tr_Cantonese_tx`, `tr_Croatian_tx`, `tr_Czech_tx`, `tr_Danish_tx`, `tr_Dutch_tx`, `tr_Farsi_tx`, `tr_FrenchCanadian_tx`, `tr_Greek_tx`, `tr_Hungarian_tx`, `tr_Indonesian_tx`, `tr_Italian_tx`, `tr_Jakartanese_tx`, `tr_Latvian_tx`, `tr_Lithuanian_tx`, `tr_Macedonian_tx`, `tr_Malay_tx`, `tr_Mandarin_tx`, `tr_Norwegian_tx`, `tr_Polish_tx`, `tr_Portuguese_tx`, `tr_Romanian_tx`, `tr_Russian_tx`, `tr_Serbian_tx`, `tr_Shanghainese_tx`, `tr_Slovak_tx`, `tr_Slovenian_tx`, `tr_Spanish_tx`, `tr_Swedish_tx`, `tr_Thai_tx`, `tr_Turkish_tx`, `tr_Ukrainian_tx`, `tr_Vietnamese_tx`) VALUES
(194, 'Backup Config', 'Backup-Konfiguration', 'Configuration des sauvegardes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(195, 'DNS Config', 'DNS-Konfiguration', 'Configuration DNS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(196, 'FTP Config', 'FTP-Konfiguration', 'Configuration FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(197, 'Mail Config', 'Mail-Konfiguration', 'Configuration des mails', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(198, 'Configure Modules', 'Module konfigurieren', 'Configurer les modules', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(199, 'Module', 'Modul', 'Module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 'On', 'an', 'Activ&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 'Off', 'aus', 'D&#233;sactiv&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 'Category', 'Kategorie', 'Cat&#233;gorie', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 'Up-to-date?', 'Aktuell?', 'A jour ?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(204, 'Enabled', 'aktiviert', 'Activ&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(205, 'Disabled', 'deaktiviert', 'D&#233;sactiv&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(206, 'N/A (Module Admin)', 'N/A(Module Admin)', 'N/A (Module administrateur)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(207, 'Save changes', '&#196;nderungen speichern', 'Sauvegarder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(208, 'Module Information', 'Modul-Information', 'Informations du module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(209, 'Module name', 'Modulname', 'Nom du module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(210, 'Module description', 'Modulbeschreibung', 'Description du module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(211, 'Module developer', 'Modulentwickler', 'D&#233;veloppeur du module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(212, 'Module version', 'Modulversion', 'Version du module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(213, 'Latest Version', 'Letzte Version', 'Derni&#232;re version', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(214, 'Module Type', 'Modultyp', 'Type du module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(215, 'Install a new module', 'Neues Modul installieren', 'Installer un nouveau module', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(216, 'You can automatically install a new module by uploading your zpanel package archive (.zpp file) and then click ''Install'' to begin the process.', 'Sie k&#246;nnen ein Modul automatisch installiern, indem Sie Ihre ZPanel-Paketdatei (.zpp) hiochladen und danach auf INSTALL klicken.', 'Vous pouvez installer automatiquement un nouveau module en envoyant l''archive de votre paquet zpanel (fichier .zpp) puis en cliquant sur "Installer".rnrnL''installation d&#233;butera automatiquement.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(217, 'Install!', 'Install', 'Installer !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(218, 'Manage Domains', 'Domains verwaltern', 'G&#233;rez les domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(219, 'Choose fom the list of domains below', 'W&#228;hlen Sie aus der Liste der Domains aus', 'Choisissez parmi cette liste', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(220, 'Select a domain', 'Domain ausw&#228;hlen', 'S&#233;lectionnez un domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(221, 'Select', 'Ausw&#228;hlen', 'S&#233;lectionner', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(222, 'Power Plug', 'Powerplug', 'Plugin On/Off', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(223, 'Changes to your module options have been saved successfully!', '&#196;nderungen an Ihrem Modul wurden erfolgreich gespeichert.', 'Modifications du module prises en compte !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(224, 'Power Plug is a simple module that enables you to shutdown or restart your server from the ZPanel web interface.', 'Powerplu ist ein einfaches Modul, welches Ihnen erlaubt Ihren Server &#252;ber das Zpanel Web-Interface herunterzufahren oder neu zu starten', 'Le module Plugin On/Off permet d''arr&#234;ter - red&#233;marrer le serveur via le panel.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(225, 'FTP Browser', 'FTP-Browser', 'Navigateur FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(226, 'Secure FTP Applet is a JAVA based FTP client component that runs within your web browser. It is designed to let non-technical users exchange data security with an FTP server.', 'Secure FTP Applet ist eine Java-basierte FTP-Client-Komponente, die in Ihrem Web-Browser l&#228;uft. Sie wurde entwickelt, um nicht-technische Anwender den sicheren Datenaustausch mit einem einem FTP-Server zu erm&#246;glichen.', 'L''applet FTP Secure de JAVA permet un &#233;change de donn&#233;es s&#233;curis&#233;.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(227, 'Launch phpSysInfo', 'php Sysinfo starten', 'Lancer phpSysInfo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(228, 'Select the client you wish to shadow', 'W&#228;hlen sie den Kunden, den Sie Remote admiknistrieren m&#246;chten', 'S&#233;lectionnez le client que vous souhaitez visualiser', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(229, 'Package', 'Paket', 'Pack d''h&#233;bergement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(230, 'Group', 'Gruppe', 'Groupe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(231, 'Current Disk', 'Aktueller Speicherplatz', 'Espace disque actuel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(232, 'Current Bandwidth', 'Aktelle Bandbreite', 'Bande passante actuelle', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(233, 'Shadow', 'Remoteadmin', 'Suivre', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(234, 'You have no Clients at this time.', 'Sie haben zur Zeit keine Kunden', 'Aucun client enregistr&#233;.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(235, 'As a reseller you can configure theme settings for your clients. If the theme you select has multiple CSS versions you will be prompted for which theme ''version'' you would like to use after you ''save'' the changes.', 'Als Wiederverk&#228;ufer k&#246;nnen Sie Design-Einstellungen f&#252;r Ihre Kunden zu konfigurieren. Wenn das Design, das Sie w&#228;hlen mehrere CSS-Versionen werden Sie dazu aufgefordert werden, die ''Version'' die Sie verwenden m&#246;chten, auszuw&#228;hlen.', 'En tant que revendeur, vous pouvez configurer les param&#232;tres de th&#232;me pour vos clients. Si le th&#232;me que vous s&#233;lectionnez poss&#232;de plusieurs versions CSS, vous devrez selectionner quelle "version" de th&#232;me vous souhaitez utiliser apr&#232;s avoir sauvegard&#233; les changements.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(236, 'Select a theme', 'Design ausw&#228;hlen', 'S&#233;lectionnez un th&#232;me', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 'Theme name', 'Design-Name', 'Nom du th&#232;me', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(238, 'Save', 'Speichern', 'Sauvegarder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(239, 'Theme variation', 'Design-Version', 'Variante du th&#232;me', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(240, 'Update', 'Update', 'Mise &#224; jour', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(241, 'Delete', 'L&#246;schen', 'Supprimer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(242, 'Please confirm that you want to delete this domain.', 'Bitte best&#228;tigen Sie die L&#246;schung dieser Domain !', 'Confirmez la suppression du domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(243, 'Cancel', 'Abbrechen', 'Annuler', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(244, 'Current domains', 'Aktuelle Domains', 'Domaines actuels', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(245, 'Domain name', 'Domain-Name', 'Nom de domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(246, 'Home directory', 'Home-Verzeichnis', 'R&#233;pertoire racine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(247, 'Status', 'Status', 'Statut', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(248, 'You currently do not have any domains configured. Create a domain using the form below.', 'Sie haben nich keine Domain konfiguriert. Bitte benutzen Sie dazu das unten stehende Formular.', 'Vous n''avez enregistr&#233; aucun nom de domaine. Enregistrez-en un ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(249, 'Create a new home directory', 'Neues Home-Verzeichnis ertsellen', 'Cr&#233;ez un nouveau r&#233;pertoire racine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(250, 'Use existing home directory', 'Bestehendes Home-Verzeichnis', 'Utiliser un r&#233;pertoire racine existant', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(251, 'You have reached your domain limit!', 'maximale Anzahl Domains erreicht !', 'Vous avez atteint votre limite de domaines !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(252, 'Changes to your domain web hosting has been saved successfully.', '&#196;nderungen an Ihrem Domainhosting wurden erfolgreich gespeichert!', 'Modifications prises en compte avec succ&#232;s.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(253, 'Pending', 'in Bearbeitung', 'En attente', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(254, 'Your domain will become active at the next scheduled update.  This can take up to one hour.', 'Ihre Domain wird mit dem n&#228;chsten geplanten Update aktiviert. Dies kann bis zu einer Stunde dauern.', 'Votre domaine sera actif sous peu.rnAu maximum, cela peut prendre une heure.rnSi au bout de 24 heures, votre domaine est toujours en attente, contactez le support technique.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(255, 'Create Deafult DNS Records', 'Standard-DNS-Records erstellen', 'Cr&#233;er les enregistrements DNS par d&#233;faut', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(256, 'No records were found for this domain.  Click the button below to set up your domain records for the first time', 'Keine Records f&#252;r diese Domain gefunden. Klicken Sie auf den unten stehenden Button, um Ihre Domain-Records einzustellen.', 'Aucun enregistrement trouv&#233; pour ce domaine. Cliquez sur le bouton ci-dessous pour configurer les premiers enregistrements de votre domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(257, 'Create Records', 'Records erstellen', 'Cr&#233;er un enregistrement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(258, 'Your DNS zone has been loaded without errors.', 'Ihre DNS-Zone wurde fehlerfrei geladen.', 'Votre zone DNS a &#233;t&#233; charg&#233;e avec succ&#232;s !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(259, 'DNS records for', 'DNS-Records f&#252;r', 'Enregistrements DNS pour', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(260, 'Undo Changes', '&#196;nderunegn zur&#252;cknehmen', 'Annuler les changements', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(261, 'Domain List', 'Domain-Liste', 'Liste des domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(262, 'The A record contains an IPv4 address. It''s target is an IPv4 address, e.g. ''192.168.1.1''.', 'Der A-Record beinhaltet eine IPv4-Adresse (z.Bsp. ''192.168.1.1'')', 'L''enregistrement A contient une adresse IP interne. Il doit contenir une adresse IP externe. rnExemple : "92.247.51.284"', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(263, 'Host Name', 'Hostname', 'Nom d''h&#244;te', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(264, 'Target', 'Ziel', 'Cible', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(265, 'Actions', 'Aktionen', 'Actions', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(266, 'Add New Record', 'Neuen Record hinzuf&#252;gen', 'Ajouter un nouvel enregistrement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(267, 'The AAAA record contains an IPv6 address. It''s target is an IPv6 address, e.g. ''2607:fe90:2::1''.', 'Der AAAA-Record beinhaltet eine IPv6-Adresse (z.Bsp. ''2607:fe90:2::1'')', 'L''enregistrement AAAA doit contenir une adresse IPv6. rnPar exemple : "2607:fe90:2::1"', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(268, 'The CNAME record specifies the canonical name of a record. It''s target is a fully qualified domain name, e.g. \r\n''webserver-01.example.com''.', 'Der CNAME-Record bezeichnet den Kanonischen Namen, d.h. einen Vollqualifizierten Domain-Namen (FQDN), z.Bsp. ''webserver-01.example.com''', 'L''enregistrement CNAME est en fait une copie d''un autre nom de domaine.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(269, 'The MX record specifies a mail exchanger host for a domain. Each mail exchanger has a priority or preference that is a numeric value between 0 and 65535.  It''s target is a fully qualified domain name, e.g. ''mail.example.com''.', 'Der MX-Record zeigt auf die Mailserver der Domain, jedem Mailserver kann eine Priorit&#228;t zuwischen 0 und 65535 zugeordnet werden. Ziel ist ein FQDN, z. Bsp. ''mail.example.com'' ', 'L''enregistrement MX sp&#233;cifie un serveur de messagerie pour un domaine. Chaque serveur de messagerie a une priorit&#233; ou pr&#233;f&#233;rence qui est une valeur num&#233;rique entre 0 et 65535. Il doit contenir un nom de domaine pleinement qualifi&#233;. Par exemple : "mail.example.com".', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(270, 'Priority', 'Prorit&#228;t', 'Priorit&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(271, 'The TXT field can be used to attach textual data to a domain.', 'Mit dem TXT-Feld kann man einer Domain textdaten zuordnen', 'Le champ TXT peut &#234;tre utilis&#233; pour attacher des donn&#233;es textuelles &#224; un domaine.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(272, 'SRV records can be used to encode the location and port of services on a domain name.  It''s target is a fully qualified domain name, e.g. ''host.example.com''.', 'SRV-Records k&#246;nne verwendet werden, um die Position und den Port der Dienste auf einem Domain-Namen zu codieren. Ziel ist ein FQDN (z.Bsp.''host.example.com'')', 'L''enregistrements SRV peut &#234;tre utilis&#233; pour coder l''emplacement et le port de services sur un nom de domaine. Il doit contenir un nom de domaine pleinement qualifi&#233;. Par exemple : "host.example.com".', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(273, 'Weight', 'Gewichtung', 'Poids', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(274, 'Port', 'Port', 'Port', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(275, 'SPF records is used to store Sender Policy Framework details.  It''s target is a text string, e.g.<br>''v=spf1 a:192.168.1.1 include:example.com mx ptr -all'' (Click <a href="http://www.microsoft.com/mscorp/safety/content/technologies/senderid/wizard/" target="_blank">HERE</a> for the Microsoft SPF Wizard.)', 'SPF-Eintr&#228;ge beinhalten Sender Policy Framework InformationenrnEs entspricht einer Zeichenkette, z. B.rn''v = spf1 a: 192.168.1.1 include: example.com mx ptr-all'' (. Klicken Sie HIER f&#252;r den Microsoft SPF-Assistenten)', 'L''enregistrements SPF est utilis&#233; pour stocker les d&#233;tails du "Sender Policy Framework". Il doit contenir une cha&#238;ne de texte. Exemple :rn''v=spf1 a:127.0.0.1 include:example.com mx ptr -all'' (Clicquer <a href="http://www.microsoft.com/mscorp/safety/content/technologies/senderid/wizard/">ici</a> pour l''assistant Microsoft SPF.)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(276, 'Nameserver record. Specifies nameservers for a domain. It''s target is a fully qualified domain name, e.g.  ''ns1.example.com''.  The records should match what the domain name has registered with the internet root servers.', 'Nameserver Rekord. Bezeichnet den Nameserver f&#252;r eine Domain. Ziel ist der Fully Qualified Domain Name, z. B. ''ns1.example.com''. Dise Records sollten denrninternet Root-Servern enstprechen, bei denen die Domain registriert ist. ', 'L''enregistrement NS vous est communiqu&#233; par votre h&#233;bergeur (si autre que HabbHeberg).', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(277, 'Please note that changes to your zone records can take up to 24 hours before they become ''live''.', 'Bitte beachten Sie, das &#196;nderunegn an Ihrer Zone bis zu 24 h dauern, bevor Sie &#246;ffentlich sind.', 'Les modifications DNS peuvent mettre 24 heures avant d''&#234;tre effectives (temps de propagation DNS)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(278, 'Output of DNS zone checker:', 'Ausgabe des DNS-Zone-Checker:', 'Rapport de v&#233;rification de la zone DNS :', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(279, 'Live', 'Live:', 'Actif', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(280, 'Changes to your DNS have been saved successfully!', '&#196;nderungen an Ihrem DNS wurden erfolgreich gespeichert.', 'Les modifications apport&#233;es &#224; vos DNS ont &#233;t&#233; sauvegard&#233;es avec succ&#232;s !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(281, 'Delete package', 'Paket l&#246;schen', 'Supprimer le pack', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(282, 'Please confirm and choose a package to move any existing clients to before the selected package is deleted.', 'Bitte best&#228;tigen Sie und w&#228;hlen Sie ob das Paket zu einem vorhandenen Kunden verschoben wird, bevor das Paket ausgew&#228;hlt und gel&#246;scht wird.', 'Choisissez un paquet dans lequel tous les clients du paquet &#224; supprimer seront transf&#233;r&#233;s.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(283, 'Package to delete', 'Paket l&#246;schen', 'paquet &#224; supprimer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(284, 'Move current package members to', 'Paket verschieben zu', 'Transf&#233;rer les clients du paquet vers', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(285, 'Edit package', 'Paket bearbeiten', 'Modifier le paquet ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286, 'Enable PHP', 'PHP aktivieren', 'Activer PHP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(287, 'Enable CGI', 'CGI aktivieren', 'Activer CGI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(288, 'No. Domains', 'Anzahl Domains', 'Nombre de domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(289, 'No. Sub-domains', 'Anzahl Sub-Domains', 'Nombre de sous domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(290, 'No. Parked domains', 'Anzahl geparke Domains', 'Nombre de domaine r&#233;serv&#233;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(291, 'No. Mailboxes', 'Anzahl Mailboxen', 'Nombre d''adresses mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(292, 'No. Forwarders', 'Anzahl Weiterleitungen', 'Nombre de redirections d''emails', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(293, 'No. Dist Lists', 'Anzahl Verteilerlisten', 'Nombre de liste de distribution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(294, 'No. FTP accounts', 'Anzahl FTP-Konten', 'Nombre de comptes FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(295, 'No. MySQL databases', 'Anzahl MySQl-Datenbanken', 'Nombre de base de donn&#233;es MySQL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(296, 'Disk space quota', 'Speicherplatz', 'Quota d''espace disque', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(297, 'Monthly bandwidth quota', 'Monatliche Bandbreite', 'Quota de bande passante mensuelle', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(298, 'Created', 'Erstellt', 'Cr&#233;er', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(299, 'No. of clients', 'Anzahl Kunden', 'Nombre de clients', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(300, 'Edit', 'Bearbeiten', 'Modifier', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(301, 'You have no packages at this time. Create a package using the form below.', 'Sie haben keine Pakete definiert.rnErstellen Sie ein Paket mit unten stehendem Formular', 'Aucun pack. Cr&#233;ez-en un ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(302, 'Create a new package', 'Neues Paket erstellen', 'Cr&#233;er un nouveau pack.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(303, 'Administration', 'Administration', 'Administration', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(304, 'Demo', 'Demo', 'D&#233;mo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(305, 'Changes to your packages have been saved successfully!', '&#196;nderungen an Ihren Paketen wurden erfolgreich gespeichert.', 'Les modifications apport&#233;es &#224; vos packs ont &#233;t&#233; sauvegard&#233;es avec succ&#232;s!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(306, 'This module enables you to manage user groups for your client, User groups enable you to control what modules your users can see and access. ', 'Dieses Modul erm&#246;glicht es Ihnen, Ihre Kunden in Benutzergruppen zu verwalten und zu steuern, auf welche Module Ihre Benutzer zugreifen k&#246;nnen.', 'G&#233;rez les groupes d''utilisateurs (pratique pour les modules)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(307, 'Delete user group', 'Benutzergruppe l&#246;schen', 'Supprimer groupe d''utilisateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(308, 'Please confirm and choose a group to move any existing clients to before the selected group is deleted.', 'Bitte best&#228;tigen Sie und w&#228;hlen Sie ob der Kunde zu einer vorhandenen Gruppe verschoben wird, bevor er ausgew&#228;hlt und gel&#246;scht wird.', 'D&#233;placer les clients du groupe en suppression vers le groupe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(309, 'Please confirm that you want to delete this group.', 'L&#246;schen der Benutzergruppe best&#228;tigen', 'Confirmer la suppression de ce groupe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(310, 'Move current group members to', 'aktuelle Gruppenmitglieder verschieben ui', 'D&#233;placement des membres du groupe en cours', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(311, 'Default user groups', 'Standard Benutzergruppe', 'Groupe d''utilisateurs par d&#233;faut', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(312, 'Users', 'Benutzer', 'Utilisateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(313, 'Description ', 'Beschreibung', 'Description', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(314, 'Current user groups', 'Aktuelle Benutzergruppen', 'Groupes d''utilisateurs actuels', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(315, 'There are currently no custom user groups configured!', 'Zur Zeit sind keine eigenen Benutzergruppen definiert.', 'Aucun groupe d''utilisateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(316, 'Create new user group', 'Neue Gruppe erstellen', 'Cr&#233;er un groupe d''utilisateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(317, 'Group name', 'Gruppen-Name', 'Nom du groupe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(318, 'Description', 'Beschreibung', 'Description', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(319, 'Create', 'Erstellen', 'Cr&#233;er', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(320, 'Edit user group', 'Benutzergruppe bearbeiten', 'Editer le groupe d''utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(321, 'Administrators', 'Adminitratoren', 'Administrateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(322, 'The main administration group, this group allows access to all areas of ZPanel.', 'Die Administrations-Gruppe hat Uigriff auf alle ZPanel-Einstellungen', 'Groupe de SU (acc&#232;s &#224; toutes les fonctions du Panel).', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(323, 'Resellers', 'Wiederverk&#228;ufer', 'Revendeurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(324, 'Resellers have the ability to manage, create and maintain user accounts within ZPanel.', 'Wiederverk&#228;ufer k&#246;nnen mittels ZPanel Benutzerkonten erstellen und verwalten.', 'Les revendeurs peuvent g&#233;rer et cr&#233;er des packs et des utilisateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(325, 'Users have basic access to ZPanel.', 'Benutzer haben eingeschr&#228;nkte Rechternin ZPanel.', 'Les utilisateurs ont un acc&#232;s basique au Panel.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(326, 'Checking if your version of ZPanel is up to date...', 'Pr&#252;fen Sie, ob Ihre Version von Zpanel aktuell ist...', 'V&#233;rifier une mise &#224; jour', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(327, 'Delete database', 'Datenbank l&#246;schen', 'Supprimer la base de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(328, 'Please confirm that you want to delete this database.', 'L&#246;schung der Datenbank best&#228;tigen.', 'Confirmer la suppression de cette base de donn&#233;es.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(329, 'Current MySQL&reg Databases', 'Aktuelle MySQL&#174;-Datenbank', 'Bases de donn&#233;es MySQL&#174; Actuelles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(330, 'Database name', 'Datenbankname', 'Nom de la base', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(331, 'Size', 'Gr&#246;sse', 'Taille', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `x_translations` (`tr_id_pk`, `tr_en_tx`, `tr_German_tx`, `tr_French_tx`, `tr_Afrikaans_tx`, `tr_Acholi_tx`, `tr_Albanian_tx`, `tr_Arabic_tx`, `tr_Bulgarian_tx`, `tr_Cantonese_tx`, `tr_Croatian_tx`, `tr_Czech_tx`, `tr_Danish_tx`, `tr_Dutch_tx`, `tr_Farsi_tx`, `tr_FrenchCanadian_tx`, `tr_Greek_tx`, `tr_Hungarian_tx`, `tr_Indonesian_tx`, `tr_Italian_tx`, `tr_Jakartanese_tx`, `tr_Latvian_tx`, `tr_Lithuanian_tx`, `tr_Macedonian_tx`, `tr_Malay_tx`, `tr_Mandarin_tx`, `tr_Norwegian_tx`, `tr_Polish_tx`, `tr_Portuguese_tx`, `tr_Romanian_tx`, `tr_Russian_tx`, `tr_Serbian_tx`, `tr_Shanghainese_tx`, `tr_Slovak_tx`, `tr_Slovenian_tx`, `tr_Spanish_tx`, `tr_Swedish_tx`, `tr_Thai_tx`, `tr_Turkish_tx`, `tr_Ukrainian_tx`, `tr_Vietnamese_tx`) VALUES
(332, 'You have no databases at this time. Create a database using the form below.', 'Sie haben noch keine Datenbank erstellt. Bitte erstellen Sie eine Datenbank.', 'Aucune base de donn&#233;es, cr&#233;ez-en une ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(333, 'Create a new MySQL&reg database', 'Neue MySQL&#174;-Datenbank erstellen.', 'Cr&#233;er une nouvelle base de donn&#233;es MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(334, 'You have reached your MySQL database limit!', 'Maximale Anzahl von Datenbanken erreicht!', 'Vous avez atteint votre limite de base de donn&#233;es MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(335, 'Delete User', 'Benutzer l&#246;schen', 'Supprimer un utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(336, 'Please confirm that you want to delete this MySQL user.', 'Bitte best&#228;tigen Sie, dass Sie den MySQL-Benutzer l&#246;schen wollen.', 'Confirmer la suppression de l''utilisateur MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(337, 'Databases for user', 'Datenbank f&#252;r Benutzer', 'Bases de donn&#233;es pour l''utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(338, 'Remove from user', 'Vom Benutzer entfernen', 'Supprimer l''utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(339, 'Remove database', 'Datenbank entfernen', 'Supprimer la base de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(340, 'This user currently has no databases. Assign a database using the form below.', 'Derm Benutzer ist noch keine Datenbank zugeteilt, bitte mittels unten stehndem Formular zuweisen.', 'Aucune base de donn&#233;es pour cet utilisateur, attribuez-lui en une.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(341, 'Add Database', 'Datenbank hinzuf&#252;gen', 'Ajouter la base de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(342, 'Reset Password', 'Passwort zur&#252;cksetzen', 'R&#233;initialiser le mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(343, 'Password', 'Passwort', 'Mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(344, 'Save Password', 'Passwort speichern', 'Sauvegarder le mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(345, 'Generate Password', 'Passwort generieren', 'G&#233;n&#233;rer un mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(346, 'Current MySQL&reg Users', 'aktuelle MySQL-Benutzer', 'Utilisateurs actuels de MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(347, 'User name', 'Benutzername', 'Nom d''utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(348, 'Access', 'Rechte', 'Acc&#232;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(349, 'Databases', 'Datenbanken', 'Bases de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(350, 'You have no MySQL&reg users at this time. Create a user using the form below.', 'Sie haben noch keinen MySQL-Benutzer,rnBitte erstellen', 'Aucun utilisateur MySQL&#174;.rnCr&#233;ez-en un ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(351, 'Create a new MySQL&reg User', 'Neuer MySQL&#174;-Benutzer erstellen', 'Cr&#233;er un utilisateur MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(352, 'Map Database', 'Datenbank zuordnen', 'Plan de la base de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(353, 'Remote Access', 'Remote-Zugriff', 'Acc&#232;s &#224; distance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(354, 'Allow from any IP', 'Zugriff von jeder IP', 'Depuis n''importe quelle adresse IP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(355, 'Only from single IP', 'Zugriff auf IP beschr&#228;nken', 'Seulement depuis une adresse IP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(356, 'Databases Usage', 'Datenbankbenutzung', 'Utilisation des bases de donn&#233;es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(357, 'Launch phpMyAdmin', 'phpMyAdmin starten', 'Lancer phpMyAdmin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(358, 'Enter your current and new password', 'aktuelles und neues Passwort eingeben', 'Entrez votre mot de passe actuel ainsi que le nouveau', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(359, 'Current password', 'aktuelles Passwort', 'Mot de passe actuel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(360, 'New password', 'Neues Passwort', 'Nouveau mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(361, 'Confirm new password', 'neues Passort best&#228;tigen', 'Confirmer le nouveau mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(362, 'Change', '&#196;ndern', 'Sauvegarder', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(363, 'Disk Usage Total', 'genutzter Speicherplatz', 'Total d''utilisation des disques', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(364, 'Package Usage Total', 'Paketbenutzung', 'Total d''utilisation du pack', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(365, 'Disk space', 'Speicherplatz', 'Espace disque', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(366, 'Bandwidth', 'Bandbreite', 'Bande passante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(367, 'MySQL&reg databases', 'MySQL-Datenbanken', 'Bases de donn&#233;es MySQL &#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(368, 'Mail forwarders', 'E-Mail-Weiterleitungen', 'Transitaires d'' E-mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(369, 'Domain Usage', 'Domain-Nutzung', 'Utilisation des domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(370, 'Sub-Domain Usage', 'Sub-Domain-Nutzung', 'Utilisation des sous-domaines', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(371, 'Parked-Domain Usage', 'geparkte Domains', 'Utilisation des domaines r&#233;serv&#233;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(372, 'MySQL&reg Database Usage', 'Mysql-Datenbanknutzung', 'utilisation des base de donn&#233;es MySQL&#174;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(373, 'FTP Usage', 'FTP-Nutzung', 'Utilisation FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(374, 'Mailbox Usage', 'Mailb-Nutzung', 'Utilisation des bo&#238;tes mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(375, 'Forwarders Usage', 'genutzte Weiterleitungen', 'Utilisation des redirections', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(376, 'Distribution List Usage', 'genutzte Verteilerlisten', 'Utilisation des listes de distribution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(377, 'Power Options', 'Power-Options', 'Options d''alimentation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(378, 'Do it!', 'Tu es !', 'Do it !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(379, 'Configure your ZPanel Settings', 'ZPanel-Einstellungen anpassen', 'Configurer les param&#232;tres du panel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(380, 'ZPanel Daemon', 'ZPanel-Daemon', 'Mise &#224; jour configuration serveur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(381, 'Next Daemon Run', 'N&#228;chste Daemon-Laufzeit', 'Prochain Daemon', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(382, 'Never', 'Niemals', 'Jamais', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(383, 'Last Daemon Run', 'Letzte Daemon-Laufzeit', 'Ex&#233;cuter le dernier Daemon', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(384, 'Last Day Daemon Run', 'letzter Daemon-Lauf Tag ', 'Dernier lancement Daemon (j)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(385, 'Last Week Daemon Run', 'Letzte Woche Daemon-Lauf', 'Dernier lancement Daemon (s)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(386, 'Last Month Daemon Run', 'Letzter Monat Daemon-Lauf', 'Dernier lancement Daemon (m)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(387, 'Queue a full daemon run (reset)', 'Full-Daemon-Warteschlange erstellen(Reset)', 'R&#233;initialiser les demandes de configuration', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(388, 'Go', 'Los', 'Aller', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(389, 'Run Daemon Now', 'Deamon jetzt starten', 'Ex&#233;cuter le Daemon', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(390, 'Date Format', 'Datumsformat', 'Format de date', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(391, 'Set the date format used by modules.', 'Datumsformat wie in den Modulen setzen', 'R&#233;gler le format de date utilis&#233; par les modules.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(392, 'From Address', 'Absenderadresse', 'Depuis l'' Adresse', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(393, 'The email address to appear in the From field of emails sent by ZPanel.', 'Zpanel-Absender-Mailadresse', 'Adresse mail exp&#233;ditrice des mails depuis le Panel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(394, 'From Name', 'Von Name', 'De ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(395, 'The name to appear in the From field of emails sent by ZPanel.', 'Erscheint als Absender', 'Nom de l''exp&#233;diteur des mails depuis le Panel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(396, 'Icons per Row', 'Symbole pro Reihe', 'Ic&#244;nes par rang&#233;e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(397, 'Set the number of icons to display before beginning a new line.', 'Anzahl der Symbole in einer Reihe', 'D&#233;finir le nombre d''ic&#244;nes &#224; afficher avant de commencer une nouvelle ligne.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(398, 'Log Directory', 'Log-Verzsichnis', 'ou r&#233;pertoire LOG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(399, 'Root path to directory log folders', 'Root-Pfad zum Verzsichnis-log', 'Chemin de la racine vers les dossiers du LOG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(400, 'Mail method', 'Mailsendemethode', 'Methode Mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(401, 'Method to use when sending emails out. (mail = PHP Mail())', 'Mit welcher Methode sollen Mails versendet werden (mail = PHP Mail())', 'M&#233;thode &#224; utiliser lors de l''envoi de mails. (Mail = PHP mail ())', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(402, 'Min Password Length', 'Minimale Passwortl&#228;nge', 'Longueur de mot de passe minimum', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(403, 'Minimum length required for new passwords', 'minimale L&#228;nge die ein neues Passwort haben muss', 'Longueur minimale requise pour nouveau mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(404, 'Root Drive', 'Root-Laufwerk', 'R&#233;pertoire racine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(405, 'The root drive where ZPanel is installed.', 'Root-Laufeerk in dem Zpanel installiert ist. ', 'R&#233;pertoire racine o&#249; est install&#233; ZPanel.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(406, 'Server IP Address', 'Server IP-Adresse', 'Adresse IP du serveur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(407, 'If set this will use this manually entered server IP address which is the prefered method for use behind a firewall.', 'Bei dieser Einstellung wird diese manuelle IP-Adresse benutzt. Dies ist die bevorzugte Methode hinter einer Firewall', 'Adresse recommand&#233;e pour utilisation derri&#232;re un pare-feu', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(408, 'Service Check Timeout', 'Dienste-Check Timeout', 'V&#233;rifier le Temps d''activit&#233; du service', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(409, 'SMTP Auth method', 'SMTP-Authentifizierungsmethode', 'M&#233;thode d&#8217;authentification SMTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(410, 'If specified will attempt to use encryption to connect to the server, if ''false'' this is disabled. Available options: false, ssl, tls', 'Diese Einstellung wird zur verschl&#252;sselten Verbindung gew&#228;hlt, ''false'' bedeutet ohne Verschl&#252;sselung. M&#246;gliche Optionen:rnfalse, ssl, tls', 'Si mentionn&#233;e, tentative de connexion crypt&#233;e.rnOptions disponibles: false, SSL, TLS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(411, 'SMTP Pass', 'SMTP-Passwort', 'Mot de passe SMTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(412, 'Password for authentication on the SMTP server.', 'Passwort ums sich beim SMTP-Server zu authentifizieren', 'Mot de passe pour l''authentification sur le serveur SMTP.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(413, 'SMTP Port', 'SMTP-Port', 'Port SMTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(414, 'The port address of the SMTP server (usually 25)', 'SMTP Port-Adresse (normalerweise 25)', 'Port du serveur SMTP (Par d&#233;faut 25)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(415, 'SMTP Server', 'SMTP-Server', 'Serveur SMTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(416, 'The address of the SMTP server.', 'Adresse des SMTP-Servers', 'L''adresse du serveur SMTP.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(417, 'SMTP User', 'SMTP-User', 'Utilisateur SMTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(418, 'Username for authentication on the SMTP server.', 'SMTP-Benutzername', 'Nom d''utilisateur pour l''authentification sur le serveur SMTP.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(419, 'Temp Directory', 'Tempor&#228;re verzeichnis', 'R&#233;pertoire Temporaire', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(420, 'Global temp directory.', 'Globals Tempor&#228;res Verzeichnis', 'R&#233;pertoire temporaire g&#233;n&#233;ral.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(421, 'Use AUTH', 'Benutze Authentifizierung', 'Utiliser l''authentification', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(422, 'SMTP requires authentication. (true/false)', 'SMTP ben&#246;tigt Authentifizierung (true/false)', 'SMTP requiert une authentification.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(423, 'Use SMTP', 'SMTP benutzen', 'Utiliser SMTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(424, 'Use SMTP server to send emails from. (true/false)', 'SMTp zum senden von E-Mail benutzen (true/false)', 'Utiliser un serveur SMTP pour envoyer des emails.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(425, 'ZIP Exe', 'Zip Exe', 'Executable ZIP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(426, 'Path to the ZIP Executable', 'Pfad zur zip-Anwendung', 'Chemin de l''ex&#233;cutable ZIP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(427, 'ZPanel Debug Mode', 'Zpanel Debug Moder', 'Chemin du deboguage Panel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(428, 'Whether or not to show PHP debug errors,warnings and notices', 'PHP Debug-Fehler, Warnungen und Notizen anzeigen ?', 'Ne JAMAIS montrer les erreurs PHP, les avertissements et les messages d''erreurs !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(429, 'ZPanel Log file', 'Zpanel Log-Datei', 'Le fichier LOG ZPanel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(430, 'If loggging is set to ''file'' mode this is the path to the log file that is to be used by ZPanel.', 'Falls log-methode ''file'' ausgew&#228;hlt ist, bitte hier den Pfad eingeben', 'Si loggging est r&#233;gl&#233; sur le mode "fichier", c''est le chemin vers le fichier LOG qui doit &#234;tre utilis&#233;.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(431, 'ZPanel root path', 'Zpanel Root-Pfad', 'Chemin d''acc&#232;s racine ZPanel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(432, 'Zpanel Web Root', 'ZPanel web-Root', 'Fichiers "site" Zpanel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(433, 'zsudo path', 'zsudo-Pfad', 'chemin zsudo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(434, 'Path to the zsudo binary used by Apache to run system commands.', 'Pfad zur zsudo-binary welche f&#252;r Apache-Befehle genutzt werden', 'Chemin vers le binaire zsudo utilis&#233; par Apache pour ex&#233;cuter des commandes syst&#232;me.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(435, 'Current Cron Tasks', 'aktuelle Cron-Jobs', 'T&#226;ches Cron actuelles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(436, 'You currently do no have any tasks setup.', 'Keine Cronjobs eingestellt', 'Aucune t&#226;che Cron.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(437, 'Script', 'Skript', 'Script', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(438, 'example', 'Beispiel', 'Exemple', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(439, 'Comment', 'Kommentar', 'Commentaire', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(440, 'Executed', 'Ausgef&#252;hrt', 'Ex&#233;cut&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(441, 'Every 1 minute', 'jede 1 Minute', 'Chaque minute', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(442, 'Every 5 minutes', 'alle 5 minuten', 'Toutes les 5 minutes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(443, 'Every 10 minutes', 'alle 10 Minuten', 'Toutes les 10 minutes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(444, 'Every 30 minutes', 'alle 30 Minuten', 'Toutes les 30 minutes', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(445, 'Every 1 hour', 'Jede 1 Stunde', 'Chaque 1 heure', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(446, 'Every 2 hours', 'alle 2 Stunden', 'Toutes les 2 heures', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(447, 'Every 8 hours', 'alle 8 Stunden', 'Toutes les 8 heures', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(448, 'Every 12 hours', 'alle 12 Stunden', 'Toutes les 12 heures', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(449, 'Every 1 day', 'jeden 1 Tag', 'Chaque jour 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(450, 'Every week', 'jede Woche', 'Chaque semaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(451, 'Select Folder', 'Ordner w&#228;hlen', 'S&#233;lectionner un dossier', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(452, 'root', 'root', 'racine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(453, 'Selected folder', 'gew&#228;hlter Ordner', 'Dossier s&#233;lectionn&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(454, 'REMOVE Protection from Directory', 'Verzeichnisschutz ENTFERNEN', 'Suppression de la protection du r&#233;pertoire', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(455, 'Please confirm that you want to remove password protection from this directory.', 'Bitte best&#228;tigen Sie, dass Sie den Verzeichnisschutz aufeben m&#246;chten!', 'Confirmer la suppression de la protection de ce r&#233;pertoire.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(456, 'EDIT .htaccess Users in Directory', 'BEARBEITE .htaccess Benutzer im Verzeichnis', 'G&#233;rer les utilisateurs de ce r&#233;pertoire.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(457, 'From here you can add or remove users that are allowed to access this directory', 'Hier k&#246;nne Sie den Benutzerzugriff auf dieses Verzeichnis verwalten ', 'Ajouter ou supprimer des utilisateurs ayant acc&#232;s &#224; ce r&#233;pertoire.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(458, 'User', 'Benutzer', 'Utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(459, 'Encrypted Password', 'Verschl&#252;ssltes Passwort', 'Mot de passe crypt&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(460, 'Remove', 'Entfernen', 'Supprimer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(461, 'Add New User', 'Neuer Benutzer hinzuf&#252;gen', 'Ajouter un nouvel utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(462, 'Confirm PW', 'Passwort best&#228;tigen', 'Confirmer PW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(463, '.htaccess file is missing. Delete this password entry and recreate it to fix this error.', '.htaccess nicht vorhanden. Diesen Passwort eintrag l&#246;schen und neu erstellen um diesen Fehler zu beheben', 'Le fichier .htaccess est manquant. veuillez supprimer cette entr&#233;e, et la recr&#233;er pour corriger cette erreur.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(464, 'Message', 'Nachricht', 'Message', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(465, 'Restricted Area', 'gesch&#252;tzter Bereich', 'Restricted Area', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(466, 'Password protect your directories', 'Ihre Verzeichnisse mit Passwort sch&#252;tzen', 'Mot de passe de protection du r&#233;pertoire', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(467, 'To begin, first select the directory you want to protect.', 'Bite w&#228;hlen Sie das zu sch&#252;tzende Verzeichnis aus.', 'Pour commencer, s&#233;lectionnez d''abord le r&#233;pertoire que vous voulez prot&#233;ger.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(468, 'Click the folder Links to expand the directory tree.', 'Klicken Sie auf den Ordner um den Verzeichnisbaum anzuzeigen.', 'Cliquez sur les liens de dossier pour d&#233;velopper l''arborescence du r&#233;pertoire.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(469, 'Click on the Select Folder button to select the directory for protection.', 'Klicken Sie auf den Ordner-Button umrndas zu sch&#252;tzende Verzeichnis auszuw&#228;hlen. ', 'Cliquez sur le bouton "S&#233;lectionner un dossier" pour s&#233;lectionner le r&#233;pertoire &#224; prot&#233;ger.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(470, 'All Protected Directories', 'alle gesch&#252;tzte Verzeichnisse', 'Tous les r&#233;pertoires prot&#233;g&#233;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(471, 'Edit allowed users, or remove protection from passworded directories.', 'Erlaubte Benutzer bearbeiten oder Schutz von Verzeichnis entfernen', 'Modifier les utilisateurs autoris&#233;s, ou supprimer la protection du r&#233;pertoire..', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(472, 'Directory', 'Verzeichnis', 'Annuaire', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(473, 'Checking status of services...', 'Dienste-Status pr&#252;fen...', 'V&#233;rification de l''&#233;tat des services ...', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(474, 'Uptime', 'Laufzeit', 'Uptime', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(475, 'Daemon Status', 'Daemon-Status', 'Statut Daemon', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(476, 'To view Webalizer stats for a particular domain or subdomain use the drop-down menu to select the domain or sub-domain you want to view. Stats may take up to 24 hours before they are generated.', 'Um die Webalizer-Statistiken f&#252;r eine einzelne Domain oder Sub-Domain anzuzeigen, verwenden Sie bitte das Dropdown-Men&#252; zur Auswahl. Bis zur Generierung der Statistik k&#246;nnen 24 Stunden vergehen.', 'Pour voir les stats Webalizer pour un domaine particulier ou sous-domaine utilisez le menu d&#233;roulant pour s&#233;lectionner le domaine ou sous-domaine que vous souhaitez afficher. Statistiques peut prendre jusqu''&#224; 24 heures avant qu''ils ne soient g&#233;n&#233;r&#233;s.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(477, 'Select Domain', 'Domain ausw&#228;hlen', 'S&#233;lectionner un domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(478, 'Display', 'anzeigen', 'Afficher', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(479, 'You currently do not have any domains configured.', 'Sie haben noch keine Domains konfiguriert.', 'Aucun domaine configur&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(480, 'Create a new domain', 'Neue Domain erstellen', 'Cr&#233;er un nouveau domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `x_translations` (`tr_id_pk`, `tr_en_tx`, `tr_German_tx`, `tr_French_tx`, `tr_Afrikaans_tx`, `tr_Acholi_tx`, `tr_Albanian_tx`, `tr_Arabic_tx`, `tr_Bulgarian_tx`, `tr_Cantonese_tx`, `tr_Croatian_tx`, `tr_Czech_tx`, `tr_Danish_tx`, `tr_Dutch_tx`, `tr_Farsi_tx`, `tr_FrenchCanadian_tx`, `tr_Greek_tx`, `tr_Hungarian_tx`, `tr_Indonesian_tx`, `tr_Italian_tx`, `tr_Jakartanese_tx`, `tr_Latvian_tx`, `tr_Lithuanian_tx`, `tr_Macedonian_tx`, `tr_Malay_tx`, `tr_Mandarin_tx`, `tr_Norwegian_tx`, `tr_Polish_tx`, `tr_Portuguese_tx`, `tr_Romanian_tx`, `tr_Russian_tx`, `tr_Serbian_tx`, `tr_Shanghainese_tx`, `tr_Slovak_tx`, `tr_Slovenian_tx`, `tr_Spanish_tx`, `tr_Swedish_tx`, `tr_Thai_tx`, `tr_Turkish_tx`, `tr_Ukrainian_tx`, `tr_Vietnamese_tx`) VALUES
(481, 'Current Sub-domains', 'Bestehende Sub-Domains', 'Sous-domaines actuel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(482, 'Sub-domain', 'Sub-Domain', 'Sous-domaine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(483, 'You currently do not have any Sub-domains configured. Create a Sub-domain using the form below.', 'Sie haben noch keine Sub-Domain konfiguriert. Bitte erstellen Sie eine Sub-Domain.', 'Aucun sous-domaine configur&#233;.rnCr&#233;ez-en un ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(484, 'You have reached your Sub-domains limit!', 'maximale Anzahl an Sub-Domains erreicht', 'Vous avez atteint votre limite de sous-domaines.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(485, 'Current parked domains', 'aktuell geparkte Domains', 'Domaines actuellement r&#233;serv&#233;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(486, 'Date parked', 'geparkt am:', 'Date de r&#233;servation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(487, 'You currently do not have any parked domains setup on your account. Create a parked domain using the form below.', 'Sia haben noch keine geparkten Domains. Bitte nutzen Sie das Formular unten auf dieser Seite. ', 'Aucun domaine r&#233;serv&#233;.rnR&#233;servez-en un ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(488, 'You have reached your parked domains limit!', 'maximale Anzahl an geparkten Domains erreicht', 'Vous avez atteint votre limite de r&#233;servation de domaines.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(489, 'Delete Alias', 'Alias l&#246;schen', 'Supprimer Alias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(490, 'Please confirm that you want to delete this Alias.', 'L&#246;schen des Alias best&#228;tigen', 'Confirmer la suppression de cet alias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(491, 'Current Aliases', 'aktuelle Aliase', 'Alias &#8203;&#8203;actuels', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(492, 'Address', 'Adresse', 'Adresse', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(493, 'Destination', 'Ziel', 'Destination', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(494, 'You currently do not have any aliases configured on this server.', 'Sie haben noch keine Aliase konfiguriert', 'Aucun alias configur&#233;', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(495, 'Create a new Alias', 'Neuen Alias erstellen', 'Cr&#233;er un nouvel alias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(496, 'Alias Address', 'Alias-Adresse', 'Adresse Alias', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(497, 'Select a mailbox', 'Mailbox ausw&#228;hlen', 'S&#233;lectionnez une bo&#238;te mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(498, 'Sorry, you have reached your mailbox forward quota limit!', 'Sie haben die maximale Anzahl an Weiterleitungen erreicht!', 'Quota de boites mail atteint !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(499, 'Delete Distribution List', 'Verteilerliste l&#246;schen', 'Supprimer la liste de distribution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(500, 'Please confirm that you want to delete this distribution list.', 'L&#246;schen der Verteilerliste best&#228;tigen', 'Confirmer la suppression de la liste de distribution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(501, 'Edit Distribution List', 'Verteilerliste bearbeiten', 'Modifier la liste de distribution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(502, 'Add New Address', 'Neue Adresse hinzuf&#252;gen', 'Ajouter une nouvelle adresse', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(503, 'Add Mailbox', 'Mailbox hinzuf&#252;gen', 'Ajouter la bo&#238;te mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(504, 'Current Distribution Lists', 'aktuelle Verteilerlisten', 'Listes de distribution actuelles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(505, 'You currently do not have any distribution lists setup.', 'Sie haben noch keine Verteilerliste erstellt', 'Aucune liste de distribution configur&#233;e.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(506, 'Create a new Distribution List', 'Neue Verteilerliste erstellen', 'Cr&#233;er une liste de distribution', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(507, 'You have reached your Distribution List quota limit!', 'Maximale Anzahl vom Verteilerlisten erreicht!', 'Vous avez atteint votre quota limite de listes de distribution!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(508, 'Delete Forwarder', 'Weiterleitung l&#246;schen', 'Supprimer Porteur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(509, 'Please confirm that you want to delete this forwarder.', 'L&#246;schen dieser Weiterleitung best&#228;tigen.', 'Confirmer la suppression de cette redirection', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(510, 'Current Forwarders', 'Aktuelle Weiterleitungen', 'Porteurs actuels', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(511, 'Keep Message', 'Originalnachrichten behalten', 'Garder un message', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(512, 'Sorry there are currently no mailbox forwards configured!', 'Keine Mailbox-Weiterleitungen konfoguriert!', 'Aucune boite mail configur&#233;e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(513, 'Create a new forward', 'Neue Weiterleitung erstellen', 'Cr&#233;er un nouveau porteur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(514, 'Keep original message', 'Originalnachrichten behalten', 'Garder le message d''origine', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(515, 'Delete Mailbox', 'Mailbox l&#246;schen', 'Supprimer la bo&#238;te mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(516, 'Please confirm that you want to delete this mailbox.', 'Bitte best&#228;tigen Sie das L&#246;schen der Mailbox.', 'Confirmer la suppression de cette boite mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(517, 'Edit mailbox', 'Mailbox bearbeiten', 'Modifier cette boite mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(518, 'Set Password', 'Passwort setzen', 'D&#233;finir mot de passe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(519, 'Current mailboxes', 'Aktuelle Mailboxen', 'Bo&#238;tes mail actuelles', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(520, 'Date created', 'Erstelldatum', 'Date de cr&#233;ation', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(521, 'Sorry there are currently no mailboxes configured!', 'Sie haben noch keine Mailboxen konfoguriert!', 'Aucune boite mail configur&#233;e', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(522, 'Create a new mailbox', 'Neue Mailbox erstellen', 'Cr&#233;er une bo&#238;te mail', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(523, 'Sorry, you have reached your mailbox quota limit!', 'Sie haben die maximale Anzahl an Mailboxen erreicht!', 'D&#233;sol&#233;, vous avez atteint votre quota limite de bo&#238;tes mail!', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(524, 'zpanel-hosting.tk', 'zpanel-hosting.tk', 'http://habbheberg.fr.nf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(525, 'zpanel.tk', 'zpanel.tk', 'http://habbheberg.fr.nf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(526, 'This module enables you to set a notice banner that will appear when any of your clients access ZPanel. ', 'Mit diesem Modul k&#246;nnen Sie ein Banner einblenden.', 'Ce module permet de choisir une banni&#232;re pour le Panel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(527, 'Client message', 'Kunden-Nachricht', 'Message clients', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(528, 'Notice message', 'Notiz-Text', 'Message de notification', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(529, 'Hi {{fullname}},\r\rWe are pleased to inform you that your new hosting account is now active, you can now login to ZPanel using the following credentials:\r\rUsername: {{username}}\rPassword: {{password}}', 'Hallo {{fullname}},rnrnIhr Webhosting-Paket wurde angelegt, Sie k&#246;nnen sich mit den folgenden Daten anmelden:rnrnUsername: {{username}}rnPassword: {{password}}rnrnWir w&#252;nschen Ihnen viel Erfolg mit Ihrem Hostingpaket.rn', 'Bonjour {{fullname}}, rnNous avons le plaisir de vous annoncer que votre compte d''h&#233;bergement est d&#233;sormais actif. rnVous pouvez d&#233;sormais vous connecter en utilisant les identifiants suivants: rnNom d''utilisateur: {{username}} rnMot de passe: {{password}}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(530, 'Please confirm that you want to delete this client.', 'Bitte best&#228;tigen Sie, dass Sie den Kunden l&#246;schen m&#246;chten.', 'Confirmer la suppression de ce client (action irr&#233;versible, supprime toutes ses donn&#233;es).', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(531, 'WARNING! This will remove all files and services belonging to this client!', 'WARNUNG! Alle Dateien und Dienste diese Kunden werden unwideruflich gel&#246;scht.', 'ATTENTION ! Ceci va supprimer tous les fichiers et services qui d&#233;pendent de ce client.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(532, 'Move clients and packages (if any exist) this user has to', 'Kunden und Pakete(falls vorhanden) verschieben zu:', 'D&#233;placer les clients et packs (si existants) du revendeur vers', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(533, 'Edit existing client', 'Kunde bearbeiten', '&#201;diter un client existant', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(534, 'User group', 'Benutzergruppe', 'Groupe d''utilisateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(535, 'Current Clients', 'Aktuelle Kunden', 'Clients Actuels', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(536, 'Clients', 'Kunden', 'Clients', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(537, 'You have no client accounts at this time. Create a client using the form below.', 'Noch keine Kunden angelegt. Bitte nutzen Sie das Formular weiter unten auf dieser Seite.', 'Aucun compte client.rnCr&#233;ez-en un ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(538, 'You must first create a Package with the Package Manager module before you can create a client.', 'Sie m&#252;ssen erst ein Paket mit dem Pakete-Manager erstellen, bevor Sie einen Kunden erstellen k&#246;nnen.', 'Vous devez d''abord cr&#233;er un package avant de cr&#233;er un client.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(539, 'Disabled Clients', 'Deakivierte Kunden', 'Clients d&#233;sactiv&#233;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(540, 'Create new client account', 'Neuen Kunden erstellen', 'Cr&#233;er un nouveau compte client', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(541, 'Usergroup', 'Benutzergruppe', 'Groupe d''utilisateurs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(542, 'Send welcome email?', 'Willkommensmail senden ?', 'Envoyez l''email de bienvenue ?', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(543, 'Email Subject', 'E-Mail-Betreff', 'Sujet de l''email', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(544, 'Your ZPanel Account details', 'Ihre ZPanel Konto-Details', 'Vos identifiants ZPanel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(545, 'Email Body', 'E-Mail-Text', 'Contenu de l''email', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(546, 'Delete FTP account', 'FTP-Konto l&#246;schen', 'Supprimer un compte FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(547, 'Please confirm that you want to delete this FTP account.', 'Bitte best&#228;tigen Sie die L&#246;schung des FTP-Kontos.', 'Confirmer la suppression de ce compte FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(548, 'Reset FTP Password for user', 'FTP-Passwort f&#252;r Benutzer zur&#252;cksetzen', 'R&#233;initialiser le mot de passe FTP pour l''utilisateur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(549, 'Current FTP accounts', 'Aktuelle FTP-Konten', 'Comptes FTP actuels', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(550, 'Account name', 'Konto-Name', 'Nom du compte', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(551, 'Permission', 'Berechtigungen', 'Permission', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(552, 'You do not have any FTP Accounts setup. Create an FTP account using the form below.', 'Sie haben noch keine FTP-Konten erstellen. Bitte nutzen Sie das Formular weiter unten auf dieser Seite.', 'Aucun compte FTP.rnCr&#233;ez-en un ci-dessous.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(553, 'Create a new FTP Account', 'Neues FTP-Konto erstellen.', 'Cr&#233;ez un nouveau compte FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(554, 'Access type', 'Zugriffs-Typ', 'Type d''acc&#232;s', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(555, 'Read-only', 'Nur-Lesen', 'Lecture uniquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(556, 'Write-only', 'Nur-Schreiben', '&#201;criture uniquement', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(557, 'Full access', 'Vollzugriff', 'Acc&#232;s complet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(558, 'You have reached your FTP account limit!', 'Sie haben die maximale Anzahl an FTP-Konten ereicht!', 'Vous avez atteint votre limite de comptes FTP !', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(559, 'FTP Client Login', 'FTP-Konto Anmeldung', 'Connexion au client FTP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(560, 'Please choose the FTP account you want to log in with...', 'Bitte w&#228;hlen Sie das FTP-Konto zu Anmeldung aus', 'Veuillez choisir le compte FTP sur lequel vous souhaitez vous authentifier.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(561, 'You have no FTP accounts at this time.', 'Sie haben noch keine FTP-Konten erstellt.', 'Aucun compte FTP.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(562, 'Webmail is a convenient way to check your email accounts online without the need to configure a mail client to connect to webmail click on the image of your choice.', NULL, 'WebMail est un moyen pratique de v&#233;rifier vos comptes de messagerie en ligne sans avoir besoin de configurer un client de messagerie pour se connecter au webmail clic sur l''image de votre choix.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


/*Table structure for table `x_vhosts` */

DROP TABLE IF EXISTS `x_vhosts`;

CREATE TABLE `x_vhosts` (
  `vh_id_pk` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `vh_acc_fk` int(6) DEFAULT NULL,
  `vh_name_vc` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `vh_directory_vc` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `vh_type_in` int(1) DEFAULT '1',
  `vh_active_in` int(1) DEFAULT '0',
  `vh_suhosin_in` int(1) DEFAULT '1',
  `vh_obasedir_in` int(1) DEFAULT '1',
  `vh_custom_tx` text,
  `vh_enabled_in` int(1) DEFAULT '1',
  `vh_created_ts` int(30) DEFAULT NULL,
  `vh_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`vh_id_pk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `x_vhosts` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
