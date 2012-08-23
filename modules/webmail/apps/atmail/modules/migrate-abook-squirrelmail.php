<?php

// Setup standard module env
require_once(dirname(__FILE__) . '/module_header.php');

require_once('Abook.class.php');
require_once('Config.php');
require_once('Flat.php');
require_once('DB.php');


define('SCRIPT_USAGE_MSG', "usage: php migrate-abook-squirrelmail.php [squirrelmail_database_name] [sql_username:sql_password] [sql_hostname(optional)]\n");

// get squirrelmail database details
$db = $_SERVER['argv'][1];
list($sqluser, $sqlpass) = explode(':', $_SERVER['argv'][2]);
$sqlhost = ($_SERVER['argv'][3]) ? $_SERVER['argv'][3] : 'localhost';

// Check for correct command useage
if (empty($db) || empty($sqluser)) {
	fwrite(STDOUT, "Please specify the database name and sql username for accessing the Squirrelmail addressbook\n");
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
if (!in_array('address', $tables)) {
    fwrite(STDOUT, "It appears the database specified ($db) may not be the squirrelmail database,\nthe table 'address' could not be found...\n");
    fwrite(STDOUT, "Please specify the database name for the Squirrelmail addressbook\n");
    fwrite(STDOUT, SCRIPT_USAGE_MSG);
    exit;
}


// presuming all is ok

$owner = $sql->getCol("select owner from $db.address group by owner");

foreach ($owner as $own) {

	$own = strtolower($own);

	// Get each unique email from the abook
	fwrite(STDOUT, "Viewing records for $own ...\n");
	$emails = $sql->getCol("select email from $db.address where owner=? group by email", 0, array($own));

	foreach ($emails as $email) {

		if (!$email)
            continue;

		// Save the unique abook entry into the @Mail table
		$row = $sql->getRow("select * from $db.address where owner=? and email=?", array($own, $email), DB_FETCHMODE_ASSOC);

		// Insert the entry into the DB
		$abook = new Abook( array('Account' => $own) );
		$id = $abook->add(
            array(
		        'UserFirstName' => $row['firstname'],
		        'UserLastName'  => $row['lastname'],
		        'UserEmail'     => $row['email']
		        )
		);

		if (is_numeric($id))
	        fwrite(STDOUT, "Added '{$row['firstname']} {$row['lastname']}' <{$row['email']}>\n");
	    else
	        fwrite(STDOUT, "ERROR adding '{$row['firstname']} {$row['lastname']}' <{$row['email']}>\n");
	}

}

fwrite(STDOUT, "Abook Migration complete.\n");

?>