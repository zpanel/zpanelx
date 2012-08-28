<?php
/*
############################################################
# Enviroment configuration script for ZPI                  #
# Developedy by Bobby Allen, 18th November 2009            #
############################################################
# Last updated by Bobby Allen 09/03/2012                   #
############################################################
 */

fwrite(STDOUT, "\r
##################################################\r
# ZPANELX CONFIG WIZARD FOR WINDOWS              #\r
##################################################\r");

// ZPanel version (Sent to ZPanel)
$version = "10.0.0a";

// Set default MySQL account details etc...
$hostname_db = "localhost";
$username_db = "root";
$password_db = "";
$db = mysql_pconnect($hostname_db, $username_db, $password_db) or trigger_error('Unable to connect to database server.');

// Generate two random passwords...
$p1 = substr(md5(uniqid(rand(), 1)), 3, 22);
$p2 = substr(md5(uniqid(rand(), 1)), 3, 6);
$p3 = substr(md5(uniqid(rand(), 1)), 3, 22);

// Set MySQL ROOT password to a random password and display to user!
fwrite(STDOUT, "\rConfiguring MySQL 'root' password...\r\r");
$sql = "SET PASSWORD FOR `root`@`localhost`=PASSWORD('" . $p1 . "')";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "FLUSH PRIVILEGES;";
$resault = @mysql_query($sql, $db) or die(mysql_error());

// Create system.php file for database access:-
$db_settings_file = fopen("G:/zpanel/panel/modules/webmail/apps/roundcube/config/db.inc.php", "w");
fwrite($db_settings_file, "<?php
\$rcmail_config = array();
\$rcmail_config['db_dsnw'] = 'mysql://webmail:" . $p3 . "@localhost/zpanel_roundcube';
\$rcmail_config['db_dsnr'] = '';
\$rcmail_config['db_max_length'] = 512000;
\$rcmail_config['db_persistent'] = FALSE;
\$rcmail_config['db_table_users'] = 'users';
\$rcmail_config['db_table_identities'] = 'identities';
\$rcmail_config['db_table_contacts'] = 'contacts';
\$rcmail_config['db_table_session'] = 'session';
\$rcmail_config['db_table_cache'] = 'cache';
\$rcmail_config['db_table_messages'] = 'messages';
\$rcmail_config['db_sequence_users'] = 'user_ids';
\$rcmail_config['db_sequence_identities'] = 'identity_ids';
\$rcmail_config['db_sequence_contacts'] = 'contact_ids';
\$rcmail_config['db_sequence_cache'] = 'cache_ids';
\$rcmail_config['db_sequence_messages'] = 'message_ids';
");
fclose($db_settings_file);

// Now we connect with the correct username and password as we just reset it...
$hostname_db = "localhost";
$username_db = "root";
$password_db = $p1;
$db = mysql_pconnect($hostname_db, $username_db, $password_db) or trigger_error('Unable to connect to database server.');

// Create databases (zpanel_core, zpanel_roundcube and zpanel_hmail)
fwrite(STDOUT, "Creating databases...\r");
$sql = "CREATE DATABASE `zpanel_roundcube` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "CREATE DATABASE `zpanel_hmail` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = 'CREATE DATABASE IF NOT EXISTS `zpanel_atmail`';
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = 'CREATE DATABASE IF NOT EXISTS `zpanel_AfterLogic`';
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "CREATE USER 'webmail'@'localhost' IDENTIFIED BY ''";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "GRANT USAGE ON * . * TO 'webmail'@'localhost' IDENTIFIED BY ''";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "GRANT ALL PRIVILEGES ON `zpanel_atmail` . * TO 'webmail'@'localhost'";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "GRANT ALL PRIVILEGES ON `zpanel_AfterLogic` . * TO 'webmail'@'localhost'";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "SET PASSWORD FOR `webmail`@`localhost`=PASSWORD('" . $p3 . "')";
$resault = @mysql_query($sql, $db) or die(mysql_error());
$sql = "GRANT ALL PRIVILEGES ON `zpanel_roundcube` . * TO 'webmail'@'localhost'";

// SQL script executor...

function RunSQL($sqlFileToExecute) {
    $f = fopen($sqlFileToExecute, "r+");
    $sqlFile = fread($f, filesize($sqlFileToExecute));
    $sqlArray = explode(';', $sqlFile);
    foreach ($sqlArray as $stmt) {
        if (strlen($stmt) > 3) {
            $result = mysql_query($stmt);
            if (!$result) {
                $sqlErrorCode = mysql_errno();
                $sqlErrorText = mysql_error();
                $sqlStmt = $stmt;
                break;
            }
        }
    }
}

// Get the 'true' server IP address.

function GetServerIPFromZWS() {
    $response = @file_get_contents('http://api.zpanelcp.com/ip.json');
    $decoded = json_decode($response, true);
    if ($decoded['ipaddress']) {
        return $decoded['ipaddress'];
    } else {
        return "127.0.0.1";
    }
}

// Insert Roundcube inital SQL into the zpanel_roundcube database.
mysql_select_db('zpanel_roundcube', $db);
$sqlFileToExecute = "G:/zpanel/panel/modules/webmail/apps/roundcube/SQL/mysql.initial.sql";
$res = RunSQL($sqlFileToExecute);

// Insert hMailServer inital SQL into the zpanel_hmail database.
mysql_select_db('zpanel_hmail', $db);
$sqlFileToExecute = "G:/zpanel/bin/hmailserver/INSTALL/zpanel_hmail.sql";
$res = RunSQL($sqlFileToExecute);

mysql_select_db('zpanel_afterlogic', $db);
$sqlFileToExecute = "G:/zpanel/panel/modules/webmail/install/zpanel_afterlogic.sql";
$res = RunSQL($sqlFileToExecute);

mysql_select_db('zpanel_atmail', $db);
$sqlFileToExecute = "G:/zpanel/panel/modules/webmail/install/zpanel_atmail.sql";
$res = RunSQL($sqlFileToExecute);


// Set database back to ZPanel core to continue with the install.
@mysql_select_db('zpanel_core', $db);


// Ask user what domain they will be hosting the control panel on and then create it and add entries to the hosts file...
fwrite(STDOUT, "\r
##################################################\r
# ZPANELX CONFIG WIZARD FOR WINDOWS              #\r
##################################################\r
\r
Please enter details when asked below for the main\r
admin account.\r
\r
Full name: ");
$fullname = trim(fgets(STDIN));
fwrite(STDOUT, "Email address: ");
$email = trim(fgets(STDIN));
fwrite(STDOUT, "\r\r
Please now tell us where you want to access your\r
control panel from (eg. zpanel.yourdomain.com)\r
this should be a domain or sub-domain (FQDN).\r
\r\r
FQDN: ");
$location = trim(fgets(STDIN));

fwrite(STDOUT, "\r\r");
@mysql_select_db('zpanel_core', $db);
exec("setso --set dbversion " . $version . "");
exec("setso --set zpanel_domain " . $location . "");
exec("setso --set email_from_address " . $email . "");
exec("setso --set email_from_address " . $email . "");
exec("setso --set daemon_lastrun 0");
exec("setso --set daemon_dayrun 0");
exec("setso --set daemon_weekrun 0");
exec("setso --set daemon_monthrun 0");
exec("setso --set apache_changed true");
exec("setso --set server_ip " . GetServerIPFromZWS() . "");

@mysql_select_db('zpanel_core', $db);
// We now update the MySQL user for the default 'zadmin' account..
$log = "UPDATE x_accounts SET ac_pass_vc='" . md5($p2) . "', ac_email_vc='" . $email . "', ac_created_ts=" . time() . " WHERE ac_user_vc='zadmin'";
$do = @mysql_query($log, $db) or die(mysql_error());

// Now we add the server admin to the server admin database table..
$sql = "UPDATE x_profiles SET ud_created_ts=" . time() . ", ud_fullname_vc='" . $fullname . "' WHERE ud_user_fk=1;";
mysql_query($sql, $db);


// Create hMailServer.INI for hMailServer MySQL configuration:-
$db_settings_file = @fopen("G:/zpanel/bin/hmailserver/Bin/hMailServer.ini", "w");
fwrite($db_settings_file, "\r
################################################################\r
# hMailServer configuration file                               #\r
# Automatically generated by ZPanelX installer for Windows     #\r
################################################################\r
\r");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "[Directories]\r\n");
fwrite($db_settings_file, "ProgramFolder=G:\zpanel\bin\hmailserver\r\n");
fwrite($db_settings_file, "DataFolder=G:\zpanel\bin\hmailserver\Data\r\n");
fwrite($db_settings_file, "LogFolder=G:\zpanel\logs\r\n");
fwrite($db_settings_file, "TempFolder=G:\zpanel\bin\hmailserver\Temp\r\n");
fwrite($db_settings_file, "EventFolder=G:\zpanel\bin\hmailserver\Events\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "[GUILanguages]\r\n");
fwrite($db_settings_file, "ValidLanguages=english,swedish,chinese,czech,danish,dutch,finnish,french,german,greek,hebrew,hindi,hungarian,icelandic,indonesian,italian,japanese,korean,lithuanian,macedonian,norwegian,polish,portuguesebrazilian,portugueseportugal,romanian,russian,serbian,slovak,slovenian,spanish,taiwanese,thai,turkish,ukrainian\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "[Database]\r\n");
fwrite($db_settings_file, "Type=MYSQL\r\n");
fwrite($db_settings_file, "Username=root\r\n");
fwrite($db_settings_file, "Password=" . $p1 . "\r\n");
fwrite($db_settings_file, "PasswordEncryption=0\r\n");
fwrite($db_settings_file, "Port=3306\r\n");
fwrite($db_settings_file, "Server=localhost\r\n");
fwrite($db_settings_file, "Database=zpanel_hmail\r\n");
fwrite($db_settings_file, "Internal=0\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "[Security]\r\n");
fwrite($db_settings_file, "AdministratorPassword=" . md5($p2) . "\r\n");
fclose($db_settings_file);

fwrite(STDOUT, "\r
################################################################\r
# YOUR ZPANEL SERVER LOGIN DETAILS                             #\r
################################################################\r
\r
\r
Your new MySQL 'root' password is: " . $p1 . "\r
\r
Your new AfterLogic adminpanel 'mailadmin' password is: 080e5a52\r
\r
URL: http://" . $location . "/modules/webmail/apps/AfterLogic/adminpanel/\r
Your new ZPanel details are as follows:-\r
\r
URL: http://" . $location . "/\r
Username: zadmin\r
Password: " . $p2 . "\r
\r
These details can also be found in G:\zpanel\login_details.txt\r
\r
Thank you for installing ZPanel!\r\r");

// Now we add a static route so the server admin can instantly access the control panel, and reboot Apache so VHOST is activated.
exec("G:/zpanel/bin/zpss/setroute.exe " . $location . "");

// Add the password details to a file in G:\zpanel
$db_settings_file = fopen("G:/zpanel/login_details.txt", "w");
fwrite($db_settings_file, "MySQL Root Password: " .$p1. "\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "Your new AfterLogic adminpanel 'mailadmin' password is: " .$p2. "\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "URL: http://" . $location . "/modules/webmail/apps/AfterLogic/adminpanel/\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "ZPanel URL: http://" .$location. "\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "ZPanel account: zadmin\r\n");
fwrite($db_settings_file, "\r\n");
fwrite($db_settings_file, "ZPanel password: " .$p2. "");
fclose($db_settings_file);
$afterLogicsettings = @fopen("G:/zpanel/panel/modules/webmail/apps/AfterLogic/data/settings/settings.xml", "w");
fwrite($afterLogicsettings, "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<Settings xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
	<Common>
		<!-- Default title that will be shown in browser's header (Default domain settings). -->
		<SiteName>AfterLogic WebMail Lite</SiteName>
		<!-- License key is supplied here. -->
		<LicenseKey />
		<AdminLogin>mailadmin</AdminLogin>
		<AdminPassword>" . $p2 . "</AdminPassword>
		<DBType>MySQL</DBType>
		<DBPrefix>AfterLogic</DBPrefix>
		<DBHost>localhost</DBHost>
		<DBName>zpanel_AfterLogic</DBName>
		<DBLogin>webmail</DBLogin>
		<DBPassword>" . $p3 . "</DBPassword>
		<UseSlaveConnection>Off</UseSlaveConnection>
		<DBSlaveHost>127.0.0.1</DBSlaveHost>
		<DBSlaveName />
		<DBSlaveLogin>root</DBSlaveLogin>
		<DBSlavePassword />
		<DefaultLanguage>English</DefaultLanguage>
		<DefaultTimeZone>29</DefaultTimeZone>
		<DefaultTimeFormat>F24</DefaultTimeFormat>
		<AllowRegistration>Off</AllowRegistration>
		<AllowPasswordReset>Off</AllowPasswordReset>
		<EnableLogging>Off</EnableLogging>
		<EnableEventLogging>Off</EnableEventLogging>
		<LoggingLevel>Full</LoggingLevel>
		<EnableMobileSync>Off</EnableMobileSync>
	</Common>
	<WebMail>
		<AllowWebMail>On</AllowWebMail>
		<IncomingMailProtocol>IMAP4</IncomingMailProtocol>
		<IncomingMailServer>127.0.0.1</IncomingMailServer>
		<IncomingMailPort>143</IncomingMailPort>
		<IncomingMailUseSSL>Off</IncomingMailUseSSL>
		<OutgoingMailServer>127.0.0.1</OutgoingMailServer>
		<OutgoingMailPort>25</OutgoingMailPort>
		<OutgoingMailAuth>AuthCurrentUser</OutgoingMailAuth>
		<OutgoingMailLogin />
		<OutgoingMailPassword />
		<OutgoingMailUseSSL>Off</OutgoingMailUseSSL>
		<OutgoingSendingMethod>Specified</OutgoingSendingMethod>
		<UserQuota>0</UserQuota>
		<AutoCheckMailInterval>0</AutoCheckMailInterval>
		<DefaultSkin>AfterLogic</DefaultSkin>
		<MailsPerPage>20</MailsPerPage>
		<EnableMailboxSizeLimit>Off</EnableMailboxSizeLimit>
		<MailboxSizeLimit>0</MailboxSizeLimit>
		<TakeImapQuota>On</TakeImapQuota>
		<AllowUsersChangeInterfaceSettings>On</AllowUsersChangeInterfaceSettings>
		<AllowUsersChangeEmailSettings>On</AllowUsersChangeEmailSettings>
		<EnableAttachmentSizeLimit>Off</EnableAttachmentSizeLimit>
		<AttachmentSizeLimit>10240000</AttachmentSizeLimit>
		<AllowLanguageOnLogin>On</AllowLanguageOnLogin>
		<FlagsLangSelect>Off</FlagsLangSelect>
		<LoginFormType>Email</LoginFormType>
		<UseLoginAsEmailAddress>On</UseLoginAsEmailAddress>
		<LoginAtDomainValue />
		<DefaultDomainValue />
		<UseAdvancedLogin>Off</UseAdvancedLogin>
		<UseCaptcha>Off</UseCaptcha>
		<UseReCaptcha>Off</UseReCaptcha>
		<AllowNewUsersRegister>On</AllowNewUsersRegister>
		<AllowUsersAddNewAccounts>Off</AllowUsersAddNewAccounts>
		<AllowIdentities>Off</AllowIdentities>
		<StoreMailsInDb>Off</StoreMailsInDb>
		<AllowInsertImage>On</AllowInsertImage>
		<AllowBodySize>Off</AllowBodySize>
		<MaxBodySize>600</MaxBodySize>
		<MaxSubjectSize>255</MaxSubjectSize>
		<Layout>Side</Layout>
		<AlwaysShowImagesInMessage>Off</AlwaysShowImagesInMessage>
		<SaveMail>Always</SaveMail>
		<IdleSessionTimeout>0</IdleSessionTimeout>
		<UseSortImapForDateMode>Off</UseSortImapForDateMode>
		<DetectSpecialFoldersWithXList>On</DetectSpecialFoldersWithXList>
		<EnableLastLoginNotification>Off</EnableLastLoginNotification>
	</WebMail>
	<Calendar>
		<AllowCalendar>Off</AllowCalendar>
		<ShowWeekEnds>Off</ShowWeekEnds>
		<WorkdayStarts>9</WorkdayStarts>
		<WorkdayEnds>18</WorkdayEnds>
		<ShowWorkDay>On</ShowWorkDay>
		<WeekStartsOn>Monday</WeekStartsOn>
		<DefaultTab>Month</DefaultTab>
		<AllowReminders>On</AllowReminders>
		<DAVUrl />
	</Calendar>
	<Contacts>
		<AllowContacts>On</AllowContacts>
		<ContactsPerPage>20</ContactsPerPage>
		<PersonalAddressBook>
			<Mode>Sql</Mode>
		</PersonalAddressBook>
		<ShowGlobalContactsInAddressBook>Off</ShowGlobalContactsInAddressBook>
		<GlobalAddressBook>
			<Mode>Off</Mode>
			<Sql>
				<Visibility>Off</Visibility>
			</Sql>
		</GlobalAddressBook>
	</Contacts>
	<StorageTypes>
		<MailSuite>db</MailSuite>
		<Db>db</Db>
		<Domains>db</Domains>
		<Subadmins>db</Subadmins>
		<Contacts>db</Contacts>
		<Users>db</Users>
		<WebMail>db</WebMail>
		<Calendar>sabredav</Calendar>
	</StorageTypes>
</Settings>
");
fclose($afterLogicsettings);

$atmailconfig = @fopen("G:/zpanel/panel/modules/webmail/apps/atmail/libs/Atmail/config.php", "w");
fwrite($atmailconfig, "<?php

\$pref = array (
  'debug_sql' => 0,
  'aspell_path' => NULL,
  'addressbook_ldap_entries' => '0',
  'autocomplete_ldap_entries' => '0',
  'imap_sort_extension' => '1',
  'imap_sort_charset' => 'us-ascii',
  'quota_bar' => '1',
  'quota_alert' => '1',
  'quota_alert_over' => '90',
  'quota_alert_html' => '<p style=\"font-weight:bold;text-align:center;font-size:24px;\">
YOUR QUOTA IS NEARLY EXHAUSTED - PLEASE DELETE UNNECESSARY ITEMS
</p>
<p style=\"text-align:center;font-size:18px;\"> 
You will be unable to receive or send any messages once you have exhausted your quota.
</p>',
  'plesk' => 0,
  'opensource' => 1,
  'decode_tnef' => 0,
  'tnef_path' => '',
  'AbookLimitOverride' => 1000,
  'EmailDefaultDomain' => '',
  'filter_awl_support' => '1',
  'crypt' => 0,
  'allowed_mailservers' => '',
  'large_domains' => '',
  'default_domain' => '',
  'filter_skip_trusted' => '1',
  'filter_auto_dl_av' => '1',
  'filter_awl_purge_high' => '30',
  'filter_awl_purge_medium' => '14',
  'filter_awl_purge_low' => '7',
  'filter_uridns_support' => '1',
  'openssl_path' => '',
  'filter_spf_support' => '1',
  'smtpauth_password' => '',
  'smtpauth_username' => '',
  'pop3imap_authdaemons' => '5',
  'login_rememberme' => 1,
  'allowed_domains' => '',
  'smtp_popimaprelay_timeout' => '60',
  'pop3imap_querytype' => 'group',
  'debug_imap_file_size_limit' => 10000,
  'atmail_root' => '/mail',
  'memory_limit' => 128,
  'error_log' => 'logs/error_log',
  'smtp_max_rcpt' => '100',
  'ispell_german' => '',
  'imap_idle' => '60',
  'allow_Sync' => '0',
  'login_preselect' => '1',
  'session_timeout' => '7200',
  'allow_Folders' => '1',
  'imap_ip' => '0',
  'popimap_debug' => NULL,
  'popimap_debug_file' => 'logs/popimap_debug',
  'error_overquota' => 'Email Message Error -

********** Sorry, the message could not be delivered **********

USER IS OVER THE QUOTA - The users email quota has exceeded. The message could not be delivered. Please try again later.
  ',
  'filter_bayes_auto_learn_threshold_spam' => '10.0',
  'smtp_smssupport' => '1',
  'logo_big_alt' => 'WebMail System',
  'filter_blocked_attachments' => '.exe,.pif,.bat,.scr,.lnk,.com,',
  'gpg_path' => '',
  'pallow_FaxWork' => '1',
  'queue_run_max' => '20',
  'welcome_msg' => 'html/welcome_msg.html',
  'Price' => '',
  'mail_group_support' => '1',
  'pop3_ip' => '0',
  'allow_VideoMail' => '1',
  'videomail_server' => 'video.atmail.com',
  'websync_permissions' => 'All Users',
  'imapfolder_cache' => '0',
  'allow_FontStyle' => '1',
  'openssl_CApath' => '/usr/local/atmail/webmail/modules/ca-Atmail.crt',
  'remote_max_parallel' => '20',
  'logo_big_img' => 'imgs/about.png',
  'allow_Language' => '1',
  'mailserver_auth' => '1',
  'disclaimer' => 'html/disclaimer.html',
  'downloadid' => '',
  'allow_HtmlEditor' => '1',
  'filter_use_bayes' => '1',
  'datetime' => '1',
  'allow_Signature' => '1',
  'pallow_FirstName' => '1',
  'imap_subdirectory' => NULL,
  'ssl_certfile_pop3' => '/usr/local/atmail/mailserver/share/pop3d.pem',
  'smtp_throttle' => '1',
  'ldap_chserver' => '1',
  'pallow_Country' => '1',
  'install_type' => 'standalone',
  'sql_host' => 'localhost',
  'allow_EmailEncoding' => 1,
  'version' => '1.05',
  'virus_scanner' => '/usr/local/atmail/av/clamdsocket',
  'pallow_Address' => '1',
  'sql_mysqlversion' => 5,
  'ssl_ip' => '0',
  'virus_msg' => 'Virus \$malware_name detected. Mail delivery avoided.',
  'pallow_TelHome' => '1',
  'allow_BlockEmailAddress' => '1',
  'login_defaultinterface' => NULL,
  'ispell_portuguese' => '',
  'pallow_PostCode' => '1',
  'imap_max' => '40',
  'logo_small_img' => 'imgs/logo_simple_head.png',
  'windows' => '1',
  'sql_pass' => '" . $p3 . "',
  'allow_AskQuestion' => '1',
  'brandname' => 'Atmail Open',
  'allow_AutoComplete' => '1',
  'pallow_PasswordQuestion' => '1',
  'allow_EmptyTrash' => '1',
  'ssl_cache' => '1',
  'filter_max_msgs' => '100',
  'ssl_certfile_imap' => '/usr/local/atmail/mailserver/share/imapd.pem',
  'UserStatus' => '0',
  'split_spool_directory' => '1',
  'smtp_load_queue' => '10',
  'filter_required_hits_reject' => '10',
  'filter_report_safe_enable' => '1',
  'ispell_catalan' => '',
  'sql_table' => 'zpanel_atmail',
  'filter_bayes_min_ham_num' => '200',
  'smtp_type' => NULL,
  'allow_Mobile' => '1',
  'sendmode' => 'smtp',
  'login_newwindow' => '0',
  'allow_ReplyTo' => '1',
  'queue_run_in_order' => '1',
  'allow_Emotion' => 1,
  'logo_alt' => '',
  'ispell_arabic' => '',
  'allow_LeaveMsgs' => '',
  'smtp_max_connections_perip' => '5',
  'max_recipients_per_msg' => '100',
  'ispell_greek' => '',
  'admin_email' => 'postmaster" . $location . "',
  'max_msg_size' => '18',
  'virus_enable' => '1',
  'smtp_enforce_sync' => '1',
  'logout_url' => '../../../../?module=webmail',
  'imap_folders' => '1',
  'allow_AbookImportExport' => '1',
  'pallow_TelPager' => '1',
  'error_maxsize' => 'Email Message Error -

********** Sorry, the message could not be delivered **********

Message Too Big - The Message sent was too big and could not be delivered. Reduce the message size and try again.
  ',
  'message_cache' => '1',
  'Language' => 'english',
  'filter_max_bodysize' => '40',
  'pallow_Industry' => '1',
  'attachmentdeny_msg' => '---

The \$pref[brandname] email system has blocked an email message for \$this->EmailTo from the recipient \$this->EmailFrom.

The email message contained the attachment filename \\\\\"\$filename\\\\\" which is blocked by the email-system.

Please resend the message without the attachment for the email to be successfully delivered.

For additional information about the email service contact the Administrator \$pref[admin_email]',
  'allow_Templates' => 1,
  'virus_return' => NULL,
  'imap_enable' => 'YES',
  'ispell_espanol' => '',
  'filter_rbl_servers' => 'sbl-xbl.spamhaus.org',
  'pallow_Gender' => '1',
  'IMAP' => '1',
  'pallow_City' => '1',
  'mailserver' => '',
  'user_dir' => 'G:/zpanel/panel/modules/webmail/apps/atmail',
  'GlobalAbook' => '0',
  'allow_DateFormat' => '1',
  'pallow_State' => '1',
  'smtp_popimaprelay' => '1',
  'logo_small_alt' => 'Atmail Open',
  'filter_subject_tag' => '{SPAM}',
  'allow_AbookTrusted' => '1',
  'allow_EmailToFolderRules' => '1',
  'max_accounts_per_day' => '25',
  'allow_AntiVirus' => 1,
  'Quota' => '',
  'imap_emptytrash' => '30',
  'allow_DisplayImages' => '1',
  'allow_EmailForwarding' => '1',
  'timezone' => 'east',
  'allow_Signup' => '0',
  'install_size' => 'normal',
  'allow_TimeZone' => '1',
  'ispell_french' => '',
  'GlobalAbookRead' => '0',
  'pallow_FaxHome' => '1',
  'allow_MailMonitor' => '1',
  'allow_SpamTreatment' => '1',
  'POP3' => '1',
  'iconv' => '1',
  'allow_MailTemplates' => '0',
  'filter_rbl_support' => '1',
  'allow_MboxOrder' => '1',
  'allow_advanceduser' => 1,
  'allow_IMAPutility' => '1',
  'domain' => 'au.mailos.com',
  'pallow_TelMobile' => '1',
  'filter_trusted_networks' => '192.168/16, 127/8',
  'allow_Passutil' => '1',
  'ispell_russian' => '',
  'pop3_max' => '40',
  'allow_Advanced' => 1,
  'sql_type' => 'mysql',
  'smtp_auth' => '1',
  'install_dir' => 'G:/zpanel/panel/modules/webmail/apps/atmail',
  'filter_attach_check' => '1',
  'ldap_local' => NULL,
  'websync_enable_shared' => '1',
  'allow_Refresh' => 1,
  'allow_MultiAccounts' => '1',
  'smtp_load_queue_delivery' => '8',
  'imap_perip' => '5',
  'filter_bayes_auto_learn_threshold_nonspam' => '1.0',
  'allow_AbookGroup' => '1',
  'smtp_verify_senders' => '1',
  'virus_args' => NULL,
  'jpsupport' => 0,
  'maildir_sql_cache' => '0',
  'allow_AdvancedPopup' => '1',
  'ssl_enable' => '0',
  'smtp_max_connections' => '75',
  'sendmail' => NULL,
  'allow_AutoTrash' => '1',
  'ldap_server' => '',
  'base_dn' => '',
  'allow_Encoding' => '1',
  'mail_type' => 'pop3imap',
  'filter_spam_treatment' => 'mark',
  'bind_dn' => '',
  'allow_Forward' => '1',
  'builddate' => 'Dec 5 2008',
  'installdate' => 'Aug 24 2012',
  'ldap_passwd' => '',
  'allow_LoginHistory' => '1',
  'message_cache_time' => '30',
  'pop3_enable' => 'YES',
  'allow_FullName' => '1',
  'error_message' => '<html><body background=\\\\\"imgs/watermark.gif\\\\\">
<table width=\\\\\"100%\\\\\" border=\\\\\"0\\\\\" cellspacing=\\\\\"0\\\\\" cellpadding=\\\\\"2\\\\\">
  <tr>
    <td><font face=\\\\\"Verdana, Arial, Helvetica, sans-serif\\\\\"><strong>Software Configuration
      Error</strong></font></td>
    <td align=\\\\\"right\\\\\"><img src=\\\\\"\$pref[logo_small_img]\\\\\"></td>
  </tr>
  <tr>
    <td colspan=\\\\\"2\\\\\"> <font face=\\\\\"Verdana, Arial\\\\\" size=\\\\\"-1\\\\\">
      <p>The error message follows: <b>\$msg</b></p>
      </font> </td>
  </tr>
  <tr>
    <td colspan=\\\\\"2\\\\\">
	<iframe src=\\\\\"http://calacode.com/error.pl?prog=atmail&id=\$reg[downloadid]&error=\$msg&admin=\$pref[admin_email]\\\\\" width=\\\\\"100%\\\\\" height=\\\\\"140\\\\\" scrolling=\\\\\"auto\\\\\" frameborder=\\\\\"0\\\\\"></iframe>
</tr>
  <tr>
    <td colspan=\\\\\"2\\\\\"><form method=\\\\\"post\\\\\" action=\\\\\"http://webbasedemail.net/bug.pl\\\\\">
        <p>
          <input type=\\\\\"submit\\\\\" name=\\\\\"Submit\\\\\" value=\\\\\"Submit Bug Report\\\\\">
          <input type=\\\\\"hidden\\\\\" name=\\\\\"msg\\\\\" value=\\\\\"\$msg\\\\\">
          <input type=\\\\\"hidden\\\\\" name=\\\\\"server\\\\\" value=\\\\\"\$_SERVER[REMOTE_ADDR]\\\\\">
          <input type=\\\\\"hidden\\\\\" name=\\\\\"referer\\\\\" value=\\\\\"\$_SERVER[HTTP_REFERER]\\\\\">
          <input type=\\\\\"hidden\\\\\" name=\\\\\"admin\\\\\" value=\\\\\"\$pref[admin_email]\\\\\">
          <input type=\\\\\"hidden\\\\\" name=\\\\\"domain\\\\\" value=\\\\\"\\\\\">
        </p>
        <p><font face=\\\\\"Verdana\\\\\" size=\\\\\"-1\\\\\">Submit a bug-report to Technical Support.
          A staff member will be alerted of the error and will notify you via
          email for a solution.</font></p>
</form></td>
</tr>
</table>
</body></html>',
  'filter_required_hits' => '4',
  'smtphost' => 'localhost',
  'error_nouser' => 'Email Message Error -

********** Sorry, the message could not be delivered **********

  USER DOES NOT EXIST - Check the spelling of the email address and try again.',
  'smtp_load_reserve' => '20',
  'allow_Profile' => '0',
  'allow_MsgNum' => '1',
  'pallow_TelWork' => '1',
  'pallow_Occupation' => '1',
  'allow_SpamSettings' => '1',
  'allow_TimeFormat' => '1',
  'allow_PassThrough' => '1',
  'ispell_japanese' => '',
  'websync_log_support' => '1',
  'pallow_LastName' => '1',
  'sql_user' => 'webmail',
  'allow_LDAP' => '0',
  'allow_LDAPsearch' => '0',
  'pop3_max_perip' => '5',
  'logo_img' => 'imgs/logosmall.gif',
  'company_url' => 'http://atmail.com/',
  'allow_Layout' => '0',
  'allow_SMS' => '0',
  'pallow_DOB' => '1',
  'pallow_OtherEmail' => '1',
  'allow_Calendar' => '1',
  'footer_msg' => '<hr />Message sent via Atmail Open - http://atmail.org/',
  'allow_AcceptWhiteListOnly' => '1',
  'Description' => '',
  'filter_sa_enable' => '1',
  'aspell_arabic' => '',
  'aspell_chinese' => '',
  'aspell_english' => '1',
  'aspell_espanol' => '',
  'aspell_french' => '',
  'aspell_german' => '',
  'aspell_greek' => '',
  'aspell_italiano' => '',
  'aspell_portuguese' => '',
  'aspell_dir' => '',
  'use_php_pspell' => 0,
  'imap_functions' => '',
  'installed' => 1,
  'log_purge_days' => '30',
  'filter_auto_dl_spam' => '1',
  'use_mailparse_ext' => NULL,
  'display_php_errors' => 0,
  'expunge_logout' => 0,
  'DefaultEncoding' => 'iso-8859-1',
  'allow_utf7_folders' => '1',
  'mail_type_ssl' => 'allow',
);

\$settings = array (
  'NewWindow' => '0',
  'VlinkColor' => '#000033',
  'PrimaryColor' => '#EBE9E4',
  'Language' => 'english',
  'EmailHeaders' => 'standard',
  'TextColor' => '#000033',
  'RealName' => '',
  'LeaveMsgs' => '1',
  'SecondaryColor' => '#F8FBFD',
  'HeaderColor' => '#FBFBFB',
  'BgColor' => '#FFFFFF',
  'FontStyle' => 'Verdana',
  'UserQuota' => '51200',
  'TimeZone' => '',
  'LinkColor' => '#000000',
  'ReplyTo' => '',
  'Refresh' => '1200',
  'EmptyTrash' => '0',
  'Service' => '3',
  'HeadColor' => '#E2E7FA',
  'MboxOrder' => 'id',
  'TextHeadColor' => '#002675',
  'Advanced' => '1',
  'MsgNum' => '25',
  'OffColor' => '#FFFFFF',
  'ThirdColor' => '#FAFAFA',
  'AutoTrash' => '0',
  'LoginType' => NULL,
  'HtmlEditor' => '1',
  'TopBg' => 'imgs/bluegrad.gif',
  'OnColor' => '#F3F3F3',
  'SelectColor' => '#DFEAF4',
  'DateFormat' => '%e/%m/%y',
  'TimeFormat' => '%l:%M %p',
  'EmailEncoding' => 'UTF-8',
  'AutoComplete' => 1,
  'MailType' => 'sql',
  'Mode' => 'sql',
  'DisplayImages' => '1',
);

\$domains = array (
);

\$groups = array (
  'Default' => 
  array (
    'POP3' => '1',
    'IMAP' => '1',
    'allow_SMS' => '0',
    'allow_MultiAccounts' => '0',
    'Price' => '0',
    'Description' => 'Default group for accounts',
    'allow_Forward' => '1',
    'Quota' => '1000000',
    'allow_SpamSettings' => '1',
    'GlobalAbook' => '0',
    'GlobalAbookRead' => '0',
  ),
);

\$reg = array (
  'serial' => '',
  'expiry' => '',
  'downloadid' => '',
);

\$language = array (
  'english' => 'English',
  'espanol' => 'Espanol',
  'french' => 'French',
  'german' => 'German',
  'italiano' => 'Italiano',
);

\$reserved = array (
  'anonymous' => '1',
  'nobody' => '1',
  'mail' => '1',
  'mailer-daemon' => '1',
  'admin*' => '1',
  'daemon' => '1',
  'root' => '1',
);

\$brand = array (
);

// start functions -- do not remove this comment, it used to find the start
// of functions when writeconf() is rewriting this file.
// Place all functions below here

//look for PEAR files in our bundled lib first
set_include_path('./libs/PEAR/' . PATH_SEPARATOR . get_include_path());

/**
 * catches errors and displays a html error page
 *
 * @param string error message
 */
function catcherror(\$msg)
{
	global \$pref, \$reg;

	eval(\"\\\$error = \\\"{\$pref['error_message']}\\\";\");

    if ( strpos(\$_SERVER['SCRIPT_NAME'], 'wap.php') !== false)
	{
    	print \"<wml><card id='sent' title='Error'><p>Configuration Error: \$msg</p></card></wml>\";
    }
    else
	{
    	echo \$error;
    }
	exit();
}

/**
 * Write the configuraton file, other scripts can call this function to
 * save new settings to the Config.php
 */
function writeconf(\$extras=null)
{
	global \$pref, \$settings, \$domains, \$groups, \$reg, \$language, \$reserved, \$brand;

	\$configs = array('pref', 'settings', 'domains', 'groups', 'reg', 'language', 'reserved', 'brand');

	if (is_array(\$extras))
	{
		extract(\$extras);
		foreach (array_keys(\$extras) as \$name)
			\$configs[] = \$name;
	}

    if (!file_exists(\"{\$pref['install_dir']}/libs/Atmail/Config.php\"))
		die(\"Can't find myself\");

    // Make a backup of Config.php
	\$mod = \"{\$pref['install_dir']}/libs/Atmail/Config.php\";
    \$bak = \"{\$pref['install_dir']}/libs/Atmail/Config.php.bak\";

    copy(\$mod, \$bak) or die(\"Can't copy file: \$mod to \$bak\");
	if (!\$old = @fopen(\$bak, \"r\")) die(\"Can't open file: \$bak\");
	if (!\$new = @fopen(\$mod, \"w\")) die(\"Can't create file: \$mod\");

	fwrite(\$new, \"<?php\\n\\n\");

	foreach(\$configs as \$name)
	{
		fwrite(\$new, \"\\\$\$name = \");
		fwrite(\$new, var_export($\$name, true));
		fwrite(\$new, \";\\n\\n\");
	}

	\$write = 0;
	while (!feof(\$old))
	{
		if (isset(\$fail) && \$fail) break;

		\$buff = fgets(\$old);
		if (!\$write)
		{
			if (strpos(\$buff, '// start functions') !== false)
			{
				\$write = 1;
				if(fwrite(\$new, \$buff) === FALSE)
				{
				\$fail = true;
				}
			}
		}
		else
			if (fwrite(\$new, \$buff) === FALSE)
			{
			\$fail = true;
			}
	}

	//if we have had a failure, restore Config.php.bak
	if (isset(\$fail) && \$fail)
	{
		unlink(\$mod);
		rename(\$bak, \$mod);
		print \"An error occurred when writing the config file Config.php!  Restoring from Config.php.bak\\n\";
	}

    fclose(\$old);
    fclose(\$new);
}

?>
");
fclose($atmailconfig);
$squirrelmailconfig = @fopen("G:/zpanel/panel/modules/webmail/apps/squirrelmail/config/config.php", "w");
fwrite($squirrelmailconfig, "<?php
global \$version;
global \$config_version;
\$config_version = '1.4.0';
\$org_name = \"SquirrelMail\";
\$org_logo = SM_PATH . 'images/sm_logo.png';
\$org_logo_width = '308';
\$org_logo_height = '111';
\$org_title = \"SquirrelMail \$version\";
\$signout_page = '';
\$frame_top = '_top';
\$provider_name = 'SquirrelMail';
\$provider_uri = 'http://squirrelmail.org/';
\$domain = 'example.com';
\$invert_time = false;
\$useSendmail = false;
\$smtpServerAddress = 'localhost';
\$smtpPort = 25;
\$encode_header_key = '';
\$sendmail_args = '-i -t';
\$imapServerAddress = 'localhost';
\$imapPort = 143;
\$imap_server_type = 'other';
\$use_imap_tls = false;
\$use_smtp_tls = false;
\$smtp_auth_mech = 'none';
\$smtp_sitewide_user = '';
\$smtp_sitewide_pass = '';
\$imap_auth_mech = 'login';
\$optional_delimiter = 'detect';
\$pop_before_smtp = false;
\$pop_before_smtp_host = '';
\$default_folder_prefix = '';
\$show_prefix_option = false;
\$default_move_to_trash = true;
\$default_move_to_sent  = true;
\$default_save_as_draft = true;
\$trash_folder = 'INBOX.Trash';
\$sent_folder  = 'INBOX.Sent';
\$draft_folder = 'INBOX.Drafts';
\$auto_expunge = true;
\$delete_folder = false;
\$use_special_folder_color = true;
\$auto_create_special = true;
\$list_special_folders_first = true;
\$default_sub_of_inbox = true;
\$show_contain_subfolders_option = false;
\$default_unseen_notify = 2;
\$default_unseen_type   = 1;
\$noselect_fix_enable = false;
\$data_dir = 'G:/zpanel/panel/modules/webmail/apps/squirrelmail/data/';
\$attachment_dir = 'G:/zpanel/panel/modules/webmail/apps/squirrelmail/attach/';
\$dir_hash_level = 0;
\$default_left_size = '150';
\$force_username_lowercase = false;
\$default_use_priority = true;
\$hide_sm_attributions = false;
\$default_use_mdn = true;
\$edit_identity = true;
\$edit_name = true;
\$hide_auth_header = false;
\$allow_thread_sort = false;
\$allow_server_sort = false;
\$allow_charset_search = true;
\$uid_support              = true;
\$session_name = 'SQMSESSID';
\$config_location_base = '';
\$theme_default = 0;
\$theme_css = '';
\$theme[0]['PATH'] = SM_PATH . 'themes/default_theme.php';
\$theme[0]['NAME'] = 'Default';

\$theme[1]['PATH'] = SM_PATH . 'themes/plain_blue_theme.php';
\$theme[1]['NAME'] = 'Plain Blue';

\$theme[2]['PATH'] = SM_PATH . 'themes/sandstorm_theme.php';
\$theme[2]['NAME'] = 'Sand Storm';

\$theme[3]['PATH'] = SM_PATH . 'themes/deepocean_theme.php';
\$theme[3]['NAME'] = 'Deep Ocean';

\$theme[4]['PATH'] = SM_PATH . 'themes/slashdot_theme.php';
\$theme[4]['NAME'] = 'Slashdot';

\$theme[5]['PATH'] = SM_PATH . 'themes/purple_theme.php';
\$theme[5]['NAME'] = 'Purple';

\$theme[6]['PATH'] = SM_PATH . 'themes/forest_theme.php';
\$theme[6]['NAME'] = 'Forest';

\$theme[7]['PATH'] = SM_PATH . 'themes/ice_theme.php';
\$theme[7]['NAME'] = 'Ice';

\$theme[8]['PATH'] = SM_PATH . 'themes/seaspray_theme.php';
\$theme[8]['NAME'] = 'Sea Spray';

\$theme[9]['PATH'] = SM_PATH . 'themes/bluesteel_theme.php';
\$theme[9]['NAME'] = 'Blue Steel';

\$theme[10]['PATH'] = SM_PATH . 'themes/dark_grey_theme.php';
\$theme[10]['NAME'] = 'Dark Grey';

\$theme[11]['PATH'] = SM_PATH . 'themes/high_contrast_theme.php';
\$theme[11]['NAME'] = 'High Contrast';

\$theme[12]['PATH'] = SM_PATH . 'themes/black_bean_burrito_theme.php';
\$theme[12]['NAME'] = 'Black Bean Burrito';

\$theme[13]['PATH'] = SM_PATH . 'themes/servery_theme.php';
\$theme[13]['NAME'] = 'Servery';

\$theme[14]['PATH'] = SM_PATH . 'themes/maize_theme.php';
\$theme[14]['NAME'] = 'Maize';

\$theme[15]['PATH'] = SM_PATH . 'themes/bluesnews_theme.php';
\$theme[15]['NAME'] = 'BluesNews';

\$theme[16]['PATH'] = SM_PATH . 'themes/deepocean2_theme.php';
\$theme[16]['NAME'] = 'Deep Ocean 2';

\$theme[17]['PATH'] = SM_PATH . 'themes/blue_grey_theme.php';
\$theme[17]['NAME'] = 'Blue Grey';

\$theme[18]['PATH'] = SM_PATH . 'themes/dompie_theme.php';
\$theme[18]['NAME'] = 'Dompie';

\$theme[19]['PATH'] = SM_PATH . 'themes/methodical_theme.php';
\$theme[19]['NAME'] = 'Methodical';

\$theme[20]['PATH'] = SM_PATH . 'themes/greenhouse_effect.php';
\$theme[20]['NAME'] = 'Greenhouse Effect (Changes)';

\$theme[21]['PATH'] = SM_PATH . 'themes/in_the_pink.php';
\$theme[21]['NAME'] = 'In The Pink (Changes)';

\$theme[22]['PATH'] = SM_PATH . 'themes/kind_of_blue.php';
\$theme[22]['NAME'] = 'Kind of Blue (Changes)';

\$theme[23]['PATH'] = SM_PATH . 'themes/monostochastic.php';
\$theme[23]['NAME'] = 'Monostochastic (Changes)';

\$theme[24]['PATH'] = SM_PATH . 'themes/shades_of_grey.php';
\$theme[24]['NAME'] = 'Shades of Grey (Changes)';

\$theme[25]['PATH'] = SM_PATH . 'themes/spice_of_life.php';
\$theme[25]['NAME'] = 'Spice of Life (Changes)';

\$theme[26]['PATH'] = SM_PATH . 'themes/spice_of_life_lite.php';
\$theme[26]['NAME'] = 'Spice of Life - Lite (Changes)';

\$theme[27]['PATH'] = SM_PATH . 'themes/spice_of_life_dark.php';
\$theme[27]['NAME'] = 'Spice of Life - Dark (Changes)';

\$theme[28]['PATH'] = SM_PATH . 'themes/christmas.php';
\$theme[28]['NAME'] = 'Holiday - Christmas';

\$theme[29]['PATH'] = SM_PATH . 'themes/darkness.php';
\$theme[29]['NAME'] = 'Darkness (Changes)';

\$theme[30]['PATH'] = SM_PATH . 'themes/random.php';
\$theme[30]['NAME'] = 'Random (Changes every login)';

\$theme[31]['PATH'] = SM_PATH . 'themes/midnight.php';
\$theme[31]['NAME'] = 'Midnight';

\$theme[32]['PATH'] = SM_PATH . 'themes/alien_glow.php';
\$theme[32]['NAME'] = 'Alien Glow';

\$theme[33]['PATH'] = SM_PATH . 'themes/dark_green.php';
\$theme[33]['NAME'] = 'Dark Green';

\$theme[34]['PATH'] = SM_PATH . 'themes/penguin.php';
\$theme[34]['NAME'] = 'Penguin';

\$theme[35]['PATH'] = SM_PATH . 'themes/minimal_bw.php';
\$theme[35]['NAME'] = 'Minimal BW';

\$theme[36]['PATH'] = SM_PATH . 'themes/redmond.php';
\$theme[36]['NAME'] = 'Redmond';

\$theme[37]['PATH'] = SM_PATH . 'themes/netstyle_theme.php';
\$theme[37]['NAME'] = 'Net Style';

\$theme[38]['PATH'] = SM_PATH . 'themes/silver_steel_theme.php';
\$theme[38]['NAME'] = 'Silver Steel';

\$theme[39]['PATH'] = SM_PATH . 'themes/simple_green_theme.php';
\$theme[39]['NAME'] = 'Simple Green';

\$theme[40]['PATH'] = SM_PATH . 'themes/wood_theme.php';
\$theme[40]['NAME'] = 'Wood';

\$theme[41]['PATH'] = SM_PATH . 'themes/bluesome.php';
\$theme[41]['NAME'] = 'Bluesome';

\$theme[42]['PATH'] = SM_PATH . 'themes/simple_green2.php';
\$theme[42]['NAME'] = 'Simple Green 2';

\$theme[43]['PATH'] = SM_PATH . 'themes/simple_purple.php';
\$theme[43]['NAME'] = 'Simple Purple';

\$theme[44]['PATH'] = SM_PATH . 'themes/autumn.php';
\$theme[44]['NAME'] = 'Autumn';

\$theme[45]['PATH'] = SM_PATH . 'themes/autumn2.php';
\$theme[45]['NAME'] = 'Autumn 2';

\$theme[46]['PATH'] = SM_PATH . 'themes/blue_on_blue.php';
\$theme[46]['NAME'] = 'Blue on Blue';

\$theme[47]['PATH'] = SM_PATH . 'themes/classic_blue.php';
\$theme[47]['NAME'] = 'Classic Blue';

\$theme[48]['PATH'] = SM_PATH . 'themes/classic_blue2.php';
\$theme[48]['NAME'] = 'Classic Blue 2';

\$theme[49]['PATH'] = SM_PATH . 'themes/powder_blue.php';
\$theme[49]['NAME'] = 'Powder Blue';

\$theme[50]['PATH'] = SM_PATH . 'themes/techno_blue.php';
\$theme[50]['NAME'] = 'Techno Blue';

\$theme[51]['PATH'] = SM_PATH . 'themes/turquoise.php';
\$theme[51]['NAME'] = 'Turquoise';
\$default_use_javascript_addr_book = false;
\$abook_global_file = '';
\$abook_global_file_writeable = false;
\$abook_global_file_listing = true;
\$abook_file_line_length = 2048;
\$motd = \"\";
\$addrbook_dsn = '';
\$addrbook_table = 'address';
\$prefs_dsn = '';
\$prefs_table = 'userprefs';
\$prefs_key_field = 'prefkey';
\$prefs_user_field = 'user';
\$prefs_val_field = 'prefval';
\$addrbook_global_dsn = '';
\$addrbook_global_table = 'global_abook';
\$addrbook_global_writeable = false;
\$addrbook_global_listing = false;
\$squirrelmail_default_language = 'en_US';
\$default_charset = 'iso-8859-1';
\$lossy_encoding = false;
\$no_list_for_subscribe = false;
\$config_use_color = 2;
@include SM_PATH . 'config/config_local.php';


");
fclose($squirrelmailconfig);
$hastymail_config_file = @fopen("G:/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.conf", "w");
fwrite($hastymail_config_file, "host_name = " . $location . "
url_base = /modules/webmail/apps/Hastymail/
http_prefix = http
attachments_path = G:/zpanel/panel/modules/webmail/apps/Hastymail/attachments
settings_path = G:/zpanel/panel/modules/webmail/apps/Hastymail/user_settings
imap_port = 143
imap_server = localhost
imap_read_only = false
imap_ssl = false
imap_auth = false
imap_starttls = false
imap_folder_prefix =
imap_folder_exclude_hidden = true
imap_folder_delimiter_override = false
imap_folder_list_restricted = false
imap_use_folder_cache = true
imap_use_uid_cache = true
imap_use_header_cache = true
imap_display_name = Main
imap_disable_sort_speedup = false
imap_search_charset =
imap_use_namespaces = false
imap_enable_proxyauth = false
smtp_server = localhost
smtp_port = 25
smtp_tls = false
smtp_starttls = false
smtp_authentication_type =
enable_database = false
db_hostname = localhost
db_username = username
db_password = password
db_database = hastymail
db_pear_type = DB
db_type = mysql
db_persistent = false
site_settings_storage = file
site_contacts_storage = file
site_random_session_id = false
site_append_login_domain = false
percent_d_host = (|www|mail)
site_ajax_enabled = true
http_content_header = html
site_default_lang = en_US
site_default_timezone = false
page_title = Hastymail2
search_max = 3
html_message_iframe = true
site_theme = default
use_cookies = true
no_simplemode_cookies = false
cookie_name = hastymail2
site_key = asdfasdfasdfasdfasdf
site_logo = <span>Hm<span class=\"super\">2</span></span>
sent_folder   = Sent
trash_folder  = Trash
drafts_folder = Drafts
auto_create_sent   = true
auto_create_drafts = true
auto_create_trash  = true
utf7_folders = false
basic_http_auth = false
logout_url = logout.php
alt_imap_profiles = false
trim_login_fields = false
plugin = auto_address
plugin = compose_warning 
plugin = js_help
plugin = js_notice
plugin = js_sign
plugin = html_mail
plugin = filters
plugin =  notices
plugin = news
plugin = context
plugin = uuencode
plugin = custom_reply_to
plugin = move_sent
plugin = message_digest
plugin = saved_search
plugin = message_tags
plugin = select_range
theme = default,true,true,true
theme = green,true,true,false
theme = buuf,true,true,true
theme = buuf_deuce,true,true,false
theme = dark,true,true,false
theme = albook_sepia,true,true,true
theme = aqua,true,true,false
theme = newstyle,true,true,true
theme = moss,true,true,false
theme = tango,true,true,false
theme = dark_gray,true,true,false
theme = clean,true,true,true
show_imap_debug = false
show_full_debug = false
show_smtp_debug = false
show_cache_usage = false
db_debug = false
default_email_address = %u@hastymail.org
default_theme = default
default_display_mode = 1
default_timezone = America/Chicago
default_first_page = mailbox
default_font_size = 100%
default_lang = en_US
default_show_folder_list = true
default_auto_switch_simple_mode = 1
default_enable_delete_warning = true
default_expunge_on_exit = false
default_time_format = h:i:s: A
default_date_format = m/d/y
default_mailbox_date_format_2 = false
default_mailbox_date_format = h
default_start_page = false
default_disable_checked_js = false
default_disable_folder_icons = false
default_disable_list_icons = false
default_hide_deleted_messages = false
default_new_window_icon = true
default_folder_style = 1
default_folder_detail = 1
default_dropdown_ajax = true
default_ajax_update_interval = 120
default_folder_list_ajax = false
default_subscribed_only = false
default_text_links = false
default_text_email = false
default_hl_reply = false
default_font_family = monospace
default_image_thumbs = true
default_full_headers_default = false
default_small_headers = subject
default_small_headers = from
default_small_headers = date
default_small_headers = to
default_html_first = false
default_remote_image = false
default_default_message_action = false
default_short_message_parts = true
default_message_window = false
default_mailbox_per_page_count = 15
default_mailbox_controls_bottom = false
default_mailbox_freeze = false
default_always_expunge = false
default_selective_expunge = false
default_top_page_link = false
default_trim_from_fld = 0
default_trim_subject_fld = 0
default_full_mailbox_option = true
default_mailbox_update = true
default_folder_check = INBOX
default_new_page_refresh = 60
default_hide_folder_on_empty = false
default_compose_text_format = 0
default_compose_text_encoding = 0
default_compose_hide_mailer = false
default_compose_autosave = 120
default_delete_draft = false
default_compose_window = false
default_close_on_send = false
default_compose_confirm_send = false
default_compose_confirm_subject = false
default_compose_exit_warn = false
default_html_format_mail = false
default_auto_address_max_results = 10
default_auto_address_min_chars = 2
default_auto_address_search_fld = 3
default_auto_address_source_type = false
default_calendar_event_summary = false
default_custom_header_enabled = false
default_html_font_family = Arial
default_html_font_size = small
default_html_mode_toggle = true
default_move_sent_enabled = false
default_notices_enable_popup = false
default_notices_enable_sound = false
default_notices_sound_file = false
default_quota_display = false
default_enable_digest_display = true
default_custom_reply_to_enabled = false
");
fclose($hastymail_config_file);

    $site_config_file = false;
    $atts = "G:/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.conf";
            $site_config_file = $atts;
    $conf = array();
    if (is_readable($site_config_file)) {
        $lines = file($site_config_file);
        foreach ($lines as $line) {
            if (!trim($line)) {
                continue;
            }
            elseif (substr(trim($line), 0, 1) == '#') {
                continue;
            }
            else {
                $name = false;
                $value = false;
                $parts = explode('=', $line, 2);
                if (isset($parts[0]) && trim($parts[0])) {
                    $name = trim(strtolower($parts[0]));
                }
                if (isset($parts[1]) && trim($parts[1])) {
                    $value = trim($parts[1]);
                }
                if ($name == 'theme') {
                    $val_bits = explode(',', $value);
                    $theme = false;
                    $css = false;
                    $icons = false;
                    $templates = false;
                    if (isset($val_bits[0])) {
                        $theme = $val_bits[0];
                    }
                    if (isset($val_bits[1]) && $val_bits[1] == 'true') {
                        $css = true;
                    }
                    if (isset($val_bits[2]) && $val_bits[2] == 'true') {
                        $icons = true;
                    }
                    elseif (isset($val_bits[2]) && $val_bits[2] == 'default') {
                        $icons = 'default';
                    }
                    if (isset($val_bits[3]) && $val_bits[3] == 'true') {
                        $templates = true;
                    }
                    if ($theme) {
                        $conf['site_themes'][$theme] = array('icons' => $icons, 'templates' => $templates, 'css' => $css);
                    }
                }
                elseif ($name == 'plugin') {
                    $conf['plugins'][] = $value;
                }
                elseif (substr($name, 0, 7) == 'default') {
                    if ($name == 'default_folder_check') {
                        $conf['user_defaults']['folder_check'][] = $value;
                    }
                    else {
                        if (strtolower($value) == 'true') {
                            $value = true;
                        }
                        elseif (strtolower($value) == 'false') {
                            $value = false;
                        }
                        $conf['user_defaults'][substr($name, 8)] = $value;
                    }
                }
                elseif ($name) {
                    if (strtolower($value) == 'true') {
                        $value = true;
                    }
                    elseif (strtolower($value) == 'false') {
                        $value = false;
                    }
                    $conf[$name] = $value;
                }
            }
        }
    }
    else {
        echo "input file was Unreadable\n\n";
    }
    if (!empty($conf)) {
        $data = serialize($conf);
			$hastymail_config_file2 = fopen("G:/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.rc", "w");
fwrite($hastymail_config_file2, "".$data."");
fclose($hastymail_config_file2);
    }
$newmailconfig = @fopen("G:/zpanel/panel/modules/webmail/apps/squirrelmail/config/config.php", "w");	
fwrite($newmailconfig, "USE zpanel_hmail;

INSERT INTO  `zpanel_hmail`.`hm_domains` (

`domainid` ,
`domainname` ,
`domainactive` ,
`domainpostmaster` ,
`domainmaxsize` ,
`domainaddomain` ,
`domainmaxmessagesize` ,
`domainuseplusaddressing` ,
`domainplusaddressingchar` ,
`domainantispamoptions` ,
`domainenablesignature` ,
`domainsignaturemethod` ,
`domainsignatureplaintext` ,
`domainsignaturehtml` ,
`domainaddsignaturestoreplies` ,
`domainaddsignaturestolocalemail` ,
`domainmaxnoofaccounts` ,
`domainmaxnoofaliases` ,
`domainmaxnoofdistributionlists` ,
`domainlimitationsenabled` ,
`domainmaxaccountsize` ,
`domaindkimselector` ,
`domaindkimprivatekeyfile`
)
VALUES (
'1',  '" . $location . "',  '1',  '',  '0',  '',  '0',  '0',  '',  '0',  '0',  '1',  '',  '',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '',  ''
)

INSERT INTO  `zpanel_hmail`.`hm_accounts` (

`accountid` ,
`accountdomainid` ,
`accountadminlevel` ,
`accountaddress` ,
`accountpassword` ,
`accountactive` ,
`accountisad` ,
`accountaddomain` ,
`accountadusername` ,
`accountmaxsize` ,
`accountvacationmessageon` ,
`accountvacationmessage` ,
`accountvacationsubject` ,
`accountpwencryption` ,
`accountforwardenabled` ,
`accountforwardaddress` ,
`accountforwardkeeporiginal` ,
`accountenablesignature` ,
`accountsignatureplaintext` ,
`accountsignaturehtml` ,
`accountlastlogontime` ,
`accountvacationexpires` ,
`accountvacationexpiredate` ,
`accountpersonfirstname` ,
`accountpersonlastname`
)
VALUES (
'1',  '1',  '0',  'postmaster@" . $location . "',  '926022ea67d7a36c679178bf396a622d993f9254888cfa5259dffb7bb39cca3781ae45',  '1',  '0',  '',  '',  '100',  '0',  '',  '',  '3',  '0',  '',  '0',  '0',  '',  '',  '2012-08-27 21:49:15',  '0',  '2012-08-27 21:49:15',  '',  ''
)
");
fclose($newmailconfig);

?>