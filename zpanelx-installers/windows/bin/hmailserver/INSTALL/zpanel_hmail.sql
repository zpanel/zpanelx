-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Client: localhost:3306
-- Généré le: Ven 03 Août 2012 à 14:01
-- Version du serveur: 5.5.21
-- Version de PHP: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `zpanel_hmail`
--

-- --------------------------------------------------------

--
-- Structure de la table `hm_accounts`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_accounts` (
  `accountid` int(11) NOT NULL AUTO_INCREMENT,
  `accountdomainid` int(11) NOT NULL,
  `accountadminlevel` tinyint(4) NOT NULL,
  `accountaddress` varchar(255) NOT NULL,
  `accountpassword` varchar(255) NOT NULL,
  `accountactive` tinyint(4) NOT NULL,
  `accountisad` tinyint(4) NOT NULL,
  `accountaddomain` varchar(255) NOT NULL,
  `accountadusername` varchar(255) NOT NULL,
  `accountmaxsize` int(11) NOT NULL,
  `accountvacationmessageon` tinyint(4) NOT NULL,
  `accountvacationmessage` text NOT NULL,
  `accountvacationsubject` varchar(200) NOT NULL,
  `accountpwencryption` tinyint(4) NOT NULL,
  `accountforwardenabled` tinyint(4) NOT NULL,
  `accountforwardaddress` varchar(255) NOT NULL,
  `accountforwardkeeporiginal` tinyint(4) NOT NULL,
  `accountenablesignature` tinyint(4) NOT NULL,
  `accountsignatureplaintext` text NOT NULL,
  `accountsignaturehtml` text NOT NULL,
  `accountlastlogontime` datetime NOT NULL,
  `accountvacationexpires` tinyint(3) unsigned NOT NULL,
  `accountvacationexpiredate` datetime NOT NULL,
  `accountpersonfirstname` varchar(60) NOT NULL,
  `accountpersonlastname` varchar(60) NOT NULL,
  PRIMARY KEY (`accountid`),
  UNIQUE KEY `accountid` (`accountid`),
  UNIQUE KEY `accountaddress` (`accountaddress`),
  KEY `idx_hm_accounts` (`accountaddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `hm_accounts`
--

INSERT INTO `hm_accounts` (`accountid`, `accountdomainid`, `accountadminlevel`, `accountaddress`, `accountpassword`, `accountactive`, `accountisad`, `accountaddomain`, `accountadusername`, `accountmaxsize`, `accountvacationmessageon`, `accountvacationmessage`, `accountvacationsubject`, `accountpwencryption`, `accountforwardenabled`, `accountforwardaddress`, `accountforwardkeeporiginal`, `accountenablesignature`, `accountsignatureplaintext`, `accountsignaturehtml`, `accountlastlogontime`, `accountvacationexpires`, `accountvacationexpiredate`, `accountpersonfirstname`, `accountpersonlastname`) VALUES
(1, 1, 0, 'postmaster@localhost.com', 'afbf269297a7c2064e924cee28afb991613b8e00b22bb2dc0fd83be88a564f1e7569c4', 1, 0, '', '', 1, 0, '', '', 3, 0, '', 0, 0, '', '', '2012-08-01 23:51:25', 0, '2008-02-10 00:00:00', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `hm_acl`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_acl` (
  `aclid` bigint(20) NOT NULL AUTO_INCREMENT,
  `aclsharefolderid` bigint(20) NOT NULL,
  `aclpermissiontype` tinyint(4) NOT NULL,
  `aclpermissiongroupid` bigint(20) NOT NULL,
  `aclpermissionaccountid` bigint(20) NOT NULL,
  `aclvalue` bigint(20) NOT NULL,
  PRIMARY KEY (`aclid`),
  UNIQUE KEY `aclid` (`aclid`),
  UNIQUE KEY `aclsharefolderid` (`aclsharefolderid`,`aclpermissiontype`,`aclpermissiongroupid`,`aclpermissionaccountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_aliases`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_aliases` (
  `aliasid` int(11) NOT NULL AUTO_INCREMENT,
  `aliasdomainid` int(11) NOT NULL,
  `aliasname` varchar(255) NOT NULL,
  `aliasvalue` varchar(255) NOT NULL,
  `aliasactive` tinyint(4) NOT NULL,
  PRIMARY KEY (`aliasid`),
  UNIQUE KEY `aliasid` (`aliasid`),
  UNIQUE KEY `aliasname` (`aliasname`),
  KEY `idx_hm_aliases` (`aliasdomainid`,`aliasname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_blocked_attachments`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_blocked_attachments` (
  `baid` bigint(20) NOT NULL AUTO_INCREMENT,
  `bawildcard` varchar(255) NOT NULL,
  `badescription` varchar(255) NOT NULL,
  PRIMARY KEY (`baid`),
  UNIQUE KEY `baid` (`baid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `hm_blocked_attachments`
--

INSERT INTO `hm_blocked_attachments` (`baid`, `bawildcard`, `badescription`) VALUES
(1, '*.bat', 'Batch processing file'),
(2, '*.cmd', 'Command file for Windows NT'),
(3, '*.com', 'Command'),
(4, '*.cpl', 'Windows Control Panel extension'),
(5, '*.csh', 'CSH script'),
(6, '*.exe', 'Executable file'),
(7, '*.inf', 'Setup file'),
(8, '*.lnk', 'Windows link file'),
(9, '*.msi', 'Windows Installer file'),
(10, '*.msp', 'Windows Installer patch'),
(11, '*.reg', 'Registration key'),
(12, '*.scf', 'Windows Explorer command'),
(13, '*.scr', 'Windows Screen saver');

-- --------------------------------------------------------

--
-- Structure de la table `hm_dbversion`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_dbversion` (
  `value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `hm_dbversion`
--

INSERT INTO `hm_dbversion` (`value`) VALUES
(5400);

-- --------------------------------------------------------

--
-- Structure de la table `hm_distributionlists`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_distributionlists` (
  `distributionlistid` int(11) NOT NULL AUTO_INCREMENT,
  `distributionlistdomainid` int(11) NOT NULL,
  `distributionlistaddress` varchar(255) NOT NULL,
  `distributionlistenabled` tinyint(4) NOT NULL,
  `distributionlistrequireauth` tinyint(4) NOT NULL,
  `distributionlistrequireaddress` varchar(255) NOT NULL,
  `distributionlistmode` tinyint(4) NOT NULL,
  PRIMARY KEY (`distributionlistid`),
  UNIQUE KEY `distributionlistid` (`distributionlistid`),
  UNIQUE KEY `distributionlistaddress` (`distributionlistaddress`),
  KEY `idx_hm_distributionlists` (`distributionlistdomainid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_distributionlistsrecipients`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_distributionlistsrecipients` (
  `distributionlistrecipientid` int(11) NOT NULL AUTO_INCREMENT,
  `distributionlistrecipientlistid` int(11) NOT NULL,
  `distributionlistrecipientaddress` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`distributionlistrecipientid`),
  UNIQUE KEY `distributionlistrecipientid` (`distributionlistrecipientid`),
  KEY `idx_hm_distributionlistsrecipients` (`distributionlistrecipientlistid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_dnsbl`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_dnsbl` (
  `sblid` int(11) NOT NULL AUTO_INCREMENT,
  `sblactive` tinyint(4) NOT NULL,
  `sbldnshost` varchar(255) NOT NULL,
  `sblresult` varchar(255) NOT NULL,
  `sblrejectmessage` varchar(255) NOT NULL,
  `sblscore` int(11) NOT NULL,
  PRIMARY KEY (`sblid`),
  UNIQUE KEY `sblid` (`sblid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `hm_dnsbl`
--

INSERT INTO `hm_dnsbl` (`sblid`, `sblactive`, `sbldnshost`, `sblresult`, `sblrejectmessage`, `sblscore`) VALUES
(1, 0, 'zen.spamhaus.org', '127.0.0.2-8|127.0.0.10-11', 'Rejected by Spamhaus.', 3),
(2, 0, 'bl.spamcop.net', '127.0.0.2', 'Rejected by SpamCop.', 3);

-- --------------------------------------------------------

--
-- Structure de la table `hm_domains`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_domains` (
  `domainid` int(11) NOT NULL AUTO_INCREMENT,
  `domainname` varchar(80) NOT NULL,
  `domainactive` tinyint(4) NOT NULL,
  `domainpostmaster` varchar(80) NOT NULL,
  `domainmaxsize` int(11) NOT NULL,
  `domainaddomain` varchar(255) NOT NULL,
  `domainmaxmessagesize` int(11) NOT NULL,
  `domainuseplusaddressing` tinyint(4) NOT NULL,
  `domainplusaddressingchar` varchar(1) NOT NULL,
  `domainantispamoptions` int(11) NOT NULL,
  `domainenablesignature` tinyint(4) NOT NULL,
  `domainsignaturemethod` tinyint(4) NOT NULL,
  `domainsignatureplaintext` text NOT NULL,
  `domainsignaturehtml` text NOT NULL,
  `domainaddsignaturestoreplies` tinyint(4) NOT NULL,
  `domainaddsignaturestolocalemail` tinyint(4) NOT NULL,
  `domainmaxnoofaccounts` int(11) NOT NULL,
  `domainmaxnoofaliases` int(11) NOT NULL,
  `domainmaxnoofdistributionlists` int(11) NOT NULL,
  `domainlimitationsenabled` int(11) NOT NULL,
  `domainmaxaccountsize` int(11) NOT NULL,
  `domaindkimselector` varchar(255) NOT NULL,
  `domaindkimprivatekeyfile` varchar(255) NOT NULL,
  PRIMARY KEY (`domainid`),
  UNIQUE KEY `domainid` (`domainid`),
  UNIQUE KEY `domainname` (`domainname`),
  KEY `idx_hm_domains` (`domainname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `hm_domains`
--

INSERT INTO `hm_domains` (`domainid`, `domainname`, `domainactive`, `domainpostmaster`, `domainmaxsize`, `domainaddomain`, `domainmaxmessagesize`, `domainuseplusaddressing`, `domainplusaddressingchar`, `domainantispamoptions`, `domainenablesignature`, `domainsignaturemethod`, `domainsignatureplaintext`, `domainsignaturehtml`, `domainaddsignaturestoreplies`, `domainaddsignaturestolocalemail`, `domainmaxnoofaccounts`, `domainmaxnoofaliases`, `domainmaxnoofdistributionlists`, `domainlimitationsenabled`, `domainmaxaccountsize`, `domaindkimselector`, `domaindkimprivatekeyfile`) VALUES
(1, 'localhost.com', 1, '', 0, '', 0, 0, '', 0, 0, 1, '', '', 0, 0, 0, 0, 0, 0, 0, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `hm_domain_aliases`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_domain_aliases` (
  `daid` int(11) NOT NULL AUTO_INCREMENT,
  `dadomainid` int(11) NOT NULL,
  `daalias` varchar(255) NOT NULL,
  PRIMARY KEY (`daid`),
  UNIQUE KEY `daid` (`daid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_fetchaccounts`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_fetchaccounts` (
  `faid` int(11) NOT NULL AUTO_INCREMENT,
  `faactive` tinyint(4) NOT NULL,
  `faaccountid` int(11) NOT NULL,
  `faaccountname` varchar(255) NOT NULL,
  `faserveraddress` varchar(255) NOT NULL,
  `faserverport` int(11) NOT NULL,
  `faservertype` tinyint(4) NOT NULL,
  `fausername` varchar(255) NOT NULL,
  `fapassword` varchar(255) NOT NULL,
  `faminutes` int(11) NOT NULL,
  `fanexttry` datetime NOT NULL,
  `fadaystokeep` int(11) NOT NULL,
  `falocked` tinyint(4) NOT NULL,
  `faprocessmimerecipients` tinyint(4) NOT NULL,
  `faprocessmimedate` tinyint(4) NOT NULL,
  `fausessl` tinyint(4) NOT NULL,
  `fauseantispam` tinyint(4) NOT NULL,
  `fauseantivirus` tinyint(4) NOT NULL,
  `faenablerouterecipients` tinyint(4) NOT NULL,
  PRIMARY KEY (`faid`),
  UNIQUE KEY `faid` (`faid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_fetchaccounts_uids`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_fetchaccounts_uids` (
  `uidid` int(11) NOT NULL AUTO_INCREMENT,
  `uidfaid` int(11) NOT NULL,
  `uidvalue` varchar(255) NOT NULL,
  `uidtime` datetime NOT NULL,
  PRIMARY KEY (`uidid`),
  UNIQUE KEY `uidid` (`uidid`),
  KEY `idx_hm_fetchaccounts_uids` (`uidfaid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_greylisting_triplets`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_greylisting_triplets` (
  `glid` bigint(20) NOT NULL AUTO_INCREMENT,
  `glcreatetime` datetime NOT NULL,
  `glblockendtime` datetime NOT NULL,
  `gldeletetime` datetime NOT NULL,
  `glipaddress1` bigint(20) NOT NULL,
  `glipaddress2` bigint(20) DEFAULT NULL,
  `glsenderaddress` varchar(255) NOT NULL,
  `glrecipientaddress` varchar(255) NOT NULL,
  `glblockedcount` int(11) NOT NULL,
  `glpassedcount` int(11) NOT NULL,
  PRIMARY KEY (`glid`),
  UNIQUE KEY `glid` (`glid`),
  KEY `idx_greylisting_triplets` (`glipaddress1`,`glipaddress2`,`glsenderaddress`(40),`glrecipientaddress`(40))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_greylisting_whiteaddresses`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_greylisting_whiteaddresses` (
  `whiteid` bigint(20) NOT NULL AUTO_INCREMENT,
  `whiteipaddress` varchar(255) NOT NULL,
  `whiteipdescription` varchar(255) NOT NULL,
  PRIMARY KEY (`whiteid`),
  UNIQUE KEY `whiteid` (`whiteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_groups`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_groups` (
  `groupid` bigint(20) NOT NULL AUTO_INCREMENT,
  `groupname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`groupid`),
  UNIQUE KEY `groupid` (`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_group_members`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_group_members` (
  `memberid` bigint(20) NOT NULL AUTO_INCREMENT,
  `membergroupid` bigint(20) NOT NULL,
  `memberaccountid` bigint(20) NOT NULL,
  PRIMARY KEY (`memberid`),
  UNIQUE KEY `memberid` (`memberid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_imapfolders`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_imapfolders` (
  `folderid` int(11) NOT NULL AUTO_INCREMENT,
  `folderaccountid` int(10) unsigned NOT NULL,
  `folderparentid` int(11) NOT NULL,
  `foldername` varchar(255) NOT NULL,
  `folderissubscribed` tinyint(3) unsigned NOT NULL,
  `foldercreationtime` datetime NOT NULL,
  `foldercurrentuid` bigint(20) NOT NULL,
  PRIMARY KEY (`folderid`),
  UNIQUE KEY `folderid` (`folderid`),
  UNIQUE KEY `idx_hm_imapfolders_unique` (`folderaccountid`,`folderparentid`,`foldername`),
  KEY `idx_hm_imapfolders` (`folderaccountid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `hm_imapfolders`
--

INSERT INTO `hm_imapfolders` (`folderid`, `folderaccountid`, `folderparentid`, `foldername`, `folderissubscribed`, `foldercreationtime`, `foldercurrentuid`) VALUES
(1, 1, -1, 'INBOX', 1, '2012-08-01 21:09:16', 4),
(2, 1, 1, 'Sent', 1, '2012-08-01 21:52:36', 0),
(3, 1, 1, 'Trash', 1, '2012-08-01 21:52:36', 0),
(4, 1, 1, 'Drafts', 1, '2012-08-01 21:52:36', 0);

-- --------------------------------------------------------

--
-- Structure de la table `hm_incoming_relays`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_incoming_relays` (
  `relayid` int(11) NOT NULL AUTO_INCREMENT,
  `relayname` varchar(100) NOT NULL,
  `relaylowerip1` bigint(20) NOT NULL,
  `relaylowerip2` bigint(20) DEFAULT NULL,
  `relayupperip1` bigint(20) NOT NULL,
  `relayupperip2` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`relayid`),
  UNIQUE KEY `relayid` (`relayid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_logon_failures`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_logon_failures` (
  `ipaddress1` bigint(20) NOT NULL,
  `ipaddress2` bigint(20) DEFAULT NULL,
  `failuretime` datetime NOT NULL,
  KEY `idx_hm_logon_failures_ipaddress` (`ipaddress1`,`ipaddress2`),
  KEY `idx_hm_logon_failures_failuretime` (`failuretime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `hm_messagerecipients`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_messagerecipients` (
  `recipientid` bigint(20) NOT NULL AUTO_INCREMENT,
  `recipientmessageid` bigint(20) NOT NULL,
  `recipientaddress` varchar(255) NOT NULL,
  `recipientlocalaccountid` int(11) NOT NULL,
  `recipientoriginaladdress` varchar(255) NOT NULL,
  PRIMARY KEY (`recipientid`),
  UNIQUE KEY `recipientid` (`recipientid`),
  KEY `idx_hm_messagerecipients` (`recipientmessageid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `hm_messagerecipients`
--

INSERT INTO `hm_messagerecipients` (`recipientid`, `recipientmessageid`, `recipientaddress`, `recipientlocalaccountid`, `recipientoriginaladdress`) VALUES
(6, 5, 'localhost', 0, 'localhost'),
(8, 8, 'localhost', 0, 'localhost');

-- --------------------------------------------------------

--
-- Structure de la table `hm_messages`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_messages` (
  `messageid` bigint(20) NOT NULL AUTO_INCREMENT,
  `messageaccountid` int(11) NOT NULL,
  `messagefolderid` int(11) NOT NULL DEFAULT '0',
  `messagefilename` varchar(255) NOT NULL,
  `messagetype` tinyint(4) NOT NULL,
  `messagefrom` varchar(255) NOT NULL,
  `messagesize` bigint(20) NOT NULL,
  `messagecurnooftries` int(11) NOT NULL,
  `messagenexttrytime` datetime NOT NULL,
  `messageflags` tinyint(4) NOT NULL,
  `messagecreatetime` datetime NOT NULL,
  `messagelocked` tinyint(4) NOT NULL,
  `messageuid` bigint(20) NOT NULL,
  PRIMARY KEY (`messageid`),
  UNIQUE KEY `messageid` (`messageid`),
  KEY `idx_hm_messages` (`messageaccountid`,`messagefolderid`),
  KEY `idx_hm_messages_type` (`messagetype`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `hm_messages`
--

INSERT INTO `hm_messages` (`messageid`, `messageaccountid`, `messagefolderid`, `messagefilename`, `messagetype`, `messagefrom`, `messagesize`, `messagecurnooftries`, `messagenexttrytime`, `messageflags`, `messagecreatetime`, `messagelocked`, `messageuid`) VALUES
(2, 1, 1, 'c:\\zpanel\\bin\\hmailserver\\Data\\localhost.localhost\\postmaster\\43\\{43EA8D08-D850-4C2C-9D19-4162D8C01483}.eml', 2, 'postmaster@localhost.localhost', 488, 0, '1901-01-01 00:00:00', 64, '2012-08-01 21:42:09', 0, 1),
(3, 1, 1, 'c:\\zpanel\\bin\\hmailserver\\Data\\localhost.localhost\\postmaster\\3A\\{3A5E1C99-6050-4BD4-A851-697E4A889BAE}.eml', 2, '', 772, 0, '1901-01-01 00:00:00', 0, '2012-08-01 21:42:11', 1, 2),
(5, 0, 0, 'c:\\zpanel\\bin\\hmailserver\\Data\\{C28CC4C0-64BC-45D7-A20A-0683716105A2}.eml', 1, 'postmaster@localhost.com', 682, 1, '2012-08-02 00:51:26', 96, '2012-08-01 23:51:26', 0, 0),
(6, 1, 1, 'c:\\zpanel\\bin\\hmailserver\\Data\\localhost.com\\postmaster\\41\\{41928626-4AAE-4640-A77F-285920167F0D}.eml', 2, 'postmaster@localhost.com', 682, 0, '1901-01-01 00:00:00', 96, '2012-08-01 23:51:26', 0, 3),
(7, 1, 1, 'c:\\zpanel\\bin\\hmailserver\\Data\\localhost.com\\postmaster\\E5\\{E54316D7-D49F-4EC6-9364-4E671CA7118D}.eml', 2, '', 760, 0, '1901-01-01 00:00:00', 32, '2012-08-01 23:51:27', 1, 4),
(8, 0, 0, 'c:\\zpanel\\bin\\hmailserver\\Data\\{B592A359-F34B-450E-84BE-448BEC84596F}.eml', 1, '', 760, 1, '2012-08-02 00:52:26', 32, '2012-08-01 23:51:27', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `hm_message_metadata`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_message_metadata` (
  `metadata_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `metadata_accountid` int(11) NOT NULL,
  `metadata_folderid` int(11) NOT NULL,
  `metadata_messageid` bigint(20) NOT NULL,
  `metadata_dateutc` datetime DEFAULT NULL,
  `metadata_from` varchar(255) NOT NULL,
  `metadata_subject` varchar(255) NOT NULL,
  `metadata_to` varchar(255) NOT NULL,
  `metadata_cc` varchar(255) NOT NULL,
  PRIMARY KEY (`metadata_id`),
  UNIQUE KEY `idx_message_metadata_unique` (`metadata_accountid`,`metadata_folderid`,`metadata_messageid`),
  KEY `idx_message_metadata_id` (`metadata_messageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_routeaddresses`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_routeaddresses` (
  `routeaddressid` mediumint(9) NOT NULL AUTO_INCREMENT,
  `routeaddressrouteid` int(11) NOT NULL,
  `routeaddressaddress` varchar(255) NOT NULL,
  PRIMARY KEY (`routeaddressid`),
  UNIQUE KEY `routeaddressid` (`routeaddressid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_routes`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_routes` (
  `routeid` int(11) NOT NULL AUTO_INCREMENT,
  `routedomainname` varchar(255) NOT NULL,
  `routedescription` varchar(255) NOT NULL,
  `routetargetsmthost` varchar(255) NOT NULL,
  `routetargetsmtport` int(11) NOT NULL,
  `routenooftries` int(11) NOT NULL,
  `routeminutesbetweentry` int(11) NOT NULL,
  `routealladdresses` tinyint(3) unsigned NOT NULL,
  `routeuseauthentication` tinyint(4) NOT NULL,
  `routeauthenticationusername` varchar(255) NOT NULL,
  `routeauthenticationpassword` varchar(255) NOT NULL,
  `routetreatsecurityaslocal` tinyint(4) NOT NULL,
  `routeusessl` tinyint(4) NOT NULL,
  `routetreatsenderaslocaldomain` tinyint(4) NOT NULL,
  PRIMARY KEY (`routeid`),
  UNIQUE KEY `routeid` (`routeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_rules`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_rules` (
  `ruleid` int(11) NOT NULL AUTO_INCREMENT,
  `ruleaccountid` int(11) NOT NULL,
  `rulename` varchar(100) NOT NULL,
  `ruleactive` tinyint(4) NOT NULL,
  `ruleuseand` tinyint(4) NOT NULL,
  `rulesortorder` int(11) NOT NULL,
  PRIMARY KEY (`ruleid`),
  UNIQUE KEY `ruleid` (`ruleid`),
  KEY `idx_rules` (`ruleaccountid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_rule_actions`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_rule_actions` (
  `actionid` int(11) NOT NULL AUTO_INCREMENT,
  `actionruleid` int(11) NOT NULL,
  `actiontype` tinyint(4) NOT NULL,
  `actionimapfolder` varchar(255) NOT NULL,
  `actionsubject` varchar(255) NOT NULL,
  `actionfromname` varchar(255) NOT NULL,
  `actionfromaddress` varchar(255) NOT NULL,
  `actionto` varchar(255) NOT NULL,
  `actionbody` text NOT NULL,
  `actionfilename` varchar(255) NOT NULL,
  `actionsortorder` int(11) NOT NULL,
  `actionscriptfunction` varchar(255) NOT NULL,
  `actionheader` varchar(80) NOT NULL,
  `actionvalue` varchar(255) NOT NULL,
  `actionrouteid` int(11) NOT NULL,
  PRIMARY KEY (`actionid`),
  UNIQUE KEY `actionid` (`actionid`),
  KEY `idx_rules_actions` (`actionruleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_rule_criterias`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_rule_criterias` (
  `criteriaid` int(11) NOT NULL AUTO_INCREMENT,
  `criteriaruleid` int(11) NOT NULL,
  `criteriausepredefined` tinyint(4) NOT NULL,
  `criteriapredefinedfield` tinyint(4) NOT NULL,
  `criteriaheadername` varchar(255) NOT NULL,
  `criteriamatchtype` tinyint(4) NOT NULL,
  `criteriamatchvalue` varchar(255) NOT NULL,
  PRIMARY KEY (`criteriaid`),
  UNIQUE KEY `criteriaid` (`criteriaid`),
  KEY `idx_rules_criterias` (`criteriaruleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_securityranges`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_securityranges` (
  `rangeid` int(11) NOT NULL AUTO_INCREMENT,
  `rangepriorityid` int(11) NOT NULL,
  `rangelowerip1` bigint(20) NOT NULL,
  `rangelowerip2` bigint(20) DEFAULT NULL,
  `rangeupperip1` bigint(20) NOT NULL,
  `rangeupperip2` bigint(20) DEFAULT NULL,
  `rangeoptions` int(11) NOT NULL,
  `rangename` varchar(100) NOT NULL,
  `rangeexpires` tinyint(4) NOT NULL,
  `rangeexpirestime` datetime NOT NULL,
  PRIMARY KEY (`rangeid`),
  UNIQUE KEY `rangeid` (`rangeid`),
  UNIQUE KEY `rangename` (`rangename`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `hm_securityranges`
--

INSERT INTO `hm_securityranges` (`rangeid`, `rangepriorityid`, `rangelowerip1`, `rangelowerip2`, `rangeupperip1`, `rangeupperip2`, `rangeoptions`, `rangename`, `rangeexpires`, `rangeexpirestime`) VALUES
(4, 15, 2130706433, NULL, 2130706433, NULL, 71627, 'My computer', 0, '2001-01-01 00:00:00'),
(5, 10, 0, NULL, 4294967295, NULL, 96203, 'Internet', 0, '2001-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `hm_servermessages`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_servermessages` (
  `smid` int(11) NOT NULL AUTO_INCREMENT,
  `smname` varchar(255) NOT NULL,
  `smtext` text NOT NULL,
  PRIMARY KEY (`smid`),
  UNIQUE KEY `smid` (`smid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `hm_servermessages`
--

INSERT INTO `hm_servermessages` (`smid`, `smname`, `smtext`) VALUES
(1, 'VIRUS_FOUND', 'Virus found'),
(2, 'VIRUS_ATTACHMENT_REMOVED', 'Virus found:\r\nThe attachment(s) of this message was removed since a virus was detected in at least one of them.\r\n\r\n'),
(3, 'VIRUS_NOTIFICATION', 'The message below contained a virus and did not\r\nreach some or all of the intended recipients.\r\n\r\n   From: %MACRO_FROM%\r\n   To: %MACRO_TO%\r\n   Sent: %MACRO_SENT%\r\n   Subject: %MACRO_SUBJECT%\r\n\r\nhMailServer\r\n'),
(4, 'SEND_FAILED_NOTIFICATION', 'Your message did not reach some or all of the intended recipients.\r\n\r\n   Sent: %MACRO_SENT%\r\n   Subject: %MACRO_SUBJECT%\r\n\r\nThe following recipient(s) could not be reached:\r\n\r\n%MACRO_RECIPIENTS%\r\n\r\nhMailServer\r\n'),
(5, 'MESSAGE_UNDELIVERABLE', 'Message undeliverable'),
(6, 'MESSAGE_FILE_MISSING', 'The mail server could not deliver the message to you since the file %MACRO_FILE% does not exist on the server.\r\n\r\nThe file may have been deleted by anti virus software running on the server.\r\n\r\nhMailServer'),
(7, 'ATTACHMENT_REMOVED', 'The attachment %MACRO_FILE% was blocked for delivery by the e-mail server. Please contact your system administrator if you have any questions regarding this.\r\n\r\nhMailServer\r\n');

-- --------------------------------------------------------

--
-- Structure de la table `hm_settings`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_settings` (
  `settingid` int(11) NOT NULL AUTO_INCREMENT,
  `settingname` varchar(30) NOT NULL,
  `settingstring` varchar(255) NOT NULL,
  `settinginteger` int(11) NOT NULL,
  PRIMARY KEY (`settingid`),
  UNIQUE KEY `settingid` (`settingid`),
  UNIQUE KEY `settingname` (`settingname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=107 ;

--
-- Contenu de la table `hm_settings`
--

INSERT INTO `hm_settings` (`settingid`, `settingname`, `settingstring`, `settinginteger`) VALUES
(1, 'maxpop3connections', '', 0),
(2, 'maxsmtpconnections', '', 0),
(3, 'mirroremailaddress', '', 0),
(4, 'relaymode', '', 2),
(5, 'authallowplaintext', '', 0),
(6, 'allowmailfromnull', '', 1),
(7, 'logging', '', 0),
(8, 'logdevice', '', 0),
(11, 'smtpnoofretries', '', 4),
(12, 'smtpminutesbetweenretries', '', 60),
(13, 'protocolimap', '', 1),
(14, 'protocolsmtp', '', 1),
(15, 'protocolpop3', '', 1),
(16, 'welcomeimap', '', 0),
(17, 'welcomepop3', '', 0),
(18, 'welcomesmtp', '', 0),
(19, 'smtprelayer', '', 0),
(20, 'maxdelivertythreads', '', 10),
(21, 'logformat', '', 0),
(22, 'avclamwinenable', '', 0),
(23, 'avclamwinexec', '', 0),
(24, 'avclamwindb', '', 0),
(25, 'avnotifysender', '', 0),
(26, 'avnotifyreceiver', '', 0),
(27, 'avaction', '', 0),
(28, 'sendstatistics', '', 0),
(29, 'hostname', '', 0),
(30, 'smtprelayerusername', '', 0),
(31, 'smtprelayerpassword', '', 0),
(32, 'usesmtprelayerauthentication', '', 0),
(33, 'smtprelayerport', '', 25),
(34, 'usecustomvirusscanner', '', 0),
(35, 'customvirusscannerexecutable', '', 0),
(36, 'customviursscannerreturnvalue', '', 0),
(37, 'usespf', '', 0),
(38, 'usemxchecks', '', 0),
(39, 'usescriptserver', '', 0),
(40, 'scriptlanguage', 'VBScript', 0),
(41, 'maxmessagesize', '', 20480),
(42, 'usecache', '', 1),
(43, 'domaincachettl', '', 60),
(44, 'accountcachettl', '', 60),
(45, 'awstatsenabled', '', 0),
(46, 'rulelooplimit', '', 5),
(47, 'backupoptions', '', 0),
(48, 'backupdestination', '', 0),
(49, 'defaultdomain', '', 0),
(50, 'avmaxmsgsize', '', 0),
(51, 'smtpdeliverybindtoip', '', 0),
(52, 'enableimapquota', '', 1),
(53, 'enableimapidle', '', 1),
(54, 'enableimapacl', '', 1),
(55, 'maximapconnections', '', 0),
(56, 'enableimapsort', '', 1),
(57, 'workerthreadpriority', '', 0),
(58, 'ascheckhostinhelo', '', 0),
(59, 'tcpipthreads', '', 15),
(60, 'smtpallowincorrectlineendings', '', 1),
(61, 'usegreylisting', '', 0),
(62, 'greylistinginitialdelay', '', 30),
(63, 'greylistinginitialdelete', '', 24),
(64, 'greylistingfinaldelete', '', 864),
(65, 'antispamaddheaderspam', '', 1),
(66, 'antispamaddheaderreason', '', 1),
(67, 'antispamprependsubject', '', 0),
(68, 'antispamprependsubjecttext', '[SPAM]', 0),
(69, 'enableattachmentblocking', '', 0),
(70, 'maxsmtprecipientsinbatch', '', 100),
(71, 'disconnectinvalidclients', '', 0),
(72, 'maximumincorrectcommands', '', 100),
(73, 'aliascachettl', '', 60),
(74, 'distributionlistcachettl', '', 60),
(75, 'smtprelayerusessl', '', 0),
(76, 'adddeliveredtoheader', '', 0),
(77, 'groupcachettl', '', 60),
(78, 'imappublicfoldername', '#Public', 0),
(79, 'antispamenabled', '', 0),
(80, 'usespfscore', '', 3),
(81, 'ascheckhostinheloscore', '', 2),
(82, 'usemxchecksscore', '', 2),
(83, 'spammarkthreshold', '', 5),
(84, 'spamdeletethreshold', '', 20),
(85, 'spamassassinenabled', '', 1),
(86, 'spamassassinscore', '', 5),
(87, 'spamassassinmergescore', '', 0),
(88, 'spamassassinhost', '127.0.0.1', 0),
(89, 'spamassassinport', '', 783),
(90, 'antispammaxsize', '', 1024),
(91, 'ASDKIMVerificationEnabled', '', 0),
(92, 'ASDKIMVerificationFailureScore', '', 5),
(93, 'AutoBanOnLogonFailureEnabled', '', 1),
(94, 'MaxInvalidLogonAttempts', '', 3),
(95, 'LogonAttemptsWithinMinutes', '', 30),
(96, 'AutoBanMinutes', '', 60),
(97, 'IMAPHierarchyDelimiter', '.', 0),
(98, 'MaxNumberOfAsynchronousTasks', '', 15),
(99, 'MessageIndexing', '', 0),
(100, 'BypassGreylistingOnSPFSuccess', '', 1),
(101, 'BypassGreylistingOnMailFromMX', '', 0),
(102, 'EnableWhitelisting', '', 1),
(103, 'MaxNumberOfMXHosts', '', 15),
(104, 'ClamAVEnabled', '', 0),
(105, 'ClamAVHost', 'localhost', 0),
(106, 'ClamAVPort', '', 3310);

-- --------------------------------------------------------

--
-- Structure de la table `hm_sslcertificates`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_sslcertificates` (
  `sslcertificateid` bigint(20) NOT NULL AUTO_INCREMENT,
  `sslcertificatename` varchar(255) NOT NULL,
  `sslcertificatefile` varchar(255) NOT NULL,
  `sslprivatekeyfile` varchar(255) NOT NULL,
  PRIMARY KEY (`sslcertificateid`),
  UNIQUE KEY `sslcertificateid` (`sslcertificateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `hm_surblservers`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_surblservers` (
  `surblid` int(11) NOT NULL AUTO_INCREMENT,
  `surblactive` tinyint(4) NOT NULL,
  `surblhost` varchar(255) NOT NULL,
  `surblrejectmessage` varchar(255) NOT NULL,
  `surblscore` int(11) NOT NULL,
  PRIMARY KEY (`surblid`),
  UNIQUE KEY `surblid` (`surblid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `hm_surblservers`
--

INSERT INTO `hm_surblservers` (`surblid`, `surblactive`, `surblhost`, `surblrejectmessage`, `surblscore`) VALUES
(1, 0, 'multi.surbl.org', 'Rejected by SURBL.', 3);

-- --------------------------------------------------------

--
-- Structure de la table `hm_tcpipports`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_tcpipports` (
  `portid` bigint(20) NOT NULL AUTO_INCREMENT,
  `portprotocol` tinyint(4) NOT NULL,
  `portnumber` int(11) NOT NULL,
  `portaddress1` bigint(20) NOT NULL,
  `portaddress2` bigint(20) DEFAULT NULL,
  `portusessl` tinyint(4) NOT NULL,
  `portsslcertificateid` bigint(20) NOT NULL,
  PRIMARY KEY (`portid`),
  UNIQUE KEY `portid` (`portid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `hm_tcpipports`
--

INSERT INTO `hm_tcpipports` (`portid`, `portprotocol`, `portnumber`, `portaddress1`, `portaddress2`, `portusessl`, `portsslcertificateid`) VALUES
(1, 1, 25, 0, NULL, 0, 0),
(2, 3, 110, 0, NULL, 0, 0),
(3, 5, 143, 0, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `hm_whitelist`
--
-- Création: Ven 03 Août 2012 à 13:14
--

CREATE TABLE IF NOT EXISTS `hm_whitelist` (
  `whiteid` bigint(20) NOT NULL AUTO_INCREMENT,
  `whiteloweripaddress1` bigint(20) NOT NULL,
  `whiteloweripaddress2` bigint(20) DEFAULT NULL,
  `whiteupperipaddress1` bigint(20) NOT NULL,
  `whiteupperipaddress2` bigint(20) DEFAULT NULL,
  `whiteemailaddress` varchar(255) NOT NULL,
  `whitedescription` varchar(255) NOT NULL,
  PRIMARY KEY (`whiteid`),
  UNIQUE KEY `whiteid` (`whiteid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
