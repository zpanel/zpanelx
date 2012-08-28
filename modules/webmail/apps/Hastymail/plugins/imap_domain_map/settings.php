<?php

/*  settings.php: Settings file for the imap domain map
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

$server_map = array(
    'imap1.hostname' => array( /* this is the IMAP server name defined using alt_1, alt_2, etc in the config file */
        'domain1.com',  /* domains in login email addresses to map to the above imap server */
        'domain2.com',
        'domain3.com',
    ),
    'imap2.hostname' => array(
        'domain3.com',
        'domain4.com',
        'domain5.com',
    )
);

/* after the username is matched to one of the IMAP servers above, set the following to
 * true to strip the domain part from the username before logging in
 */

$strip_domain = false;
 
?>
