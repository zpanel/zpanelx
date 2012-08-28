<?php
/********************************************************

    conf/db_conf.php
    
	PURPOSE:
		Provide central location for configuring DB-related variables.
		This file replaces (or will replace) the mysqlrc.inc file.

********************************************************/

// DB connection/login info
$DB_HOST="";
$DB_USER="";
$DB_PASSWORD="";

// database name
// ***REQUIRED***
$DB_NAME="webmail";

// database brand
// ***REQUIRED***
$DB_TYPE="MySQL";

// Users table name
// ***REQUIRED***
$DB_USERS_TABLE = "users";

// Sessions table name
// ***REQUIRED***
$DB_SESSIONS_TABLE = "sessions";

// Contacts table name
$DB_CONTACTS_TABLE = "contacts";

// Prefs table name
$DB_PREFS_TABLE = "prefs";

// Colors table name
$DB_COLORS_TABLE = "colors";

// Identities table name
$DB_IDENTITIES_TABLE = "identities";

// Calendars table name
$DB_CALENDAR_TABLE = "calendar";

// Bookmarks table name
$DB_BOOKMARKS_TABLE = "bookmarks";

// Bookmarks table name
//		Optional: Comment out to use file based backend
$DB_CACHE_TABLE = "cache";

// Log table name
//		Optional: Comment out to use file based backend
//$DB_LOG_TABLE = "user_log";

// Use persistent connections
//		Optional: Set to 'true' to enable
$DB_PERSISTENT = false;

?>
