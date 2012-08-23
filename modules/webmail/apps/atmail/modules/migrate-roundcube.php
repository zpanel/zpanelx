<?php

// Setup standard module env
require_once(dirname(__FILE__) . '/module_header.php');

require_once('Abook.class.php');
require_once('Config.php');
require_once('Flat.php');
require_once('DB.php');


define('SCRIPT_USAGE_MSG', "usage: php migrate-roundcube.php [rc_database_name] [sql_username:sql_password] [sql_hostname(optional)]\n");

// get squirrelmail database details
$db = $_SERVER['argv'][1];
list($sqluser, $sqlpass) = explode(':', $_SERVER['argv'][2]);
$sqlhost = ($_SERVER['argv'][3]) ? $_SERVER['argv'][3] : 'localhost';

// Check for correct command useage
if (empty($db) || empty($sqluser)) {
	fwrite(STDOUT, "Please specify the database name and sql username for accessing the RoundCube database\n");
	fwrite(STDOUT, SCRIPT_USAGE_MSG);
	exit;
}

$sql = DB::connect("mysql://$sqluser:$sqlpass@$sqlhost/$db");

// Check that the specified database exists
//$databases = $sql->sqlarray('show databases');

if (DB::isError($sql)) {
    fwrite(STDOUT, "There was an error connecting to '$db' ($error), please details and try again...\n");
    fwrite(STDOUT, SCRIPT_USAGE_MSG);
    exit;
}

// Check for squirrelmail address table
$tables = $sql->getListOf('tables');
if (!in_array('contacts', $tables) && !in_array('identities', $tables)) {
    fwrite(STDOUT, "It appears the database specified ($db) may not be the RoundCube database,\nthe tables 'contacts' and 'identities' could not be found...\n");
    fwrite(STDOUT, "Please specify the database name for RoundCube\n");
    fwrite(STDOUT, SCRIPT_USAGE_MSG);
    exit;
}


// presuming all is ok

$query = "select contacts.user_id, users.username from $db.users, $db.contacts where users.user_id = contacts.user_id group by users.user_id";
$owner = $sql->getAll($query, array(), DB_FETCHMODE_ASSOC);

foreach ($owner as $own) {

	// Get each unique email from the abook
	fwrite(STDOUT, "fetching records for {$own['username']} ...\n");

	// Save the unique abook entry into the @Mail table
	$rows = $sql->getAll("select email, firstname, surname from $db.contacts where user_id = ?", array($own['user_id']), DB_FETCHMODE_ASSOC);

	foreach($rows as $row) {

		// Insert the entry into the DB
		$abook = new Abook( array('Account' => $own['username']) );
		$id = $abook->add(
	        array(
		        'UserFirstName' => $row['firstname'],
		        'UserLastName'  => $row['surname'],
		        'UserEmail'     => $row['email']
		        )
		);
	
		if (is_numeric($id))
	        fwrite(STDOUT, "Added '{$row['firstname']} {$row['surname']}' <{$row['email']}>\n");
	    else
	        fwrite(STDOUT, "ERROR adding '{$row['firstname']} {$row['surname']}' <{$row['email']}>\n");
	}
}

fwrite(STDOUT, "Abook Migration complete.\n");

?>