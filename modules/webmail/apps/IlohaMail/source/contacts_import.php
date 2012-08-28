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

function page_bail($message){
	echo $message;
	echo '</body></html>';
	exit;
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
	include('../lang/'.$my_prefs["lang"].'/edit_contact.inc');
	include('../lang/'.$my_prefs["lang"].'/compose.inc');


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
	
	if (!$userfile && !$upload && !$file){
		$this_page = 'contacts_import.php?user='.$user;
		echo '<FORM NAME="messageform" ENCTYPE="multipart/form-data" ACTION="'.$this_page.'" METHOD="POST">';
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'">';
		echo '<INPUT NAME="userfile" TYPE="file">';
		echo '<INPUT TYPE="submit" NAME="upload" VALUE="'.$composeStrings[2].'">';
		echo '</form>';
	}else{
		include_once('../include/mod_base64.inc');
		include_once("../include/fs_path.inc");

		if (($userfile)&&($userfile!="none")){
			//create path to destination file in uploads folder
			$newpath = fs_get_path("upload", $loginID, $host);
			if (!$newpath)
				page_bail('No uploads directory');
			$newfilename = mod_base64_encode($userfile_name).'.upload';
			$newpath.= '/'.$newfilename;
			
			//try to move it there...
			if (!move_uploaded_file($userfile, $newpath))
				page_bail('Failed to move uploaded file "'.$userfile_name.'" to "'.$newpath.'"');
			$data_file = $userfile;
		}else if ($file){
			list($file_name, $extension)=explode('.',$file);
			$userfile_name = base64_decode($file_name);
			$datapath = fs_get_path("upload", $loginID, $host);
			$datapath.= '/'.$file;
		}
		
		//get file extension		
		$dot_pos = strrpos($userfile_name, '.');
		$extension = substr($userfile_name, $dot_pos+1);
		$extension = strtolower($extension);
		
		//find handler for extension		
		require_once('../conf/plugins.php');
		$handler = $PLUGIN_HANDLERS['ctin'][$extension];
			
		if ($handler){
			require_once(PLUGIN_DIR.'/'.$handler.'/ctin_'.$handler.'.php');
			if ($newpath){
				//echo 'Good file:'.$newpath;

				$init_func = 'ctin_'.$handler.'_init';
				$confirm_func = 'ctin_'.$handler.'_confirm';
				if (function_exists($init_func)) $init_func();
				if (function_exists($confirm_func)) $done = $confirm_func($newpath, $newfilename, $dm);
			}else if ($file){
				if (!$func_name) $func_name = 'custom';
				$custom_func = 'ctin_'.$handler.'_'.$func_name;
				if (function_exists($custom_func)) $done = $custom_func($datapath, $file, $dm);
			}
		}else{
			echo 'Unknown file extension "'.$extension.'" in '.$userfile_name;
		}
			
		//we're done with the file, delete
		if ($done) unlink($datapath);
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
