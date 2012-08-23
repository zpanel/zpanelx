<?php
// +----------------------------------------------------------------+
// | Abook.php  													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: Febuary 2005												|
// +----------------------------------------------------------------+

require_once('header.php');

require_once('Global.php');

class Abook
{

    var $db;

    // We want to ignore some fields that may exist if
	// the import file was produced from an @Mail abook export
	var $ignoreFields = array('id', 'DateAdded', 'UserType', 'EntryID', 'DateModified', 'Account');
	var $limit = '100000';

	function Abook($arg)
	{
		global $atmail;

	    foreach ($arg as $k => $v)
	        $this->$k = $v;

	    if (!isset($atmail))
	    {
	        require_once('SQL.php');
	        require_once('Global.php');
            $this->db = new SQL();
            $this->Account_Escape = $this->db->quote($this->Account);
	    }
        else
        {
            $this->db = $atmail->share_db();
            $this->Account_Escape = $this->db->quote($atmail->Account);
        }

        $this->db->table_names($this->Account);
        $this->Type = "sql";

		$this->Abook = Filter::cleanSqlFieldNames($this->db->Abook); //'Abook_' . substr($atmail->Account, 0, 1);
		$this->Ugroup = $this->db->quote( $this->db->getvalue("SELECT Ugroup
															   FROM Users
															   WHERE Account = $this->Account_Escape") );

		// Find our domain-name
		$pos = strpos($atmail->Account, '@');
		$this->pop3host = $this->db->quote(substr($atmail->Account, $pos+1));

		// Default limit for abook entries, if undefined ( e.g from sync.php )
		$this->limit = '100000';

	}


	/**
	 * Return an array index
	 *
	 * @param string $type
	 * @param string $sort
	 *
	 * @return array $id
	 */
	function abookid($type, $sort = "UserEmail")
	{
		global $atmail;

		$id = array();

		$sort = Filter::cleanSqlFieldNames($sort);

		if ($type == "personal")
		{
			$query = "SELECT id FROM $this->Abook
					  WHERE Account = $this->Account_Escape
					  ORDER BY $sort DESC";

			$id = $this->db->sqlarray($query);
		}
		
		return $id;
	}

	function addpermission($abookid, $account, $permission)
	{
		// Do not insert the entry if we are missing a field
		//if (!$account || !$abookid || !$permission);
		//    return 1

		// Check we do not already exist
		if($this->db->sqlgetfield("select AbookID from AbookPermissions where AbookID=? and Account=? and Permissions=?", array($abookid, $account, $permission)))
		return 1;

		// Append the data into the AbookPermissions table
		$res = $this->db->sqldo("INSERT INTO AbookPermissions ( AbookID, Account, Permissions ) VALUES ( ? , ?, ? ) ", array($abookid, $account, $permission));

		return 0;
	}

	// Add an addressbook entry
	function add($user)
	{
		// Take away ' , < and > characters from the Email ; they are illegal characters
		$user['Email'] = str_replace(array("'", '<', '>'), '', $user['Email']);
		$user['DateModified'] = ($user['DateModified'])? $user['DateModified'] : AtmailGlobal::outlook_date();
		$user['UserDOB'] = ($user['UserDB']) ? $user['UserDB'] : '';
		
		$data = array(
			$user['UserEmail'],
			$user['UserEmail2'],
			$user['UserEmail3'],
			$user['UserEmail4'],
			$user['UserEmail5'],
			$user['UserFirstName'],
			$user['UserMiddleName'],
			$user['UserLastName'],
			$user['UserTitle'],
			$user['UserGender'],
			$user['UserDOB'],
			$user['UserHomeAddress'],
			$user['UserHomeCity'],
			$user['UserHomeState'],
			$user['UserHomeZip'],
			$user['UserHomeCountry'],
			$user['UserHomePhone'],
			$user['UserHomeMobile'],
			$user['UserHomeFax'],
			$user['UserURL'],
			$user['UserWorkCompany'],
			$user['UserWorkTitle'],
			$user['UserWorkDept'],
			$user['UserWorkOffice'],
			$user['UserWorkAddress'],
			$user['UserWorkCity'],
			$user['UserWorkState'],
			$user['UserWorkZip'],
			$user['UserWorkCountry'],
			$user['UserWorkPhone'],
			$user['UserWorkMobile'],
			$user['UserWorkFax'],
			$user['UserInfo'],
			$user['UserPgpKey'],
			$user['DateModified'],
			$user['EntryID']);

		$res = $this->db->sqldo( "INSERT INTO $this->Abook (
			UserEmail,
			UserEmail2,
			UserEmail3,
			UserEmail4,
			UserEmail5,
			UserFirstName,
			UserMiddleName,
			UserLastName,
			UserTitle,
			UserGender ,
			UserDOB,
			UserHomeAddress,
			UserHomeCity,
			UserHomeState,
			UserHomeZip,
			UserHomeCountry,
			UserHomePhone,
			UserHomeMobile,
			UserHomeFax,
			UserURL,
			UserWorkCompany,
			UserWorkTitle,
			UserWorkDept,
			UserWorkOffice,
			UserWorkAddress,
			UserWorkCity,
			UserWorkState,
			UserWorkZip,
			UserWorkCountry,
			UserWorkPhone,
			UserWorkMobile,
			UserWorkFax,
			UserInfo,
			UserPgpKey,
			Account,
			DateModified,
			EntryID,
			DateAdded
			) VALUES (
			?,?,?,?,?,?,
			?,?,?,?,?,?,
			?,?,?,?,?,?,
			?,?,?,?,?,?,
			?,?,?,?,?,?,
			?,?,?,?, $this->Account_Escape, ?, ?, {$this->db->NOW}
			)", $data);

        if (!DB::isError($res)) {
		    return $this->db->getid();
        }
        
	    return false;
	}


	/**
	 * List all users in the addressbook
	 *
	 * @param string $sort sorking order
	 * @param string [$fields] needed fields
	 *
	 * @return array $h
	 */

	function view($sort, $fields='*', $start=0, $direction='Up')
	{
	    global $atmail, $pref;

	    $h = array();
		if (strlen($this->limit) == 0 ) {
			$this->limit = 0;
		}

	    if ( !$sort ) $sort = "UserEmail" ;

	    if ($direction == 'Down') {
	    	$direction = 'desc';
	    } else {
	    	$direction = 'asc';
	    }

	    $sort = Filter::cleanSqlFieldNames($sort);
	    $fields = Filter::cleanSqlFieldNames($fields);

	    if (!is_numeric($start))
	       $start = 0;

	    // List all users from the personal addressbook
	    $users = $this->db->sqlarray( "SELECT id
	                                     FROM $this->Abook
	                                     WHERE Account = $this->Account_Escape
	                                     ORDER BY $sort $direction LIMIT $start,$this->limit");

	    if (is_array($users)) {

	        foreach ($users as $id)
	        {
	            $db = $this->db->sqlhash("SELECT $fields
	                                      FROM $this->Abook
	                                      WHERE Account = $this->Account_Escape AND id = ?", array($id));

	            $indexid = $db['id'];

	            foreach ($db as $k => $v)
	            $h[$indexid][$k] = $v;

	            // Disallow the user to edit the entry if 'Read' access is granted to the user via the Webadmin Group
	            if ($pref['GlobalAbookRead'] && !$pref['GlobalAbook'])
	            $h[$indexid]['Permissions'] = 0 ;

	        }
	    }
	    
	    // let's get the ldap results and show them in the addressbook
	    // _IF_ the config file says so!
	    if ($pref['addressbook_ldap_entries']) {
			$db['abookview'] = 'ldap';
			$db['Account'] = '*';
			$db['FirstName'] = '*';
			$db['LastName'] = '*';
			$ldap_results = $this->_search_ldap($db);
	
			// get highest array value for appending ldap stuff onto
			$array_max = array_keys($h);
			$key_max = max($array_max);
	
			// let's add 1 onto it as always! 
			$max_id = $key_max;
			$i = $max_id+1;
 
			foreach($ldap_results as $ldap_entry) {
				$temp['UserEmail']     = $ldap_entry['Account'];           
				$temp['UserFirstName'] = $ldap_entry['UserFirstName'];
				$temp['UserLastName']  = $ldap_entry['UserLastName'];
				$temp['UserGender']    = '';                         
				$temp['UserHomePhone'] = '';
				$temp['UserWorkPhone'] = $ldap_entry['UserWorkPhone'];
				$temp['id']            = $i;
				$temp['ldap']          = 1;
				$h[$i]                 = $temp;
				$i+=1;         
			}

			// determine sort function to use
			$sortfunction = "sort" . $sort;
	
			// sort functions for array shenanigans (messy perhaps?!)
			function sortUserEmail($a,$b) { return (strcmp ($a['UserEmail'],$b['UserEmail'])); }
			function sortUserLastName($a,$b) { return (strcmp ($a['UserLastName'],$b['UserLastName'])); }
			function sortUserWorkPhone($a,$b) { return (strcmp ($a['UserWorkPhone'],$b['UserWorkPhone'])); }
			function sortUserHomePhone($a,$b) { return (strcmp ($a['UserHomePhone'],$b['UserHomePhone'])); }
	
			// sort the array out
			uasort($h, $sortfunction);
	
			// reverse that array if we're descending (never used at present?)
			if ($direction == 'desc') {
				array_reverse($h, TRUE);
			}
	    }

	    return $h;
	}

	function get_abook_size()
	{
	    global $atmail;
	    return $this->db->getvalue("SELECT COUNT(id)
	                                FROM $this->Abook
	                                WHERE Account = $this->Account_Escape");
	}


	/**
	 * List all groups in the personal addressbook
	 *
	 * @return array $h selected groups
	 */
	function listgroup()
	{
		global $atmail;

	    $h = array();

	    $abookGroup = Filter::cleanSqlFieldNames($this->db->AbookGroup);

	    // users from addressbooks for current account
	    $users = $this->db->sqlarray("SELECT id
	                                  FROM $abookGroup
	                                  WHERE Account = $this->Account_Escape AND GroupEmail != ''");

	    foreach ($users as $user)
	    {
	        $user = $this->db->quote($user);
	        $v    = $this->db->getvalue("SELECT GroupName
	                                     FROM $abookGroup
	                                     WHERE id = $user");

	        $h[$v] .= $this->db->getvalue("SELECT GroupEmail
	                                       FROM $abookGroup
	                                       WHERE id = $user") . ", ";
	    }

	    return $h;
	}


	/**
	 * List a selected group in the personal addressbook
	 *
	 * @param int $id personal group id
	 * @return array $db
	 */
	function listpersonalgroup($id)
	{
		global $atmail;

	    $h = array();

		$id = $this->db->quote($id);
        $abookGroup = Filter::cleanSqlFieldNames($this->db->AbookGroup);

		// Load all the data for the shared group, if our account has permissions to do so
		$db = $this->db->sqlhash("SELECT *
								  FROM $this->Abook
								  WHERE $this->Abook.id = $id AND $this->Abook.Account = $this->Account_Escape");

		// List users within the group
		$users = $this->db->sqlarray("SELECT $abookGroup.GroupEmail
									  FROM $this->Abook, $abookGroup
									  WHERE $this->Abook.id = $id  AND $abookGroup.GroupName = $this->Abook.UserEmail
									  AND $abookGroup.Account = $this->Account_Escape");

		$db['UsersArray'] = implode(",", $users);

	    return $db;
	}

	// Search the user table
	function search($sort=null, $db=null, $start=0)
	{
		if ($db['abookview'] == 'ldap')
		{
			return $this->_search_ldap($db);
		}

		global $atmail, $pref;

		// Init some vars
	    $h = $users = $results = array();
        $extend = '';

        if (!is_numeric($start))
            $start = 0;

	    if (empty($sort))
	    	$sort = "Account" ;
        else
    	    $sort = Filter::cleanSqlFieldNames($sort);

    	$db['SearchType'] = Filter::stringMatch($db['SearchType'], array('and', 'or'));

		if (!$db['SearchType'])
			$db['SearchType'] = 'and';

		if ($db['SearchType'] == 'or')
			$extend = 'and (';

		foreach ( array('FirstName', 'LastName', 'Account', 'UserWorkCompany', 'UserHomeAddress', 'UserHomeCity', 'UserHomeState', 'UserHomeCountry') as $field )
		{
			// Toggle which database we are using
			if ($db['abookview'] == "personal")
				$type = $this->Abook;

			if ($field == "FirstName" && !empty($db[$field]) )
				$extend .= "{$db['SearchType']} $type.UserFirstName LIKE " . $this->db->quote("%{$db[$field]}%") . " ";

			elseif ($field == "LastName" && !empty($db[$field]) )
				$extend .= "{$db['SearchType']} $type.UserLastName LIKE " . $this->db->quote("%{$db[$field]}%") . " ";

			elseif ($field == "Account" && !empty($db[$field]) )
				$extend .= "{$db['SearchType']} $type.UserEmail LIKE " . $this->db->quote("%{$db[$field]}%") . " ";

			// Other fields are correctly formatted with the field-name
			elseif (!empty($db[$field]))
				$extend .= "AND $type.$field LIKE " . $this->db->quote("%{$db[$field]}%") . " ";

			$id = $db['id'] ? $db['id'] : 0;

	        if ($id > 0 && is_numeric($id)) {
                $extend .= " AND $type.id > $id ";
			}

			//if ($atmail->isset_chk($db['limit']) && is_numeric($db['limit']))
			//	$limit = "LIMIT {$db['limit']}";
			//else
			//	$limit = "LIMIT 300";

			$limit = "LIMIT $start,$this->limit";

			$db[$field] = $this->db->quote("%$db[$field]%") ;
		}

		if ($db['abookview'] == "personal")
		{
			// If there is no search query, specify a default searching for valid email-accounts
			if (!$extend)
			$extend = " AND UserEmail LIKE '%@%'" ;

			elseif ($db['SearchType'] == 'or')
			{
				$extend = str_replace('and (or', 'and (', $extend);
				$extend .= ')';
			}

			$results['FullTotal'] = $this->db->sqlgetfield("SELECT COUNT(id)
															FROM $this->Abook
															WHERE Account = $this->Account_Escape $extend
															ORDER BY $this->Abook.$sort");

			// Search the users Personal addressbook only
			$users = $this->db->sqlarray("SELECT id
										  FROM $this->Abook
										  WHERE Account = $this->Account_Escape $extend
										  ORDER BY $this->Abook.$sort $limit");
		} else {
			catcherror('Unknown search supplied');
		}

	    foreach ($users as $id)
	    {
	        $id = stripslashes($id);

			$id_escape = $this->db->quote($id);

		    $h = $this->db->sqlhash( "SELECT UserEmail, UserFirstName, UserLastName, UserGender, id,
		    						  UserWorkPhone, UserHomePhone
		    						  FROM $this->Abook
		    						  WHERE id = $id_escape");

			// The UserEmail is the unique identifier for the personal addressbook
			$h['Account'] = $h['UserEmail'];

	        if ( !$h['Account'] )
	        	continue;

	        foreach ( $h as $k => $v)
			{
				if ($pref['iconv'] && defined('ICONV_VERSION'))
					$results[$id][$k] = iconv("utf-8", "utf-8", $v);
				else
					$results[$id][$k] = $v;
			}

			// Escape ' in users First/Last name - Otherwise breaks the JS function
			$results[$id]['FirstName'] = addslashes($results[$id]['FirstName']);
			$results[$id]['LastName'] = addslashes($results[$id]['LastName']);

	    }

		// do we want to autocomplete using ldap server?
	    if ($pref['autocomplete_ldap_entries']) {
	    	
			$db['abookview'] = 'ldap';
			$db['Account']   = str_replace('%','',$db['Account']);
			$db['Account']   = str_replace("'",'',$db['Account']);
			$db['FirstName'] = str_replace('%','',$db['FirstName']);
			$db['FirstName'] = str_replace("'",'',$db['FirstName']);
			$db['LastName']  = str_replace('%','',$db['LastName']);
			$db['LastName']  = str_replace("'",'',$db['LastName']);
			
			$ldap_results    = $this->_search_ldap($db);
			
			$max_id = $this->db->sqlarray("SELECT max(id)
	                                          FROM $this->Abook
	                                          WHERE Account = $this->Account_Escape $extend
	                                          ORDER BY $this->Abook.$sort $limit");
	
			$i = $max_id[0]+1;
	
			foreach($ldap_results as $ldap_entry) {
			   $temp['UserEmail']     = $ldap_entry['Account'];
			   $temp['UserFirstName'] = $ldap_entry['UserFirstName'];
			   $temp['UserLastName']  = $ldap_entry['UserLastName'];
			   $temp['id']            = $i;     
			   $results[$i]           = $temp;               
			   $i+=1;
			}
			
			$results['FullTotal'] += count($ldap_results);
	    }

	    return $results;
	}

	//
	function addgroup($group, $email)
	{
		global $atmail;

		$groupTable = Filter::cleanSqlFieldNames($this->db->AbookGroup);
		$res = $this->db->sqldo("INSERT INTO $groupTable (GroupName, GroupEmail, Account) VALUES (?,?,?)", array($group, $email, $this->Account));

	    return;
	}


	/**
	 * List all users in the addressbook
	 *
	 * @param string $sort sorting order
	 * @return array
	 */
	function viewgroup($sort)
	{

		global $atmail;

		$h = array();

		$abookGroup = Filter::cleanSqlFieldNames($this->db->AbookGroup);

		$users = $this->db->sqlarray("SELECT GroupName
		                                FROM $abookGroup
		                                WHERE Account = $this->Account_Escape
		                                GROUP BY GroupName");

	    foreach ($users as $group)
	    {
			$group_escape = $this->db->quote($group);

	        $email = $this->db->sqlarray("SELECT GroupEmail
	                                        FROM $abookGroup
	                                        WHERE Account = $this->Account_Escape
	                                        AND GroupName = $group_escape AND GroupEmail IS NOT NULL");

	        foreach ($email as $v)
	        {
	        	 if ( !$v )
	        	 	continue;
	        	 $h[$group][$v]++;
	        }

	    }

	    return $h;
	}


	// Return an array of User email-addresses
	function getgroup($group)
	{
		global $atmail;

		$group = $this->db->quote($group);
		$abookGroup = Filter::cleanSqlFieldNames($this->db->AbookGroup);

		$users = $this->db->sqlarray("select GroupEmail from $abookGroup where Account=$this->Account_Escape and GroupName=$group group by GroupEmail");

		$allusers = array();

		foreach($users as $user)
		{

			// Skip a bad record
			if(empty($user))
				continue;

			if (strpos($user, '@') === false)
				array_push($allusers, $this->getgroup($user));
			else
				array_push($allusers, $user);

		}

		return $allusers;
	}


	/**
	 * View a specified user
	 *
	 * @param string $user
	 * @param string $type type of selected user
	 * @return array
	 */
	function viewuser($user, $type)
	{
		global $atmail, $pref;

		$db = array();

		$user = $this->db->quote($user);
	   
		// Otherwise the contact is a 'personal' addressbook entry. Permission is always granted for read/write access
        $db = $this->db->sqlhash("SELECT *
        						  FROM $this->Abook
        						  WHERE Account = $this->Account_Escape AND id = $user" );

		$db['Permissions'] = 1;
		$UserDOB = explode(" ", $db['UserDOB']);
        $db['UserDOB'] = $UserDOB[0];

        $abookGroup = Filter::cleanSqlFieldNames($this->db->AbookGroup);

		$usergroup = $this->db->sqlarray("SELECT GroupName
										  FROM $abookGroup
										  WHERE Account = ? AND GroupEmail = ?
										  GROUP BY GroupName", array($this->Account_Escape, $db['UserEmail']));


		foreach ($usergroup as $ug)
		{
			if (!$ug)
				continue;
			$db['UserGroups'] .= "UserGroups[\"$ug\"] = 1;\n";
		}

		$db['UserInfo'] = str_replace("\n", "<BR>", $db['UserInfo']);

	    return $db;
	}

	/**
	 * Delete the specified addressbook item.
	 *
	 * @param string $user UserEmail
	 * @param int $id user id
	 */
	function delete($user, $id)
	{
		global $atmail;

		if (strlen($user) > 0)
		{
			$this->db->sqldo("DELETE
			                  FROM $this->Abook
			                  WHERE UserEmail = ? AND id = ? AND Account = $this->Account_Escape", array($user, $id));
		}
		else
		{
			$this->db->sqldo("DELETE
			                  FROM $this->Abook
			                  WHERE id = ? AND Account = $this->Account_Escape", $id);
		}

	}

	/**
	 * Delete all the entrys in the database with
	 * the Groupname, for our Account.
	 *
	 * @param string $group selected group name
	 */
	function deletegroup($group)
	{
		global $atmail;

		$group = $this->db->quote($group);
        $abookGroup = Filter::cleanSqlFieldNames($this->db->AbookGroup);

		// Delete the reference from the AbookGroup, and Personal Abook tables
	    $this->db->sqldo("DELETE
	                      FROM $abookGroup
	                      WHERE GroupName = $group AND Account = $this->Account_Escape" );

	    // Delete group name from addressbook table
	    $this->db->sqldo("DELETE
	                      FROM $this->Abook
	                      WHERE UserEmail= $group AND Account = $this->Account_Escape" );

	}


	function updatefield($user)
	{
		global $atmail;

		$args = array();
		$sql = '';

		foreach($user as $k => $v)
		{
		    $k = Filter::cleanSqlFieldNames($k);

			if ($v)
			{
				array_push($args, $v);
				if ($k == "id" || $k == 'Shared') continue;

				if ($v == "(null)")
					$sql .= "$k = " . $this->db->quote('') . ", ";
				else
					$sql .= "$k = " . $this->db->quote($v) . ", ";
			}
		}

		$sql = preg_replace('/, $/', '', $sql);

        $sql = "update $this->Abook set $sql where id = ? and Account=$this->Account_Escape";
		
		// Execute the SQL Query
		$this->db->sqldo($sql, $user['id']);

		return;
	}

	function update($user)
	{
		global $atmail;

		$user['id'] = $this->db->quote($user['id']);

		$user['DateModified'] = $atmail->outlook_date();

	    // Update a users addressbook data
	    $this->db->sqldo( "update $this->Abook set
			UserEmail = ?,
			UserEmail2 = ?,
			UserEmail3 = ?,
			UserEmail4 = ?,
			UserEmail5 = ?,
			UserFirstName = ?,
			UserMiddleName = ?,
			UserLastName = ?,
			UserTitle = ?,
			UserGender = ?,
			UserDOB = ?,
			UserHomeAddress = ?,
			UserHomeCity = ?,
			UserHomeState = ?,
			UserHomeZip = ?,
			UserHomeCountry = ?,
			UserHomePhone = ?,
			UserHomeMobile = ?,
			UserHomeFax = ?,
			UserURL = ?,
			UserWorkCompany = ?,
			UserWorkTitle = ?,
			UserWorkDept = ?,
			UserWorkOffice = ?,
			UserWorkAddress = ?,
			UserWorkCity = ?,
			UserWorkState = ?,
			UserWorkZip = ?,
			UserWorkCountry = ?,
			UserWorkPhone = ?,
			UserWorkMobile = ?,
			UserWorkFax = ?,
			UserInfo = ?,
			UserPgpKey = ?,
			DateModified = ?
			where Account = $this->Account_Escape and id={$user['id']}",

			array(
			$user['UserEmail'],
			$user['UserEmail2'],
			$user['UserEmail3'],
			$user['UserEmail4'],
			$user['UserEmail5'],
			$user['UserFirstName'],
			$user['UserMiddleName'],
			$user['UserLastName'],
			$user['UserTitle'],
			$user['UserGender'],
			$user['UserDOB'],
			$user['UserHomeAddress'],
			$user['UserHomeCity'],
			$user['UserHomeState'],
			$user['UserHomeZip'],
			$user['UserHomeCountry'],
			$user['UserHomePhone'],
			$user['UserHomeMobile'],
			$user['UserHomeFax'],
			$user['UserURL'],
			$user['UserWorkCompany'],
			$user['UserWorkTitle'],
			$user['UserWorkDept'],
			$user['UserWorkOffice'],
			$user['UserWorkAddress'],
			$user['UserWorkCity'],
			$user['UserWorkState'],
			$user['UserWorkZip'],
			$user['UserWorkCountry'],
			$user['UserWorkPhone'],
			$user['UserWorkMobile'],
			$user['UserWorkFax'],
			$user['UserInfo'],
			$user['UserPgpKey'],
			$user['DateModified'])
		);

	}


	function clean($var, $num)
	{
        if (function_exists('mb_substr')) {
	        $newvar = mb_substr( $var, 0, $num );
        } else {
            $newvar = substr( $var, 0, $num );
	    }
        $newvar = rtrim($newvar);
	    if ( count($var) > $num )
	    	$newvar .= "..." ;

	    return $newvar;
	}

	function importparse($num)
	{
		global $pref, $atmail;

		$arr = array();

		if ($fh = @fopen($atmail->tmpdir . $this->Import, 'r'))
		{
			$i = 0;

			$del = $this->_get_csv_delimiter($fh);

			while($fields = fgetcsv($fh, 10000, $del))
			{
				if ($num == $i)
					return $arr;

				array_push($arr, $fields);
				$i++;
			}
		}
		else
			print "Cannot open : $atmail->tmpdir$this->Import";

		return $arr;
	}

	function importfile()
	{
		global $pref, $atmail;

		// remove script time limit
		set_time_limit(0);

		// Read the temporary abook file
		if ($fh = fopen($atmail->tmpdir . $this->Import, 'r' ))
		{
		    $count = 0;
		    $line = 0;
		    $is_email = false;

		    $del = $this->_get_csv_delimiter($fh);

		    while (false !== $fields = fgetcsv($fh, 10000, $del))
		    {
		        // Skip if the field contains the header information
                if ($this->ColumnType && $line == 0) {
                	$line++;
                	continue;
                }

                $db = array();

		        for ($i = 0 ; $i < count($fields); $i++)
		        {
		        	$j = $i + 1;
		            $name = $_REQUEST["ImportField_$j"];

		            if (empty($name) || isset($db[$name]) && !empty($db[$name])) {
		            	continue;
		            }

		            // Take away any characters which are invalid for the addressbook
		            $fields[$i] = str_replace(array('"', "'"), '', $fields[$i]);

		            $db[$name] = $fields[$i];

		            if (strpos($fields[$i], '@'))
		                $is_email = true;
		        }

		        // Skip if the email address is not defined
		        if ($is_email)
		        {
		            // Finally, add the entry into the database
		            $this->add($db);
		            $count++;
		            $is_email = false;
		        }
		        $line++;
		    }

		    unlink($atmail->tmpdir . $this->Import);
		}
		else
			die ("Cannot open $this->Import");

		return $count;

	}

	function importupload()
	{
		global $pref, $atmail;

		//check file size is not too large
	    if ( $_FILES['fileupload']['size'] > ( $pref['max_msg_size'] * 1048576 ) ) {
	        $this->jsalert = 'csv_import_file_oversize';
	        return false;
	    }

	    // Check file extension
	    if (!preg_match('/\.(csv|txt)$/i', $_FILES['fileupload']['name'])) {
	        $this->jsalert = 'csv_import_bad_filetype';
	        return false;
	    }

	    // Lets do a further (lame) check to test that this IS a csv file
	    // Just read in first few lines and check format - we require
	    // at least 2 entries (e.g email_address, first_name) per line
	    $fh = fopen($_FILES['fileupload']['tmp_name'], 'r');

	    $del = $this->_get_csv_delimiter($fh);

	    $row = 0;
        while ($row < 5 && ($data = fgetcsv($fh, 10000, $del)) !== FALSE) {

        	// ignore bank lines
        	if (is_null($data[0]) || count($data) == 1 && empty($data[0])) {
        		continue;
        	}

            if (count($data) < 2) {
                $this->jsalert = 'csv_import_bad_filetype';
                return false;
            }
            $row++;
        }
        fclose($fh);

        // File appears empty
        if ($row == 0 || $row == 1 && isset($_REQUEST['ColumnType'])) {
        	$this->jsalert = 'csv_import_file_empty';
            return false;
        }

    	$filename = $_FILES['fileupload']['name'];
		$pathname = AtmailGlobal::escape_pathname($atmail->tmpdir . "{$atmail->Account}-$filename");

		if ( file_exists($pathname) )
		{
			$pathname = AtmailGlobal::escape_pathname($atmail->tmpdir . "$atmail->Account".getmypid().$filename);
		}

		if (move_uploaded_file($_FILES['fileupload']['tmp_name'], $pathname))
		{
			$this->Import = str_replace($atmail->tmpdir, '', $pathname);
		}
		else
		{
			$this->jsalert = 'csv_import_failed';
			return false;
		}


		return true;
	}


	function importsuggest($name, $select)
	{
		global $atmail;

		require_once('Language.php');

		if ( preg_match('/(First)?\s*Name/i', $name ) || $name == Language::translateAbookField("UserFirstName", $atmail->Language))
			$match = "UserFirstName" ;
		elseif ( preg_match('/Last\s*Name/i', $name) || $name == Language::translateAbookField('UserLastName', $atmail->Language))
			$match = "UserLastName" ;
		elseif ( preg_match('/Middle\s*Name/i', $name) || $name == Language::translateAbookField('UserMiddleName', $atmail->Language))
			$match = "UserMiddleName" ;
		elseif ( preg_match('/(E-mail Address|Email)\s*(\d)?/i', $name, $m) )
			$match = "UserEmail{$m[2]}" ;
		elseif ( preg_match('/Job Title/i', $name) || $name == Language::translateAbookField('UserTitle', $atmail->Language))
			$match = "UserTitle" ;
		elseif ( preg_match('/Home\s*Address/i', $name) || $name == Language::translateAbookField('UserHomeAddress', $atmail->Language))
			$match = "UserHomeAddress" ;
		elseif ( preg_match('/Home\s*City/i', $name) || $name == Language::translateAbookField('UserHomeCity', $atmail->Language))
			$match = "UserHomeCity" ;
		elseif ( preg_match('/Home\*State/i', $name) || $name == Language::translateAbookField('UserHomeState', $atmail->Language))
			$match = "UserHomeState";
		elseif ( preg_match('/Home Postal Code/i', $name) || $name == Language::translateAbookField('UserHomeZip', $atmail->Language))
			$match = "UserHomeZip" ;
		elseif ( preg_match('/Home\s*Country(\/Region)?/i', $name) || $name == Language::translateAbookField('UserHomeCountry', $atmail->Language))
			$match = "UserHomeCountry" ;
		elseif ( preg_match('/Home\*Phone/i', $name) || $name == Language::translateAbookField('UserHomePhone', $atmail->Language))
			$match = "UserHomePhone" ;
		elseif ( preg_match('/(?<!Work )Mobile( Phone)?/i', $name) || $name == Language::translateAbookField('UserHomeMobile', $atmail->Language))
			$match = "UserHomeMobile" ;
		elseif ( preg_match('/Home\*Fax/i', $name) || $name == Language::translateAbookField('UserHomeFax', $atmail->Language))
			$match = "UserHomeFax" ;
		elseif ( preg_match('/Personal Web Page|URL/i', $name) || $name == Language::translateAbookField('UserUrl', $atmail->Language))
			$match = "UserURL" ;
		elseif ( preg_match('/Company/i', $name) || $name == Language::translateAbookField('UserWorkCompany', $atmail->Language))
			$match = "UserWorkCompany" ;
		elseif ( preg_match('/Gender|Sex/i', $name) || $name == Language::translateAbookField('UserGender', $atmail->Language))
			$match = "UserGender" ;
		elseif ( preg_match('/Department|Work Dept/i', $name) || $name == Language::translateAbookField('UserWorkDept', $atmail->Language))
			$match = "UserWorkDept" ;
		elseif ( preg_match('/Office Location/i', $name) || $name == Language::translateAbookField('UserWorkOffice', $atmail->Language))
			$match = "UserWorkOffice" ;
		elseif ( preg_match('/(Business|Work) Street/i', $name) || $name == Language::translateAbookField('UserWorkAddress', $atmail->Language))
			$match = "UserWorkAddress" ;
		elseif ( preg_match('/(Business|Work)\s*City/i', $name) || $name == Language::translateAbookField('UserWorkCity', $atmail->Language))
			$match = "UserWorkCity" ;
		elseif ( preg_match('/(Business|Work)\s*State/i', $name) || $name == Language::translateAbookField('UserWorkState', $atmail->Language))
			$match = "UserWorkState" ;
		elseif ( preg_match('/(Business|Work) Postal Code/i', $name) || $name == Language::translateAbookField('UserWorkZip', $atmail->Language) )
			$match = "UserWorkZip" ;
		elseif ( preg_match('/(Business|Work)\s*Country\/Region/i', $name) || $name == Language::translateAbookField('UserWorkCountry', $atmail->Language))
			$match = "UserWorkCountry" ;
		elseif ( preg_match('/(Business|Work)\s*Phone/i', $name) || $name == Language::translateAbookField('UserWorkPhone', $atmail->Language))
			$match = "UserWorkPhone" ;
		elseif ( $name == 'Work Mobile' || $name == Language::translateAbookField('UserWorkMobile', $atmail->Language))
			$match = "UserWorkMobile" ;
		elseif ( preg_match('/(Business|Work)\s*Fax/i', $name) || $name == Language::translateAbookField('UserWorkFax', $atmail->Language))
			$match = "UserWorkFax" ;
		elseif ($name == Language::translateAbookField('UserInfo', $atmail->Language))
			$match = "UserInfo" ;
		elseif ($name == Language::translateAbookField('UserWorkTitle', $atmail->Language))
			$match = "UserWorkTitle" ;
		elseif ($name == Language::translateAbookField('UserPgpKey', $atmail->Language))
			$match = "UserPgpKey" ;
		elseif ($name == "Date of Birth" || $name == "DOB" || $name == Language::translateAbookField('UserDOB', $atmail->Language))
			$match = "UserDOB" ;

		if (isset($match)) {
			$select = str_replace("$match\"", "$match\" selected", $select);
		}

		return $select;
	}


	/**
	 * Try to detect the csv delimiter
	 *
	 * This is not fool-proof, just a guess really
	 * The comma get preference by requiring
	 * less hits for a match (also defaults to comma)
	 * upon failure. Perhaps some funky regex that
	 * looks for comma/semi-colon outside of any
	 * quotes would be better??
	 *
	 * @param $fh resource File handle to csv file
	 * @return String
	 */
	function _get_csv_delimiter($fh)
	{

	    $line = fgetcsv($fh, 10000, ',');
	    if ($line !== false && count($line) > 2) {
	    	$del = ',';
	    }
	    rewind($fh);

	    $line = fgetcsv($fh, 10000, ';');

	    // If $del is already set as ',' then require more hits
	    // for ';' to replace it
	    if ($line !== false && isset($del) && count($line) > 5) {
	    	$del = ';';
	    } elseif ($line !== false && !isset($del) && count($line) > 3) {
	    	$del = ';';
	    }
	    rewind($fh);

	    if (!isset($del)) {
	    	$del = ',';
	    }

	    return $del;
	}


	/**
	 * Check user permissions
	 *
	 * @param int $id
	 * @param string [$user]
	 * @return boolean
	 */
	function check_permissions($id, $user = '')
	{
		global $atmail;

		$id = $this->db->quote($id);
		$permission = false;

		if (strlen($user) > 0 )
		{
			$permission = $this->db->getvalue("SELECT AbookPermissions.Permissions
										   FROM Abook_shared, AbookPermissions
										   WHERE Abook_shared.id = $id AND Abook_shared.UserEmail = $user AND AbookPermissions.AbookID = $id
										   AND (AbookPermissions.Account = $this->Account_Escape OR AbookPermissions.Account = $this->Ugroup
										   		OR AbookPermissions.Account = 'All Users' OR AbookPermissions.Account = $this->pop3host)
										   AND AbookPermissions.Permissions = '1'");

		}
		else
		{

			// Check our account has permissions to delete the addressbook entry
			$permission = $this->db->getvalue("SELECT AbookPermissions.Permissions
										   FROM Abook_shared, AbookPermissions
										   WHERE Abook_shared.id = $id AND AbookPermissions.AbookID = $id
										   AND (AbookPermissions.Account = $this->Account_Escape OR AbookPermissions.Account = $this->Ugroup
										   		OR AbookPermissions.Account='All Users' OR AbookPermissions.Account=$this->pop3host)
										   AND AbookPermissions.Permissions ='1'");
		}

		return $permission;
	}


	// Check to see if a group already exists
	function checkgroup($group)
	{
		global $atmail;
        $abookGroup = Filter::cleanSqlFieldNames($this->db->AbookGroup);
		$group = $this->db->sqlgetfield("select GroupName from $abookGroup where GroupName=$this->Ugroup
		and Account=$this->Account_Escape");

		return $group;
	}


	// LDAP search functions
	function _search_ldap($db)
	{
		// Check for PHP LDAP extension
		if (!defined('LDAP_OPT_TIMELIMIT'))
			return array();

		global $atmail, $pref;

		if (empty($db['Account']) && empty($db['FirstName']) && empty($db['LastName']))
			return array();

		if (!@$ldap = ldap_connect($pref['ldap_server'], 389))
			return array();

		if (!@ldap_bind($ldap, $pref['bind_dn'], $pref['ldap_password']))
			return array();

		//$query = "(& ";
		$query = "(| ";

		// we're doing a wildcard search
		if ( ($db['Account'] == '*') && ($db['FirstName'] == '*') && ($db['LastName'] == '*') ) {
			$db['Account'] = '';
			$db['FirstName'] = '';
			$db['LastName'] = '';
			$query .= "(mail=*) ";
		}

		if (!empty($db['Account']))
	        $query .= "(mail={$db['Account']}*) ";

		if (!empty($db['FirstName']) && !empty($db['LastName']))
	        $query .= "(cn={$db['FirstName']}*{$db['LastName']}*) ";

		if (!empty($db['FirstName']))
			$query .= "(givenName={$db['FirstName']}*) ";

		if (!empty($db['LastName']))
			$query .= "(sn=*{$db['LastName']}*) ";

		$query .= ")";

	    // Now we make our query to the LDAP server according to the users
	    // input via the form
		$result = @ldap_search($ldap, $pref['base_dn'], $query );

		if ($result === false) {
		    $atmail->ldap_error("$query: ". ldap_error($ldap));
		} else {
		    foreach (@ldap_get_entries($ldap, $result) as $entry)
		    {
		        if (!is_array($entry))
		        continue;

		        $email = $entry['mail'][0];

		        $h[$email]['Account'] = $entry['mail'][0];
		        $h[$email]['UserLastName'] = $entry['sn'][0];
		        $h[$email]['UserFirstName'] = $entry['givenname'][0];
		        $h[$email]['UserWorkPhone'] = $entry['telephonenumber'][0];
		    }

		    return $h;
		}
	}

}

?>
