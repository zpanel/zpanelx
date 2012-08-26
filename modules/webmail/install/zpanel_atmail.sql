-- MySQL dump 10.11
--
-- Host: localhost    Database: atmail
-- ------------------------------------------------------
-- Server version	5.0.45-log
-- Created using
-- mysqldump --no-data --skip-add-drop-table --no-create-db --compatible=mysql40 atmail > /tmp/atmail.sql
-- cat /tmp/atmail.sql | sed "s/ AUTO_INCREMENT=.*;/;/g" | sed "s/ TYPE=MyISAM;/;/g" | sed "s/ TYPE=InnoDB;/;/g"  > /tmp/atmail2.sql

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO,MYSQL40' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `AbookGroup_a`
--

CREATE TABLE `AbookGroup_a` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_b`
--

CREATE TABLE `AbookGroup_b` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_c`
--

CREATE TABLE `AbookGroup_c` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_d`
--

CREATE TABLE `AbookGroup_d` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_e`
--

CREATE TABLE `AbookGroup_e` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_f`
--

CREATE TABLE `AbookGroup_f` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_g`
--

CREATE TABLE `AbookGroup_g` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_h`
--

CREATE TABLE `AbookGroup_h` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_i`
--

CREATE TABLE `AbookGroup_i` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_j`
--

CREATE TABLE `AbookGroup_j` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_k`
--

CREATE TABLE `AbookGroup_k` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_l`
--

CREATE TABLE `AbookGroup_l` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_m`
--

CREATE TABLE `AbookGroup_m` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_n`
--

CREATE TABLE `AbookGroup_n` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_o`
--

CREATE TABLE `AbookGroup_o` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_other`
--

CREATE TABLE `AbookGroup_other` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_p`
--

CREATE TABLE `AbookGroup_p` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_q`
--

CREATE TABLE `AbookGroup_q` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_r`
--

CREATE TABLE `AbookGroup_r` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_s`
--

CREATE TABLE `AbookGroup_s` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_shared`
--

CREATE TABLE `AbookGroup_shared` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) NOT NULL default '',
  `id` smallint(6) NOT NULL auto_increment,
  `Domain` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_t`
--

CREATE TABLE `AbookGroup_t` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_u`
--

CREATE TABLE `AbookGroup_u` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_v`
--

CREATE TABLE `AbookGroup_v` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_w`
--

CREATE TABLE `AbookGroup_w` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_x`
--

CREATE TABLE `AbookGroup_x` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_y`
--

CREATE TABLE `AbookGroup_y` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookGroup_z`
--

CREATE TABLE `AbookGroup_z` (
  `GroupName` varchar(32) default NULL,
  `GroupEmail` varchar(64) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(12) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `AbookPermissions`
--

CREATE TABLE `AbookPermissions` (
  `AbookID` mediumint(8) unsigned default NULL,
  `Account` varchar(96) NOT NULL default '',
  `Permissions` smallint(1) default NULL,
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `Type` varchar(6) default NULL,
  `Domain` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `Abook_a`
--

CREATE TABLE `Abook_a` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_b`
--

CREATE TABLE `Abook_b` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`),
  KEY `UserEmail_2` (`UserEmail`)
);

--
-- Table structure for table `Abook_c`
--

CREATE TABLE `Abook_c` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_d`
--

CREATE TABLE `Abook_d` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_e`
--

CREATE TABLE `Abook_e` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_f`
--

CREATE TABLE `Abook_f` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_g`
--

CREATE TABLE `Abook_g` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_h`
--

CREATE TABLE `Abook_h` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_i`
--

CREATE TABLE `Abook_i` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_j`
--

CREATE TABLE `Abook_j` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_k`
--

CREATE TABLE `Abook_k` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_l`
--

CREATE TABLE `Abook_l` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_m`
--

CREATE TABLE `Abook_m` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_n`
--

CREATE TABLE `Abook_n` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_o`
--

CREATE TABLE `Abook_o` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_other`
--

CREATE TABLE `Abook_other` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_p`
--

CREATE TABLE `Abook_p` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_q`
--

CREATE TABLE `Abook_q` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_r`
--

CREATE TABLE `Abook_r` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_s`
--

CREATE TABLE `Abook_s` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_shared`
--

CREATE TABLE `Abook_shared` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `Abook_t`
--

CREATE TABLE `Abook_t` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_u`
--

CREATE TABLE `Abook_u` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_v`
--

CREATE TABLE `Abook_v` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_w`
--

CREATE TABLE `Abook_w` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_x`
--

CREATE TABLE `Abook_x` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_y`
--

CREATE TABLE `Abook_y` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Abook_z`
--

CREATE TABLE `Abook_z` (
  `UserEmail` varchar(255) default NULL,
  `UserEmail2` varchar(255) default NULL,
  `UserEmail3` varchar(255) default NULL,
  `UserEmail4` varchar(255) default NULL,
  `UserEmail5` varchar(255) default NULL,
  `UserFirstName` varchar(128) default NULL,
  `UserMiddleName` varchar(128) default NULL,
  `UserLastName` varchar(128) default NULL,
  `UserTitle` varchar(128) default NULL,
  `UserGender` char(1) default NULL,
  `UserDOB` datetime default NULL,
  `UserHomeAddress` varchar(128) default NULL,
  `UserHomeCity` varchar(128) default NULL,
  `UserHomeState` varchar(128) default NULL,
  `UserHomeZip` varchar(128) default NULL,
  `UserHomeCountry` varchar(128) default NULL,
  `UserHomePhone` varchar(128) default NULL,
  `UserHomeMobile` varchar(128) default NULL,
  `UserHomeFax` varchar(128) default NULL,
  `UserURL` varchar(128) default NULL,
  `UserWorkCompany` varchar(128) default NULL,
  `UserWorkTitle` varchar(128) default NULL,
  `UserWorkDept` varchar(128) default NULL,
  `UserWorkOffice` varchar(128) default NULL,
  `UserWorkAddress` varchar(128) default NULL,
  `UserWorkCity` varchar(128) default NULL,
  `UserWorkState` varchar(128) default NULL,
  `UserWorkZip` varchar(128) default NULL,
  `UserWorkCountry` varchar(128) default NULL,
  `UserWorkPhone` varchar(128) default NULL,
  `UserWorkMobile` varchar(128) default NULL,
  `UserWorkFax` varchar(128) default NULL,
  `UserType` varchar(16) default NULL,
  `UserInfo` text,
  `UserPgpKey` text,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  `DateModified` varchar(12) default NULL,
  `EntryID` varchar(64) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `UserEmail` (`UserEmail`)
);

--
-- Table structure for table `Accounts`
--

CREATE TABLE `Accounts` (
  `UserAccount` varchar(32) default NULL,
  `Account` varchar(128) NOT NULL default '',
  `Type` varchar(4) default NULL,
  `MailServer` varchar(64) default NULL,
  `UseSSL` tinyint(1) default 0
);


--
-- Table structure for table `EmailMessage_Reply`
--

CREATE TABLE `EmailMessage_Reply` (
  `EmailMessage` longtext,
  `id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
);


--
-- Table structure for table `Filter`
--

CREATE TABLE `Filter` (
  `Header` varchar(32) NOT NULL default '',
  `Value` varchar(255) default NULL,
  `Score` int(2) default NULL,
  `Account` varchar(128) NOT NULL default '',
  `Type` int(1) default NULL,
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Origin` varchar(8) default NULL,
  `Description` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `iHeader` (`Header`)
);


--
-- Table structure for table `Log_Error`
--

CREATE TABLE `Log_Error` (
  `LogMsg` varchar(255) default NULL,
  `LogDate` datetime default NULL,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `LogDate` (`LogDate`)
);

--
-- Table structure for table `Log_Login`
--

CREATE TABLE `Log_Login` (
  `LogMsg` varchar(64) default NULL,
  `LogDate` datetime default NULL,
  `Account` varchar(128) default NULL,
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `LogDate` (`LogDate`)
);

--
-- Table structure for table `Log_RecvMail`
--

CREATE TABLE `Log_RecvMail` (
  `LogMsg` varchar(64) default NULL,
  `LogDate` datetime default NULL,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `LogDate` (`LogDate`)
);

--
-- Table structure for table `Log_SMS`
--

CREATE TABLE `Log_SMS` (
  `LogMsg` varchar(255) default NULL,
  `LogDate` datetime default NULL,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `LogDate` (`LogDate`)
);

--
-- Table structure for table `Log_SendMail`
--

CREATE TABLE `Log_SendMail` (
  `LogMsg` varchar(64) default NULL,
  `LogDate` datetime default NULL,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `LogDate` (`LogDate`)
);

--
-- Table structure for table `Log_Spam`
--

CREATE TABLE `Log_Spam` (
  `LogMsg` varchar(255) default NULL,
  `LogDate` datetime default NULL,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `LogDate` (`LogDate`)
);

--
-- Table structure for table `Log_Virus`
--

CREATE TABLE `Log_Virus` (
  `LogMsg` varchar(255) default NULL,
  `LogDate` datetime default NULL,
  `Account` varchar(128) NOT NULL default '',
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `LogDate` (`LogDate`)
);



--
-- Table structure for table `MailSort_a`
--

CREATE TABLE `MailSort_a` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_b`
--

CREATE TABLE `MailSort_b` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_c`
--

CREATE TABLE `MailSort_c` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_d`
--

CREATE TABLE `MailSort_d` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_e`
--

CREATE TABLE `MailSort_e` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_f`
--

CREATE TABLE `MailSort_f` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_g`
--

CREATE TABLE `MailSort_g` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_h`
--

CREATE TABLE `MailSort_h` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_i`
--

CREATE TABLE `MailSort_i` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_j`
--

CREATE TABLE `MailSort_j` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_k`
--

CREATE TABLE `MailSort_k` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_l`
--

CREATE TABLE `MailSort_l` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_m`
--

CREATE TABLE `MailSort_m` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_n`
--

CREATE TABLE `MailSort_n` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_o`
--

CREATE TABLE `MailSort_o` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_other`
--

CREATE TABLE `MailSort_other` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_p`
--

CREATE TABLE `MailSort_p` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_q`
--

CREATE TABLE `MailSort_q` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_r`
--

CREATE TABLE `MailSort_r` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_s`
--

CREATE TABLE `MailSort_s` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_t`
--

CREATE TABLE `MailSort_t` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_u`
--

CREATE TABLE `MailSort_u` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_v`
--

CREATE TABLE `MailSort_v` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_w`
--

CREATE TABLE `MailSort_w` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_x`
--

CREATE TABLE `MailSort_x` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_y`
--

CREATE TABLE `MailSort_y` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailSort_z`
--

CREATE TABLE `MailSort_z` (
  `EmailAddress` varchar(64) default NULL,
  `EmailSubject` varchar(64) default NULL,
  `EmailFolder` varchar(32) default NULL,
  `Account` varchar(128) default NULL,
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `MailThreads`
--

CREATE TABLE `MailThreads` (
  `MessageID` varchar(128) default NULL,
  `ThreadID` varchar(128) default NULL,
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Account` varchar(128) default NULL,
  `ParentID` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `MessageID` (`MessageID`)
);



--
-- Table structure for table `SpellCheck`
--

CREATE TABLE `SpellCheck` (
  `Account` varchar(128) NOT NULL default '',
  `Word` varchar(40) default NULL,
  `SUnique` varchar(10) default '0',
  `id` bigint(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

--
-- Table structure for table `UserPermissions`
--

CREATE TABLE `UserPermissions` (
  `Account` varchar(128) default NULL,
  `DateAdded` timestamp NOT NULL,
  `id` mediumint(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
);



--
-- Table structure for table `UserSession`
--

CREATE TABLE `UserSession` (
  `Account` varchar(128) NOT NULL default '',
  `Password` varchar(64) default NULL,
  `SessionID` varchar(64) NOT NULL default '',
  `LastLogin` int(10) unsigned default NULL,
  `PasswordMD5` varchar(64) default NULL,
  `SessionData` text,
  `ChangePass` int(1) default NULL,
  PRIMARY KEY  (`Account`),
  KEY `iAccount` (`SessionID`)
);

--
-- Table structure for table `UserSettings_a`
--

CREATE TABLE `UserSettings_a` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_b`
--

CREATE TABLE `UserSettings_b` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_c`
--

CREATE TABLE `UserSettings_c` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_d`
--

CREATE TABLE `UserSettings_d` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_e`
--

CREATE TABLE `UserSettings_e` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_f`
--

CREATE TABLE `UserSettings_f` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_g`
--

CREATE TABLE `UserSettings_g` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_h`
--

CREATE TABLE `UserSettings_h` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_i`
--

CREATE TABLE `UserSettings_i` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_j`
--

CREATE TABLE `UserSettings_j` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_k`
--

CREATE TABLE `UserSettings_k` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_l`
--

CREATE TABLE `UserSettings_l` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_m`
--

CREATE TABLE `UserSettings_m` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_n`
--

CREATE TABLE `UserSettings_n` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_o`
--

CREATE TABLE `UserSettings_o` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_other`
--

CREATE TABLE `UserSettings_other` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_p`
--

CREATE TABLE `UserSettings_p` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_q`
--

CREATE TABLE `UserSettings_q` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_r`
--

CREATE TABLE `UserSettings_r` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_s`
--

CREATE TABLE `UserSettings_s` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_t`
--

CREATE TABLE `UserSettings_t` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_u`
--

CREATE TABLE `UserSettings_u` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_v`
--

CREATE TABLE `UserSettings_v` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_w`
--

CREATE TABLE `UserSettings_w` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_x`
--

CREATE TABLE `UserSettings_x` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_y`
--

CREATE TABLE `UserSettings_y` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `UserSettings_z`
--

CREATE TABLE `UserSettings_z` (
  `Account` varchar(128) NOT NULL default '',
  `MboxOrder` varchar(12) default NULL,
  `RealName` varchar(40) default NULL,
  `Refresh` smallint(5) default NULL,
  `EmailHeaders` varchar(8) default NULL,
  `TimeZone` varchar(32) default NULL,
  `MsgNum` varchar(4) default NULL,
  `EmptyTrash` smallint(1) default NULL,
  `NewWindow` smallint(1) default NULL,
  `HtmlEditor` smallint(1) default NULL,
  `ReplyTo` varchar(255) default NULL,
  `Signature` text,
  `FontStyle` varchar(21) default NULL,
  `LeaveMsgs` smallint(1) default NULL,
  `LoginType` varchar(12) default NULL,
  `Advanced` smallint(1) default NULL,
  `PrimaryColor` varchar(7) default NULL,
  `SecondaryColor` varchar(7) default NULL,
  `LinkColor` varchar(7) default NULL,
  `VlinkColor` varchar(7) default NULL,
  `BgColor` varchar(7) default NULL,
  `TextColor` varchar(7) default NULL,
  `HeaderColor` varchar(7) default NULL,
  `HeadColor` varchar(7) default NULL,
  `MailType` varchar(4) default NULL,
  `ThirdColor` varchar(7) default NULL,
  `Mode` varchar(4) default NULL,
  `Service` smallint(1) default NULL,
  `Language` varchar(10) default NULL,
  `StartPage` char(1) default NULL,
  `OnColor` varchar(7) default NULL,
  `OffColor` varchar(7) default NULL,
  `TextHeadColor` varchar(7) default NULL,
  `SelectColor` varchar(7) default NULL,
  `TopBg` varchar(24) default NULL,
  `AutoTrash` tinyint(1) default NULL,
  `MailServer` varchar(64) default NULL,
  `MailAuth` tinyint(1) default NULL,
  `PGPenable` smallint(1) default NULL,
  `PGPappend` smallint(1) default NULL,
  `PGPsign` smallint(1) default NULL,
  `SpamTreatment` varchar(10) default NULL,
  `AbookTrusted` smallint(1) default NULL,
  `AntiVirus` smallint(1) default NULL,
  `PassCode` varchar(64) default NULL,
  `DateFormat` varchar(8) default NULL,
  `TimeFormat` varchar(8) default NULL,
  `AutoComplete` smallint(1) default NULL,
  `EmailEncoding` varchar(16) default NULL,
  `DisplayImages` smallint(1) default NULL,
  `Ajax` char(1) default NULL,
  `UseSSL` tinyint(1) default 0,
  PRIMARY KEY  (`Account`)
);

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `Account` varchar(128) NOT NULL default '',
  `PasswordQuestion` varchar(64) default NULL,
  `OtherEmail` varchar(128) default NULL,
  `FirstName` varchar(96) default NULL,
  `LastName` varchar(96) default NULL,
  `BirthDay` smallint(2) default NULL,
  `BirthMonth` smallint(2) default NULL,
  `BirthYear` smallint(4) default NULL,
  `Gender` char(1) default NULL,
  `Industry` varchar(42) default NULL,
  `Occupation` varchar(40) default NULL,
  `Address` varchar(96) default NULL,
  `City` varchar(96) default NULL,
  `PostCode` varchar(36) default NULL,
  `State` varchar(24) default NULL,
  `Country` varchar(32) default NULL,
  `DateModified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `DateCreate` timestamp NOT NULL default '0000-00-00 00:00:00',
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `TelHome` varchar(16) default NULL,
  `FaxHome` varchar(16) default NULL,
  `TelWork` varchar(16) default NULL,
  `FaxWork` varchar(16) default NULL,
  `TelMobile` varchar(16) default NULL,
  `TelPager` varchar(16) default NULL,
  `Ugroup` varchar(16) default NULL,
  `UserStatus` int(1) default NULL,
  `MailDir` varchar(255) default NULL,
  `Forward` varchar(128) default NULL,
  `AutoReply` text,
  `UserQuota` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
);

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

