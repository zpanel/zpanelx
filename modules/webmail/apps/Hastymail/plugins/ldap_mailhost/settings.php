<?php

// this is the mapping of IMAP servers that corresponds to
// the alt_1, alt_2, etc syntax in hastymail.conf
// The right hand side of the mapping corresponds to the 
// value of the attribute in the user's LDAP entry
$server_map = array(
    '1' => '__STORE_NAME_PRE__1pvt.pri.doit.wisc.edu',
    '2' => '__STORE_NAME_PRE__2pvt.pri.doit.wisc.edu',
    '3' => '__STORE_NAME_PRE__3pvt.pri.doit.wisc.edu',
    '4' => '__STORE_NAME_PRE__4pvt.pri.doit.wisc.edu',
    '5' => '__STORE_NAME_PRE__5pvt.pri.doit.wisc.edu',
    '6' => '__STORE_NAME_PRE__6pvt.pri.doit.wisc.edu',
    '7' => '__STORE_NAME_PRE__7pvt.pri.doit.wisc.edu',
    '8' => '__STORE_NAME_PRE__8pvt.pri.doit.wisc.edu'
);

// This is the LDAP server to connect to
$ldap_server = "ldap.mydomain.org";

// This is the LDAP bind dn to authenticate as
$ldap_user = "uid=foo,ou=apps,o=isp";

// This is the LDAP bind password
$ldap_pass = "12345";

// This is the LDAP attribute that contains the IMAP server
$mailhost_attr = 'mailhost';

// This is the LDAP search filter for searching for the user entry
// It is used in sprintf() with the username
$uid_filter_format = "uid=%s";

// For security reasons, make sure the supplied username matches this
$uid_pattern = '/^[\w+\.\-]{1,50}$/';

// This is the format used with sprintf with the domain
// to set the LDAP search base
$domain_base_format = "o=%s,o=isp";

// For security reasons, make sure the supplied domain matches this
$domain_pattern = '/^[\w\.\-]{1,50}$/';

// If no domain is specified by the user then this search base is used
$default_base = "o=mydomain.org,o=isp";

?>
