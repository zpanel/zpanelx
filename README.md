# ZPanelCP

Version: `11.0.0-alpha`

![Example screenshoot](https://i.postimg.cc/ZngPxNj3/screenshoot.png)

## Description

ZPanel is an open-source web hosting control panel written in PHP and is compatible
with Microsoft Windows and POSIX (Linux, UNIX, MacOSX and the BSD's).

## License agreement

ZPanel is licensed under the [GNU GENERAL PUBLIC LICENSE (GPL v3)](http://www.gnu.org/copyleft/gpl.html) you can view a copy of this license by opening the LICENSE file in the root of this folder.

## Warning

This version wasn't fully tested and may contain security flaws.
Therefore **it is not recommended for productions servers**.
Feel free to use it on local enviroment.

## Installation

Below you can find installation example for diffrent path than default one (`C:/zpanel`)

**_!!! Using spaces in direcotries names may produce errors in panel usage !!!_**

### On windows

Packages versions used during testing:

-   Apache 2.4.54 Win64 [here](https://www.apachelounge.com/download/VS16/binaries/httpd-2.4.54-win64-VS16.zip)
-   PHP 8.0.20 - VS16 x64 Thread Safe [here](https://windows.php.net/downloads/releases/php-8.0.20-Win32-vs16-x64.zip)
-   MySql Server 8.0.26 [here](https://downloads.mysql.com/archives/get/p/23/file/mysql-8.0.26-winx64.zip)
-   DNS Server 9.16.29 x64 [here](https://downloads.isc.org/isc/bind9/9.16.29/BIND9.16.29.x64.zip)
-   FTP Server 0.9.42 [here](https://download.filezilla-project.org/server/FileZilla_Server-0_9_42.exe)
-   hMail Server 5.6.8 - Build 2574 [here](https://www.hmailserver.com/download_getfile/?performdownload=1&downloadid=271)

#### Prepering enviroment

_PHP part_

1. Download and unpack [PHP](https://windows.php.net/download)
   (In my case under `Z:/php8.0` path)

2. Add PHP `bin` folder to system enviroment path [here's how](https://helpdeskgeek.com/windows-10/add-windows-path-environment-variable/)

3. In php foler copy `php.ini-development` file as `php.ini`

4. Set configuration in PHP `php.ini` file

Set correct `extension_dir` path and uncomment it
(in my case)

-   `extension_dir = "Z:/php8.0/ext"`

Uncomment (delete `;` sign) following lines in `php.ini` file

-   `extension=curl`
-   `extension=fileinfo`
-   `extension=gd`
-   `extension=intl`
-   `extension=ldap`
-   `extension=mbstring`
-   `extension=exif`
-   `extension=mysqli`
-   `extension=openssl`
-   `extension=pdo_mysql`

_Apache part_

1. Download and unpack [Apache server](https://www.apachelounge.com/download/)
   (I will be presentig configuration when Apache is uder `Z:/Apache24` path)

2. Go to `bin` directory and install Apache service with CMD (Administartor mode)

```
httpd.exe -k install
```

3. Set configuration in Apache `conf/httpd.conf` file

Find and change server root path

_Here is changed line for my case_

`Define SRVROOT "Z:/Apache24"`

Find and change server name

`ServerName localhost:80`

Find and add index.php to _DirectoryIndex_ as below

```
<IfModule dir_module>
    DirectoryIndex index.php index.html
</IfModule>
```

Uncomment (delete `#` sign) following lines

-   `LoadModule rewrite_module modules/mod_rewrite.so`
-   `Include conf/extra/httpd-vhosts.conf`

Add zpanel domain in the end of document

```
<VirtualHost *:80>
    DocumentRoot "${SRVROOT}/htdocs/zpanel"
    ServerName zpanel.localhost
    ErrorLog "logs/zpanel-error.log"
    CustomLog "logs/zpanel-access.log" common
    <Directory "${SRVROOT}/htdocs/zpanel">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add PHP 8.0 support for `php` extension by adding at the end

```
PHPIniDir "Z:/php8.0"
AddHandler application/x-httpd-php .php
LoadModule php_module "Z:/php8.0/php8apache2_4.dll"
```

4. Copy `zpanel` files to Apache `htdocs/zpanel` directory

_MySQL part_

1. Download and install [MySQL server](https://dev.mysql.com/downloads/mysql/)

Settings you need to remeber for next steps

-   server port `(default: 3306)`
-   admin login `(default: root)`
-   admin password

2. Import `zpanel` database from `etc/build/config_packs/ms_windows/zpanel_core.sql` to database
   If you used linked eariler installer you shoud have option to launch `MySQL Workbench` app and after logging in there is an option for import database

_DNS part_

1. Download and install [DNS server](https://www.isc.org/download/)

[Here's guide how to do it](https://www.winbind.org/installing-bind-on-windows/)

[Generation of needed keys](https://www.winbind.org/configuring-an-authoritative-nameserver/)

In my case DNS server will be installed in `Z:/Apache24/htdocs/zpanel/bin/bind` direcotry

2. Create and paste (with your own paths) to `named.conf` file

(In my case it is located in `Z:/Apache24/htdocs/zpanel/bin/bind/etc/named.conf`)

```
// This is the primary configuration file for the BIND DNS server named.
include "Z:/Apache24/htdocs/zpanel/bin/bind/etc/named.conf.options";
include "Z:/Apache24/htdocs/zpanel/bin/bind/etc/named.conf.logging";
```

3. Create and paste (with your own paths) to `named.conf.options` file

(In my case it is located in `Z:/Apache24/htdocs/zpanel/bin/bind/etc/named.conf.options`)

```
options {
        directory "Z:/Apache24/htdocs/zpanel/bin/bind/zones";  # Sets the location of all zone files (including the root hints file)
        recursion no;							        # Don't allow recursion (in other words, don't allow this sever to be used as a caching server)
        allow-recursion { none; };					    # This would allow recursion from specific clients only, so it's a useful way to ensure that recursion definitely CANNOT occur)
        listen-on { any; };						        # Listen on all IPv4 interfaces
        listen-on-v6 { any; };						    # Listen on all IPv6 interfaces (not needed if IPv6 not enabled)
        allow-transfer { 10.0.0.2; };					# Allows the zone(s) on this server to be transferred to specific secondary servers only - so enter your secondary and tertiary autooritative nameservers here
									                    # On secondary and tertiary servers it should be: allow-transfer { none; };
        version none;                                   # Prevents rogue hosts trying to determine the BIND version of this nameserver
        rate-limit { responses-per-second 10; };		# Attempts to prevent DoS/DDoS by limiting the responses per second (to a single IP address) to ten per second.

	blackhole {                 					    # Drop queries that result in IPs for these ranges
            10/8;               					    #  - remove this line if you are running on a 10.x.y.z network
            172.16/12;          					    #  - remove this line if you are running on a 172.16.x.y network
            192.168/16;							        #  - remove this line if you are running on a 192.168.x.y network
	};
	dnssec-validation auto;     					    # sets the DNS root zone as the trust anchor for DNSSEC
};

```

4. Create and paste (with your own paths) to `named.conf.logging` file

(In my case it is located in `Z:/Apache24/htdocs/zpanel/bin/bind/etc/named.conf.logging`)

```
logging {
     channel default_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/default.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel auth_servers_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/auth_servers.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel dnssec_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/dnssec.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel zone_transfers_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/zone_transfers.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel ddns_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/ddns.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel client_security_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/client_security.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel rate_limiting_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/rate_limiting.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel rpz_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/rpz.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel dnstap_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/dnstap.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel queries_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/queries.log";
		  print-time yes;
          print-category yes;
          print-severity yes;
          severity info;
     };
     channel query-errors_log {
          file "Z:/Apache24/htdocs/zpanel/bin/bind/etc/logs/query-errors.log";
          print-time yes;
          print-category yes;
          print-severity yes;
          severity dynamic;
     };
     channel default_syslog {
          print-time yes;
          print-category yes;
          print-severity yes;
          syslog daemon;
          severity info;
     };
     channel default_debug {
          print-time yes;
          print-category yes;
          print-severity yes;
          file "named.run";
          severity dynamic;
     };
     category default { default_syslog; default_debug; default_log; };
     category config { default_syslog; default_debug; default_log; };
     category dispatch { default_syslog; default_debug; default_log; };
     category network { default_syslog; default_debug; default_log; };
     category general { default_syslog; default_debug; default_log; };
     category zoneload { default_syslog; default_debug; default_log; };
     category resolver { auth_servers_log; default_debug; };
     category cname { auth_servers_log; default_debug; };
     category delegation-only { auth_servers_log; default_debug; };
     category lame-servers { auth_servers_log; default_debug; };
     category edns-disabled { auth_servers_log; default_debug; };
     category dnssec { dnssec_log; default_debug; };
     category notify { zone_transfers_log; default_debug; };
     category xfer-in { zone_transfers_log; default_debug; };
     category xfer-out { zone_transfers_log; default_debug; };
     category update{ ddns_log; default_debug; };
     category update-security { ddns_log; default_debug; };
     category client{ client_security_log; default_debug; };
     category security { client_security_log; default_debug; };
     category rate-limit { rate_limiting_log; default_debug; };
     category spill { rate_limiting_log; default_debug; };
     category database { rate_limiting_log; default_debug; };
     category rpz { rpz_log; default_debug; };
     category dnstap { dnstap_log; default_debug; };
     category trust-anchor-telemetry { default_syslog; default_debug; default_log; };
     category queries { queries_log; };
     category query-errors {query-errors_log; };
};
```

5. Create following folders (change directories to match your enviroment)

-   `Z:/Apache24/htdocs/zpanel/bin/bind/ets/logs`
-   `Z:/Apache24/htdocs/zpanel/bin/bind/zones`

_FTP part_

**Currently, the latest servers are not supported. Please use similar versions to listed at the beginning of installation**

1. Download and install [FTP server](https://filezilla-project.org/download.php?type=server)

In my case FTP server will be installed in `Z:/Apache24/htdocs/zpanel/bin/filezilla` direcotry

_hMail server part_

1. Download and install [hMail server](https://www.hmailserver.com/download)

It is recommended to create new `MySQL` database with database name `zpanel_hmail` during first configuration

In my case FTP server will be installed in `Z:/hMail` direcotry

#### On ready enviroment

1. Set correct database credentials in `cnf/db.php` file

2. Set up password in CMD for "zadmin" user via `php setzadmin --set <password>` command in `bin` folder

3. Set correct domain for password reset with following command in zpanel database

    ```sql
    UPDATE `x_settings` SET `so_value_tx` = "yourdomain.com" WHERE `x_settings`.`so_name_vc` = "zpanel_domain";
    ```

4. Set correct paths if zpanel isn't installed in default direcotry like in this example

    (In my case it look like this)

    ```sql
    UPDATE `x_settings` SET `so_value_tx` = REPLACE(`so_value_tx`, "C:/zpanel/panel", "Z:/Apache24/htdocs/zpanel");
    ```

5. Create following folders (change directions to match your enviroment)

-   `Z:/Apache24/htdocs/zpanel/logs`
-   `Z:/Apache24/htdocs/zpanel/logs/bind`

#### _At this point after starting/restarting Apache server you shoud be able to login to panel_

1. Go to `Admin/Module Admin` and configure right paths to Apache, DNS, FTP and Mail modules

2. Go to `zpanel.localhost/etc/apps/webmail/installer/` and install WebMail module

3. Add file located in zpanel `bin/daemon.php` to run every few minutes (It is responsible for applying changes). Runing `bin/daemon.bat` (as Administrator) is alternative way

## Planned changes and new features

-   support for new Filezilla Server
-   single page application UI
-   simple installation page
