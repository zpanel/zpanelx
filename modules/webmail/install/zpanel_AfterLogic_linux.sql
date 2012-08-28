-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 26, 2012 at 03:35 AM
-- Server version: 5.1.61
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `zpanel_AfterLogic`
--

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_appointments`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_appointments` (
  `id_appointment` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `access_type` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `hash` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_appointment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_awm_fnbl_runs`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_awm_fnbl_runs` (
  `id_run` int(11) NOT NULL AUTO_INCREMENT,
  `run_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_run`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_calendars`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_calendars` (
  `calendar_id` int(11) NOT NULL AUTO_INCREMENT,
  `calendar_str_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `calendar_name` varchar(100) NOT NULL DEFAULT '',
  `calendar_description` text,
  `calendar_color` int(11) NOT NULL DEFAULT '0',
  `calendar_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`calendar_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_cron_runs`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_cron_runs` (
  `id_run` bigint(20) NOT NULL AUTO_INCREMENT,
  `run_date` datetime DEFAULT NULL,
  `latest_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_run`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_eventrepeats`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_eventrepeats` (
  `id_repeat` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) DEFAULT NULL,
  `repeat_period` tinyint(1) NOT NULL DEFAULT '0',
  `repeat_order` tinyint(1) NOT NULL DEFAULT '1',
  `repeat_num` int(11) NOT NULL DEFAULT '0',
  `repeat_until` datetime DEFAULT NULL,
  `sun` tinyint(1) NOT NULL DEFAULT '0',
  `mon` tinyint(1) NOT NULL DEFAULT '0',
  `tue` tinyint(1) NOT NULL DEFAULT '0',
  `wed` tinyint(1) NOT NULL DEFAULT '0',
  `thu` tinyint(1) NOT NULL DEFAULT '0',
  `fri` tinyint(1) NOT NULL DEFAULT '0',
  `sat` tinyint(1) NOT NULL DEFAULT '0',
  `week_number` tinyint(1) DEFAULT NULL,
  `repeat_end` tinyint(1) NOT NULL DEFAULT '0',
  `excluded` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_repeat`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_events`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_str_id` varchar(255) DEFAULT NULL,
  `fnbl_pim_id` bigint(20) DEFAULT NULL,
  `calendar_id` int(11) DEFAULT NULL,
  `event_timefrom` datetime DEFAULT NULL,
  `event_timetill` datetime DEFAULT NULL,
  `event_allday` tinyint(1) NOT NULL DEFAULT '0',
  `event_name` varchar(100) NOT NULL DEFAULT '',
  `event_text` text,
  `event_priority` tinyint(4) DEFAULT NULL,
  `event_repeats` tinyint(1) NOT NULL DEFAULT '0',
  `event_last_modified` datetime DEFAULT NULL,
  `event_owner_email` varchar(255) NOT NULL DEFAULT '',
  `event_appointment_access` tinyint(4) NOT NULL DEFAULT '0',
  `event_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_exclusions`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_exclusions` (
  `id_exclusion` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) DEFAULT NULL,
  `id_calendar` int(11) DEFAULT NULL,
  `id_repeat` int(11) DEFAULT NULL,
  `id_recurrence_date` datetime DEFAULT NULL,
  `event_timefrom` datetime DEFAULT NULL,
  `event_timetill` datetime DEFAULT NULL,
  `event_name` varchar(100) DEFAULT NULL,
  `event_text` text,
  `event_allday` tinyint(1) NOT NULL DEFAULT '0',
  `event_last_modified` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_exclusion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_publications`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_publications` (
  `id_publication` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_calendar` int(11) DEFAULT NULL,
  `str_md5` varchar(32) DEFAULT NULL,
  `int_access_level` tinyint(4) NOT NULL DEFAULT '1',
  `access_type` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_publication`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_reminders`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_reminders` (
  `id_reminder` int(11) NOT NULL AUTO_INCREMENT,
  `id_event` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `notice_type` tinyint(4) NOT NULL DEFAULT '0',
  `remind_offset` int(11) NOT NULL DEFAULT '0',
  `sent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_reminder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_sharing`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_sharing` (
  `id_share` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_calendar` int(11) DEFAULT NULL,
  `id_to_user` int(11) DEFAULT NULL,
  `str_to_email` varchar(255) NOT NULL DEFAULT '',
  `int_access_level` tinyint(4) NOT NULL DEFAULT '2',
  `calendar_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_share`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicacal_users_data`
--

CREATE TABLE IF NOT EXISTS `AfterLogicacal_users_data` (
  `settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `showweekends` tinyint(1) NOT NULL DEFAULT '0',
  `workdaystarts` tinyint(4) NOT NULL DEFAULT '9',
  `workdayends` tinyint(4) NOT NULL DEFAULT '17',
  `showworkday` tinyint(1) NOT NULL DEFAULT '0',
  `weekstartson` tinyint(4) NOT NULL DEFAULT '0',
  `defaulttab` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`settings_id`),
  KEY `AFTERLOGICACAL_USERS_DATA_USER_ID_INDEX` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_addressbooks`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_addressbooks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `principaluri` varchar(255) DEFAULT NULL,
  `displayname` varchar(255) DEFAULT NULL,
  `uri` varchar(100) DEFAULT NULL,
  `description` text,
  `ctag` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_cache`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(255) DEFAULT NULL,
  `calendaruri` varchar(255) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `starttime` int(11) DEFAULT NULL,
  `eventid` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_calendarobjects`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_calendarobjects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `calendardata` text,
  `uri` varchar(255) DEFAULT NULL,
  `calendarid` int(11) unsigned NOT NULL,
  `lastmodified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_calendars`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_calendars` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `principaluri` varchar(100) DEFAULT NULL,
  `displayname` varchar(100) DEFAULT NULL,
  `uri` varchar(100) DEFAULT NULL,
  `ctag` int(11) unsigned NOT NULL DEFAULT '0',
  `description` text,
  `calendarorder` int(11) unsigned NOT NULL DEFAULT '0',
  `calendarcolor` varchar(10) DEFAULT NULL,
  `timezone` text,
  `components` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_cards`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_cards` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `addressbookid` int(11) unsigned NOT NULL,
  `carddata` text,
  `uri` varchar(100) DEFAULT NULL,
  `lastmodified` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_delegates`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_delegates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `calendarid` int(11) unsigned NOT NULL,
  `principalid` int(11) unsigned NOT NULL,
  `mode` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_groupmembers`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_groupmembers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `principal_id` int(11) unsigned NOT NULL,
  `member_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `AFTERLOGICADAV_GROUPMEMBERS_MEMBER_ID_PRINCIPAL_ID_INDEX` (`principal_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_locks`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_locks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner` varchar(100) DEFAULT NULL,
  `timeout` int(11) unsigned DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `scope` tinyint(4) DEFAULT NULL,
  `depth` tinyint(4) DEFAULT NULL,
  `uri` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicadav_principals`
--

CREATE TABLE IF NOT EXISTS `AfterLogicadav_principals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(100) NOT NULL,
  `email` varchar(80) DEFAULT NULL,
  `vcardurl` varchar(80) DEFAULT NULL,
  `displayname` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `AFTERLOGICADAV_PRINCIPALS_URI_INDEX` (`uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_accounts`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_accounts` (
  `id_acct` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_domain` int(11) NOT NULL DEFAULT '0',
  `def_acct` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `quota` int(11) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL DEFAULT '',
  `friendly_nm` varchar(255) DEFAULT NULL,
  `mail_protocol` tinyint(4) NOT NULL DEFAULT '1',
  `mail_inc_host` varchar(255) DEFAULT NULL,
  `mail_inc_port` int(11) NOT NULL DEFAULT '143',
  `mail_inc_login` varchar(255) DEFAULT NULL,
  `mail_inc_pass` varchar(255) DEFAULT NULL,
  `mail_inc_ssl` tinyint(1) NOT NULL DEFAULT '0',
  `mail_out_host` varchar(255) DEFAULT NULL,
  `mail_out_port` int(11) NOT NULL DEFAULT '25',
  `mail_out_login` varchar(255) DEFAULT NULL,
  `mail_out_pass` varchar(255) DEFAULT NULL,
  `mail_out_auth` tinyint(4) NOT NULL DEFAULT '0',
  `mail_out_ssl` tinyint(1) NOT NULL DEFAULT '0',
  `def_order` tinyint(4) NOT NULL DEFAULT '0',
  `getmail_at_login` tinyint(1) NOT NULL DEFAULT '0',
  `mail_mode` tinyint(4) NOT NULL DEFAULT '1',
  `mails_on_server_days` smallint(6) NOT NULL DEFAULT '7',
  `signature` text,
  `signature_type` tinyint(4) NOT NULL DEFAULT '1',
  `signature_opt` tinyint(4) NOT NULL DEFAULT '0',
  `delimiter` varchar(1) NOT NULL DEFAULT '/',
  `mailbox_size` bigint(20) NOT NULL DEFAULT '0',
  `mailing_list` tinyint(1) NOT NULL DEFAULT '0',
  `namespace` varchar(255) NOT NULL DEFAULT '',
  `custom_fields` text,
  PRIMARY KEY (`id_acct`),
  KEY `AFTERLOGICAWM_ACCOUNTS_ID_USER_INDEX` (`id_user`),
  KEY `AFTERLOGICAWM_ACCOUNTS_ID_ACCT_ID_USER_INDEX` (`id_acct`,`id_user`),
  KEY `AFTERLOGICAWM_ACCOUNTS_EMAIL_INDEX` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `AfterLogicawm_accounts`
--

INSERT INTO `AfterLogicawm_accounts` (`id_acct`, `id_user`, `id_domain`, `def_acct`, `deleted`, `quota`, `email`, `friendly_nm`, `mail_protocol`, `mail_inc_host`, `mail_inc_port`, `mail_inc_login`, `mail_inc_pass`, `mail_inc_ssl`, `mail_out_host`, `mail_out_port`, `mail_out_login`, `mail_out_pass`, `mail_out_auth`, `mail_out_ssl`, `def_order`, `getmail_at_login`, `mail_mode`, `mails_on_server_days`, `signature`, `signature_type`, `signature_opt`, `delimiter`, `mailbox_size`, `mailing_list`, `namespace`, `custom_fields`) VALUES
(1, 1, 0, 1, 0, 0, 'postmaster@ks392623.kimsufi.com', '', 1, '127.0.0.1', 143, 'postmaster@ks392623.kimsufi.com', '330303020a010a06', 0, '127.0.0.1', 25, '', '', 2, 0, 0, 1, 1, 7, '', 1, 0, '.', 0, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_addr_book`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_addr_book` (
  `id_addr` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `str_id` varchar(255) DEFAULT NULL,
  `fnbl_pim_id` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `view_email` varchar(255) NOT NULL DEFAULT '',
  `use_friendly_nm` tinyint(1) NOT NULL DEFAULT '1',
  `h_email` varchar(255) DEFAULT NULL,
  `h_street` varchar(255) DEFAULT NULL,
  `h_city` varchar(200) DEFAULT NULL,
  `h_state` varchar(200) DEFAULT NULL,
  `h_zip` varchar(10) DEFAULT NULL,
  `h_country` varchar(200) DEFAULT NULL,
  `h_phone` varchar(50) DEFAULT NULL,
  `h_fax` varchar(50) DEFAULT NULL,
  `h_mobile` varchar(50) DEFAULT NULL,
  `h_web` varchar(255) DEFAULT NULL,
  `b_email` varchar(255) DEFAULT NULL,
  `b_company` varchar(200) DEFAULT NULL,
  `b_street` varchar(255) DEFAULT NULL,
  `b_city` varchar(200) DEFAULT NULL,
  `b_state` varchar(200) DEFAULT NULL,
  `b_zip` varchar(10) DEFAULT NULL,
  `b_country` varchar(200) DEFAULT NULL,
  `b_job_title` varchar(100) DEFAULT NULL,
  `b_department` varchar(200) DEFAULT NULL,
  `b_office` varchar(200) DEFAULT NULL,
  `b_phone` varchar(50) DEFAULT NULL,
  `b_fax` varchar(50) DEFAULT NULL,
  `b_web` varchar(255) DEFAULT NULL,
  `other_email` varchar(255) DEFAULT NULL,
  `primary_email` tinyint(4) DEFAULT NULL,
  `birthday_day` tinyint(4) NOT NULL DEFAULT '0',
  `birthday_month` tinyint(4) NOT NULL DEFAULT '0',
  `birthday_year` smallint(6) NOT NULL DEFAULT '0',
  `id_addr_prev` bigint(20) DEFAULT NULL,
  `tmp` tinyint(1) NOT NULL DEFAULT '0',
  `use_frequency` int(11) NOT NULL DEFAULT '11',
  `auto_create` tinyint(1) NOT NULL DEFAULT '0',
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_addr`),
  KEY `AFTERLOGICAWM_ADDR_BOOK_ID_USER_INDEX` (`id_user`),
  KEY `AFTERLOGICAWM_ADDR_BOOK_DELETED_ID_USER_INDEX` (`id_user`,`deleted`),
  KEY `AFTERLOGICAWM_ADDR_BOOK_USE_FREQUENCY_INDEX` (`use_frequency`),
  KEY `AFTERLOGICAWM_ADDR_BOOK_VIEW_EMAIL_INDEX` (`view_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_addr_groups`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_addr_groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `group_nm` varchar(255) DEFAULT NULL,
  `group_str_id` varchar(100) DEFAULT NULL,
  `use_frequency` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `company` varchar(200) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(200) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `organization` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_group`),
  KEY `AFTERLOGICAWM_ADDR_GROUPS_ID_USER_INDEX` (`id_user`),
  KEY `AFTERLOGICAWM_ADDR_GROUPS_USE_FREQUENCY_INDEX` (`use_frequency`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_addr_groups_contacts`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_addr_groups_contacts` (
  `id_addr` bigint(20) NOT NULL DEFAULT '0',
  `id_group` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_columns`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_columns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_column` int(11) NOT NULL DEFAULT '0',
  `id_user` int(11) NOT NULL DEFAULT '0',
  `column_value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_COLUMNS_ID_USER_INDEX` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_domains`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_domains` (
  `id_domain` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `user_quota` int(11) NOT NULL DEFAULT '0',
  `override_settings` tinyint(1) NOT NULL DEFAULT '0',
  `mail_protocol` tinyint(4) NOT NULL DEFAULT '1',
  `mail_inc_host` varchar(255) DEFAULT NULL,
  `mail_inc_port` int(11) NOT NULL DEFAULT '143',
  `mail_inc_ssl` tinyint(1) NOT NULL DEFAULT '0',
  `mail_out_host` varchar(255) DEFAULT NULL,
  `mail_out_port` int(11) NOT NULL DEFAULT '25',
  `mail_out_auth` tinyint(4) NOT NULL DEFAULT '1',
  `mail_out_login` varchar(255) DEFAULT NULL,
  `mail_out_pass` varchar(255) DEFAULT NULL,
  `mail_out_ssl` tinyint(1) NOT NULL DEFAULT '0',
  `mail_out_method` tinyint(4) NOT NULL DEFAULT '1',
  `allow_webmail` tinyint(1) NOT NULL DEFAULT '1',
  `site_name` varchar(255) DEFAULT NULL,
  `allow_change_interface_settings` tinyint(1) NOT NULL DEFAULT '0',
  `allow_users_add_acounts` tinyint(1) NOT NULL DEFAULT '0',
  `allow_change_account_settings` tinyint(1) NOT NULL DEFAULT '0',
  `allow_new_users_register` tinyint(1) NOT NULL DEFAULT '1',
  `def_user_timezone` int(11) NOT NULL DEFAULT '0',
  `def_user_timeformat` tinyint(4) NOT NULL DEFAULT '0',
  `msgs_per_page` smallint(6) NOT NULL DEFAULT '20',
  `skin` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL,
  `allow_contacts` tinyint(1) NOT NULL DEFAULT '1',
  `contacts_per_page` smallint(6) NOT NULL DEFAULT '20',
  `allow_calendar` tinyint(1) NOT NULL DEFAULT '1',
  `cal_week_starts_on` tinyint(4) NOT NULL DEFAULT '0',
  `cal_show_weekends` tinyint(1) NOT NULL DEFAULT '0',
  `cal_workday_starts` tinyint(4) NOT NULL DEFAULT '9',
  `cal_workday_ends` tinyint(4) NOT NULL DEFAULT '18',
  `cal_show_workday` tinyint(1) NOT NULL DEFAULT '0',
  `cal_default_tab` tinyint(4) NOT NULL DEFAULT '2',
  `layout` tinyint(4) NOT NULL DEFAULT '0',
  `xlist` tinyint(1) NOT NULL DEFAULT '1',
  `global_addr_book` tinyint(4) NOT NULL DEFAULT '0',
  `check_interval` int(11) NOT NULL DEFAULT '0',
  `allow_registration` tinyint(1) NOT NULL DEFAULT '0',
  `allow_pass_reset` tinyint(1) NOT NULL DEFAULT '0',
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_domain`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_filters`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_filters` (
  `id_filter` int(11) NOT NULL AUTO_INCREMENT,
  `id_acct` int(11) NOT NULL DEFAULT '0',
  `field` tinyint(4) NOT NULL DEFAULT '0',
  `condition` tinyint(4) NOT NULL DEFAULT '0',
  `filter` varchar(255) DEFAULT NULL,
  `action` tinyint(4) NOT NULL DEFAULT '0',
  `id_folder` bigint(20) NOT NULL DEFAULT '0',
  `applied` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_filter`),
  KEY `AFTERLOGICAWM_FILTERS_ID_ACCT_ID_FOLDER_INDEX` (`id_acct`,`id_folder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_folders`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_folders` (
  `id_folder` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_parent` bigint(20) NOT NULL DEFAULT '0',
  `id_acct` int(11) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `full_path` varchar(255) DEFAULT NULL,
  `sync_type` tinyint(4) NOT NULL DEFAULT '0',
  `hide` tinyint(1) NOT NULL DEFAULT '0',
  `fld_order` smallint(6) NOT NULL DEFAULT '1',
  `flags` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_folder`),
  KEY `AFTERLOGICAWM_FOLDERS_ID_ACCT_ID_FOLDER_INDEX` (`id_acct`,`id_folder`),
  KEY `AFTERLOGICAWM_FOLDERS_ID_ACCT_ID_PARENT_INDEX` (`id_acct`,`id_parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `AfterLogicawm_folders`
--

INSERT INTO `AfterLogicawm_folders` (`id_folder`, `id_parent`, `id_acct`, `type`, `name`, `full_path`, `sync_type`, `hide`, `fld_order`, `flags`) VALUES
(1, -1, 1, 1, 'INBOX#', 'INBOX#', 5, 0, 0, '\\hasnochildren'),
(2, -1, 1, 3, 'Drafts#', 'Drafts#', 5, 0, 1, '\\hasnochildren'),
(3, -1, 1, 2, 'Sent#', 'Sent#', 5, 0, 2, '\\hasnochildren'),
(4, -1, 1, 5, 'Spam#', 'Spam#', 5, 0, 3, '\\hasnochildren'),
(5, -1, 1, 4, 'Trash#', 'Trash#', 5, 0, 4, '\\hasnochildren');

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_folders_tree`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_folders_tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_folder` bigint(20) NOT NULL DEFAULT '0',
  `id_parent` bigint(20) NOT NULL DEFAULT '0',
  `folder_level` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_FOLDERS_TREE_ID_FOLDER_INDEX` (`id_folder`),
  KEY `AFTERLOGICAWM_FOLDERS_TREE_ID_FOLDER_ID_PARENT_INDEX` (`id_folder`,`id_parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `AfterLogicawm_folders_tree`
--

INSERT INTO `AfterLogicawm_folders_tree` (`id`, `id_folder`, `id_parent`, `folder_level`) VALUES
(1, 1, 1, 0),
(2, 2, 2, 0),
(3, 3, 3, 0),
(4, 4, 4, 0),
(5, 5, 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_identities`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_identities` (
  `id_identity` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `id_acct` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL DEFAULT '',
  `friendly_nm` varchar(255) NOT NULL DEFAULT '',
  `signature` text,
  `signature_type` tinyint(4) NOT NULL DEFAULT '1',
  `use_signature` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_identity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_mailaliases`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_mailaliases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acct` int(11) DEFAULT NULL,
  `alias_name` varchar(255) NOT NULL DEFAULT '',
  `alias_domain` varchar(255) NOT NULL DEFAULT '',
  `alias_to` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_MAILALIASES_ID_ACCT_INDEX` (`id_acct`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_mailforwards`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_mailforwards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acct` int(11) DEFAULT NULL,
  `forward_name` varchar(255) NOT NULL DEFAULT '',
  `forward_domain` varchar(255) NOT NULL DEFAULT '',
  `forward_to` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_MAILFORWARDS_ID_ACCT_INDEX` (`id_acct`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_mailinglists`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_mailinglists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_acct` int(11) DEFAULT NULL,
  `list_name` varchar(255) NOT NULL DEFAULT '',
  `list_to` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_MAILINGLISTS_ID_ACCT_INDEX` (`id_acct`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_messages`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_msg` bigint(20) NOT NULL DEFAULT '0',
  `id_acct` int(11) NOT NULL DEFAULT '0',
  `id_folder_srv` bigint(20) NOT NULL DEFAULT '0',
  `id_folder_db` bigint(20) NOT NULL DEFAULT '0',
  `str_uid` varchar(255) DEFAULT NULL,
  `int_uid` bigint(20) NOT NULL DEFAULT '0',
  `from_msg` varchar(255) DEFAULT NULL,
  `to_msg` varchar(255) DEFAULT NULL,
  `cc_msg` varchar(255) DEFAULT NULL,
  `bcc_msg` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `msg_date` datetime DEFAULT NULL,
  `attachments` tinyint(1) NOT NULL DEFAULT '0',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `flagged` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(4) NOT NULL DEFAULT '0',
  `downloaded` tinyint(1) NOT NULL DEFAULT '0',
  `x_spam` tinyint(1) NOT NULL DEFAULT '0',
  `rtl` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `is_full` tinyint(1) NOT NULL DEFAULT '1',
  `replied` tinyint(1) DEFAULT NULL,
  `forwarded` tinyint(1) DEFAULT NULL,
  `flags` int(11) DEFAULT NULL,
  `body_text` longtext,
  `grayed` tinyint(1) NOT NULL DEFAULT '0',
  `charset` int(11) NOT NULL DEFAULT '-1',
  `sensitivity` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_MESSAGES_ID_ACCT_ID_FOLDER_DB_INDEX` (`id_acct`,`id_folder_db`),
  KEY `AFTERLOGICAWM_MESSAGES_ID_ACCT_ID_FOLDER_DB_SEEN_INDEX` (`id_acct`,`id_folder_db`,`seen`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_messages_body`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_messages_body` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_msg` bigint(20) NOT NULL DEFAULT '0',
  `id_acct` int(11) NOT NULL DEFAULT '0',
  `msg` longblob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `AFTERLOGICAWM_MESSAGES_BODY_ID_ACCT_ID_MSG_INDEX` (`id_acct`,`id_msg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_reads`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_reads` (
  `id_read` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_acct` int(11) NOT NULL DEFAULT '0',
  `str_uid` varchar(255) DEFAULT NULL,
  `tmp` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_read`),
  KEY `AFTERLOGICAWM_READS_ID_ACCT_INDEX` (`id_acct`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_realms`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_realms` (
  `id_realm` int(11) NOT NULL AUTO_INCREMENT,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_realm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_senders`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_senders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `safety` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_SENDERS_ID_USER_INDEX` (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_settings`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_settings` (
  `id_setting` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL DEFAULT '0',
  `msgs_per_page` smallint(6) NOT NULL DEFAULT '20',
  `contacts_per_page` smallint(6) NOT NULL DEFAULT '20',
  `last_login` datetime DEFAULT NULL,
  `logins_count` int(11) NOT NULL DEFAULT '0',
  `auto_checkmail_interval` int(11) NOT NULL DEFAULT '0',
  `def_skin` varchar(255) NOT NULL DEFAULT 'AfterLogic',
  `def_editor` tinyint(1) NOT NULL DEFAULT '1',
  `layout` tinyint(4) NOT NULL DEFAULT '0',
  `save_mail` tinyint(4) NOT NULL DEFAULT '0',
  `def_timezone` smallint(6) NOT NULL DEFAULT '0',
  `def_time_fmt` varchar(255) DEFAULT NULL,
  `def_lang` varchar(255) DEFAULT NULL,
  `def_date_fmt` varchar(255) NOT NULL DEFAULT 'MM/DD/YY',
  `mailbox_limit` bigint(20) NOT NULL DEFAULT '0',
  `incoming_charset` varchar(30) NOT NULL DEFAULT 'iso-8859-1',
  `question_1` varchar(255) DEFAULT NULL,
  `answer_1` varchar(255) DEFAULT NULL,
  `question_2` varchar(255) DEFAULT NULL,
  `answer_2` varchar(255) DEFAULT NULL,
  `enable_fnbl_sync` tinyint(1) NOT NULL DEFAULT '0',
  `custom_fields` text,
  PRIMARY KEY (`id_setting`),
  UNIQUE KEY `AFTERLOGICAWM_SETTINGS_ID_USER_INDEX` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `AfterLogicawm_settings`
--

INSERT INTO `AfterLogicawm_settings` (`id_setting`, `id_user`, `msgs_per_page`, `contacts_per_page`, `last_login`, `logins_count`, `auto_checkmail_interval`, `def_skin`, `def_editor`, `layout`, `save_mail`, `def_timezone`, `def_time_fmt`, `def_lang`, `def_date_fmt`, `mailbox_limit`, `incoming_charset`, `question_1`, `answer_1`, `question_2`, `answer_2`, `enable_fnbl_sync`, `custom_fields`) VALUES
(1, 1, 20, 20, '1970-01-01 00:00:00', 0, 0, 'AfterLogic', 1, 0, 1, 29, '0', 'English', 'MM/DD/YY', 0, 'iso-8859-1', '', '', '', '', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_subadmins`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_subadmins` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `is_owner` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogicawm_subadmin_domains`
--

CREATE TABLE IF NOT EXISTS `AfterLogicawm_subadmin_domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) DEFAULT NULL,
  `id_domain` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `AFTERLOGICAWM_SUBADMIN_DOMAINS_ID_ADMIN_INDEX` (`id_admin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `AfterLogica_users`
--

CREATE TABLE IF NOT EXISTS `AfterLogica_users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `AfterLogica_users`
--

INSERT INTO `AfterLogica_users` (`id_user`, `deleted`) VALUES
(1, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
