<?php
##########################################################################################################
# Set up environment                                                                                     #
##########################################################################################################
$stopnamed="net stop named";
$rndcconfgen="D:/zpanel/bin/bind/bin/rndc-confgen.exe";
$rcnd="D:/zpanel/bin/bind/etc/rndc.conf";
$logdir='D:/zpanel/logs/bind/';
$nameddir="D:\\zpanel\\bin\\bind\\etc\\";
$zonedir="D:/zpanel/bin/bind/zones/";
$zonedir="D:/zpanel/bin/bind/zones/";
$configdir="D:/zpanel/configs/bind/";
$configetcdir="D:/zpanel/configs/bind/etc/";
$configzonedir="D:/zpanel/configs/bind/zones/";
##########################################################################################################
# Set up environment complete                                                                            #
##########################################################################################################

#stop bind server if running
@system($stopnamed);
$secret ="";
#generate rcnd.conf
system(''.$rndcconfgen.' > '.$rcnd.'');
#get the key from rcnd
if(file_exists($rcnd)){
$d = file($rcnd);
	foreach($d as $v) {
		if (strstr($v, 'secret') && !strstr($v, '#')){
		$secret=$v;
		break;
		}
	}
}
#generate bind log directory
if(!is_dir($logdir)){
	mkdir($logdir, true);
}
#generate bind config directory
if(!is_dir($configdir)){
	mkdir($configdir, 0777, true);
}
#generate bind config etc directory
if(!is_dir($configetcdir)){
	mkdir($configetcdir, 0777, true);
}
#generate bind config zone directory
if(!is_dir($configzonedir)){
	mkdir($configzonedir, 0777, true);
}
#create the default zpanel /config/bind/etc/named.conf include file so bind will start
$handle = fopen($configetcdir . 'named.conf', 'w');
	if(!$handle){
	exit;
	}
$data='';
$write = fwrite($handle, $data);
fclose($handle);
#generate named.conf and add secret key
if(is_dir($nameddir)){
	$handle = fopen(''.$nameddir.'named.conf', 'w');
	if(!$handle){
	exit;
	}
$named='include "'.$nameddir.'key.conf";
options {
	directory "'.$zonedir.'";
	version "zpanel_'.rand().'";
	allow-transfer { none; };
	recursion yes;
};
controls {
	inet 127.0.0.1 port 953
	allow { 127.0.0.1; } keys { "rndc-key"; };
};
logging {
	channel bind_log{
	file "'.$logdir.'bind.log" versions 3 size 2m;
	severity info;
	print-severity yes;
	print-time yes;
	print-category yes;
	};
	category default{
	bind_log;
	};
};
zone "." IN {
	type hint;
	file "root.servers.zone";
};
zone "0.0.127.in-addr.arpa" IN {
	type master;
	file "localhost.rev.zone";
};
zone "localhost" IN {
  	type master;
  	file "localhost.zone";
};
include "D:\\zpanel\\configs\\bind\\etc\\named.conf";
include "D:\\zpanel\\configs\\bind\\etc\\named.conf.slave";';
$write = fwrite($handle, $named);
fclose($handle);
#configure keyfile
$handle = fopen(''.$nameddir.'key.conf', 'w');
	if(!$handle){
	exit;
	}
$key='key "rndc-key" {
	algorithm hmac-md5;
    '.$secret.'
};';
$write = fwrite($handle, $key);
fclose($handle);
}
#configure localhost zone
if(is_dir($zonedir)){
$handle = fopen(''.$zonedir.'localhost.zone', 'w');
	if(!$handle){
	exit;
	}
$zone='$TTL	86400
$ORIGIN localhost.
@  1D  IN	 SOA @	root (
			      '.time().' ; serial
			      3H ; refresh
			      15 ; retry
			      1w ; expire
			      3h ; minimum
			     )
@  1D  IN  NS @ 
   1D  IN  A  127.0.0.1
';
$write = fwrite($handle, $zone);
fclose($handle);
#configure localhost reverse zone
$handle = fopen(''.$zonedir.'localhost.rev.zone', 'w');
	if(!$handle){
	exit;
	}
$zone='$TTL	86400
$ORIGIN 0.0.127.IN-ADDR.ARPA.
@       IN      SOA     localhost. root.localhost.  (
                        '.time().' ; Serial
                        3h      ; Refresh
                        15      ; Retry
                        1w      ; Expire
                        3h )    ; Minimum
        IN      NS      localhost.
1       IN      PTR     localhost.
';
$write = fwrite($handle, $zone);
fclose($handle);
#configure managed keys (blank file to get rid of error in log)
$handle = fopen(''.$zonedir.'managed-keys.bind', 'w');
	if(!$handle){
	exit;
	}
$zone='';
$write = fwrite($handle, $zone);
fclose($handle);
#configure root hints zone
$handle = fopen(''.$zonedir.'root.servers.zone', 'w');
	if(!$handle){
	exit;
	}
$zone='.			518400	IN	NS	g.root-servers.net.
.			518400	IN	NS	c.root-servers.net.
.			518400	IN	NS	h.root-servers.net.
.			518400	IN	NS	e.root-servers.net.
.			518400	IN	NS	b.root-servers.net.
.			518400	IN	NS	d.root-servers.net.
.			518400	IN	NS	k.root-servers.net.
.			518400	IN	NS	f.root-servers.net.
.			518400	IN	NS	i.root-servers.net.
.			518400	IN	NS	l.root-servers.net.
.			518400	IN	NS	j.root-servers.net.
.			518400	IN	NS	m.root-servers.net.
.			518400	IN	NS	a.root-servers.net.
a.root-servers.net.	3600000	IN	A	198.41.0.4
b.root-servers.net.	3600000	IN	A	192.228.79.201
c.root-servers.net.	3600000	IN	A	192.33.4.12
d.root-servers.net.	3600000	IN	A	128.8.10.90
e.root-servers.net.	3600000	IN	A	192.203.230.10
f.root-servers.net.	3600000	IN	A	192.5.5.241
g.root-servers.net.	3600000	IN	A	192.112.36.4
h.root-servers.net.	3600000	IN	A	128.63.2.53
i.root-servers.net.	3600000	IN	A	192.36.148.17
j.root-servers.net.	3600000	IN	A	192.58.128.30
k.root-servers.net.	3600000	IN	A	193.0.14.129
l.root-servers.net.	3600000	IN	A	199.7.83.42
m.root-servers.net.	3600000	IN	A	202.12.27.33
a.root-servers.net.	3600000	IN	AAAA	2001:503:ba3e::2:30
f.root-servers.net.	3600000	IN	AAAA	2001:500:2f::f
';
$write = fwrite($handle, $zone);
fclose($handle);
}
#set permissions on bind directory
system('CACLS D:/zpanel/bin/bind/*.* /T /E /C /G "Users":C');
?>