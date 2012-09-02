#!/bin/bash

#################################################################
# ZPanelX Automated Installer for CentOS 6.2                    #
# Created by : nickelj                                          #
# Current maintainer : Kevin Andrews (kandrews@zpanelcp.com)    #
# Licensed Under the GPL (http://www.gnu.org/licenses/gpl.html) #
# Version 1.0.0                                                 #
#################################################################

yum -y install wget chkconfig

M="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
while [ "${n:=1}" -le "22" ]
do  webmail="$webmail${M:$(($RANDOM%${#M})):1}"
  let n+=1
done

r=`wget -q http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-7.noarch.rpm`
if [ $? -ne 0 ]
  then
        echo -e "PROFTPD will currently failed to install\nPlease update the following url to the correct URL:\n"
        echo -e "This url is used in two places in this installation script!\n\n"
        echo -e "http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-*.noarch.rpm\n\n"
        echo -e "Visit http://dl.fedoraproject.org/pub/epel/6/x86_64/ and find the epel-release url."
        exit
fi

tz=``
echo -e "Find your timezone from : http://php.net/manual/en/timezones.php e.g Europe/London"
echo -e ""
read -e -p "Enter Your Time Zone: " tz

echo $tz

password=""
password2="old"
echo -e "Centos 6.2 ZPanelX Official Automated Installer\n\n"
echo -e "This script assumes you have installed Centos 6.2 as either Minimal or Basic Server.\n"
echo -e "If you selected additional options during the CentOS install please consider reinstalling with no additional options.\n\n"
fqdn=`/bin/hostname`
pubip=`curl -s http://automation.whatismyip.com/n09230945.asp`
while true; do
   read -e -p "Enter the FQDN of the server (example: zpanel.yourdomain.com): " -i $fqdn fqdn
   read -e -p "Enter the Public (external) IP of the server: " -i $pubip pubip
   while [ "$password" != "$password2" ]
   do
         password=""
         password2="old"
         echo -e "MySQL Password is currently blank, please change it now.\n"
         prompt="Password you will use for MySQL: "
         while IFS= read -p "$prompt" -r -s -n 1 char 
         do 
               if [[ $char == $'\0' ]] 
               then 
                  break 
               fi
               prompt='*' 
               password+="$char" 
         done
         password2=""
         echo
         prompt="Re-enter the password you will use for MySQL: "
         while IFS= read -p "$prompt" -r -s -n 1 char 
         do 
               if [[ $char == $'\0' ]] 
               then 
                  break 
               fi
               prompt='*' 
               password2+="$char" 
         done
         if [ "$password" != "$password2" ]
         then
            echo -e "\nPasswords did not match!\n"
         fi
   done
   echo -e "\n\nZPanelX Install Configuration Parameters:\n"
   echo -e "Fully Qualified Domain Name: " $fqdn
   echo -e "Public IP address: " $pubip
   echo -e ""
   read -e -p "Proceed with installation (y/n/q)? " yn
   case $yn in
        [Yy]* ) break;;
        [Qq]* ) exit;
   esac
done

echo -e "## PREPARING THE SERVER ##"
sed -i 's/SELINUX=enforcing/SELINUX=disabled/g' /etc/selinux/config
setenforce 0
if [ -e "/etc/init.d/sendmail" ]
then
chkconfig --levels 235 sendmail off
/etc/init.d/sendmail stop
fi   
service iptables save
service iptables stop
chkconfig iptables off

echo -e ""
echo -e "###########################"
echo -e "## Adding PROFTPD REPO   ##"
echo -e "## This can take a while ##"
echo -e "###########################"
echo -e ""

###################################
# PROFTPD REPO                    #
# THIS URL CAN BECOME OUT OF DATE #
###################################
wget http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-7.noarch.rpm
rpm -Uvh epel-release*rpm
###################################

echo -e "## Removing sendmail and vsftpd"
yum -y remove sendmail vsftpd
echo -e "## Running System Update ##";
yum -y update
echo -e "## Installing vim make zip unzip ld-linux.so.2 libbz2.so.1 libdb-4.7.so libgd.so.2 ##"
yum -y install sudo wget vim make zip unzip git ld-linux.so.2 libbz2.so.1 libdb-4.7.so libgd.so.2
echo -e "## Installing httpd php php-suhosin php-devel php-gd php-mbstring php-mcrypt php-intl php-imap php-mysql php-xml php-xmlrpc curl curl-devel perl-libwww-perl libxml2 libxml2-devel mysql-server zip webalizer gcc gcc-c++ httpd-devel at make mysql-devel bzip2-devel ##"
yum -y -q install httpd php php-suhosin php-devel php-gd php-mbstring php-mcrypt php-intl php-imap php-mysql php-xml php-xmlrpc curl curl-devel perl-libwww-perl libxml2 libxml2-devel mysql-server zip webalizer gcc gcc-c++ httpd-devel at make mysql-devel bzip2-devel
echo -e "## Installing postfix dovecot dovecot-mysql ##"
yum -y install postfix dovecot dovecot-mysql
echo -e "## Installing proftpd proftpd-mysql ##"
yum -y install proftpd proftpd-mysql
echo -e "## Installing bind bind-utils bind-libs ##"
yum -y install bind bind-utils bind-libs

mkdir /etc/zpanel
chmod -R 777 /etc/zpanel/
cd /etc/zpanel
git clone git://github.com/andykimpe/zpanelx.git
mv "zpanelx" "panel"

######################
# INSTALL THE PANEL! #
######################
echo -e "## INSTALLING THE PANEL... ##"
cd /etc/zpanel/panel/etc/build/
echo "ZPanel Enviroment Configuration Tool"
echo "===================================="
echo ""
echo "If you need help, please visit our forums: http://forums.zpanelcp.com/"
echo ""
echo "Creating folder structure.."
mkdir /etc/zpanel/configs
mkdir /etc/zpanel/docs
mkdir /var/zpanel
mkdir /var/zpanel/hostdata
mkdir /var/zpanel/hostdata/zadmin
mkdir /var/zpanel/hostdata/zadmin/public_html
mkdir /var/zpanel/logs
mkdir /var/zpanel/backups
mkdir /var/zpanel/temp
echo "Complete!"
#echo "Copying ZPanel files into place.."
#cp -R ../../* /etc/zpanel/panel/ 
#echo "Complete!"
# echo "Copying application configuration files.."
# cp -R -v config_packs/NAME_OF_PACK/* /etc/zpanel/configs
# echo "Complete!"
echo "Setting permissions.."
chmod -R 777 /var/zpanel/
echo "Complete!"
echo "Registering 'zppy' client.."
ln -s /etc/zpanel/panel/bin/zppy /usr/bin/zppy
chmod +x /usr/bin/zppy
ln -s /etc/zpanel/panel/bin/setso /usr/bin/setso
chmod +x /usr/bin/setso
echo "Complete!"
echo ""
echo ""
echo "The Zpanel directories have now been created in /etc/zpanel and /var/zpanel"
echo ""
chmod -R 777 /var/zpanel/
chmod 644 /etc/zpanel/panel/etc/apps/phpmyadmin/config.inc.php    
chmod +x /etc/zpanel/panel/bin/zppy
chmod +x /etc/zpanel/panel/bin/setso
cp -R /etc/zpanel/panel/etc/build/config_packs/centos_6_2/* /etc/zpanel/configs/

###################    
# CONFIGURE MYSQL #
###################
echo -e "## CONFIGURE MYSQL ##"
chkconfig --levels 235 mysqld on
service mysqld start
mysqladmin -u root password $password
mysql -u root -p$password -e "DROP DATABASE test";
read -p "Remove access to root MySQL user from remote connections? (Recommended) Y/n " -n 1 -r
if [[ $REPLY =~ ^[Yy]$ ]]
then
mysql -u root -p$password -e "DELETE FROM mysql.user WHERE User='root' AND Host!='localhost'";
echo "Remote access to the root MySQL user has been removed"
else
echo "Remote access to the root MySQL user is still available, we hope you selected a very strong password"
fi
mysql -u root -p$password -e "DELETE FROM mysql.user WHERE User=''";
mysql -u root -p$password -e "FLUSH PRIVILEGES";


##############################
# SET ZPANEL DATABASE CONFIG #
##############################
cat > /etc/zpanel/panel/cnf/db.php <<EOF
<?php

/**
 * Database configuration file.
 * @package zpanelx
 * @subpackage core -> config
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
\$host = "localhost";
\$dbname = "zpanel_core";
\$user = "root";
\$pass = "$password";
?>
EOF

    
#########################
# IMPORT PANEL DATABASE #
#########################
mysql -uroot -p$password < /etc/zpanel/configs/zpanel_core.sql

####################
# CONFIGURE APACHE #
####################
echo "Include /etc/zpanel/configs/apache/httpd.conf" >> /etc/httpd/conf/httpd.conf

#Change default docroot
sed -i 's|DocumentRoot "/var/www/html"|DocumentRoot "/etc/zpanel/panel"|' /etc/httpd/conf/httpd.conf

#Set ZPanel Network info and compile the default vhost.conf
/etc/zpanel/panel/bin/setso --set zpanel_domain $fqdn
/etc/zpanel/panel/bin/setso --set server_ip $pubip
/etc/zpanel/panel/bin/setso --set daemon_lastrun 0
/etc/zpanel/panel/bin/setso --set daemon_dayrun 0
/etc/zpanel/panel/bin/setso --set daemon_weekrun 0
/etc/zpanel/panel/bin/setso --set daemon_monthrun 0
/etc/zpanel/panel/bin/setso --set apache_changed true
/etc/zpanel/panel/bin/setso --set dbversion 10.0.0a
# Set PHP Time Zone
sed -i "s|;date.timezone =|date.timezone = $tz|" /etc/php.ini
#upload directory
sed -i "s|;upload_tmp_dir =|upload_tmp_dir = /var/zpanel/temp/|" /etc/php.ini
chown -R apache:apache /var/zpanel/temp/

php /etc/zpanel/panel/bin/daemon.php

echo "127.0.0.1 "$fqdn >> /etc/hosts
chkconfig --levels 235 httpd on
service httpd start

echo "apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo" >> /etc/sudoers

###########################################
# POSTFIX-DOVECOT (CentOS6 uses Dovecot2) #
###########################################
mkdir -p /var/zpanel/vmail
chmod -R 777 /var/zpanel/vmail
chmod -R g+s /var/zpanel/vmail
groupadd -g 5000 vmail
useradd -m -g vmail -u 5000 -d /var/zpanel/vmail -s /bin/bash vmail
chown -R vmail.vmail /var/zpanel/vmail
    
mysql -uroot -p$password < /etc/zpanel/configs/postfix/zpanel_postfix.sql

# Postfix Master.cf
echo "# Dovecot LDA" >> /etc/postfix/master.cf
echo "dovecot   unix  -       n       n       -       -       pipe" >> /etc/postfix/master.cf
echo '  flags=DRhu user=vmail:vmail argv=/usr/libexec/dovecot/deliver -d ${recipient}' >> /etc/postfix/master.cf

#Edit these files and add mysql root and password:
sed -i "s|YOUR_ROOT_MYSQL_PASSWORD|$password|" /etc/zpanel/configs/postfix/conf/dovecot-sql.conf
sed -i "s|#connect|connect|" /etc/zpanel/configs/postfix/conf/dovecot-sql.conf
sed -i "s|#password = YOUR_ROOT_MYSQL_PASSWORD|password = $password|" /etc/zpanel/configs/postfix/conf/mysql_relay_domains_maps.cf
sed -i "s|#password = YOUR_ROOT_MYSQL_PASSWORD|password = $password|" /etc/zpanel/configs/postfix/conf/mysql_virtual_alias_maps.cf
sed -i "s|#password = YOUR_ROOT_MYSQL_PASSWORD|password = $password|" /etc/zpanel/configs/postfix/conf/mysql_virtual_domains_maps.cf
sed -i "s|#password = YOUR_ROOT_MYSQL_PASSWORD|password = $password|" /etc/zpanel/configs/postfix/conf/mysql_virtual_mailbox_limit_maps.cf
sed -i "s|#password = YOUR_ROOT_MYSQL_PASSWORD|password = $password|" /etc/zpanel/configs/postfix/conf/mysql_virtual_mailbox_maps.cf
sed -i "s|#password = YOUR_ROOT_MYSQL_PASSWORD|password = $password|" /etc/zpanel/configs/postfix/conf/mysql_virtual_transport.cf
        
mv /etc/postfix/main.cf /etc/postfix/main.old
ln /etc/zpanel/configs/postfix/conf/main.cf /etc/postfix/main.cf
mv /etc/dovecot/dovecot.conf /etc/dovecot/dovecot.old
ln -s /etc/zpanel/configs/dovecot2/dovecot.conf /etc/dovecot/dovecot.conf
sed -i '1ilisten = *' /etc/zpanel/configs/dovecot2/dovecot.conf
sed -i "s|myhostname = control.yourdomain.com|myhostname = $fqdn|" /etc/zpanel/configs/postfix/conf/main.cf
# This next line is not a typo - the original file has youromain.com
sed -i "s|mydomain   = control.youromain.com|mydomain   = $fqdn|" /etc/zpanel/configs/postfix/conf/main.cf
    
chkconfig --levels 345 postfix on
chkconfig --levels 345 dovecot on
service postfix start
service dovecot start

################################################################    
# Server will need a reboot for postfix to be fully functional #
################################################################

###################    
# Modules Webmail #
###################

chmod -R 777 /etc/zpanel/panel/modules/webmail/
chmod -R 777 /etc/zpanel/panel/modules/webmail/install/
cat > /etc/zpanel/panel/modules/webmail/install/install-centos.sql <<EOF
USE zpanel_postfix;
INSERT INTO  `zpanel_postfix`.`domain` (
`domain` ,
`description` ,
`aliases` ,
`mailboxes` ,
`maxquota` ,
`quota` ,
`transport` ,
`backupmx` ,
`created` ,
`modified` ,
`active`
)
VALUES (
'$fqdn',  '',  '0',  '0',  '0',  '0',  '',  '0',  '2012-08-27 14:18:45',  '2012-08-27 14:18:45',  '1'
);
INSERT INTO  `zpanel_postfix`.`mailbox` (
`username` ,
`password` ,
`name` ,
`maildir` ,
`quota` ,
`local_part` ,
`domain` ,
`created` ,
`modified` ,
`active`
)
VALUES (
'postmaster@$fqdn',  '{PLAIN-MD5}d5a98d2e3bd31ee2ed3ebf457f4eb645',  'postmaster',  '$fqdn/postmaster/',  '200',  'postmaster',  '$fqdn', '2012-08-27 14:42:45',  '2012-08-27 14:42:45',  '1'
);
EOF

mysql -uroot -p$password < /etc/zpanel/panel/modules/webmail/install/install-centos.sql
mysql -uroot -p$password < /etc/zpanel/panel/modules/webmail/apps/roundcube/SQL/mysql.initial.sql
mysql -u root -p$password -e 'CREATE DATABASE IF NOT EXISTS `zpanel_atmail`';
mysql -u root -p$password -e 'CREATE DATABASE IF NOT EXISTS `zpanel_AfterLogic`';
mysql -u root -p$password -e "CREATE USER 'webmail'@'localhost' IDENTIFIED BY '$webmail'";
mysql -u root -p$password -e "GRANT USAGE ON * . * TO 'webmail'@'localhost' IDENTIFIED BY '$webmail';";
mysql -u root -p$password -e "GRANT ALL PRIVILEGES ON `zpanel_atmail` . * TO 'webmail'@'localhost'";
mysql -u root -p$password -e "GRANT ALL PRIVILEGES ON `zpanel_AfterLogic` . * TO 'webmail'@'localhost'";
mysql -u root -p$password -e "GRANT ALL PRIVILEGES ON `zpanel_roundcube` . * TO 'webmail'@'localhost'";


cat > /etc/zpanel/panel/modules/webmail/apps/AfterLogic/data/settings/settings.xml <<EOF
<?xml version="1.0" encoding="utf-8"?>
<Settings xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<Common>
		<!-- Default title that will be shown in browser's header (Default domain settings). -->
		<SiteName>AfterLogic WebMail Lite</SiteName>
		<!-- License key is supplied here. -->
		<LicenseKey />
		<AdminLogin>mailadmin</AdminLogin>
		<AdminPassword>$password</AdminPassword>
		<DBType>MySQL</DBType>
		<DBPrefix>AfterLogic</DBPrefix>
		<DBHost>localhost</DBHost>
		<DBName>zpanel_AfterLogic</DBName>
		<DBLogin>webmail</DBLogin>
		<DBPassword>$webmail</DBPassword>
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
EOF

cat > /etc/zpanel/panel/modules/webmail/apps/roundcube/config/db.inc.php <<EOF
<?php
\$rcmail_config = array();
\$rcmail_config['db_dsnw'] = 'mysql://webmail:$webmail@localhost/zpanel_roundcube';
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

EOF

cat > /etc/zpanel/panel/modules/webmail/apps/atmail/libs/Atmail/Config.php <<EOF
<?php

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
  'quota_alert_html' => '<p style="font-weight:bold;text-align:center;font-size:24px;">
YOUR QUOTA IS NEARLY EXHAUSTED - PLEASE DELETE UNNECESSARY ITEMS
</p>
<p style="text-align:center;font-size:18px;"> 
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
  'sql_pass' => 'jqcZw7DlFBY8NB59rxiUZE',
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
  'admin_email' => 'postmasterzpanel.us.to',
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

The email message contained the attachment filename \\"\$filename\\" which is blocked by the email-system.

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
  'user_dir' => '/etc/zpanel/panel/modules/webmail/apps/atmail',
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
  'install_dir' => '/etc/zpanel/panel/modules/webmail/apps/atmail',
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
  'error_message' => '<html><body background=\\"imgs/watermark.gif\\">
<table width=\\"100%\\" border=\\"0\\" cellspacing=\\"0\\" cellpadding=\\"2\\">
  <tr>
    <td><font face=\\"Verdana, Arial, Helvetica, sans-serif\\"><strong>Software Configuration
      Error</strong></font></td>
    <td align=\\"right\\"><img src=\\"\$pref[logo_small_img]\\"></td>
  </tr>
  <tr>
    <td colspan=\\"2\\"> <font face=\\"Verdana, Arial\\" size=\\"-1\\">
      <p>The error message follows: <b>\$msg</b></p>
      </font> </td>
  </tr>
  <tr>
    <td colspan=\\"2\\">
	<iframe src=\\"http://calacode.com/error.pl?prog=atmail&id=\$reg[downloadid]&error=\$msg&admin=\$pref[admin_email]\\" width=\\"100%\\" height=\\"140\\" scrolling=\\"auto\\" frameborder=\\"0\\"></iframe>
</tr>
  <tr>
    <td colspan=\\"2\\"><form method=\\"post\\" action=\\"http://webbasedemail.net/bug.pl\\">
        <p>
          <input type=\\"submit\\" name=\\"Submit\\" value=\\"Submit Bug Report\\">
          <input type=\\"hidden\\" name=\\"msg\\" value=\\"\$msg\\">
          <input type=\\"hidden\\" name=\\"server\\" value=\\"\$_SERVER[REMOTE_ADDR]\\">
          <input type=\\"hidden\\" name=\\"referer\\" value=\\"\$_SERVER[HTTP_REFERER]\\">
          <input type=\\"hidden\\" name=\\"admin\\" value=\\"\$pref[admin_email]\\">
          <input type=\\"hidden\\" name=\\"domain\\" value=\\"\\">
        </p>
        <p><font face=\\"Verdana\\" size=\\"-1\\">Submit a bug-report to Technical Support.
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

	eval("\$error = \"{\$pref['error_message']}\";");

    if ( strpos(\$_SERVER['SCRIPT_NAME'], 'wap.php') !== false)
	{
    	print "<wml><card id='sent' title='Error'><p>Configuration Error: \$msg</p></card></wml>";
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

    if (!file_exists("{\$pref['install_dir']}/libs/Atmail/Config.php"))
		die("Can't find myself");

    // Make a backup of Config.php
	\$mod = "{\$pref['install_dir']}/libs/Atmail/Config.php";
    \$bak = "{\$pref['install_dir']}/libs/Atmail/Config.php.bak";

    copy(\$mod, \$bak) or die("Can't copy file: \$mod to \$bak");
	if (!\$old = @fopen(\$bak, "r")) die("Can't open file: \$bak");
	if (!\$new = @fopen(\$mod, "w")) die("Can't create file: \$mod");

	fwrite(\$new, "<?php\n\n");

	foreach(\$configs as \$name)
	{
		fwrite(\$new, "\$\$name = ");
		fwrite(\$new, var_export(\$\$name, true));
		fwrite(\$new, ";\n\n");
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
		print "An error occurred when writing the config file Config.php!  Restoring from Config.php.bak\n";
	}

    fclose(\$old);
    fclose(\$new);
}

?>
EOF

cat > /etc/zpanel/panel/modules/webmail/apps/squirrelmail/config/config.php <<EOF
<?php
global \$version;
global \$config_version;
\$config_version = '1.4.0';
\$org_name = "SquirrelMail";
\$org_logo = SM_PATH . 'images/sm_logo.png';
\$org_logo_width = '308';
\$org_logo_height = '111';
\$org_title = "SquirrelMail \$version";
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
\$data_dir = '/etc/zpanel/panel/modules/webmail/apps/squirrelmail/data/';
\$attachment_dir = '/etc/zpanel/panel/modules/webmail/apps/squirrelmail/attach/';
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
\$motd = "";
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



EOF

cat > /etc/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.conf <<EOF
host_name = $fqdn
url_base = /modules/webmail/apps/Hastymail/
http_prefix = http
attachments_path = /etc/zpanel/panel/modules/webmail/apps/Hastymail/attachments
settings_path = /etc/zpanel/panel/modules/webmail/apps/Hastymail/user_settings
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
site_logo = <span>Hm<span class="super">2</span></span>
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
EOF

php /etc/zpanel/panel/modules/webmail/install/install.php
mysql -h localhost -u root -p$password zpanel_atmail < /etc/zpanel/panel/modules/webmail/install/zpanel_atmail.sql
mysql -h localhost -u root -p$password zpanel_AfterLogic < /etc/zpanel/panel/modules/webmail/install/zpanel_AfterLogic_linux.sql
rm -rf /etc/zpanel/panel/modules/webmail/install

    
###########    
# PROFTPD #
###########

mysql -u root -p$password -e "DROP DATABASE zpanel_proftpd.sql;";
mysql -uroot -p$password < /etc/zpanel/configs/proftpd/zpanel_proftpd.sql
groupadd -g 2001 ftpgroup
useradd -u 2001 -s /bin/false -d /bin/null -c "proftpd user" -g ftpgroup ftpuser

sed -i "s|zpanel_proftpd@localhost root z|zpanel_proftpd@localhost root $password|" /etc/zpanel/configs/proftpd/proftpd-mysql.conf
    
mv /etc/proftpd.conf /etc/proftpd.old
touch /etc/proftpd.conf
echo "include /etc/zpanel/configs/proftpd/proftpd-mysql.conf" >> /etc/proftpd.conf
mkdir /var/zpanel/logs/proftpd
chmod -R 644 /var/zpanel/logs/proftpd
    
chkconfig --levels 345 proftpd on
service proftpd start

########    
# BIND #
########

#CONFIGURE BIND AS NEEDED - ONCE RUNNING INCLUDE ZPANEL NAMED PATH
#vi /etc/named.conf 

cat > /etc/named.conf <<EOF
//
// named.conf
//
// Provided by Red Hat bind package to configure the ISC BIND named(8) DNS
// server as a caching only nameserver (as a localhost DNS resolver only).
//
// See /usr/share/doc/bind*/sample/ for example named configuration files.
//

options {
	listen-on port 53 { any; };
	directory 	"/var/named";
	dump-file 	"/var/named/data/cache_dump.db";
        statistics-file "/var/named/data/named_stats.txt";
        memstatistics-file "/var/named/data/named_mem_stats.txt";
	allow-query	{ any; };
	recursion yes;

	dnssec-enable yes;
	dnssec-validation yes;
	dnssec-lookaside auto;

	/* Path to ISC DLV key */
	bindkeys-file "/etc/named.iscdlv.key";
};

logging {
        channel default_debug {
                file "data/named.run";
                severity dynamic;
        };
};

zone "." IN {
	type hint;
	file "named.ca";
};

include "/etc/named.rfc1912.zones";
include "/etc/zpanel/configs/bind/etc/named.conf";
include "/etc/zpanel/configs/bind/etc/named.conf.slave";
EOF
#vi /etc/zpanel/configs/bind/etc/named.conf.slave

cat > /etc/zpanel/configs/bind/etc/named.conf.slave <<EOF

EOF


#chmod or apache can't write to the folder
chmod -R 777 /etc/zpanel/configs/bind/zones/
chkconfig --levels 345 named on
service named start

################
# ZPANEL ZSUDO #
################

# Must be owned by root with 4777 permissions, or zsudo will not work!
cc -o /etc/zpanel/panel/bin/zsudo /etc/zpanel/configs/bin/zsudo.c
sudo chown root /etc/zpanel/panel/bin/zsudo
chmod +s /etc/zpanel/panel/bin/zsudo

#################    
# ZPANEL DAEMON #
#################
touch /etc/cron.d/zdaemon

#PATH added so service can be run as a command via daemon cron job
cat > /etc/cron.d/zdaemon <<EOF
SHELL=/bin/bash
PATH=/sbin:/bin:/usr/sbin:/usr/bin
MAILTO=root
HOME=/
*/5 * * * * root /usr/bin/php -q /etc/zpanel/panel/bin/daemon.php >> /dev/null 2>&1
EOF

# Permissions must be 644 or cron will not run!
sudo chmod 644 /etc/cron.d/zdaemon
service crond restart

####################
# GET CRON WORKING #
####################
touch /var/spool/cron/apache
touch /etc/cron.d/apache
chmod -R 777 /var/spool/cron/
chmod 644 /var/spool/cron/apache 
chown -R apache:root /var/spool/cron/
service crond reload
crontab -u apache /var/spool/cron/apache

#########################
# REMOVE WEBALIZER CONF #
#########################
mv /etc/webalizer.conf /etc/webalizer.conf.old

#################
# REBOOT SERVER #
#########################################
#                                       #
# DONT YOU DARE SKIP THIS STEP          #
# ELSE DONT POST FOR SUPPORT ABOUT MAIL #
# NOT WORKING!                          #
#                                       #
#########################################
echo -e "#############################"
echo -e "# REBOOTING THE SERVER NOW! #"
echo -e "#############################"
shutdown -r now

