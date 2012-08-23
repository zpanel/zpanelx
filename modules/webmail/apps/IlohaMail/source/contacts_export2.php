<?php
/////////////////////////////////////////////////////////
//
//	source/contacts.php
//
//	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE:  source/contacts.php
	PURPOSE:
		List basic information of all contacts.
		Offer links to
			-view/edit contact
			-send email to contact
			-add new contact
		Process posted data to edit/add/remove contacts information
	PRE-CONDITIONS:
		Required:
			$user-Session ID for session validation and user prefernce retreaval
		Optional:
			POST'd data for add/remove/edit entries.  See source/edit_contact.php
	POST-CONDITIONS:
	COMMENTS:

********************************************************/

function FormatHeaderLink($user, $label, $color, $new_sort_field, $sort_field, $sort_order){
	if (strcasecmp($new_sort_field, $sort_field)==0){
		if (strcasecmp($sort_order, "ASC")==0) $sort_order="DESC";
		else $sort_order = "ASC";
	}
	$link = "<a href=\"contacts.php?user=$user&sort_field=$new_sort_field&sort_order=$sort_order\" class=\"mainHeading\">";
	$link .= "<b>".$label."</b></a>";
	return $link;
}

function page_bail($msg){
	global $user;
	
	echo '<html>'.$msg;
	echo ' Please <a href="contacts_export.php?user='.$user.'">try again</a>';
	echo'</html>';
	exit;
}

include('../include/stopwatch.inc');
//$timer = new stopwatch(true);
//$timer->register("start");
include('../include/super2global.inc');

include('../include/contacts_commons.inc');
include_once('../include/data_manager.inc');
if (isset($user)){
	include_once("../include/session_auth.inc");
	include('../lang/'.$my_prefs["lang"].'/contacts.inc');
	include('../lang/'.$my_prefs["lang"].'/edit_contact.inc');
	include('../lang/'.$my_prefs["lang"].'/compose.inc');


	//$timer->register("authenticated");


	//initialize source name
	$source_name = $DB_CONTACTS_TABLE;
	if (empty($source_name)) $source_name = "contacts";

	//open data manager connection
	$dm = new DataManager_obj;
	if ($dm->initialize($loginID, $host, $source_name, $backend)){
	}else{
		echo 'Data Manager initialization failed:<br>'."\n";
		$dm->showError();
	}

	//init datastore
	$DS->init("cntct", array());
	$grp_stat = $DS->read("cntct");

	//initialize sort fields and order
	if (empty($sort_field)) $sort_field = "grp,name";
	if (empty($sort_order)) $sort_order = "ASC";

	if (ereg("^grp", $sort_field)) $grp_sort = true;
	else $grp_sort = false;

	//make sure groups or entries to export were selected
	if (empty($action)){
		page_bail('No action selected.');
	}

	//fetch and sort
	if (!$DISABLE_CONTACTS_SHARING) $dm->is_sharable = true;
	$contacts = $dm->sort($sort_field, $sort_order);
	$numContacts = count($contacts);

	//show error, if any
	if (!empty($error)) echo "<p>".$error."<br>\n";

	if ($action=="groups"){
		if (empty($export_groups)) page_bail('Please select categories to export.');
		
		foreach($export_groups as $group){
			$index[$group]=1;
		}
		foreach($contacts as $k=>$c){
			$grp = $c["grp"];
			if (!$index[$grp]) unset($contacts[$k]);
		}
	}else if ($action=="ids"){
		if (empty($export_ids)) page_bail('Please select entries to export.');
		
		foreach($export_ids as $id){
			$index[$id]=1;
		}
		foreach($contacts as $k=>$c){
			$id = $c["id"];
			if (!$index[$id]) unset($contacts[$k]);
		}		
	}
	
	//check handler
	include("../conf/plugins.php");
	if (empty($PLUGIN_HANDLERS['ctex'][$handler])){
		page_bail('Unknown export handler: '.$handler);
	}

	//show contacts
	if (count($contacts)!=0){
		//include exporter library
		include_once(PLUGIN_DIR.'/'.$handler.'/ctex_'.$handler.'.php');
		
		//call init function if there
		$init_func = 'ctex_'.$handler.'_init';
		if (function_exists($init_func)) $init_func($file_name);
		
		//call export function
		$export_func = 'ctex_'.$handler.'_export';
		if (function_exists($export_func)) $export_func($contacts);
	}else{
		echo "<p>".$cErrors[0];
	}
}
?>
<?php
//$timer->register("stop");
//$timer->dump();
?>