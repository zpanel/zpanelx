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
rm -rf /etc/zpanel/zpanelx/zpanelx-installers
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

mysql -uroot -p$password < /etc/zpanel/panel/modules/webmail/apps/roundcube/SQL/mysql.initial.sql
mysql -uroot -p$password -e 'CREATE DATABASE IF NOT EXISTS `zpanel_atmail`';
mysql -uroot -p$password -e 'CREATE DATABASE IF NOT EXISTS `zpanel_AfterLogic`';
mysql -uroot -p$password -e "CREATE USER 'webmail'@'localhost' IDENTIFIED BY ''";
mysql -uroot -p$password -e "GRANT USAGE ON * . * TO 'webmail'@'localhost' IDENTIFIED BY ''";
mysql -uroot -p$password -e 'GRANT ALL PRIVILEGES ON `zpanel_atmail` . * TO 'webmail'@'localhost'';
mysql -uroot -p$password -e 'GRANT ALL PRIVILEGES ON `zpanel_AfterLogic` . * TO 'webmail'@'localhost'';
mysql -uroot -p$password -e 'GRANT ALL PRIVILEGES ON `zpanel_roundcube` . * TO 'webmail'@'localhost'';
mysql -uroot -p$password -e "SET PASSWORD FOR  'webmail'@'localhost' = PASSWORD(  '$webmail' )";


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
  'sql_pass' => '$webmail',
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
  'admin_email' => 'postmaster@$fqdn',
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

rm /etc/zpanel/panel/modules/webmail/apps/Hastymail/index.php
cat > /etc/zpanel/panel/modules/webmail/apps/Hastymail/index.php <<EOF
<?php

/*  index.php: Main index file. All requests start here 
    Copyright (C) 2002-2010  Hastymail Development group

    This file is part of Hastymail.

    Hastymail is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Hastymail is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hastymail; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

* $Id: index.php 2007 2011-11-11 03:58:31Z sailfrog $
*/

/* Important defaults and base includes
----------------------------------------------------------------*/

/* configuration file */
$hm2_config = '/etc/zpanel/panel/modules/webmail/apps/Hastymail/hastymail2.rc';

/* include file prefix. This should be left blank unless you want to use an
   absolute path for file includes. In that case it should be set to a
   filesystem path ending with a delimiter that leads to the main Hastymail2
   directory, for example:
   $include_path = '/var/www/hastymail2/'
 */
$include_path = '';

/* the filesystem delimiter to use when building include statements */
$fd = '/';

/* capture any accidental output */
ob_start();

/* Timer debug preperation, used by the show_imap_debug hastymail2.conf setting. */
$page_start = microtime();

/* Required includes */
require_once($include_path.'lib'.$fd.'misc_functions.php');    /* various helpers */
require_once($include_path.'lib'.$fd.'utility_classes.php');   /* base classes    */
require_once($include_path.'lib'.$fd.'url_action_class.php');  /* GET processing  */
require_once($include_path.'lib'.$fd.'imap_class.php');        /* IMAP routines   */
require_once($include_path.'lib'.$fd.'site_page_class.php');   /* print functions */

/* Read in the site configuration file */
$conf = get_config($hm2_config);

/* Get the PHP version */
$phpversion = get_php_version();

/* Get the current page URL. */
$sticky_url = get_page_url();

/* Generate a unique page id. */
$page_id = md5(uniqid(rand(),1));

/* Define the current version. */
$hastymail_version = 'Hastymail2 1.1 RC2';


/* Data structures used by different parts of the program
----------------------------------------------------------------*/

/* Available languages. Translation files are located in the
   lang directory. They are named such that they match the keys
   of this array, but with ".php" extensions (lang/en_US.php).
   This array also defines the contents of the Language dropdown
   on the options page.
*/
$langs = array(
    'bg_BG' => 'Bulgarian',
    'ca_ES' => 'Catalan',
    'zh_CN' => 'Chinese',
    'nl_NL' => 'Dutch',
    'en_US' => 'English',
    'fi_FI' => 'Finnish',
    'fr_FR' => 'French',
    'de_DE' => 'German',
    'gr_GR' => 'Greek',
    'it_IT' => 'Italian',
    'ja_JP' => 'Japanese',
    'pl_PL' => 'Polish',
    'ro_RO' => 'Romanian',
    'ru_RU' => 'Russian',
    'es_ES' => 'Spanish',
    'tr_TR' => 'Turkish',
    'uk_UA' => 'Ukranian',
);

/* Plugin display hooks. Plugins use these hooks to insert content into
   existing Hastymail pages. Some are generic and occur on every page while
   some only execute on specific pages */
$available_display_hooks = array(
    'page_top',                   'icon',                   'clock',
    'menu',                       'folder_list_top',        'folder_list_bottom',
    'notices_top',                'notices_bottom',         'content_bottom',
    'footer',                     'mailbox_top',            'mailbox_meta',
    'mailbox_sort_form',          'mailbox_controls_1',     'mailbox_controls_2',
    'mailbox_search',             'mailbox_bottom',         'message_top',
    'message_meta',               'message_headers_bottom', 'message_bottom',
    'new_page_top',               'new_page_title_row',     'new_page_controls',
    'new_page_bottom',            'search_page_top',        'search_result_meta',
    'search_result_controls',     'search_result_bottom',   'search_form_top',
    'search_form_bottom',         'search_page_bottom',     'about_page_top',
    'about_table_bottom',         'about_page_bottom',      'options_page_top',
    'options_page_title_row',     'general_options_table',  'folder_options_table',
    'message_options_table',      'mailbox_options_table',  'new_options_table',
    'options_page_bottom',        'contacts_page_top',      'contact_detail_top',
    'contact_detail_bottom',      'contacts_quick_links',   'existing_contacts_top',
    'existing_contacts_bottom',   'contacts_page_bottom',   'import_contact_form',
    'add_contact_email_table',    'add_contact_name_table', 'add_contact_address_table',
    'add_contact_phone_table',    'add_contact_org_table',  'folders_page_top',
    'folder_controls_bottom',     'folder_options_top',     'folder_options_bottom',
    'folders_page_bottom',        'compose_options_table',  'compose_top',
    'compose_form_top',           'compose_form_bottom',    'compose_contacts_top',
    'compose_contacts_bottom',    'compose_above_from',     'compose_options',
    'compose_after_message',      'compose_bottom',         'message_body_top',
    'message_parts_table',        'compose_page_to_row',    'compose_page_cc_row',
    'compose_page_bcc_row',       'compose_after_options',  'message_headers_bottom',
    'message_body_bottom',        'message_links',          'message_part_headers_top',
    'message_part_headers_bottom','message_prev_next_links','msglist_after_subject',
);

/* Plugin work hooks. Plugins can gain access to internal data before the
   the content for the requested page is built. This array defines the default
   work hooks. */
$available_work_hooks  = array(
    'init',                 'thread_view_start',            'about_page_start', 
    'not_found_start',      'search_page_start',            'folders_page_start',
    'logged_out',           'mailbox_page_start',           'message_page_start',
    'compose_page_start',   'options_page_start',           'contacts_page_start',
    'profile_page_start',   'new_page_start',               'update_settings',
    'message_send',         'compose_contact_list',         'first_time_login',
    'just_logged_in',       'register_contacts_source',     'on_login',
    'page_end',             'compose_after_send',           'message_save',
    'logged_out_init',      'mailbox_page_selected',        'message_page_selected',
    'imap_action',          'after_imap_action',            'before_logout',
    'set_config_value',
);

/* HTML message filtering package to use. Available options are:

   htmlpure    This is the most secure HTML filter and sanitizer, but it
               is also one of the slowest. If you want to use this be sure
               to setup the pure_serializer_path setting listed below to
               get the best performance.
   htmlawed    This is a newer HTML filer that can also correct some HTML
               compliance problems. It is lightweight and fast.
   legacy      This is the htmlfilter we have used since the first version.
               It is fast but has no HTML cleanup capability and is not
               actively developed anymore.
   none        Setting this to 'none' means that NO HTML FILTERING WILL
               BE USED. THIS IS EXTEMELY DANGEROUS UNLESS YOU CAN VERIFY
               THE SOURCE AND VALIDITY OF ALL HTML FORMATTED MESSAGES.
*/
$filter_backend = 'htmlpure';

/* If the filter_backend is set to htmlpure then your should enable a
   cache location for the serializer to speed up the filter. You can
   do so by setting the following to a directory to use as a cache. This
   directory MUST be writable by the user your web server software runs
   as (just like the attachment and user setting directories). To disable
   this caching in the filter set this to false. If the configured
   directory is not writable then the cache will be disabled. */

$pure_serializer_path = '/var/hastymail2/serializer_cache';

/* This defines HTML tags the filter allows when displaying an HTML 
   message part. We use a white-list approach to HTML message types.
   It can sometimes cause problems with accurate rendering but the
   additional security is worth it. Note that htmlpure has it's own
   whitelist so this list is not used for that filter. */
$allowed_tag_list  = array(
    'table', 'tr', 'td', 'tbody', 'th', 'ul', 'ol', 'li', 'hr',
    'em', 'u', 'font', 'br', 'strong', 'span', 'a', 'p', 'img',
    'blockquote', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
);

/* Mbstring available charsets. This is used to validate the character set
   defined in a message part. */
$mb_charset_codes = array_flip(array(
    'UCS-4',        'UCS-4BE',      'UCS-4LE',      'UCS-2',        'UCS-2BE',
    'UCS-2LE',      'UTF-32',       'UTF-32BE',     'UTF-32LE',     'UTF-16',
    'UTF-16BE',     'UTF-16LE',     'UTF-7',        'UTF7-IMAP',    'UTF-8',
    'ASCII',        'EUC-JP',       'SJIS',         'EUCJP-WIN',    'SJIS-WIN',
    'ISO-2022-JP',  'JIS',          'ISO-8859-1',   'ISO-8859-2',   'ISO-8859-3',
    'ISO-8859-4',   'ISO-8859-5',   'ISO-8859-6',   'ISO-8859-7',   'ISO-8859-8',
    'ISO-8859-9',   'ISO-8859-10',  'ISO-8859-13',  'ISO-8859-14',  'ISO-8859-15',
    'EUC-CN',       'CP936',        'HZ',           'EUC-TW',       'CP950',
    'BIG-5',        'BIG5',         'EUC-KR',       'UHC',          'CP949',
    'ISO-2022-KR',  'WINDOWS-1251', 'CP1251',       'WINDOWS-1252', 'CP1252',
    'CP866',        'IBM866',       'KOI8-R',       'GB2312'
));

/* Available internal charset conversions. These are defined in langs/charsets.php
   and are only used if PHP mbstring functionality is not available (this method is
   more limited and less efficient). */
$charset_codes = array(
    'iso-8859-1',   'iso-8859-2',   'iso-8859-3' ,  'iso-8859-4',
    'iso-8859-5',   'iso-8859-6',   'iso-8859-7',   'iso-8859-8',
    'iso-8859-9',   'iso-8859-10',  'iso-8859-11',  'iso-8859-14',
    'iso-8859-15',  'iso-8859-16',  'koi8-r',       'koi8-u',
    'windows-1252', 'windows-1251', 'ibm-850',      'windows-1256'
);

/* Sort types available for server side sorting. These define the sort
   options available when the IMAP server supports the SORT extension.
   The entries beginning with "R_" are the same sorting methods as the
   entries without the "R_" prefix but the order is reversed. The number
   values are the index of the display string defined in the language
   translation files. */
$sort_types = array( 
    'ARRIVAL'   => 279, 'R_ARRIVAL' => 280,
    'DATE'      => 281, 'R_DATE'    => 282,
    'FROM'      => 283, 'R_FROM'    => 284,
    'SUBJECT'   => 285, 'R_SUBJECT' => 286,
    'CC'        => 287, 'R_CC'      => 288,
    'TO'        => 289, 'R_TO'      => 290,
    'R_SIZE'    => 291, 'SIZE'      => 292,
    'THREAD_R'  => 293, 'THREAD_O'  => 294,
);

/* Sort types for client side sorting. When server side sorting is not
   available we provide some client side sorting ability to fall back on.
   it is more limited and definitely not as fast as server side. */
$client_sort_types = array (
    'ARRIVAL'   => 279, 'R_ARRIVAL' => 280,
    'DATE'      => 281, 'R_DATE'    => 282,
    'FROM'      => 283, 'R_FROM'    => 284,
    'SUBJECT'   => 285, 'R_SUBJECT' => 286,
);

/* Sort filters. For IMAP servers who support the SORT extension a message
   filtering option is available. These define the IMAP keywords available,
   the numbers represent the language file index of the string to be displayed
   for each option */
$sort_filters = array(
    'ALL'        => 113, 'UNSEEN'   => 114, 
    'SEEN'       => 115, 'FLAGGED'  => 116, 
    'UNFLAGGED'  => 117, 'ANSWERED' => 118, 
    'UNANSWERED' => 119, 'DELETED'  => 295, 
    'UNDELETED'  => 296, 
);

/* Main application pages. Pages in Hastymail2 use a simple page=some_page URL
   argument. This defines the internal pages in the program and are used to validate
   the page request. Plugins can add new pages to this dynamically. */
$app_pages = array(
    'login',    'logout',      'new',     'inline_image',
    'contacts', 'profile',     'options', 'compose',
    'search',   'thread_view', 'mailbox', 'message',
    'about',    'folders',     'contact_groups', 'not_found',
);

/* IMAP SEARCH CHARSET options. Defines what character set options are
   availble to be used with an IMAP search command. */
$imap_search_charsets = array(
    'UTF-8',
    'US-ASCII',
    '',
);

/* list of IMAP keywords to validate against. This is used to check user
   input that is supplied to an IMAP command.  */
$imap_keywords = array(
    'ARRIVAL',    'DATE',    'FROM',      'SUBJECT',
    'CC',         'TO',      'SIZE',      'UNSEEN',
    'SEEN',       'FLAGGED', 'UNFLAGGED', 'ANSWERED',
    'UNANSWERED', 'DELETED', 'UNDELETED', 'TEXT',
    'ALL',
);

/* Viewabled message parts. This defines what types of messages parts
   Hastymail2 will let the browser view. The array keys are the MIME
   type and subtype of the message part, and the values must be one of
   text, image, html, or frame. It is also possible to use plugins to
   add support for additional content types. */
$message_part_types = array( 
    'message/disposition-notification'   => 'text',   /* text part for MDN                       */
    'message/delivery-status'            => 'text',   /* text part for message bounce            */
    'message/rfc822-headers'             => 'text',   /* text part for message headers           */
    'text/csv'                           => 'text',   /* comma separated values                  */
    'text/plain'                         => 'text',   /* normal text message                     */
    'text/unknown'                       => 'text',   /* normal text message                     */
    'text/html'                          => 'html',   /* HTML message (blech)                    */
    'text/x-vcard'                       => 'text',   /* Vcard                                   */
    'text/calendar'                      => 'text',   /* Vcal                                    */
    'text/x-vCalendar'                   => 'text',   /* Vcal                                    */
    'text/x-sql'                         => 'text',   /* sql                                     */
    'text/x-comma-separated-values'      => 'text',   /* CSV                                     */
    'text/enriched'                      => 'text',   /* enriched text                           */
    'text/rfc822-headers'                => 'text',   /* another text part for message headers   */
    'text/x-diff'                        => 'text',   /* patch/diff                              */
    'text/x-patch'                       => 'text',   /* patch/diff                              */
    'image/jpeg'                         => 'image',  /* JPEG images                             */
    'image/pjpeg'                        => 'image',  /* JPEG images                             */
    'image/jpg'                          => 'image',  /* JPEG images                             */
    'image/png'                          => 'image',  /* PNG images                              */
    'image/bmp'                          => 'image',  /* BMP images                              */
    'image/gif'                          => 'image',  /* GIF images                              */
    'application/pgp-signature'          => 'text',   /* PGP signatures                          */
    'application/x-httpd-php'            => 'text',   /* PHP source code                         */
    'application/pdf'                    => 'frame',  /* PDF document                            */
);

/* Small headers available for user selection. The message view allows users
   to select which headers are visible. This list defines the availble selections
   on the options page. */
$small_header_options = array(
    'subject',            'from',          'to',               'date',
    'cc',                 'x-spam-status', 'x-spam-level',     'envelope-to',
    'received',           'content-type',  'message-id',       'sender',
    'list-id',            'precedence',    'dilevery-date',    'x-priority',
    'in-reply-to',        'references',    'list-unsubscribe', 'list-subscribe',
    'IMAP message flags', 'x-mailer',      'user-agent',       'content-transfer-encoding'
);

/* Message headers to search for the add contact dropdown. The add contact option is
   on the mesage view page and collects email addresses from the header fields defined
   in this array. */
$add_contact_headers = array(
    'sender',
    'x-envelope-from',
    'from',
    'to',
    'reply-to',
    'cc',
);

/* Date format options. These define the date format and display string
   for the date format settings on the options page. The array keys are
   the PHP date() command format strings and the values are what users
   see in the date format dropdown. */
$date_formats = array(
    'm/d/y'  => 'mm/dd/yy',
    'm/d/Y'  => 'mm/dd/yyyy',
    'm-d-y'  => 'mm-dd-yy',
    'm/d/Y'  => 'mm-dd-yyyy',
    'M j, Y' => 'mon dd, yyyy',
    'M j, y' => 'mon dd, yy',
    'M j'    => 'mon dd   ',
    'F d, Y' => 'month dd, yyyy',
    'F d, y' => 'month dd, yy',
    'r'      => 'rfc822',
    'd/m/Y'  => 'dd/mm/yyyy ',
    'd/m/y'  => 'dd/mm/yy',
    'Y-m-d'  => 'yyyy-mm-dd',
    'y-m-d'  => 'yy-mm-dd',
    'd.m.Y'  => 'dd.mm.yyyy',
    'd.m.y'  => 'dd.mm.yy',
);

/* Time format options. Same as the date options above, the keys
   are PHP date format strings the values are display strings. */
$time_formats = array(
    'g:i:s a' => '12:00:00',
    'H:i:s'   => '24:00:00',
    'g:i a'   => '12:00',
    'H:i'     => '24:00',
);

/* First page after login options. Defines the available pages
   for the first page after login setting on the options page. */
$start_pages = array(
    'mailbox' => 22,
    'new' => 10,
    'options' => 4,
    'compose' => 3,
    'contacts' => 8,
    'profile' => 236,
    'folders' => 7,
    'about' => 2,
);

/* Sort types for the contacts page. Defines the available sort
   methods for the contacts display. */
$contact_sort_types = array(
    'EMAIL'  => 16,
    'FN'     => 149,
    'FAMILY' => 150,
    'GIVEN'  => 151,
    'NAME'   => 152,
);

/* Phone types for the contacts page. Defines the phone types available
   for a contact entry. */
$phone_types = array(
    1 => 'Work',
    2 => 'Home',
    3 => 'Cell',
    4 => 'Voice',
    5 => 'Fax',
    6 => 'Preferred'
);

/* Phone display types for translations. Maps the phone type to an
   interface translation index. */
$phone_dsp_types = array(
    'Work'  => 325,
    'Home'  => 326,
    'Cell'  => 327,
    'Voice' => 328,
    'Fax'   => 329,
    'Preferred' => 330,
);

/* Address types for the contacts page. Defines the address types
   a contact can have. */
$address_types = array(
    1 => 'Work',
    2 => 'Home',
    3 => 'Parcel',
    4 => 'Postal'
);

/* Address display types for string translations. Maps to the
   interface translation index. */
$address_dsp_types = array(
    'Work' => 325,
    'Home' => 326,
    'Parcel' => 331,
    'Postal' => 332,
);

/* Text output encoding options for the compose section of the
   options page. In order they are 8bit quoted-printable, and base64.
   The values map to the interface translation index for each options.
 */
$text_encodings = array(
    0 => 308,
    1 => 309,
    2 => 310,
);

/* Text output format options for the compose section of the
   options page. In order they are Fixed, Flowed, and Preformatted.
   The values map to the interface translation index for each options.
 */
$text_formats = array(
    0 => 305,
    1 => 306,
    2 => 307,
);

/* SMTP auth mechs available. These are the authentication options available
   for sending mail with authenticated SMTP */
$smtp_auth_mechs = array(
    'none',
    'plain',
    'login',
    'cram-md5',
    'external',
);

/* SMTP auth mechs for translations. Maps the above list
   to the correct interface translation index for the compose
   section of the options page. */
$smtp_dsp_mechs = array(
    'none' => 242,
    'plain' => 311,
    'login' => 312,
    'cram-md5' => 313,
    'external' => 314,
); 

/* Output filter tags. The final HTML output of a request contains special
   tags that are used to filter out mark-up depending on the display mode.
   This defines the tag setup that corresponds to normal display mode. If the
   false and true values where reversed it would be "simple" display mode */
$hm_tags = array(
    'complex' => false,
    'simple' => true,
);

/* Previous and next options. On the message view page there is a "previous or
   next plus action dialog. This list defines the message actions available on
   the dropdown and maps them to their interface strings. */
$prev_next_actions = array(
    ' ' => 428,
    'move' => 66,
    'copy' => 67,
    'unread' => 34,
    'flag' => 35,
    'unflag' => 65, 
    'delete' => 59,
    'expunge' => 68,
);

/* Message list field order. This defines the order of fields on the mailbox
   page, search results, unread mail page, and thread view. Omitting an entry
   will remove it from the display. This can be overriden by a theme, and then
   again by a user's settings */
$msg_list_flds = array(
    'checkbox_cell',
    'image_cell',
    'from_cell',
    'indicators_cell',
    'subject_cell',
    'plugin_cell',
    'date_cell',
    'size_cell',
);

/* display message list headings or not. Can be overriden by a theme,
 * and then again by a user's settings */
$default_list_heading = true;

/* add onclick events to message list rows that open the message. Can be
 * overridden by a theme and again by a user's settings */
$default_onclick = false;

/* Contact list per page count on the compose page */
$contacts_per_page = 20;

/* Maximum messages per page in message lists */
$max_msg_per_page = 200;

/* Maximum read length for message parts (0 is unlimited) in characters.
   This only applies to text or html parts being viewed. If set too
   high a big enough text part can overload the browser. */
$max_read_length = 350000;

/* Maximum header length on the message view page. If a header value exceeds this
   length a link will be available to display the entire value. This setting is
   in characters */
$max_header_length = 300;

/* Development option to force plugins to completely reload each page load.
   Under normal circumstances plugin hooks are registered when a user logs in. */
$force_plugin_reloading = false;

/* Maximum amount of time to skip new and mailbox page updates when the content
   has not changed (in seconds). This keeps the Date field updated when it is showing
   the age of the message. */
$force_page_update = 300;

/* If an in-process message is exited without sending uploaded attachments could be
   left on the server. This sets the number of seconds to wait before purging these
   attachments from the server.*/
$attachment_lifetime = 7200;

/* Set this to true to use the uncompressed versions of the javascript include files.
   Useful for javascript development. */
$javascript_dev = false;

/* event handlers added by plugins are by default wrapped into try blocks so one problematic
   handler does not impact any others. This can make debugging difficult so the exceptions
   can be disabled here
 */
$allow_js_exception = false;

/* If a message does not contain a mime-version header IMAP servers are allowed to
   ignore the MIME structure and parse the message as plain text. Setting the following
   to true will enable a work around in hastymail that will correct this problem, but
   on for very simple single part messages. */
$override_missing_mime_header = false;

/* Css files are dynamically streamed to the browser using PHP. This sets the max age
   cache control HTTP header value for css content (in seconds), and is used to determine
   the date value for the expires HTTP header. */
$css_max_age = 21600;

/* Enable support for the hastymail_utils PHP5 module */
$hm_utils_mod = false;

/* Maximum number of recipients for a single outgoing message. Leave at 0 for unlimited */
$max_outbound_recipients = 0;

/* Disable atlernate profile support and "lock down" the email address */
$no_profiles = false;

/* Add CSS font-size to the message view area when in simple mode. This value is set
 * in pts and is used to help correct odd browser font sizing when reading messages
 * in simplemode */
$simple_msg_font_size = 11;

/* Allow for a comma separated list of SMTP servers defined in the hastymai2.conf file.
 * When a send attempt is made the servers will be selected randomly from the list
 * until one is successfully connected to. */
$smtp_server_pool = false;

/* This sets the maximum amount of messages we will show when a user uses the
 * "show all" link in the mailbox view. */
$show_all_max = 1000;

/* There are two types of E-mail validation. Full validation based on RFC 3696, and
 * a simpler regex version. The full validation is 5 times slower than the default
 * regex but both are sub millisecond. The regex pattern can be altered with the
 * $valid_email_regex value below. Valid values here are "full" and "regex". */
$email_validation_type = 'regex';

/* Default regular expression used to determine E-mail validity */
$valid_email_regex = "/^([a-zA-Z0-9\.\-\=\+\'\`])+@(localhost|(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+)$/";

/* White list of valid built in ajax callback functions we allow to be sent from a Hastymail page's
 * javascript. Plugins can add to these but those are valided dynamically and don't need to be listed
 * here. Don't remove or add things to this unless you know what you are doing or it will break some
 * ajax functionality */
$valid_ajax_callbacks = array('ajax_update_page', 'ajax_save_outgoing_message', 'ajax_prev_contacts',
    'ajax_next_contacts', 'ajax_save_folder_state', 'ajax_save_folder_vis_state');


/* Start required objects and prep global space for possible use
----------------------------------------------------------------*/

/* Holds the database connection object if needed by the request */
$dbase = false;

/* Holds the plugin tools object set if needed by the request. This
   provides an API to plugins to function within Hastymail. */
$tools = false;

/* Holds the mime message object if needed by the request. This
   class handles formatting outgoing messages. */
$message = false;

/* Holds the SMTP object if needed by the request. This class
   handles sending outgoing messages. */
$smtp = false;

/* Instantiate the imap object. This is used for all IMAP communications */
$imap = hm_new('imap');

/* Instantiate the user object. This handles the core logic of the program */
$user = hm_new('fw_user');

/* Apply the site configuration. */
get_site_config();

/* CSS streamer that dynamically outputs a theme's css files combined into one
   compressed file. If css_streamer() gets called page execution ends without
   returning here. */
if (isset($_GET['css']) && isset($_GET['page']) && isset($_GET['theme'])) {
    css_streamer($_GET['page'], $_GET['theme']);
}


/* Handle the request and perform any resulting actions needed
----------------------------------------------------------------*/

/* Start the user object checks. This handles all input frome the user and
   calls the appropriate code to perform required actions.*/
$user->init();

/* Start Sajax based ajax system if we need it. If we are handling an ajax
   request we do not return from the handle_client_request call, it outputs
   the ajax response. */
if ($user->ajax_enabled && isset($_POST['rs'])) {
    require_once($include_path.'lib'.$fd.'ajax_functions.php');
    handle_client_request();
}

/* Counter for the new page, only reset on non-ajax requests */
$_SESSION['new_page_refresh_count'] = 0;

/* Clean up IMAP communication. At this point all the work that needs to be done
   for this request is complete. */
if ($imap->connected) {
    $imap->disconnect();
}

/* Do a handy work hook. This is a good way for plugins to get access to the completed
   data for a page request before the XHTML is built. */
do_work_hook('page_end');


/* Build the XHTML and sent it to the browser 
----------------------------------------------------------------*/

/* Setup template data. The code is broken out into multiple includes to keep
   the application memory footprint smaller. */
if ($user->sub_class_names['url']) {
    $class_name = 'site_page_'.$user->sub_class_names['url'];
    $pd = hm_new($class_name);
}
else {
    $pd = hm_new('site_page');
}

/* Build the page XHTML. The resulting page is constructed but not sent to the browser yet */
build_page($pd);

/* Filter the output XHTML for the current display mode, and send it to the browser */
output_filtered_content($hm_tags);

/* IMAP debug. Outputs debug information below the page if the show_imap_debug setting
   is enabled in the hastymail2.conf file. */
if (isset($conf['show_imap_debug']) && $conf['show_imap_debug']) {
    if (isset($conf['show_full_debug']) && $conf['show_full_debug']) {
        $imap->puke(true);
    }
    else {
        $imap->puke();
    }
}

/* SMTP debug. Outputs debug information about any SMTP operations performed */
if (isset($conf['show_smtp_debug']) && $conf['show_smtp_debug']) {
    if (is_object($smtp)) {
        $smtp->puke();
    }
}

/* PHP session cache usage. Shows some memory use information if the show_cache_usage
   hastymail2.conf file setting is enabled. */
if (isset($conf['show_cache_usage']) && $conf['show_cache_usage']) {
    $imap->show_cache();
    if (function_exists('memory_get_peak_usage')) {
        echo '<br />Peak PHP memory usage : '.(sprintf("%0.2f", memory_get_peak_usage()/1024)).'KB';
    }
}

/* Clean up the user object and properly close any active sessions. */
$user->clean_up();

/* DB debug statements. Show the database connection debug out if the db_debug option is
   enabled in the hastymail2.conf file. */
if (is_object($dbase) && isset($conf['db_debug']) && $conf['db_debug']) {
    echo $dbase->puke(true);
}
?>
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

mysql -uroot -p$password < /etc/zpanel/panel/modules/webmail/install/install-centos.sql
rm -rf /etc/zpanel/panel/modules/webmail/install

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

