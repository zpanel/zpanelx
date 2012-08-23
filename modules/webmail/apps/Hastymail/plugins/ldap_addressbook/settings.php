<?php

/*  settings.php: Settings file for the ldap addressbook plugin
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

* $Id:$
*/


/*
LDAP server hostname or ip address */
$ldap_server = 'ldap.domain.com';


/*
Use LDAP over tls/ssl by setting this to true */
$ldap_ssl = false;


/*
The LDAP server port to connect to */
$ldap_port = 389;


/*
The LDAP server base DN */
$ldap_base_dn = 'dc=domain,dc=com';


/*
Optional value to narrow the LDAP search to a portion of the LDAP tree
otherwise cn=* is used */
$ldap_search_term = 'objectclass=inetOrgPerson';


/*
Hastymail defaults to an aononymous LDAP connection. Set the following
option to true to have Hastymail attempt to bind to the LDAP server
with the users IMAP login username and password */
$ldap_auth = true;


/*
The LDAP bind password which overrides the IMAP password when using ldap_auth */
$ldap_bind_pass = false;


/*
Force LDAP protocol version 3 */
$ldap_version_3 = false;


/*
The name of this contact source that is displayed on the compose page */
$contact_label = 'LDAP';


/*
LDAP rdn format used when binding to the LDAP server with the $ldap_auth
setting. Use %u to insert the username and %b to insert the base DN into
the resulting rdn. */
$rdn_format = 'uid=%u,ou=People,%b';


/*
an array of LDAP fields to collect and use to build the display name in the
contact list. Multiple values will be displayed with spaces between them in
the order they appear in this array */
$ldap_name_flds = array('cn');

?>
