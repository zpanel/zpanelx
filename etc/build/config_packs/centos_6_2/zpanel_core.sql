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
  `ac_pass_vc` varchar(200) DEFAULT NULL,
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
  `ac_passsalt_vc` varchar(22) DEFAULT NULL,
  `ac_created_ts` int(30) DEFAULT NULL,
  `ac_deleted_ts` int(30) DEFAULT NULL,
  PRIMARY KEY (`ac_id_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `x_accounts` */

INSERT  INTO `x_accounts` (
    `ac_id_pk`,
    `ac_user_vc`,
    `ac_pass_vc`,
    `ac_passsalt_vc`,
    `ac_email_vc`,
    `ac_reseller_fk`,
    `ac_package_fk`,
    `ac_group_fk`,
    `ac_usertheme_vc`,
    `ac_usercss_vc`,
    `ac_enabled_in`,
    `ac_lastlogon_ts`,
    `ac_notice_tx`,
    `ac_resethash_tx`,
    `ac_created_ts`,
    `ac_deleted_ts`
    ) 
VALUES 
    (
    1,
    'zadmin',
    'v.eCCwjd4xAGWagHafqod6SMASr25Na',
    '/L8ewHozMz0EqAmmILPFN2',
    'zadmin@localhost',
    1,
    1,
    1,
    'zpanelx',
    'default',
    1,
    0,
    'Welcome to your new ZPanel installation! You can remove this message from the Client Notice Manager module. This module allows you to notify your clients of service outages upgrades and new features etc :-)',
    NULL,
    0,
    NULL
    );

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
  `dn_host_vc` varchar(100) DEFAULT NULL,
  `dn_ttl_in` int(30) DEFAULT NULL,
  `dn_target_vc` varchar(100) DEFAULT NULL,
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

insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (1,1,'How can I update my personal contact details?','From the control panel homepage please click on the &quot;My Account&quot; icon to enable you to update your personal details.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (2,1,'How do I change my password?','Your ZPanel and MySQL password can be easily changed using the &quot;Change Password&quot; icon on the control panel.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (3,1,'I don&#39;t think one of the services(Apache, MySQL, FTP, etc) are running. Is there any easy way to check?','ZPanel comes with a service monitoring system that checks to make sure all the services are up and running, Simply go to your Control Panel Home and select the module called &quot;Service Status&quot;. From there you will be able to see if any of the services are down or up.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (4,1,'How can I set my domain to work with my ZPanel Account?','To setup up a domain with ZPanel first thing you need to do is go &quot;Domains&quot; and add your to the list. Next you need to set the Name Server on your Domain Registrar to match that of your host. This information can be obtained by contacting your host.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (7,1,'How can I create a MySQL Database?','To create a MySQL database simply go to the section of the panel called &quot;Database Management&quot; and select the module called &quot;MySQL Databases&quot; from here you will easily be able to add and manage databases on your account.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (8,1,'What is phpMyAdmin?','phpMyAdmin is an open source tool intended to handle the administration of MySQL databases. It can perform various tasks such as creating, modifying or deleting databases, tables, fields or rows or executing SQL statements',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (9,1,'How do I create FTP Accounts?','You can create FTP accounts by going to &quot;FTP Accounts&quot; from their you can add accounts and manage quotas and directories. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (10,1,'How to Password Protect Directories?','Go to Advanced and select the module &quot;Password Protect Directories&quot; From here you can generate .htaccess files to lock down directories on your site to only people with a login and password.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (11,1,'How do I create an E-Mail Account?','Go to the Mail section of ZPanel and select the module called &quot;Mailboxes&quot;, from here you can create E-Mail account for each domain setup on your account. You can also reset passwords to previously created accounts.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (12,1,'How do I create a Mail Alias?','Go to the Mail section of ZPanel and select the module called &quot;Aliases&quot;, from here you can create Alias E-Mail accounts for each previously created E-Mail account. All mail sent to the alias will be delivered to the master e-mail account.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (13,1,'How can I create a Mailing List?','Mailing lists can be setup by going to the Mail section of ZPanel and select the module called &quot;Distribution Lists&quot;, from here you can create Mailing lists by creating an E-mail Account. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (14,1,'How do I use Mail Forwards?','Go to the Mail section of ZPanel and select the module called &quot;Forwards&quot;, from here you can create E-Mail address on your domains that will forward to other E-Mail addresses that are on different servers like &quot;G-Mail, Yahoo, and MSN&quot;. ',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (15,1,'What are Subdomains?','A subdomain combines a unique identifier with a domain name to become essentially a &quot;domain within a domain.&quot; The unique identifier simply replaces the www in the web address. Yahoo!, for example, uses subdomains such as mail.yahoo.com and music.yahoo.com to reference its mail and music services, under the umbrella of www.yahoo.com. They can be created by using the Subdomain module in the Domains section. You can assign directories for each sub domain from the module.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (16,1,'How can I view how much Data I have used?','You can view how much data you have used by accessing the &quot;Usage Viewer&quot; under the Account Information section of ZPanel. It displays account information in different formats. It displays Data Usage, Domain Usage, Bandwidth Usage, MySQL Usage, and much more.',1,NULL,NULL);
insert  into `x_faqs`(`fq_id_pk`,`fq_acc_fk`,`fq_question_tx`,`fq_answer_tx`,`fq_global_in`,`fq_created_ts`,`fq_deleted_ts`) values (17,1,'How can I access Webmail?','Go to the Mail section of ZPanel and select the module called &quot;Webmail&quot;, from here you can login to your E-Mail account and view and create messages. ',1,NULL,NULL);

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

insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (1,2,'PHPInfo',100,'phpinfo','user','PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extensions and configuration details.',0,'true','','');
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
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (27,3,'FAQ\'s',100,'faqs','user','Please find a list of the most common questions from users, if you are unable to find a solution to your problem below please then contact your hosting provider. Simply click on the FAQ below to view the solution.',NULL,'true','','');
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
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (42,7,'Client Notice Manager',100,'client_notices','user','Enables resellers to set global notices for their clients.',NULL,'true',NULL,NULL);
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (43,3,'Protect Directories',100,'htpasswd','user','This module enables you to configure .htaccess files and users to protect your web directories.',0,'true',NULL,NULL);
insert  into `x_modules`(`mo_id_pk`,`mo_category_fk`,`mo_name_vc`,`mo_version_in`,`mo_folder_vc`,`mo_type_en`,`mo_desc_tx`,`mo_installed_ts`,`mo_enabled_en`,`mo_updatever_vc`,`mo_updateurl_tx`) values (46,7,'Theme Manager',100,'theme_manager','user','Enables the reseller to set themes configurations for their clients.',0,'true','',NULL);
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

insert  into `x_profiles`(`ud_id_pk`,`ud_user_fk`,`ud_fullname_vc`,`ud_language_vc`,`ud_group_fk`,`ud_package_fk`,`ud_address_tx`,`ud_postcode_vc`,`ud_phone_vc`,`ud_created_ts`) values (1,1,'Default Zadmin','en',1,1,'1 Example Road,\r\nIpswich,\r\nSuffolk','IP9 2HL','+44(1473) 000 000',0);

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

insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (6,'dbversion','ZPanel version','10.0.2',NULL,'Database Version','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (7,'zpanel_root','ZPanel root path','/etc/zpanel/panel/',NULL,'Zpanel Web Root','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (8,'module_icons_pr','Icons per Row','10',NULL,'Set the number of icons to display before beginning a new line.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (10,'zpanel_df','Date Format','H:i jS M Y T',NULL,'Set the date format used by modules.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (13,'servicechk_to','Service Check Timeout','10',NULL,'Service Check Timeout','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (14,'root_drive','Root Drive','/',NULL,'The root drive where ZPanel is installed.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (16,'php_exer','PHP executable','php',NULL,'PHP Executable','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (17,'temp_dir','Temp Directory','/var/zpanel/temp/',NULL,'Global temp directory.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (18,'news_url','ZPanel News API URL','http://api.zpanelcp.com/latestnews.json',NULL,'Zpanel News URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (19,'update_url','ZPanel Update API URL','http://api.zpanelcp.com/latestversion.json',NULL,'Zpanel Update URL','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (21,'server_ip','Server IP Address','',NULL,'If set this will use this manually entered server IP address which is the prefered method for use behind a firewall.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (22,'zip_exe','ZIP Exe','zip',NULL,'Path to the ZIP Executable','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (24,'disable_hostsen','Disable auto HOSTS file entry','false','true|false','Disable Host Entries','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (25,'latestzpversion','Cached version of latest zpanel version','10.0.0',NULL,'This is used for caching the latest version of ZPanel.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (26,'logmode','Debug logging mode','db','db|file|email','The default mode to log all errors in.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (27,'logfile','ZPanel Log file','/etc/zpanel/logs/zpanel.log',NULL,'If logging is set to \'file\' mode this is the path to the log file that is to be used by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (28,'apikey','XMWS API Key','ee8795c8c53bfdb3b2cc595186b68912',NULL,'The secret API key for the server.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (29,'email_from_address','From Address','zpanel@localhost',NULL,'The email address to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (30,'email_from_name','From Name','ZPanel Server',NULL,'The name to appear in the From field of emails sent by ZPanel.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (31,'email_smtp','Use SMTP','false','true|false','Use SMTP server to send emails from. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (32,'smtp_auth','Use AUTH','false','true|false','SMTP requires authentication. (true/false)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (33,'smtp_server','SMTP Server','',NULL,'The address of the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (34,'smtp_port','SMTP Port','465',NULL,'The port address of the SMTP server (usually 25)','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (35,'smtp_username','SMTP User','',NULL,'Username for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (36,'smtp_password','SMTP Pass','',NULL,'Password for authentication on the SMTP server.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (37,'smtp_secure','SMTP Auth method','false','false|ssl|tls','If specified will attempt to use encryption to connect to the server, if \'false\' this is disabled. Available options: false, ssl, tls','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (38,'daemon_lastrun','Daemon timeing cache','0',NULL,'Timestamp of when the daemon last ran.',NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (39,'daemon_dayrun','Daemon timeing cache','0',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (40,'daemon_weekrun','Daemon timeing cache','0',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (41,'daemon_monthrun','Daemon timeing cache','0',NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (42,'purge_bu','Purge Backups','true','true|false','Delete client backups after allotted time has elapsed to help save diskspace (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (43,'purge_date','Purge Date','30',NULL,'Time in days backups are safe from being deleted. After days have elapsed, older backups will be deleted on Daemon Day Run','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (44,'disk_bu','Disk Backups','true','true|false','Allow users to create and save backups of their home directories to disk. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (45,'schedule_bu','Daily Backups','true','true|false','Make a daily backup of each clients data, including MySQL databases to their backup folder. Backups will still be created if Disk Backups are set to false. (true/false)','Backup Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (46,'ftp_db','FTP Database','zpanel_proftpd',NULL,'The name of the ftp server database','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (47,'ftp_php','FTP PHP','proftpd.php',NULL,'Name of PHP to include when adding FTP data.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (48,'ftp_service','FTP Service Name','proftpd',NULL,'The name of the FTP service','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (49,'ftp_service_root','FTP Service Root','/etc/init.d/',NULL,'The path to the service executable if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (50,'ftp_config_file','FTP Config File','',NULL,'The path to the configuration file if applicable.','FTP Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (51,'mailserver_db','Mailserver Database','zpanel_postfix',NULL,'The name of the mail server database','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (52,'hmailserver_et','Hmail Encryption Type','2',NULL,'Type of encryption uses for hMailServer passwords','Mail Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (53,'max_mail_size','Max Mailbox Size','200',NULL,'Maximum size in megabytes allowed for mailboxes. Default = 200','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (54,'mailserver_php','Mailserver PHP','postfix.php',NULL,'Name of PHP to include when adding mailbox data.','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (55,'remove_orphan','Remove Orphans','true','true|false','When domains are deleted, also delete all mailboxes for that domain when the daemon runs. (true/false)','Mail Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (56,'named_dir','Named Directory','/etc/zpanel/configs/bind/etc/',NULL,'Path to the directory where named.conf is stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (57,'named_conf','Named Config','named.conf',NULL,'Named configuration file','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (58,'zone_dir','Zone Directory','/etc/zpanel/configs/bind/zones/',NULL,'Path to where DNS zone files are stored','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (59,'refresh_ttl','SOA Refesh TTL','21600',NULL,'Global refresh TTL.  Default = 21600 (6 hours)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (60,'retry_ttl','SOA Retry TTL','3600',NULL,'Global retry TTL. Default = 3600 (1 hour)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (61,'expire_ttl','SOA Expire TTL','604800',NULL,'Global expire TTL. Default = 604800 (1 week)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (62,'minimum_ttl','SOA Minimum TTL','86400',NULL,'Global minimum TTL. Default = 86400 (1 day)','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (63,'custom_ip','Allow Custom IP','true','true|false','Allow users to change IP settings in A records. If set to false, IP is locked to server IP setting in ZPanel Config','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (64,'bind_dir','Path to BIND Root','/etc/named/',NULL,'Path to the root directory where BIND is installed.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (65,'bind_service','BIND Service Name','named',NULL,'Name of the BIND service','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (66,'allow_xfer','Allow Zone Transfers','any',NULL,'Setting to restrict zone transfers in setting: allow-transfer {}; Default = all','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (67,'allowed_types','Allowed Record Types','A AAAA CNAME MX TXT SRV SPF NS',NULL,'Types of records allowed seperated by a space. Default = A AAAA CNAME MX TXT SRV SPF NS','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (68,'bind_log','Bind Log','/var/named/data/named.run',NULL,'Path and name of the Bind Log','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (69,'hosted_dir','Vhosts Directory','/var/zpanel/hostdata/',NULL,'Virtual host directory','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (70,'disable_hostsen','Disable HOSTS file entries','false','true|false','Disable host entries','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (71,'apache_vhost','Apache VHOST Conf','/etc/zpanel/configs/apache/httpd-vhosts.conf',NULL,'The full system path and filename of the Apache VHOST configuration name.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (72,'php_handler','PHP Handler','AddType application/x-httpd-php .php3 .php',NULL,'The PHP Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (73,'cgi_handler','CGI Handler','ScriptAlias /cgi-bin/ \"/_cgi-bin/\"\r\n<location /cgi-bin>\r\nAddHandler cgi-script .cgi .pl\r\nOptions ExecCGI -Indexes\r\n</location>',NULL,'The CGI Handler.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (74,'global_vhcustom','Global VHost Entry',NULL,NULL,'Extra directives for all apache vhost\'s.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (75,'static_dir','Static Pages Directory','/etc/zpanel/panel/etc/static/',NULL,'The ZPanel static directory, used for storing welcome pages etc. etc.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (76,'parking_path','Vhost Parking Path','/etc/zpanel/panel/etc/static/parking/',NULL,'The path to the parking website, this will be used by all clients.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (78,'shared_domains','Shared Domains','no-ip,dyndns',NULL,'Domains entered here can be shared across multiple accounts. Seperate domains with , example: no-ip,dyndns','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (79,'upload_temp_dir','Upload Temp Directory','/var/zpanel/temp/',NULL,'The path to the Apache Upload directory (with trailing slash)','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (80,'apache_port','Apache Port','80',NULL,'Apache service port','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (81,'dir_index','Directory Indexes','DirectoryIndex index.html index.htm index.php index.asp index.aspx index.jsp index.jspa index.shtml index.shtm',NULL,'Directory Index','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (82,'suhosin_value','Suhosin Value','php_admin_value suhosin.executor.func.blacklist \"passthru, show_source, shell_exec, system, pcntl_exec, popen, pclose, proc_open, proc_nice, proc_terminate, proc_get_status, proc_close, leak, apache_child_terminate, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_setuid, escapeshellcmd, escapeshellarg, exec\"',NULL,'Suhosin configuration for virtual host  blacklisting commands','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (83,'openbase_seperator','Open Base Seperator',':',NULL,'Seperator flag used in open_base_directory setting','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (84,'openbase_temp','Open Base Temp Directory','/var/zpanel/temp/',NULL,'Temp directory used in open_base_directory setting','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (85,'access_log_format','Access Log Format','combined','combined|common','Log format for the Apache access log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (86,'bandwidth_log_format','Bandwidth Log Format','common','combined|common','Log format for the Apache bandwidth log','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (87,'global_zpcustom','Global ZPanel Entry',NULL,NULL,'Extra directives for Zpanel default vhost.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (88,'use_openbase','Use Open Base Dir','true','true|false','Enable openbase directory for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (89,'use_suhosin','Use Suhosin','true','true|false','Enable Suhosin for all vhosts','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (90,'zpanel_domain','ZPanel Domain','zpanel.ztest.com',NULL,'Domain that the control panel is installed under.','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (91,'log_dir','Log Directory','/var/zpanel/logs/',NULL,'Root path to directory log folders','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (92,'apache_changed','Apache Changed','true','true|false','If set, Apache Config daemon hook will write the vhost config file changes.','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (94,'apache_allow_disabled','Allow Disabled','true','true|false','Allow webhosts to remain active even if a user has been disabled.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (95,'apache_budir','VHost Backup Dir','/var/zpanel/backups/',NULL,'Directory that vhost.conf backups are stored.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (96,'apache_purgebu','Purge Backups','true','true|false','Old backups are deleted after the date set in Puge Date','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (97,'apache_purge_date','Purge Date','7',NULL,'Time in days that vhost backups are safe from deletion','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (98,'apache_backup','VHost Backup','true','true|false','Backup vhost file before a new one is written','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (99,'zsudo','zsudo path','/etc/zpanel/panel/bin/zsudo',NULL,'Path to the zsudo binary used by Apache to run system commands.','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (100,'apache_restart','Apache Restart Cmd','reload',NULL,'Command line arguments used after the restart service request when reloading Apache.','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (101,'httpd_exe','Apache Binary','httpd',NULL,'Path to the Apache binary','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (102,'apache_sn','Apache Service Name','httpd',NULL,'Service name used to handle Apache service control','Apache Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (103,'daemon_exer',NULL,'/etc/zpanel/panel/bin/daemon.php',NULL,'Path to the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (104,'daemon_timing',NULL,'0 * * * *',NULL,'Cron time for when to run the ZPanel daemon','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (105,'cron_file','Cron File','/var/spool/cron/apache',NULL,'Path to the user cron file','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (106,'htpasswd_exe','htpassword Exe','htpasswd',NULL,'Path to htpasswd.exe for protecting directories with .htaccess','Apache Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (107,'mysqldump_exe','MySQL Dump','mysqldump',NULL,'Path to MySQL dump','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (108,'dns_hasupdates','DNS Updated',NULL,NULL,NULL,NULL,'false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (109,'named_checkconf','Named CheckConfig','named-checkconf',NULL,'Path to named-checkconf bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (110,'named_checkzone','Named CheckZone','named-checkzone',NULL,'Path to named-checkzone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (111,'named_compilezone','Named CompileZone','named-compilezone',NULL,'	Path to named-compilezone bind utility.','DNS Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (112,'mailer_type','Mail method','mail','mail|smtp|sendmail','Method to use when sending emails out. (mail = PHP Mail())','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (113,'daemon_run_interval','Number of seconds between each daemon execution','300',NULL,'The total number of seconds between each daemon run (default 300 = 5 mins)','ZPanel Config','false');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (114,'debug_mode','ZPanel Debug Mode','dev','dev|prod','Whether or not to show PHP debug errors,warnings and notices','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (115,'password_minlength','Min Password Length','6',NULL,'Minimum length required for new passwords','ZPanel Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (116,'cron_reload','Cron Reload','crontab -u apache /var/spool/cron/apache',NULL,'Cron reload command for apache user in Linux Only','Cron Config','true');
insert  into `x_settings`(`so_id_pk`,`so_name_vc`,`so_cleanname_vc`,`so_value_tx`,`so_defvalues_tx`,`so_desc_tx`,`so_module_vc`,`so_usereditable_en`) values (117,'login_csfr','Remote Login Forms','false','false|true','Disables CSFR protection on the login form to enable remote login forms.','ZPanel Config','true');


/*Table structure for table `x_translations` */

DROP TABLE IF EXISTS `x_translations`;

CREATE TABLE `x_translations` (
  `tr_id_pk` int(11) NOT NULL AUTO_INCREMENT,
  `tr_en_tx` text,
  `tr_German_tx` text,
  PRIMARY KEY (`tr_id_pk`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=832 ;

--
-- Contenu de la table `x_translations`
--

INSERT INTO `x_translations` (`tr_id_pk`, `tr_en_tx`, `tr_German_tx`) VALUES
(1, 'Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.', 'Webmail ist ein bequemer Weg fÃ¼r Sie, Ihre E-Mail-Konten online zu Ã¼berprÃ¼fen, ohne dass eine E-Mail-Client zu konfigurieren.'),
(2, 'Launch Webmail', 'Starten Sie WebMail'),
(3, 'PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extentsions and configuration details.', 'PHPInfo bietet Ihnen Informationen über die PHP-Version auf dem System, sowie PHP installiert extentsions und Konfigurationsmöglichkeiten.'),
(4, 'From here you can shadow any of your client''s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!', 'Von hier aus können alle Ihre Kunden-Accounts können Schatten, ermöglicht Ihnen dies automatisch, wenn der Benutzer mit dem Sie remote helfen zu sehen, was sie sehen, anbieten zu können login!'),
(5, 'My Account', 'Meine Konto'),
(6, 'Change Password', 'Kennwort ändern'),
(7, 'Shadowing', 'Schatten'),
(8, 'ZPanel Config', 'Config ZPanel'),
(9, 'ZPanel News', 'ZPanel Aktuelles'),
(10, 'Updates', 'Aktualisierung'),
(11, 'Report Bug', 'Fehler melden'),
(12, 'Account', 'Konto'),
(13, 'Module Admin', 'Modul Admin'),
(14, 'Backup', 'Sicherungskopie'),
(15, 'Network Tools', 'Netzwerk-Tools'),
(16, 'Service Status', 'Service Status'),
(17, 'PHPInfo', 'PHPInfo'),
(18, 'phpMyAdmin', 'phpMyAdmin'),
(19, 'Domains', 'Domains'),
(20, 'Sub Domains', 'Sub Domains'),
(21, 'Parked Domains', 'geparkte Domains'),
(22, 'Manage Clients', 'Verwalten Kunden'),
(23, 'Package Manager', 'Paket Manager'),
(24, 'Server', 'Server'),
(25, 'Database', 'Datenbank'),
(26, 'Advanced', 'Fortgeschritten'),
(27, 'Mail', 'Post'),
(28, 'Reseller', 'Wiederverkäufer'),
(29, 'Account Information', 'Account Informationen'),
(30, 'Server Admin', 'Server Admin'),
(31, 'Database Management', 'Datenbank Verwalten'),
(32, 'Domain Management', 'Verwalten von Domains'),
(33, 'Find out all the latest news and information from the ZPanel project.', 'Finden Sie heraus, alle Neuigkeiten und Informationen aus dem ZPanel Projekt.'),
(34, 'Check to see if there are any available updates to your version of the ZPanel software.', 'Prüfen Sie, ob es irgendwelche verfügbaren Aktualisierungen für Ihre Version des ZPanel Software.'),
(35, 'If you have found a bug with ZPanel you can report it here.', 'Did you mean: If you have found a bug with CPanel you can report it here.\r\nWenn Sie einen Fehler mit ZPanel gefunden haben, können Sie ihn hier melden.'),
(36, 'phpMyAdmin is a web based tool that enables you to manage your ZPanel MySQL databases via. the web.', 'phpMyAdmin ist ein webbasiertes Tool, das Sie zu Ihrem ZPanel MySQL-Datenbanken via verwalten können. im Internet.'),
(37, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.', 'Aktuelle persönlichen Daten, die Sie uns mit vorgesehen ist, bitten wir Sie, diese zu halten bis zu Datum, falls wir mit Ihnen Kontakt aufnehmen über Ihre Hosting-Paket erfordern.'),
(38, 'Webmail is a convenient way for you to check your email accounts online without the need to configure an email client.', 'Webmail ist ein bequemer Weg für Sie, Ihre E-Mail-Konten online zu überprüfen, ohne dass eine E-Mail-Client zu konfigurieren.'),
(39, 'Change your current control panel password.', 'Ändern Sie Ihre aktuelle Bedienfeld oder MySQL-Kennwort.'),
(40, 'The backup manager module enables you to backup your entire hosting account including all your MySQL&reg databases.', 'Der Backup-Manager-Modul ermöglicht es Ihnen, Ihre gesamte Hosting-Account inklusive aller Ihrer MySQL &reg Datenbank-Backup.'),
(41, 'You can use the tools below to diagnose issues or to simply test connectivity to other servers or sites around the globe.', 'Sie können die folgenden Tools verwenden, um Probleme zu diagnostizieren oder einfach testen Verbindung mit anderen Servern oder Websites rund um den Globus.'),
(42, 'Here you can check the current status of our services and see what services are up and running and which are down and not.', 'Hier können Sie den aktuellen Status unserer Dienstleistungen und sehen, welche Dienste vorhanden sind und laufen, und die nach unten und es nicht sind.'),
(43, 'This module enables you to add or configure domain web hosting on your account.', 'Dieses Modul ermöglicht es Ihnen, hinzuzufügen oder zu konfigurieren Domain Hosting auf Ihrem Konto.'),
(44, 'Domain parking refers to the registration of an Internet domain name without that domain being used to provide services such as e-mail or a website. If you have any domains that you are not using, then simply park them!', 'Domain-Parking bezieht sich auf die Registrierung von Internet Domain-Namen ohne diese Domäne verwendet, um Dienste wie E-Mail oder eine Webseite bereitzustellen. Wenn Sie alle Domains, die Sie nicht haben, dann einfach parken sie!'),
(45, 'This module enables you to add or configure domain web hosting on your account.', 'Dieses Modul ermöglicht es Ihnen, hinzuzufügen oder zu konfigurieren Domain Hosting auf Ihrem Konto.'),
(46, 'Administer or configure modules registered with module admin', 'Verwalten oder zu konfigurieren Module mit Modul admin registriert'),
(47, 'The account manager enables you to view, update and create client accounts.', 'Die Account-Manager ermöglicht es Ihnen, anzuzeigen, zu aktualisieren und erstellen Kundenkonten.'),
(48, 'Welcome to the Package Manager, using this module enables you to create and manage existing reseller packages on your ZPanel hosting account.', 'Willkommen auf der Paket-Manager, mit diesem Modul ermöglicht Ihnen die Erstellung und Verwaltung von bestehenden Reseller-Pakete auf Ihrem ZPanel Hosting-Account.'),
(49, 'Gives you access to your files with drag-and-drop, multiple file uploading, text editing, zip support.', 'Ermöglicht den Zugriff auf Ihre Dateien mit Drag-and-drop, multiple Datei-Upload, Textbearbeitung, zip unterstützen.'),
(50, 'Secure FTP Applet is a JAVA based FTP client component that runs within your web browser. It is designed to let non-technical users exchange data securely with an FTP server.', 'Secure FTP Applet ist eine Java-basierte FTP-Client-Komponente, die in Ihrem Web-Browser läuft. Es wurde entwickelt, um nicht-technische Anwender den Datenaustausch secureiy lassen mit einem FTP-Server.'),
(51, 'Full name', 'Vollständiger Name'),
(52, 'Email Address', 'E-Mail Adresse'),
(53, 'Phone Number', 'Telefonnummer'),
(54, 'Choose Language', 'Sprache wählen'),
(55, 'Postal Address', 'Postanschrift'),
(56, 'Postal Code', 'Postleitzahl'),
(57, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.', 'Aktuelle persönlichen Daten, die Sie uns mit vorgesehen ist, bitten wir Sie, diese zu halten bis zu Datum, falls wir mit Ihnen Kontakt aufnehmen über Ihre Hosting-Paket erfordern.'),
(58, 'Changes to your account settings have been saved successfully!', 'Änderungen an Ihrem Konto-Einstellungen wurden erfolgreich gespeichert!'),
(59, 'Update Account', 'Aktualisierung Konto'),
(60, 'Enter your account details', 'Geben Sie Ihre Kontodaten'),
(61, 'Home', NULL),
(62, 'File', NULL),
(63, 'FTP Accounts', NULL),
(64, 'Client Notice Manager', NULL),
(65, 'Manage Groups', NULL),
(66, 'Theme Manager', NULL),
(67, 'xBilling', NULL),
(68, 'Aliases', NULL),
(69, 'Distribution Lists', NULL),
(70, 'Forwards', NULL),
(71, 'Mailboxes', NULL),
(72, 'WebMail', NULL),
(73, 'Domain', NULL),
(74, 'DNS Manager', NULL),
(75, 'MySQL Database', NULL),
(76, 'MySQL Users', NULL),
(77, 'Cron Manager', NULL),
(78, 'FAQ\\\\\\''s', NULL),
(79, 'Protect Directories', NULL),
(80, 'Webalizer Stats', NULL),
(81, 'Admin', NULL),
(82, 'phpSysInfo', NULL),
(83, 'ZXTS', NULL),
(84, 'Usage Viewer', NULL),
(85, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\n', NULL),
(86, 'Username', NULL),
(87, 'Package name', NULL),
(88, 'Account type', NULL),
(89, 'Last Logon', NULL),
(90, 'Disk Quota', NULL),
(91, 'Bandwidth Quota', NULL),
(92, 'Used', NULL),
(93, 'Max', NULL),
(94, 'Sub-domains', NULL),
(95, 'Email Accounts', NULL),
(96, 'Email Forwarders', NULL),
(97, 'MySQL&reg; databases', NULL),
(98, 'Your IP', NULL),
(99, 'Server IP', NULL),
(100, 'Server OS', NULL),
(101, 'Apache Version', NULL),
(102, 'PHP Version', NULL),
(103, 'MySQL Version', NULL),
(104, 'ZPanel Version', NULL),
(105, 'Server uptime', NULL),
(106, 'FAQ\\''s', NULL),
(107, 'ChangeZP(br)Password', NULL),
(108, 'MyZP(br)Account', NULL),
(109, 'The account usage screen enables you to see exactly what you are currently using on your hosting package.', NULL),
(110, 'UsageZP(br)Viewer', NULL),
(111, 'ModuleZP(br)Admin', NULL),
(112, 'PHPInfo provides you with information regarding the version of PHP running on this system as well as installed PHP extensions and configuration details.', NULL),
(113, 'phpSysInfo is a web-based server hardware monitoring tool which enables you to see detailed hardware statistics of your server.', NULL),
(114, 'From here you can shadow any of your client\\\\\\''s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!', NULL),
(115, 'Changes made here affect the entire ZPanel configuration, please double check everything before saving changes.', NULL),
(116, 'ZPanelZP(br)Config', NULL),
(117, 'ZPanelZP(br)News', NULL),
(118, 'Here you can manage your lanuages, install new lanuages from ZXTS, and add support for symbolic lanuages', NULL),
(119, 'Here you can configure PHP scripts to run automatically at different time intervals.', NULL),
(120, 'CronZP(br)Manager', NULL),
(121, 'Please find a list of the most common questions from users, if you are unable to find a solution to your problem below please then contact your hosting provider. Simply click on the FAQ below to view the solution.', NULL),
(122, 'This module enables you to configure .htaccess files and users to protect your web directories.', NULL),
(123, 'ProtectZP(br)Directories', NULL),
(124, 'ServiceZP(br)Status', NULL),
(125, 'You can view many statistics such as visitor infomation, bandwidth used, referal infomation and most viewed pages etc. Web stats are based on Domains and sub-domains so to view web stats for a particular domain or subdomain use the drop-down menu to select the domain or sub-domain you want to view web stats for.', NULL),
(126, 'WebalizerZP(br)Stats', NULL),
(127, 'MySQL&reg databases are used by many PHP applications such as forums and ecommerce systems, below you can manage and create MySQL&reg databases.', NULL),
(128, 'MySQLZP(br)Database', NULL),
(129, 'MySQL&reg Users allows you to add users and permissions to your MySQL&reg databases.', NULL),
(130, 'MySQLZP(br)Users', NULL),
(131, '', NULL),
(132, 'DNSZP(br)Manager', NULL),
(133, 'ParkedZP(br)Domains', NULL),
(134, 'SubZP(br)Domains', NULL),
(135, 'Using this module you have the ability to create alias mailboxes to existing accounts.', NULL),
(136, 'This module enables you to create and manage email distribution groups.', NULL),
(137, 'DistributionZP(br)Lists', NULL),
(138, 'Using this module you have the ability to create mail forwarders.', NULL),
(139, 'Using this module you have the ability to create IMAP and POP3 Mailboxes.', NULL),
(140, 'Enables resellers to set global notices for their clients.', NULL),
(141, 'ClientZP(br)NoticeZP(br)Manager', NULL),
(142, 'ManageZP(br)Clients', NULL),
(143, 'Manage user groups to enable greater control over module permission.', NULL),
(144, 'ManageZP(br)Groups', NULL),
(145, 'PackageZP(br)Manager', NULL),
(146, 'Enables the reseller to set themes configurations for their clients.', NULL),
(147, 'ThemeZP(br)Manager', NULL),
(148, 'This module empower ZPanel with the ability of billable clients.', NULL),
(149, 'File Management', NULL),
(150, 'Using this module you can create FTP accounts which will enable you and any other accounts you create to have the ability to upload and manage files on your hosting space.', NULL),
(151, 'FTPZP(br)Accounts', NULL),
(152, 'Change<br/>Password', NULL),
(153, 'My<br/>Account', NULL),
(154, 'Usage<br/>Viewer', NULL),
(155, 'Module<br/>Admin', NULL),
(156, 'From here you can shadow any of your client\\''s accounts, this enables you to automatically login as the user which enables you to offer remote help by seeing what they see!', NULL),
(157, 'ZPanel<br/>Config', NULL),
(158, 'ZPanel<br/>News', NULL),
(159, 'Cron<br/>Manager', NULL),
(160, 'Protect<br/>Directories', NULL),
(161, 'Service<br/>Status', NULL),
(162, 'Webalizer<br/>Stats', NULL),
(163, 'MySQL<br/>Database', NULL),
(164, 'MySQL<br/>Users', NULL),
(165, 'DNS<br/>Manager', NULL),
(166, 'Parked<br/>Domains', NULL),
(167, 'Sub<br/>Domains', NULL),
(168, 'Distribution<br/>Lists', NULL),
(169, 'Client<br/>Notice<br/>Manager', NULL),
(170, 'Manage<br/>Clients', NULL),
(171, 'Manage<br/>Groups', NULL),
(172, 'Package<br/>Manager', NULL),
(173, 'Theme<br/>Manager', NULL),
(174, 'FTP<br/>Accounts', NULL),
(175, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\n', NULL),
(176, 'Enter your current and new password', NULL),
(177, 'Current password', NULL),
(178, 'New password', NULL),
(179, 'Confirm new password', NULL),
(180, 'Change', NULL),
(181, 'Current personal details that you have provided us with, We ask that you keep these upto date in case we require to contact you regarding your hosting package.\r\n', NULL),
(182, 'Disk Usage Total', NULL),
(183, 'Package Usage Total', NULL),
(184, 'Disk space', NULL),
(185, 'Bandwidth', NULL),
(186, 'MySQL&reg databases', NULL),
(187, 'Mail forwarders', NULL),
(188, 'Pie chart', NULL),
(189, 'Domain Usage', NULL),
(190, 'Sub-Domain Usage', NULL),
(191, 'Parked-Domain Usage', NULL),
(192, 'MySQL&reg Database Usage', NULL),
(193, 'FTP Usage', NULL),
(194, 'Mailbox Usage', NULL),
(195, 'Forwarders Usage', NULL),
(196, 'Distribution List Usage', NULL),
(197, 'Administration Modules', NULL),
(198, 'Apache Config', NULL),
(199, 'Backup Config', NULL),
(200, 'DNS Config', NULL),
(201, 'FTP Config', NULL),
(202, 'Mail Config', NULL),
(203, 'Configure Modules', NULL),
(204, 'Module', NULL),
(205, 'On', NULL),
(206, 'Off', NULL),
(207, 'Category', NULL),
(208, 'Up-to-date?', NULL),
(209, 'Enabled', NULL),
(210, 'Disabled', NULL),
(211, 'N/A (Module Admin)', NULL),
(212, 'Save changes', NULL),
(213, 'Module Information', NULL),
(214, 'Module name', NULL),
(215, 'Module description', NULL),
(216, 'Module developer', NULL),
(217, 'Module version', NULL),
(218, 'Latest Version', NULL),
(219, 'Module Type', NULL),
(220, 'Install a new module', NULL),
(221, 'You can automatically install a new module by uploading your zpanel package archive (.zpp file) and then click \\\\\\''Install\\\\\\'' to begin the process.', NULL),
(222, 'Install!', NULL),
(223, 'You can automatically install a new module by uploading your zpanel package archive (.zpp file) and then click \\''Install\\'' to begin the process.', NULL),
(224, 'This module enables you to configure Apache Vhost settings for your hosting accounts.', NULL),
(225, 'Configure your Apache Settings', NULL),
(226, 'Access Log Format', NULL),
(227, 'Log format for the Apache access log', NULL),
(228, 'Allow Disabled', NULL),
(229, 'Allow webhosts to remain active even if a user has been disabled.', NULL),
(230, 'Apache Binary', NULL),
(231, 'Path to the Apache binary', NULL),
(232, 'Apache Port', NULL),
(233, 'Apache service port', NULL),
(234, 'Apache Restart Cmd', NULL),
(235, 'Command line arguments used after the restart service request when reloading Apache.', NULL),
(236, 'Apache Service Name', NULL),
(237, 'Service name used to handle Apache service control', NULL),
(238, 'Apache VHOST Conf', NULL),
(239, 'The full system path and filename of the Apache VHOST configuration name.', NULL),
(240, 'Bandwidth Log Format', NULL),
(241, 'Log format for the Apache bandwidth log', NULL),
(242, 'Directory Indexes', NULL),
(243, 'Directory Index', NULL),
(244, 'Disable HOSTS file entries', NULL),
(245, 'Disable host entries', NULL),
(246, 'Global VHost Entry', NULL),
(247, 'Extra directives for all apache vhost\\''s.', NULL),
(248, 'Global ZPanel Entry', NULL),
(249, 'Extra directives for Zpanel default vhost.', NULL),
(250, 'Open Base Temp Directory', NULL),
(251, 'Temp directory used in open_base_directory setting', NULL),
(252, 'Purge Backups', NULL),
(253, 'Old backups are deleted after the date set in Puge Date', NULL),
(254, 'Purge Date', NULL),
(255, 'Time in days that vhost backups are safe from deletion', NULL),
(256, 'Shared Domains', NULL),
(257, 'Domains entered here can be shared across multiple accounts. Seperate domains with , example: no-ip,dyndns', NULL),
(258, 'Static Pages Directory', NULL),
(259, 'The ZPanel static directory, used for storing welcome pages etc. etc.', NULL),
(260, 'Suhosin Value', NULL),
(261, 'Suhosin configuration for virtual host  blacklisting commands', NULL),
(262, 'Upload Temp Directory', NULL),
(263, 'The path to the Apache Upload directory (with trailing slash)', NULL),
(264, 'Use Open Base Dir', NULL),
(265, 'Enable openbase directory for all vhosts', NULL),
(266, 'Use Suhosin', NULL),
(267, 'Enable Suhosin for all vhosts', NULL),
(268, 'VHost Backup', NULL),
(269, 'Backup vhost file before a new one is written', NULL),
(270, 'VHost Backup Dir', NULL),
(271, 'Directory that vhost.conf backups are stored.', NULL),
(272, 'Vhost Parking Path', NULL),
(273, 'The path to the parking website, this will be used by all clients.', NULL),
(274, 'Vhosts Directory', NULL),
(275, 'Virtual host directory', NULL),
(276, 'Force Update', NULL),
(277, 'Force vhost.conf to be updated on next daemon run. Any change in settings also triggers vhost.conf to be updated.', NULL),
(278, 'Cancel', NULL),
(279, 'Override a Virtual Host Setting', NULL),
(280, 'Select Vhost', NULL),
(281, 'Select a domain', NULL),
(282, 'All Virtual Hosts with Overrides', NULL),
(283, 'Disabled Virtual Hosts', NULL),
(284, 'Delete', NULL),
(285, 'Please confirm that you want to delete this domain.', NULL),
(286, 'Current domains', NULL),
(287, 'Domain name', NULL),
(288, 'Home directory', NULL),
(289, 'Status', NULL),
(290, 'You currently do not have any domains configured. Create a domain using the form below.', NULL),
(291, 'Create a new home directory', NULL),
(292, 'Use existing home directory', NULL),
(293, 'You have reached your domain limit!', NULL),
(294, 'Changes to your domain web hosting has been saved successfully.', NULL),
(295, 'Pending', NULL),
(296, 'Your domain will become active at the next scheduled update.  This can take up to one hour.', NULL),
(297, 'Live', NULL),
(298, 'Virtual Host Override', NULL),
(299, 'Set options for virtual host', NULL),
(300, 'Domain Enabled', NULL),
(301, 'Suhosin Enabled', NULL),
(302, 'OpenBase Enabled', NULL),
(304, 'Port Override', NULL),
(305, 'Forward Port 80 to Overriden Port', NULL),
(306, 'Warning requires Apache mod_rewrite to be installed on the server.', NULL),
(307, 'IP Override', NULL),
(308, 'Custom Entry', NULL),
(309, 'Save Vhost', NULL),
(310, 'Changes to your settings have been saved successfully!', NULL),
(311, 'This module enables you to configure Backup settings for your hosting accounts.', NULL),
(312, 'Configure your Backup Settings', NULL),
(313, 'Daily Backups', NULL),
(314, 'Make a daily backup of each clients data, including MySQL databases to their backup folder. Backups will still be created if Disk Backups are set to false. (true/false)', NULL),
(315, 'Disk Backups', NULL),
(316, 'Allow users to create and save backups of their home directories to disk. (true/false)', NULL),
(317, 'Delete client backups after allotted time has elapsed to help save diskspace (true/false)', NULL),
(318, 'Time in days backups are safe from being deleted. After days have elapsed, older backups will be deleted on Daemon Day Run', NULL),
(319, 'This module enables you to configure DNS settings for the DNS Manager', NULL),
(320, 'Configure your DNS Settings', NULL),
(321, 'General', NULL),
(322, 'Tools', NULL),
(323, 'Services', NULL),
(324, 'Logs', NULL),
(325, 'Allow Custom IP', NULL),
(326, 'Allow users to change IP settings in A records. If set to false, IP is locked to server IP setting in ZPanel Config', NULL),
(327, 'Allow Zone Transfers', NULL),
(328, 'Setting to restrict zone transfers in setting: allow-transfer {}; Default = all', NULL),
(329, 'Allowed Record Types', NULL),
(330, 'Types of records allowed seperated by a space. Default = A AAAA CNAME MX TXT SRV SPF NS', NULL),
(331, 'Bind Log', NULL),
(332, 'Path and name of the Bind Log', NULL),
(333, 'BIND Service Name', NULL),
(334, 'Name of the BIND service', NULL),
(335, 'Named CheckConfig', NULL),
(336, 'Path to named-checkconf bind utility.', NULL),
(337, 'Named CheckZone', NULL),
(338, 'Path to named-checkzone bind utility.', NULL),
(339, 'Named CompileZone', NULL),
(340, '	Path to named-compilezone bind utility.', NULL),
(341, 'Named Config', NULL),
(342, 'Named configuration file', NULL),
(343, 'Named Directory', NULL),
(344, 'Path to the directory where named.conf is stored', NULL),
(345, 'Path to BIND Root', NULL),
(346, 'Path to the root directory where BIND is installed.', NULL),
(347, 'SOA Expire TTL', NULL),
(348, 'Global expire TTL. Default = 604800 (1 week)', NULL),
(349, 'SOA Minimum TTL', NULL),
(350, 'Global minimum TTL. Default = 86400 (1 day)', NULL),
(351, 'SOA Refesh TTL', NULL),
(352, 'Global refresh TTL.  Default = 21600 (6 hours)', NULL),
(353, 'SOA Retry TTL', NULL),
(354, 'Global retry TTL. Default = 3600 (1 hour)', NULL),
(355, 'Zone Directory', NULL),
(356, 'Path to where DNS zone files are stored', NULL),
(357, 'Reset all Records to Default', NULL),
(358, 'GO', NULL),
(359, 'Reset Records to Default on Single Domain', NULL),
(360, 'Select Domain', NULL),
(361, 'Add Default Records to Missing Domains', NULL),
(362, 'Delete Record Type from ALL Records', NULL),
(364, 'Purge Deleted Zone Records From Database', NULL),
(365, 'Delete ALL Zone Records', NULL),
(366, 'Force Records Update on Next Daemon Run', NULL),
(367, 'Start Service', NULL),
(368, 'Stop Service', NULL),
(369, 'Reload BIND', NULL),
(370, 'Service Port Status', NULL),
(371, 'RUNNING', NULL),
(372, 'Log file is not Readable', NULL),
(373, 'Set Permissions', NULL),
(374, 'Clear errors', NULL),
(375, 'Clear', NULL),
(376, 'Clear warnings', NULL),
(377, 'Clear logs', NULL),
(378, 'View Errors', NULL),
(379, 'View', NULL),
(380, 'View warnings', NULL),
(381, 'View logs', NULL),
(382, 'STOPPED', NULL),
(383, 'No permission to write to log file.', NULL),
(384, 'Path to named-compilezone bind utility.', NULL),
(385, 'This module enables you to configure FTP settings for your hosting accounts.', NULL),
(386, 'Configure your FTP Settings', NULL),
(387, 'FTP Config File', NULL),
(388, 'The path to the configuration file if applicable.', NULL),
(389, 'FTP Database', NULL),
(390, 'The name of the ftp server database', NULL),
(391, 'FTP PHP', NULL),
(392, 'Name of PHP to include when adding FTP data.', NULL),
(393, 'FTP Service Name', NULL),
(394, 'The name of the FTP service', NULL),
(395, 'FTP Service Root', NULL),
(396, 'The path to the service executable if applicable.', NULL),
(397, 'This module enables you to configure your mail options', NULL),
(398, 'Configure your Mail Settings', NULL),
(399, 'Mailserver Database', NULL),
(400, 'The name of the mail server database', NULL),
(401, 'Mailserver PHP', NULL),
(402, 'Name of PHP to include when adding mailbox data.', NULL),
(403, 'Max Mailbox Size', NULL),
(404, 'Maximum size in megabytes allowed for mailboxes. Default = 200', NULL),
(405, 'Remove Orphans', NULL),
(406, 'When domains are deleted, also delete all mailboxes for that domain when the daemon runs. (true/false)', NULL),
(407, 'View full PHP configuration', NULL),
(408, 'Select the client you wish to shadow', NULL),
(409, 'Package', NULL),
(410, 'Group', NULL),
(411, 'Current Disk', NULL),
(412, 'Current Bandwidth', NULL),
(413, 'Shadow', NULL),
(414, 'You have no Clients at this time.', NULL),
(415, 'Hi {{fullname}},\n\nWe are pleased to inform you that your new hosting account is now active!\n\nYou can access your web hosting control panel using this link:\n{{controlpanelurl}}\n\nYour username and password is as follows:\nUsername: {{username}}\nPassword: {{password}}\n\nMany thanks,\nThe management', NULL),
(416, 'Please confirm that you want to delete this client.', NULL),
(417, 'WARNING! This will remove all files and services belonging to this client!', NULL),
(418, 'Move clients and packages (if any exist) this user has to', NULL),
(419, 'Edit existing client', NULL),
(420, 'Reset password', NULL),
(421, 'Save', NULL),
(422, 'Current Clients', NULL),
(423, 'Clients', NULL),
(424, 'You have no client accounts at this time. Create a client using the form below.', NULL),
(425, 'You must first create a Package with the Package Manager module before you can create a client.', NULL),
(426, 'Disabled Clients', NULL),
(427, 'Create new client account', NULL),
(428, 'Password', NULL),
(429, 'Generate Password', NULL),
(430, 'Send welcome email', NULL),
(431, 'Email subject', NULL),
(432, 'Your ZPanel Account details', NULL),
(433, 'Email body', NULL),
(434, 'Administrators', NULL),
(435, 'Resellers', NULL),
(436, 'Users', NULL),
(437, 'Administration', NULL),
(438, 'Demo', NULL),
(439, 'Changes to your client(s) have been saved successfully!', NULL),
(440, 'Another user account is already using this email address.', NULL),
(441, 'Hi {{fullname}},\r\rWe are pleased to inform you that your new hosting account is now active!\r\rYou can access your web hosting control panel using this link:\r{{controlpanelurl}}\r\rYour username and password is as follows:\rUsername: {{username}}\rPassword: {{password}}\r\rMany thanks,\rThe management', NULL),
(442, 'Configure your ZPanel Settings', NULL),
(443, 'ZPanel Daemon', NULL),
(444, 'Next Daemon Run', NULL),
(445, 'Never', NULL),
(446, 'Last Daemon Run', NULL),
(447, 'Last Day Daemon Run', NULL),
(448, 'Last Week Daemon Run', NULL),
(449, 'Last Month Daemon Run', NULL),
(450, 'Queue a full daemon run (reset)', NULL),
(451, 'Run Daemon Now', NULL),
(452, 'Date Format', NULL),
(453, 'Set the date format used by modules.', NULL),
(454, 'Debug logging mode', NULL),
(455, 'The default mode to log all errors in.', NULL),
(456, 'From Address', NULL),
(457, 'The email address to appear in the From field of emails sent by ZPanel.', NULL),
(458, 'From Name', NULL),
(459, 'The name to appear in the From field of emails sent by ZPanel.', NULL),
(460, 'Icons per Row', NULL),
(461, 'Set the number of icons to display before beginning a new line.', NULL),
(462, 'Log Directory', NULL),
(463, 'Root path to directory log folders', NULL),
(464, 'Mail method', NULL),
(465, 'Method to use when sending emails out. (mail = PHP Mail())', NULL),
(466, 'Min Password Length', NULL),
(467, 'Minimum length required for new passwords', NULL),
(468, 'Remote Login Forms', NULL),
(469, 'Disables CSFR protection on the login form to enable remote login forms.', NULL),
(470, 'Root Drive', NULL),
(471, 'The root drive where ZPanel is installed.', NULL),
(472, 'Server IP Address', NULL),
(473, 'If set this will use this manually entered server IP address which is the prefered method for use behind a firewall.', NULL),
(474, 'Service Check Timeout', NULL),
(475, 'SMTP Auth method', NULL),
(476, 'If specified will attempt to use encryption to connect to the server, if \\''false\\'' this is disabled. Available options: false, ssl, tls', NULL),
(477, 'SMTP Pass', NULL),
(478, 'Password for authentication on the SMTP server.', NULL),
(479, 'SMTP Port', NULL),
(480, 'The port address of the SMTP server (usually 25)', NULL),
(481, 'SMTP Server', NULL),
(482, 'The address of the SMTP server.', NULL),
(483, 'SMTP User', NULL),
(484, 'Username for authentication on the SMTP server.', NULL),
(485, 'Temp Directory', NULL),
(486, 'Global temp directory.', NULL),
(487, 'Use AUTH', NULL),
(488, 'SMTP requires authentication. (true/false)', NULL),
(489, 'Use SMTP', NULL),
(490, 'Use SMTP server to send emails from. (true/false)', NULL),
(491, 'ZIP Exe', NULL),
(492, 'Path to the ZIP Executable', NULL),
(493, 'ZPanel Debug Mode', NULL),
(494, 'Whether or not to show PHP debug errors,warnings and notices', NULL),
(495, 'ZPanel Log file', NULL),
(496, 'If logging is set to \\''file\\'' mode this is the path to the log file that is to be used by ZPanel.', NULL),
(497, 'ZPanel root path', NULL),
(498, 'Zpanel Web Root', NULL),
(499, 'zsudo path', NULL),
(500, 'Path to the zsudo binary used by Apache to run system commands.', NULL),
(501, 'Latest Zpanel News', NULL),
(502, 'Click to view this news item', NULL),
(503, 'Backup your hosting account files', NULL),
(504, 'Launch', NULL),
(505, 'Backups your data whilst you wait and then prompt\\\\\\''s you to download the backup archive.', NULL),
(506, 'There are no files in your public folder to back up.', NULL),
(507, 'Backup Archives', NULL),
(508, 'Archived files will automatically be deleted when they are older than', NULL),
(509, 'days', NULL),
(510, 'Refresh archive list', NULL),
(511, 'Location', NULL),
(512, 'File Size', NULL),
(513, 'DELETE BACKUP', NULL),
(514, 'You have no backups at this time.  If you have just created a backup then try refreshing', NULL),
(515, 'Backup to disk is not enabled on the server. You may still download backups directly to your computer through your browser.', NULL),
(516, 'Backups your data whilst you wait and then prompt\\''s you to download the backup archive.', NULL),
(517, 'Current Cron Tasks', NULL),
(518, 'You currently do not have any tasks setup.', NULL),
(519, 'Script', NULL),
(520, 'example', NULL),
(521, 'Comment', NULL),
(522, 'Executed', NULL),
(523, 'Every 1 minute', NULL),
(524, 'Every 5 minutes', NULL),
(525, 'Every 10 minutes', NULL),
(526, 'Every 30 minutes', NULL),
(527, 'Every 1 hour', NULL),
(528, 'Every 2 hours', NULL),
(529, 'Every 8 hours', NULL),
(530, 'Every 12 hours', NULL),
(531, 'Every 1 day', NULL),
(532, 'Every week', NULL),
(533, 'Every month', NULL),
(534, 'Create', NULL),
(535, 'Delete FAQ Item', NULL),
(536, 'Please confirm that you want to delete this FAQ item.', NULL),
(537, 'Frequently Asked Questions', NULL),
(538, 'Add FAQ Item', NULL),
(539, 'Since you are logged in as an Administrator or a Reseller, you can add and remove FAQ items.  Administrators can see and remove all FAQ items, but Resellers can only remove FAQ items they have created.  Your clients will be able to see any FAQ items you create, plus any FAQ items created by an Administrator. Your clients will not be able to add or remove any FAQ items.', NULL),
(540, 'Add', NULL),
(541, 'Checking status of services...', NULL),
(542, 'Uptime', NULL),
(543, 'Daemon Status', NULL),
(544, 'To view Webalizer stats for a particular domain or subdomain use the drop-down menu to select the domain or sub-domain you want to view. Stats may take up to 24 hours before they are generated.', NULL),
(545, 'Display', NULL),
(546, 'You currently do not have any domains configured.', NULL),
(547, 'Create a new domain', NULL),
(548, 'Delete database', NULL),
(549, 'Please confirm that you want to delete this database.', NULL),
(550, 'Current MySQL&reg Databases', NULL),
(551, 'Database name', NULL),
(552, 'Size', NULL),
(553, 'You have no databases at this time. Create a database using the form below.', NULL),
(554, 'Create a new MySQL&reg database', NULL),
(555, 'You have reached your MySQL database limit!', NULL),
(556, 'Delete User', NULL),
(557, 'Please confirm that you want to delete this MySQL user.', NULL),
(558, 'Databases for user', NULL),
(559, 'Remove from user', NULL),
(560, 'Remove database', NULL),
(561, 'This user currently has no databases. Assign a database using the form below.', NULL),
(562, 'Add Database', NULL),
(563, 'Save Password', NULL),
(564, 'Current MySQL&reg Users', NULL),
(565, 'User name', NULL),
(566, 'Access', NULL),
(567, 'Databases', NULL),
(568, 'Edit', NULL),
(569, 'You have no MySQL&reg users at this time. Create a user using the form below.', NULL),
(570, 'Create a new MySQL&reg User', NULL),
(571, 'Map Database', NULL),
(572, 'Remote Access', NULL),
(573, 'Allow from any IP', NULL),
(574, 'Only from single IP', NULL),
(575, 'Launch phpMyAdmin', NULL),
(576, 'Manage Domains', NULL),
(577, 'Choose fom the list of domains below', NULL),
(578, 'Create Default DNS Records', NULL),
(579, 'No records were found for this domain.  Click the button below to set up your domain records for the first time', NULL),
(580, 'Create Records', NULL),
(581, 'Your DNS zone has been loaded without errors.', NULL),
(582, 'DNS records for', NULL),
(583, 'Undo Changes', NULL),
(584, 'Domain List', NULL),
(585, 'The A record contains an IPv4 address. It\\''s target is an IPv4 address, e.g. \\''192.168.1.1\\''.', NULL),
(586, 'The AAAA record contains an IPv6 address. It\\''s target is an IPv6 address, e.g. \\''2607:fe90:2::1\\''.', NULL),
(587, 'The CNAME record specifies the canonical name of a record. It\\''s target is a fully qualified domain name, e.g.\n\\''webserver-01.example.com\\''.', NULL),
(588, 'The MX record specifies a mail exchanger host for a domain. Each mail exchanger has a priority or preference that is a numeric value between 0 and 65535.  It\\''s target is a fully qualified domain name, e.g. \\''mail.example.com\\''.', NULL),
(589, 'The TXT field can be used to attach textual data to a domain.', NULL),
(590, 'SRV records can be used to encode the location and port of services on a domain name.  It\\''s target is a fully qualified domain name, e.g. \\''host.example.com\\''.', NULL),
(591, 'SPF records is used to store Sender Policy Framework details.  It\\''s target is a text string, e.g.<br>\\''v=spf1 a:192.168.1.1 include:example.com mx ptr -all\\'' (Click <a href=\\"http://www.microsoft.com/mscorp/safety/content/technologies/senderid/wizard/\\" target=\\"_blank\\">HERE</a> for the Microsoft SPF Wizard.)', NULL),
(592, 'Nameserver record. Specifies nameservers for a domain. It\\''s target is a fully qualified domain name, e.g.  \\''ns1.example.com\\''.  The records should match what the domain name has registered with the internet root servers.', NULL),
(593, 'Host Name', NULL),
(594, 'Target', NULL),
(595, 'Actions', NULL),
(596, 'Add New Record', NULL),
(597, 'TTL', NULL),
(598, 'Priority', NULL),
(599, 'Weight', NULL),
(600, 'Port', NULL),
(601, 'Please note that changes to your zone records can take up to 24 hours before they become \\''live\\''.', NULL),
(602, 'Output of DNS zone checker:', NULL),
(603, 'Current Sub-domains', NULL),
(604, 'Sub-domain', NULL),
(605, 'You currently do not have any Sub-domains configured. Create a Sub-domain using the form below.', NULL),
(606, 'You have reached your Sub-domains limit!', NULL),
(607, 'Delete Alias', NULL),
(608, 'Please confirm that you want to delete this Alias.', NULL),
(609, 'Current Aliases', NULL),
(610, 'Address', NULL),
(611, 'Destination', NULL),
(612, 'You currently do not have any aliases configured on this server.', NULL),
(613, 'Create a new Alias', NULL),
(614, 'Alias Address', NULL),
(615, 'Select a mailbox', NULL),
(616, 'Sorry, you have reached your mailbox forward quota limit!', NULL),
(617, 'Delete Distribution List', NULL),
(618, 'Please confirm that you want to delete this distribution list.', NULL),
(619, 'Edit Distribution List', NULL),
(620, 'Add New Address', NULL),
(621, 'Add Mailbox', NULL),
(622, 'Current Distribution Lists', NULL),
(623, 'You currently do not have any distribution lists setup.', NULL),
(624, 'Create a new Distribution List', NULL),
(625, 'You have reached your Distribution List quota limit!', NULL),
(626, 'Delete Forwarder', NULL),
(627, 'Please confirm that you want to delete this forwarder.', NULL),
(628, 'Current Forwarders', NULL),
(629, 'Keep Message', NULL),
(630, 'Sorry there are currently no mailbox forwards configured!', NULL),
(631, 'Create a new forward', NULL),
(632, 'Keep original message', NULL),
(633, 'Delete Mailbox', NULL),
(634, 'Please confirm that you want to delete this mailbox.', NULL),
(635, 'Edit mailbox', NULL),
(636, 'Set Password', NULL),
(637, 'Current mailboxes', NULL),
(638, 'Date created', NULL),
(639, 'Sorry there are currently no mailboxes configured!', NULL),
(640, 'Create a new mailbox', NULL),
(641, 'Sorry, you have reached your mailbox quota limit!', NULL),
(642, 'This module enables you to set a notice banner that will appear when any of your clients access ZPanel. ', NULL),
(643, 'Client message', NULL),
(644, 'Notice message', NULL),
(645, 'This module enables you to manage user groups for your client, User groups enable you to control what modules your users can see and access. ', NULL),
(646, 'Delete user group', NULL),
(647, 'Please confirm and choose a group to move any existing clients to before the selected group is deleted.', NULL),
(648, 'Please confirm that you want to delete this group.', NULL),
(649, 'Move current group members to', NULL),
(650, 'Default user groups', NULL),
(651, 'Description ', NULL),
(652, 'Current user groups', NULL),
(653, 'There are currently no custom user groups configured!', NULL),
(654, 'Create new user group', NULL),
(655, 'Group name', NULL),
(656, 'Edit user group', NULL),
(657, 'The main administration group, this group allows access to all areas of ZPanel.', NULL),
(658, 'Resellers have the ability to manage, create and maintain user accounts within ZPanel.', NULL),
(659, 'Users have basic access to ZPanel.', NULL),
(660, 'Delete package', NULL),
(661, 'Please confirm and choose a package to move any existing clients to before the selected package is deleted.', NULL),
(662, 'Package to delete', NULL),
(663, 'Move current package members to', NULL),
(664, 'Edit package', NULL),
(665, 'Enable PHP', NULL),
(666, 'Enable CGI', NULL),
(667, 'No. Domains', NULL),
(668, 'No. Sub-domains', NULL),
(669, 'No. Parked domains', NULL),
(670, 'No. Mailboxes', NULL),
(671, 'No. Forwarders', NULL),
(672, 'No. Dist Lists', NULL),
(673, 'No. FTP accounts', NULL),
(674, 'No. MySQL databases', NULL),
(675, 'Disk space quota', NULL),
(676, 'Monthly bandwidth quota', NULL),
(677, 'Created', NULL),
(678, 'No. of clients', NULL),
(679, 'You have no packages at this time. Create a package using the form below.', NULL),
(680, 'Create a new package', NULL),
(681, 'As a reseller you can configure theme settings for your clients. If the theme you select has multiple CSS versions you will be prompted for which theme \\\\\\''version\\\\\\'' you would like to use after you \\\\\\''save\\\\\\'' the changes.', NULL),
(682, 'Select a theme', NULL),
(683, 'Theme name', NULL),
(684, 'Theme variation', NULL),
(685, 'Update', NULL),
(686, 'As a reseller you can configure theme settings for your clients. If the theme you select has multiple CSS versions you will be prompted for which theme \\''version\\'' you would like to use after you \\''save\\'' the changes.', NULL),
(687, 'Delete FTP account', NULL),
(688, 'Please confirm that you want to delete this FTP account.', NULL),
(689, 'Reset FTP Password for user', NULL),
(690, 'Current FTP accounts', NULL),
(691, 'Account name', NULL),
(692, 'Permission', NULL),
(693, 'You do not have any FTP Accounts setup. Create an FTP account using the form below.', NULL),
(694, 'Create a new FTP Account', NULL),
(695, 'Access type', NULL),
(696, 'Read-only', NULL),
(697, 'Write-only', NULL),
(698, 'Full access', NULL),
(699, 'Set Master home directory', NULL),
(700, 'Use Domain directory', NULL),
(701, 'You have reached your FTP account limit!', NULL),
(702, 'Repo Browser', NULL),
(703, 'Changes to your module options have been saved successfully!', NULL),
(704, 'Repo Browser allows you to manage your custom module repositories.', NULL),
(705, 'RepoZP(br)Browser', NULL),
(706, 'Repo<br/>Browser', NULL),
(707, 'ZPPY Patch Needed', NULL),
(708, 'For ZPanel versions BELOW 10.1.0 Repo Browser can not uninstall a module until your zppy file is patched. Your original file will be saved so you can revert back at a later time if you wish.', NULL),
(709, 'Press the PATCH button to install the modded zppy file and try uninstalling the module again.', NULL),
(710, 'Patch', NULL),
(711, 'Remove Repository', NULL),
(712, 'Please confirm that you want to remove this repository from your ZPanel server.', NULL),
(713, 'Remove', NULL),
(714, 'Debug ZPPY Client output', NULL),
(715, 'Validation failed for repository', NULL),
(716, 'The repository you are trying to add does not appear to be a valid ZPanel repository, or the repository may be offline. You can choose to ignore this and add the repository anyway. ', NULL),
(717, 'Add Anyway', NULL),
(718, 'Remove Module', NULL),
(719, 'Please confirm that you want to remove this module from your ZPanel server.', NULL),
(720, 'Install Module', NULL),
(721, 'Please confirm that you want to install this module on your ZPanel server.', NULL),
(722, 'Install', NULL),
(723, 'Update Module Database Version Number', NULL),
(724, 'You are about to manually update the version number that ZPanel is reporting with the version number that is actually reported by your module file.', NULL),
(725, 'ZPanel database version', NULL),
(726, 'Will be set to version', NULL),
(727, 'Update Module', NULL),
(728, 'Available Modules', NULL),
(729, 'total modules', NULL),
(730, 'No modules available for download.', NULL),
(731, 'Repositories', NULL),
(732, 'You currently do not have any repositories configured.', NULL),
(733, 'ZPPY Client', NULL),
(734, 'Manually run a ZPPY client command.', NULL),
(735, 'Run Command', NULL),
(736, 'Module Developer Notes', NULL),
(737, 'Command Given', NULL),
(738, 'Command Response', NULL),
(739, 'Repository and ZPPY cache has been updated successfully.', NULL),
(740, 'ZPPY COMMAND OUTPUT', NULL),
(741, 'Apache Status', NULL),
(742, 'Apache Status makes the Apache server status readable, sortable and searchable to identify and troubleshoot Apache performance issues or individual websites.', NULL),
(743, 'ApacheZP(br)Status', NULL),
(744, 'Apache<br/>Status', NULL),
(745, 'ZPanel localhost server status', NULL),
(746, 'Extended Status Needs to be ON', NULL),
(747, 'Mod_Status Needs to be enabled', NULL),
(748, 'Here you can manage your lanuages, install new lanuages from ZXTS, and add support for symbolic lanuages.', NULL),
(749, 'Apply Symbolic Language Support', NULL),
(750, 'Explanation', NULL),
(751, 'In ZPanelX there are a few tasks that need to be undertook for ZPanelX to support symbolic lanuages. The below will handle all the task for you.', NULL),
(752, 'Have Problems?', NULL),
(753, 'In an event that you update ZPanelX and the symbolic lanuages stop showing correctly just click Apply Symbolic Support button below and the languages should start to show properly again.', NULL),
(754, 'If the lanaguages still do not show up properly then clikc the install button on the lanuage again', NULL),
(755, 'If you have installed an symbolic lanuage before appling the patch please first apply the patch by click Apply Symbolic Support button and then click the install button on the lanuage again.', NULL),
(756, 'Symbolic Language Support Applied?', NULL),
(757, 'Symbolic Language Support is <strong>NOT</strong> currently applied', NULL),
(758, 'Apply Symbolic Language Support: ', NULL),
(759, 'Apply Symbolic Support', NULL),
(760, 'Remove Symbolic Language Support: ', NULL),
(761, 'Remove Symbolic Support', NULL),
(762, 'Install or Update', NULL),
(763, 'Un-Install Lanuage', NULL),
(764, 'Currently Not Installed!', NULL),
(765, 'Avalible Lanuages', NULL),
(766, 'PLEASE Read:', NULL),
(767, 'The ZXTS will not have all translation for each language this is becuase it only support core module and not custome/additional ones though it will have the bulk of the translation. So if you install a lanuage that is 100% complete that is relative to core module so may not be 100% complete on your server.', NULL),
(768, 'Percentage Complete', NULL),
(769, 'Currently Installed', NULL),
(770, 'Lanuges', NULL),
(771, 'AjaXplorer', NULL),
(772, 'AjaXplorer v4.2.3 is a powerful file management system created under the Affero GPL License by Charles de Jeu', NULL),
(773, 'Launch AjaXplorer', NULL),
(774, 'APC PHP-Cache', NULL),
(775, 'The Alternative PHP Cache (APC) is a free and open opcode cache for PHP. Its goal is to provide a free, open, and robust framework for caching and optimizing PHP intermediate code.', NULL),
(776, 'Read Me Guide', NULL),
(777, 'Open New Window', NULL),
(778, 'View PHP Cache', NULL),
(779, 'APC Administration Authentication', NULL),
(780, 'On/Off', NULL),
(781, 'APC Shared Memory Segment', NULL),
(782, 'Increase/Decrease APC cache memory limit', NULL),
(783, 'Save Setting', NULL),
(784, 'ZPanelX Module by: JD1pinoy', NULL),
(785, 'AutoIP Updater', NULL),
(786, 'Configure AutoIP Settings', NULL),
(787, 'Current IP', NULL),
(788, 'Detected IP', NULL),
(789, 'Last Update', NULL),
(790, 'Command', NULL),
(791, 'Exec Script', NULL),
(792, 'Email Alert', NULL),
(793, 'Information', NULL),
(794, 'AW Stats', NULL),
(795, 'To view AW stats for a particular domain or subdomain use the drop-down menu to select the domain or sub-domain you want to view. Stats may take up to 24 hours before they are generated.', NULL),
(796, 'Chive', NULL),
(797, 'Chive is a free, open source, web-based database management tool.', NULL),
(798, 'Launch Chive', NULL),
(799, 'DNS Checker', NULL),
(800, 'Reset Query', NULL),
(801, 'Domain Forwarder', NULL),
(802, 'This module enables you to setup and manage domain forwards.', NULL),
(803, 'Delete Forward Domain', NULL),
(804, 'Please confirm that you want to delete this forwarded domain.', NULL),
(805, 'Edit Domain Forward', NULL),
(807, 'Forward Type', NULL),
(808, 'Forward Domain', NULL),
(809, 'To', NULL),
(810, 'Forward www. address for this domain as well', NULL),
(811, 'Forwarded Domains', NULL),
(812, 'Forwarded Domain', NULL),
(813, 'Target Domain', NULL),
(814, 'Include WWW', NULL),
(815, 'There are currently no forwarded domains configured, configure a domain forward below.', NULL),
(816, 'Redirect Protocol', NULL),
(817, 'You have reached the maximum domain forwarders limit', NULL),
(818, 'Unlimited', NULL),
(819, 'Changes to your packages have been saved successfully!', NULL),
(820, 'Automatically update your control panel IP address.', NULL),
(821, 'AutoIPZP(br)Updater', NULL),
(822, 'APCZP(br)PHP-Cache', NULL),
(823, 'AWZP(br)Stats', NULL),
(824, 'DNS Checker Investigate domains and IP addresses.', NULL),
(825, 'DNSZP(br)Checker', NULL),
(826, 'DomainZP(br)Forwarder', NULL),
(827, 'AutoIP<br/>Updater', NULL),
(828, 'APC<br/>PHP-Cache', NULL),
(829, 'AW<br/>Stats', NULL),
(830, 'DNS<br/>Checker', NULL),
(831, 'Domain<br/>Forwarder', NULL);

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
  `vh_custom_port_in` int(6) DEFAULT NULL,
  `vh_custom_ip_vc` varchar(45) DEFAULT NULL,
  `vh_portforward_in` int(1) DEFAULT NULL,
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
