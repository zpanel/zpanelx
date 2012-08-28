<?php
// Setup standard module env
require_once(dirname(__FILE__) . '/module_header.php');

require_once('Abook.class.php');
require_once('Config.php');
require_once('DB.php');

define('SCRIPT_USAGE_MSG',"usage: php migrate-abook-horde.php [horde_database_name] [sql_username:sql_password] [sql_hostname(optional)]\n");

// get horde database details
$db = $_SERVER['argv'][1];
list($sqluser, $sqlpass) = explode(':', $_SERVER['argv'][2]);
$sqlhost = (isset($_SERVER['argv'][3])) ? $_SERVER['argv'][3] : 'localhost';

// Check for correct command useage
if (empty($db) || empty($sqluser)) {
	fwrite(STDOUT, "Please specify the database name and sql username for accessing the Horde addressbook\n");
	fwrite(STDOUT, SCRIPT_USAGE_MSG);
	exit;
}

$sql = DB::connect("mysql://$sqluser:$sqlpass@$sqlhost/$db");



if (DB::isError($sql)) {
    fwrite(STDOUT, "There was an error connecting to '$db', please details and try again...\n");
    fwrite(STDOUT, SCRIPT_USAGE_MSG);
    exit;
}

// Check for horde address table
$tables = $sql->getListOf('tables');
if (!in_array('turba_objects', $tables)) {
    fwrite(STDOUT, "It appears the database specified ($db) may not be the Horde database,\nthe table 'turba_objects' could not be found...\n");
    fwrite(STDOUT, "Please specify the database name for the Horde addressbook\n");
    fwrite(STDOUT, SCRIPT_USAGE_MSG);
    exit;
}


$owner = $sql->getCol("SELECT owner_id
					   FROM $db.turba_objects
					   GROUP BY owner_id");

foreach ($owner as $own) {

	$own = strtolower($own);

	// Get each object_ids from the abook
	fwrite(STDOUT, "Viewing records for $own ...\n");
	$objects = $sql->getCol("SELECT object_id
							 FROM $db.turba_objects
							 WHERE owner_id = ?", 0, array($own));
	foreach ($objects as $object) {

		if (!$object)
            continue;

		// Save the unique abook entry into the @Mail table
		$row = $sql->getRow("SELECT *
							 FROM $db.turba_objects
							 WHERE object_id = ? AND owner_id = ?", array($object, $own), DB_FETCHMODE_ASSOC);

		// Get first/last name
		preg_match_all('/(\w+)\s+(.*)/', $row['object_name'], $result);
		$firstname = $result[1][0];
		$lastname = $result[2][0];
		unset($result);

		// Insert the entry into the DB
		$abook = new Abook( array('Account' => $own) );
		$id = $abook->add(
            array(
		        'UserFirstName'   => $firstname,
		        'UserLastName'    => $lastname,
		        'UserEmail'       => $row['object_email'],
		        'UserHomeAddress' => $row['object_homeaddress'],
		        'UserWorkAddress' => $row['object_workaddress'],
		        'UserHomePhone'	  => $row['object_homephone'],
		        'UserWorkPhone'	  => $row['object_workphone'],
		        'UserHomeMobile'  => $row['object_cellphone'],
		        'UserHomeFax'	  => $row['object_fax'],
		        'UserTitle'		  => $row['object_title'],
		        'UserWorkCompany' => $row['object_company'],
		        'UserInfo'		  => $row['object_notes'],
		        'UserPgpKey'      => $row['object_pgppublickey']
		        )
		);

		if (is_numeric($id))
	        fwrite(STDOUT, "Added '{$firstname} {$lastname}' <{$row['object_email']}>\n");
	    else
	        fwrite(STDOUT, "ERROR adding '{$firstname} {$lastname}' <{$row['object_email']}>\n");
	}

}

fwrite(STDOUT, "Abook Migration complete.\n");
?>