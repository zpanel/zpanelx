<?php
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+

// NOTE:
// This script will not upgrade you to the full commercial version of
// Atmail, you still need to purchase and download the commercial Atmail
// code. This script simply makes necessary adjustments to the database
// structure after which you can run the commercial Atmail installer.

// Do not allow to run from a http request, must be CLI
if ($_SERVER['GATEWAY_INTERFACE'] || $_SERVER['REQUEST_METHOD']) {
    die("This script can only be run via the command line interface");
}

if (!file_exists('webadmin/admin.php') && !file_exists('/usr/local/atmail/webmail/webadmin/admin.php')) {
	die("It seems you do not have the Atmail commercial version available.\nIf you have purchased Atmail commercial version then extract the files into your Atmail Open directory if you purchased the client only version or into /usr/local/atmail if you purchased the full server version then run this script again.\n\n");
}

require_once('header.php');
require_once('Config.php');
require_once('Global_Base.php'); // Backwards compatability, e.g FC2, no file_put_contents if sqldo statement errors
require_once('SQL.php');

$db = new SQL();

$alpha = range('a', 'z');
$alpha[] = 'other';

fwrite(STDOUT, "Making required database modifications... ");

foreach ($alpha as $a) {
    $db->sqldo("alter table Abook_$a add `UserPhoto` mediumtext");
    $db->sqldo("alter table Abook_$a add `UserFileAs` varchar(255) default null");
    $db->sqldo("CREATE TABLE `Calander_$a` (
        `Importance` smallint(1) default NULL,
        `UserTo` varchar(64) default NULL,
        `UserFrom` varchar(64) default NULL,
        `Title` varchar(255) default NULL,
        `DatePost` timestamp NOT NULL,
        `CalMessage` text,
        `Alert` smallint(3) default NULL,
        `DateStart` datetime NOT NULL default '0000-00-00 00:00:00',
        `DateEnd` datetime default NULL,
        `id` mediumint(8) unsigned NOT NULL auto_increment,
        `Type` varchar(16) default NULL,
        `Task` smallint(1) default NULL,
        `Ref` smallint(6) default NULL,
        `Parent` tinyint(1) default NULL,
        `Permission` tinyint(1) default NULL,
        `EntryID` varchar(64) default NULL,
        `DateModified` varchar(12) default NULL,
        `Location` varchar(64) default NULL,
        `AllDayEvent` smallint(1) default NULL,
        `DateAlert` datetime default NULL,
        `IsRecurring` int(11) default NULL,
        `IsException` int(11) default NULL,
        `DateAlertPeriod` mediumint(8) unsigned default '60',
        `SMSalert` int(1) default NULL,
        `Availability` varchar(16) default 'Busy',
        `CalNameID` mediumint(8) unsigned default NULL,
        PRIMARY KEY  (`id`),
        KEY `iUserTo` (`UserTo`)
    )");

    $db->sqldo("CREATE TABLE `EmailDatabase_$a` (
        `EmailSubject` varchar(164) default NULL,
        `EmailFrom` varchar(164) default NULL,
        `EmailTo` varchar(64) default NULL,
        `EmailModified` timestamp NOT NULL,
        `EmailDate` timestamp NOT NULL default '0000-00-00 00:00:00',
        `EmailBox` varchar(64) default NULL,
        `EmailFlag` char(1) default NULL,
        `EmailAttach` char(1) default NULL,
        `id` bigint(12) unsigned NOT NULL auto_increment,
        `Account` varchar(128) NOT NULL default '',
        `EmailUIDL` varchar(70) default NULL,
        `EmailSize` int(10) unsigned default NULL,
        `FlagSeen` tinyint(1) NOT NULL default '0',
        `FlagAnswered` tinyint(1) NOT NULL default '0',
        `FlagDeleted` tinyint(1) NOT NULL default '0',
        `FlagFlagged` tinyint(1) NOT NULL default '0',
        `FlagRecent` tinyint(1) NOT NULL default '0',
        `FlagDraft` tinyint(1) NOT NULL default '0',
        `FlagInferiors` tinyint(1) NOT NULL default '0',
        `FlagSelect` tinyint(1) NOT NULL default '0',
        PRIMARY KEY  (`id`),
        KEY `iAccount` (`Account`),
        KEY `EmailUIDL` (`EmailUIDL`)
    )");

    $db->sqldo("CREATE TABLE `EmailMessage_$a` (
        `EmailMessage` longtext,
        `id` bigint(12) unsigned NOT NULL auto_increment,
        `EmailText` mediumtext,
        PRIMARY KEY  (`id`),
        FULLTEXT KEY `EmailText` (`EmailText`)
    )");

    $db->sqldo("CREATE TABLE `EmailUIDL_$a` (
        `EmailUIDL` varchar(70) NOT NULL default '',
        `EmailType` char(1) default NULL,
        `Account` varchar(128) NOT NULL default '',
        KEY `iEmailUIDL` (`EmailUIDL`)
    )");

    $db->sqldo("CREATE TABLE `SpamDB_$a` (
        `SpamEmail` varchar(64) NOT NULL default '',
        `Account` varchar(128) default NULL,
        KEY `iSpamEmail` (`SpamEmail`)
    )");

    $db->sqldo("CREATE TABLE `UserPgp_$a` (
        `Account` varchar(128) NOT NULL default '',
        `pubring` mediumblob,
        `secring` mediumblob,
        `trustdb` mediumblob,
        `public` blob,
        PRIMARY KEY  (`Account`)
    )");

}

$db->sqldo("CREATE TABLE `AdminGroup` (
    `Username` varchar(64) NOT NULL default '',
    `Ugroup` varchar(32) default NULL,
    `Domain` varchar(64) default NULL,
    `id` mediumint(8) unsigned NOT NULL auto_increment,
    PRIMARY KEY  (`id`),
    KEY `iUsername` (`Username`)
)");

$db->sqldo("CREATE TABLE `AdminUsers` (
    `Username` varchar(64) NOT NULL default '',
    `Password` varchar(64) NOT NULL default '',
    `UAdd` tinyint(1) default NULL,
    `UDelete` tinyint(1) default NULL,
    `UModify` tinyint(1) default NULL,
    `UPurge` tinyint(1) default NULL,
    `USearch` tinyint(1) default NULL,
    `UList` tinyint(1) default NULL,
    `UMigrate` tinyint(1) default NULL,
    `ULogs` tinyint(1) default NULL,
    `UAlias` tinyint(1) default NULL,
    `id` mediumint(8) unsigned NOT NULL auto_increment,
    `NumUsers` mediumint(6) unsigned default NULL,
    `Company` varchar(64) default NULL,
    `Fullname` varchar(64) default NULL,
    `DateCreate` datetime default NULL,
    `SessionID` varchar(32) default NULL,
    `LastLogin` int(10) unsigned default NULL,
    `NumQuota` mediumint(6) unsigned default NULL,
    `UBrand` tinyint(1) default NULL,
    `BrandDomain` varchar(128) default NULL,
    `UGroupAssign` tinyint(1) default NULL,
    `UAll` tinyint(1) default NULL,
    PRIMARY KEY  (`id`),
    KEY `iUsername` (`Username`)
)");

$db->sqldo("CREATE TABLE `CalendarNames` (
    `CalName` varchar(64) default NULL,
    `id` mediumint(12) unsigned NOT NULL auto_increment,
    PRIMARY KEY  (`id`),
    KEY `iCalName` (`CalName`)
)");

$db->sqldo("CREATE TABLE `CalendarPermissions` (
    `CalID` mediumint(8) unsigned default NULL,
    `Account` varchar(128) NOT NULL default '',
    `Permissions` smallint(1) default NULL,
    `Type` varchar(6) default NULL,
    `id` bigint(10) unsigned NOT NULL auto_increment,
    `Domain` varchar(64) default NULL,
    `CalNameID` mediumint(8) unsigned default NULL,
    PRIMARY KEY  (`id`),
    KEY `iAccount` (`Account`)
)");

$db->sqldo("CREATE TABLE `ClientBilling` (
    `Account` varchar(128) NOT NULL default '',
    `CardNumber` varchar(64) default NULL,
    `CardName` varchar(64) default NULL,
    `CardExpMonth` varchar(4) default NULL,
    `CardExpYear` varchar(4) default NULL,
    `PaymentStatus` tinyint(4) default NULL,
    `DateSignup` datetime default NULL,
    `DatePaid` datetime default NULL,
    `Amount` mediumint(8) unsigned default NULL,
    `id` mediumint(8) unsigned NOT NULL auto_increment,
    `Service` varchar(32) default NULL,
    PRIMARY KEY  (`id`)
)");

$db->sqldo("CREATE TABLE `ClientPayment` (
    `Account` varchar(128) NOT NULL default '',
    `Amount` mediumint(8) unsigned default NULL,
    `PaymentStatus` tinyint(4) default NULL,
    `DatePaid` datetime default NULL,
    `id` mediumint(8) unsigned NOT NULL auto_increment,
    PRIMARY KEY  (`id`)
)");

$db->sqldo("CREATE TABLE `Config` (
  `configId` int(11) NOT NULL auto_increment,
  `section` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY  (`configId`)
)");

$db->sqldo("CREATE TABLE `Domains` (
  `Hostname` varchar(255) NOT NULL default '',
  `Enable` char(1) default NULL,
  PRIMARY KEY  (`Hostname`)
)");

$db->sqldo("CREATE TABLE `Folders` (
    `id` bigint(12) unsigned NOT NULL auto_increment,
    `Account` varchar(128) NOT NULL default '',
    `FolderName` varchar(64) NOT NULL default '',
    `FolderSize` smallint(6) unsigned default NULL,
    `FolderMsgs` smallint(5) unsigned default NULL,
    `FlagSeen` tinyint(1) NOT NULL default '0',
    `FlagAnswered` tinyint(1) NOT NULL default '0',
    `FlagDeleted` tinyint(1) NOT NULL default '0',
    `FlagFlagged` tinyint(1) NOT NULL default '0',
    `FlagRecent` tinyint(1) NOT NULL default '0',
    `FlagDraft` tinyint(1) NOT NULL default '0',
    `FlagInferiors` tinyint(1) NOT NULL default '0',
    `FlagSelect` tinyint(1) NOT NULL default '0',
    `Permission` tinyint(1) default '1',
    `Subscribe` tinyint(1) NOT NULL default '0',
    `ParentID` bigint(12) unsigned default NULL,
    `IMAP` tinyint(1) NOT NULL default '0',
    PRIMARY KEY  (`id`),
    KEY `iAccount` (`Account`)
)");

$db->sqldo("CREATE TABLE `Groups` (
    `GroupName` varchar(64) NOT NULL default '',
    `GroupDescription` varchar(64) default NULL,
    `GroupPrice` varchar(5) default NULL,
    `GroupQuota` mediumint(8) unsigned default NULL,
    `MailForwarding` int(1) default NULL,
    `SMSSupport` int(1) default NULL,
    `POP3Support` int(1) default NULL,
    `IMAPSupport` int(1) default NULL,
    `MultiSupport` int(1) default NULL,
    `PersonalSpam` int(1) default NULL,
    `GlobalAbookRead` int(1) default NULL,
    `GlobalAbook` int(1) default NULL,
    `AV` int(1) default NULL,
    `SpamSupport` int(1) default NULL,
    `Sync` int(1) default NULL,
    `PushSupport` int(1) default NULL,
    PRIMARY KEY  (`GroupName`)
)");

$db->sqldo("CREATE TABLE `MailAliases` (
  `AliasName` varchar(200) default NULL,
  `AliasTo` varchar(200) default NULL,
  `Domain` varchar(128) default NULL,
  `DateCreate` datetime default NULL,
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `AliasMailDir` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
)");

$db->sqldo("CREATE TABLE `MailRelay` (
  `IPaddress` varchar(15) NOT NULL default '',
  `DateAdded` timestamp NOT NULL,
  `Account` varchar(128) default NULL,
  PRIMARY KEY  (`IPaddress`),
  KEY `DateAdded` (`DateAdded`),
  KEY `Account` (`Account`)
)");

$db->sqldo("CREATE TABLE `SMSCredits` (
  `Account` varchar(128) default NULL,
  `Credit` float unsigned default NULL,
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `Account` (`Account`)
)");

$db->sqldo("CREATE TABLE `SMSfilter` (
  `Account` varchar(128) NOT NULL default '',
  `EmailSubject` varchar(128) default NULL,
  `EmailSubjectType` char(2) default NULL,
  `EmailFrom` varchar(128) default NULL,
  `EmailFromType` char(2) default NULL,
  `EmailPriority` char(1) default NULL,
  `EmailAll` char(1) default NULL,
  `MobileNumber` varchar(16) default NULL,
  `Matches` mediumint(8) unsigned default NULL,
  `DateCreated` datetime default NULL,
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
)");

$db->sqldo("CREATE TABLE `SMSqueue` (
  `AlertNumber` varchar(128) default NULL,
  `AlertModified` timestamp NOT NULL,
  `AlertDate` varchar(11) default NULL,
  `AlertMessage` varchar(255) default NULL,
  `AlertStatus` char(1) default NULL,
  `Account` varchar(128) default NULL,
  `id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)");

$db->sqldo("CREATE TABLE `SMSsent` (
  `Account` varchar(128) NOT NULL default '',
  `SMSFrom` varchar(128) default NULL,
  `SMSTo` varchar(128) default NULL,
  `SMSStatus` varchar(8) default NULL,
  `SMSmessage` varchar(255) default NULL,
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `SMSid` varchar(64) default NULL,
  `SMSdate` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`)
)");

$db->sqldo("CREATE TABLE `SerialConf` (
  `Name` varchar(64) default NULL,
  `Value` mediumtext,
  `Account` varchar(128) default NULL,
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `DateAdded` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `iAccount` (`Account`),
  KEY `iName` (`Name`)
)");

$db->sqldo("CREATE TABLE `SharedLookup` (
  `Type` varchar(8) default NULL,
  `EntryID` varchar(64) default NULL,
  `LookupID` bigint(10) unsigned NOT NULL default '0',
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `Account` varchar(128) default NULL,
  `DateModified` varchar(12) default NULL,
  PRIMARY KEY  (`id`),
  KEY `LookupID` (`LookupID`),
  KEY `Account` (`Account`)
)");

$db->sqldo("CREATE TABLE `SpamSettings` (
  `username` varchar(255) default NULL,
  `preference` varchar(30) NOT NULL default '',
  `value` varchar(100) NOT NULL default '',
  `prefid` int(11) NOT NULL auto_increment,
  `domain` varchar(128) default NULL,
  PRIMARY KEY  (`prefid`),
  KEY `username` (`username`),
  KEY `preference` (`preference`)
)");

$db->sqldo("CREATE TABLE `awl` (
  `username` varchar(100) NOT NULL default '',
  `email` varchar(200) NOT NULL default '',
  `ip` varchar(10) NOT NULL default '',
  `count` int(11) default '0',
  `totscore` float default '0',
  `IndexDate` timestamp NOT NULL,
  PRIMARY KEY  (`username`,`email`,`ip`)
)");

$db->sqldo("CREATE TABLE `recurrencePatterns` (
  `id` int(11) NOT NULL auto_increment,
  `account` varchar(128) default NULL,
  `RecurrenceType` int(11) default NULL,
  `PatternStartDate` datetime default NULL,
  `PatternEndDate` datetime default NULL,
  `NoEndDate` int(11) default NULL,
  `Occurences` int(11) default NULL,
  `skipInterval` int(11) default NULL,
  `DayOfWeekMask` int(11) default NULL,
  `DayOfMonth` int(11) default NULL,
  `Instance` int(11) default NULL,
  `Duration` int(11) default NULL,
  `MonthOfYear` int(11) default NULL,
  `linkid` int(11) default NULL,
  `IsSimple` int(11) default NULL,
  PRIMARY KEY  (`id`)
)");

$pref['installed'] = 0;
$path = dirname(__FILE__);
unlink($path . '/install/.htaccess');
`rm -rf $path/fckeditor`;
writeconf();

fwrite(STDOUT, "Done\n\nYou should now run the installer for Atmail commercial version. Be sure to select or enter the name of your current Atmail Open database '{$pref['sql_table']}' in the database setup section of the installer.\n\n");

;