<?php

/*  settings.php: settings configuration for the login_alias plugin
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

/* storage_type can be either file or db. if db is selected then hastymail must
   be configured to use a db. */
$storage_type = 'file';

/* if storage_type is set to file then this is the location of the alias lookup file.
   the format format for each line is the login username followed by a space then the
   imap username 
*/
$file_location = '/var/hastymail2/login_alias';

/* table_name is the name of the table to lookup login alias usernames with. sql files
   to create the login_alias table for mysql and postgres are included in this directory.
*/
$table_name = 'login_alias';
?>
