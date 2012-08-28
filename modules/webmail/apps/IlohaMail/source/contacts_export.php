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

include('../include/stopwatch.inc');
$timer = new stopwatch(true);
$timer->register("start");
include('../include/super2global.inc');
include('../include/contacts_commons.inc');
include_once('../include/data_manager.inc');
if (isset($user)){
	include('../include/header_main.inc');
	include('../lang/'.$my_prefs["lang"].'/contacts.inc');
	include('../lang/'.$my_prefs["lang"].'/compose.inc');

	//authenticate
	/*
	include_once("../include/icl.inc");
	$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if ($conn){
		iil_Close($conn);
	}else{
		echo "Authentication failed.";
		echo "</html>\n";
		exit;
	}
	*/

	$timer->register("authenticated");

	echo "\n".'<table width="100%" cellpadding=2 cellspacing=0>';
	echo '<tr class="dk">'."\n";
	echo '<td align=left valign=bottom>'."\n";
	echo '<span class="bigTitle">'.$cStrings[0].'</span>'."\n";
	echo '&nbsp;&nbsp;';
	echo '<span class="mainHeadingSmall">';
	echo '[<a href="contacts_export.php?user='.$user.'" class="mainHeadingSmall">'.$cStrings["export"].'</a>]';
	echo '[<a href="contacts_import.php?user='.$user.'" class="mainHeadingSmall">'.$cStrings["import"].'</a>]';
	echo '</span>';
	echo '</td></tr></table>'."\n";

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
	if (empty($sort_field)) $sort_field = "name";
	if (empty($sort_order)) $sort_order = "ASC";

	if (ereg("^grp", $sort_field)) $grp_sort = true;
	else $grp_sort = false;

	//fetch and sort
	if (!$DISABLE_CONTACTS_SHARING) $dm->is_sharable = true;
	$contacts = $dm->sort($sort_field, $sort_order);
	$numContacts = count($contacts);

	//show error, if any
	if (!empty($error)) echo "<p>".$error."<br>\n";
	$timer->register("fetched");

	$groups_a = $dm->getDistinct("grp", "ASC");

	//show contacts
	if ( is_array($contacts) && count($contacts) > 0){
		reset($contacts);
		$target = ($my_prefs['compose_inside']?'list2':'_blank');
        echo '<form method="POST" action="contacts_export2.php">'."\n";
        echo '<input type="hidden" name="user" value="'.$user.'">'."\n";
		echo '<span class="mainLight">'."\n";
		echo '<p><input type="radio" name="action" value="all" CHECKED>'.$cStrings["exportall"];
		
		echo '<p><input type="radio" name="action" value="groups">'.$cStrings["exportgroups"].':<br>';
		echo '<select name="export_groups[]" MULTIPLE>'."\n";
		foreach($groups_a as $grp){
			echo '<option value="'.$grp.'">'.$grp."\n";
		}
		echo '</select>'."\n";
		
		echo '<p><input type="radio" name="action" value="ids">'.$cStrings["exportentries"].':<br>';
		echo '<select name="export_ids[]" MULTIPLE>'."\n";
		while( list($k1, $foobar) = each($contacts) ){
			$a=$contacts[$k1];
			$id = $a['id'];
			$name = $a['name'].' &lt;'.$a['email'].'&gt;'."\n";
			echo '<option value="'.$id.'">'.$name;
		}
		echo '</select>';
		
		require_once('../conf/plugins.php');
		echo '<p>'.$cStrings["format"].': '."\n";
		echo '<select name="handler">'."\n";
		foreach($PLUGIN_HANDLERS['ctex'] as $handler=>$handler_name){
			echo '<option value="'.$handler.'">'.$handler_name."\n";
		}
		echo '</select>'."\n";
		echo '<br>'.$cStrings["filename"].': ';
		echo '<input type="text" name="file_name" value="ilohamail_contacts">';
        echo '<p><input type="submit" name="contacts_submit" value="'.$cStrings["export"].'">'."\n";
		echo '</span>'."\n";
	}else{
		echo "<p>".$cErrors[0];
	}
}
?>
</BODY></HTML>
<!--
<?php
$timer->register("stop");
$timer->dump();
?>
//-->
