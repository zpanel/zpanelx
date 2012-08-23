<?php

/*
  +-----------------------------------------------------------------------+
  | Configuration file for database access                                |
  |                                                                       |
  | This file is part of the Roundcube Webmail client                     |
  | Copyright (C) 2005-2009, The Roundcube Dev Team                       |
  | Licensed under the GNU GPL                                            |
  |                                                                       |
  +-----------------------------------------------------------------------+

 */

$rcmail_config = array();

// PEAR database DSN for read/write operations
// format is db_provider://user:password@host/database 
// For examples see http://pear.php.net/manual/en/package.database.mdb2.intro-dsn.php
// currently supported db_providers: mysql, mysqli, pgsql, sqlite, mssql or sqlsrv
#$rcmail_config['db_dsnw'] = 'mysql://root:YOUR_ROOT_MYSQL_PASSWORD@localhost/zpanel_roundcube';
// postgres example: 'pgsql://roundcube:pass@localhost/roundcubemail';
// Warning: for SQLite use absolute path in DSN:
// sqlite example: 'sqlite:////full/path/to/sqlite.db?mode=0646';
// PEAR database DSN for read only operations (if empty write database will be used)
// useful for database replication
$rcmail_config['db_dsnr'] = '';

// maximum length of a query in bytes
$rcmail_config['db_max_length'] = 512000;  // 500K
// use persistent db-connections
// beware this will not "always" work as expected
// see: http://www.php.net/manual/en/features.persistent-connections.php
$rcmail_config['db_persistent'] = FALSE;


// you can define specific table names used to store webmail data
$rcmail_config['db_table_users'] = 'users';
$rcmail_config['db_table_identities'] = 'identities';
$rcmail_config['db_table_contacts'] = 'contacts';
$rcmail_config['db_table_contactgroups'] = 'contactgroups';
$rcmail_config['db_table_contactgroupmembers'] = 'contactgroupmembers';
$rcmail_config['db_table_session'] = 'session';
$rcmail_config['db_table_cache'] = 'cache';
$rcmail_config['db_table_cache_index'] = 'cache_index';
$rcmail_config['db_table_cache_thread'] = 'cache_thread';
$rcmail_config['db_table_cache_messages'] = 'cache_messages';


// you can define specific sequence names used in PostgreSQL
$rcmail_config['db_sequence_users'] = 'user_ids';
$rcmail_config['db_sequence_identities'] = 'identity_ids';
$rcmail_config['db_sequence_contacts'] = 'contact_ids';
$rcmail_config['db_sequence_contactgroups'] = 'contactgroups_ids';
$rcmail_config['db_sequence_cache'] = 'cache_ids';
$rcmail_config['db_sequence_searches'] = 'search_ids';


// end db config file

