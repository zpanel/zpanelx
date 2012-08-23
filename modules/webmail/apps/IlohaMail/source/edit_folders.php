<?php
/////////////////////////////////////////////////////////
//	
//	source/edit_folders.php
//
//	(C)Copyright 2000-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE:  source/edit_folders.php
	PURPOSE:
		Provide functionality to create/delete/rename folders
	PRE-CONDITIONS:
		$user - Session ID
	TODO:
		Modify to detect and allow for hierarchy delimiters other than '/'.
		
********************************************************/

function decodePath($path, $delimiter){
	$parts=explode($delimiter, $path);
	while (list($key, $part)=each($parts)){
		$parts[$key]=urldecode($part);
	}
	$path=implode($delimiter, $parts);

	return $path;
}

function encodePath($path, $delimiter){
		$parts=explode($delimiter, $path);
		while (list($key, $part)=each($parts)){
			$parts[$key]=urlencode($part);
            //echo "Encoded $part as ".$parts[$key]." <br>\n";
		}
		$path=implode($delimiter, $parts);
		
		return $path;
}

function prependRootdir($rootdir, $folder, $delim){
	if (empty($rootdir)) return $folder;
	
	$pos = strpos($folder, $rootdir);
	if (($pos!==false) && ($pos==0)) return $folder;
	else return $rootdir.($rootdir[strlen($rootdir)-1]!=$delim?$delim:"").$folder;
}

$CS_OVERRIDE = "UTF-8";
include("../include/super2global.inc");
include("../include/header_main.inc");
include("../include/icl.inc");
include("../include/cache.inc");
include("../lang/".$my_prefs["lang"]."defaultFolders.inc");
include("../lang/".$my_prefs["lang"]."edit_folders.inc");

	$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if (!$conn)
		echo "failed";
	else{
		echo "\n<table width=\"100%\" cellpadding=2 cellspacing=0><tr bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
		echo "<td align=left valign=bottom>\n";
		echo "<span class=\"bigTitle\">".$efStrings[0]."</span>\n";
		echo "</td></tr>\n";

		$hDelimiter = iil_C_GetHierarchyDelimiter($conn);
		flush();
		
		$modified=false;
		$error="";
		
	/********* Handle New Folder *******/
	if (isset($newfolder)){
		// prepend folder path with rootdir as necessary
		$newfolder = prependRootdir($my_prefs["rootdir"], $newfolder, $hDelimiter);
				
		// create new folder
		$unencNF=$newfolder;
		$newfolder = iil_utf7_encode($newfolder);
		//$newfolder=encodePath($newfolder, $hDelimiter);
		if (iil_C_CreateFolder($conn, $newfolder)){
			iil_C_Subscribe($conn, $newfolder);
			$error=$errors[0].$unencNF;
			$modified=true;
		}else{
			$error=$errors[1].$unencNF."<br>".$conn->error;
		}
	}
	/************************/
	
	/********* Handle Delete Folder ********/
	if (isset($delmenu)){
		//make sure it's unsubscribed
		iil_C_UnSubscribe($conn, $delmenu);
		
		//delete...
		//$unencDF=decodePath($delmenu, $hDelimiter);
		$unencDF = $delmenu;
		if ((empty($defaults[$unencDF])) && (iil_C_DeleteFolder($conn, $delmenu))){
			$error=$errors[2].iil_utf7_decode($unencDF);
			$modified=true;
		}else{
			$error=$errors[3].iil_utf7_decode($unencDF);
		}
	}
	/***************************/
	
	/********* Handle Rename Folder ********/
	if ((isset($newname)) &&(isset($oldname))){
		//make sure it's unsubscribed
		iil_C_UnSubscribe($conn, $oldname);

		$unencNF=$newname;

		//prepend with rootdir as necessary
		$newname = prependRootdir($my_prefs["rootdir"], $newname, $hDelimiter);
		$newname = iil_utf7_encode($newname);

		//rename
		$str=decodePath($oldname, $hDelimiter)." --> $unencNF";
		if ((empty($defaults[$unencNF])) && (iil_C_RenameFolder($conn, $oldname, $newname))) {
        	$error=$errors[4].$str;
			$modified=true;
      	} else {
        	$error=$errors[5].$str;
      	}
	}
	/***************************/
	
	/********* Handle subscribe ********/
	if ((isset($subscribe)) && (is_array($sub_folders))){
		while (list($k,$folder)=each($sub_folders)){
			iil_C_Subscribe($conn, $folder);
		}
		$modified = true;
	}
	/***************************/
	
	/********* Handle unsubscribe ********/
	if ((isset($unsubscribe)) && (is_array($unsub_folders))){
		while (list($k,$folder)=each($unsub_folders)){
			if (empty($defaults[$folder])) iil_C_UnSubscribe($conn, $folder);
		}
		$modified = true;
	}
	/***************************/
	
	echo "<tr bgcolor=\"".$my_colors["main_bg"]."\"><td>";
	echo "<p>\n";

    //check if folder support is available
	if (!$ICL_CAPABILITY["folders"])  $error .= $errors[6];
	
	if ($modified){
		echo "<font color=green>".$error."</font>";
		echo "<script> parent.list1.location=\"folders.php?user=$user\"; </script>\n";
		cache_clear($loginID, $host, "folders");
	}else{
		echo "<font color=red>".$error."</font>";
		if ($port==110){
			iil_Close($conn);
			echo "</body></html>";
			exit;
		}
	}

	//get all folders
	$mailboxes = iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
	if ($mailboxes) sort($mailboxes);
	
	//get subscribed folders...
	$subscribed = iil_C_ListSubscribed($conn, $my_prefs["rootdir"], "*");
	if (($subscribed) && (count($subscribed)>0)){
		sort($subscribed);
	}else if (!is_array($subscribed)){
		//echo "Error fetching subscribed folders: ".$conn->error."<br>";
		$subscribed = array();
	}
	$unsubscribed = array_diff($mailboxes, $subscribed);
	

	/********* Show Create ********/
	echo "<form method=\"POST\">\n";
	echo "<b>".$efStrings[1]."</b><br>\n";
	echo "<input type=\"hidden\" name=\"user\" value=\"".$user."\">\n";
	echo $efStrings[2];
	echo "\n<input type=text name=newfolder size=20>\n";
	echo "<input type=submit value=\"".$efStrings[3]."\">";
	echo "</form>\n";

	/********* Show Delete Folder *******/
	echo "<form method=\"POST\">\n";
	echo "<b>$efStrings[4]</b>\n<br>";
	echo "<input type=\"hidden\" name=\"user\" value=\"".$user."\">\n";
	echo "<select name=delmenu>\n";
		FolderOptions2($mailboxes, "");
	echo "</select>\n";
	echo "<input type=submit name=delete value=\"".$efStrings[5]."\">\n</form>\n";
	/************************/

	/********* Show Rename Folder *******/
	echo "<form method=\"POST\">\n";
	echo "<b>$efStrings[6]</b><br>\n";
	echo "<input type=\"hidden\" name=\"user\" value=\"".$user."\">\n";
	echo "<select name=\"oldname\">\n";
		FolderOptions2($mailboxes, "");
	echo "</select>\n";
	echo "--><input type=\"text\" name=\"newname\">\n";
	echo "<input type=submit name=rename value=\"".$efStrings[7]."\">\n";
	echo "</form>\n";
	/************************/
	
	/********* Show Subscribe Folder *******/
	echo "<form method=\"POST\">\n";
	echo "<table><tr>\n";
	echo "<td valign=\"top\">\n";
	echo "<b>$efStrings[10]</b><br>\n";
	echo "<input type=\"hidden\" name=\"user\" value=\"".$user."\">\n";
	echo "<select name=\"sub_folders[]\" MULTIPLE sizse=10>\n";
		FolderOptions2($unsubscribed, "");
	echo "</select>\n";
	echo "<br><input type=submit name=subscribe value=\"".$efStrings[9]."\">\n";
	echo "</td>\n";

	echo "<td valign=\"top\">\n";
	echo "<b>$efStrings[8]</b><br>\n";
	echo "<input type=\"hidden\" name=\"user\" value=\"".$user."\">\n";
	echo "<select name=\"unsub_folders[]\" MULTIPLE sizse=10>\n";
		FolderOptions2($subscribed, "");
	echo "</select>\n";
	echo "<br><input type=submit name=unsubscribe value=\"".$efStrings[11]."\">\n";
	echo "</td>\n";

	
	echo "</tr></table>\n";
	echo "</form>\n";
	
	/************************/

	echo "<br>&nbsp;<br>&nbsp;\n";
	echo "</td></tr></table>";

	//echo "successful: $mbox ";
	iil_Close($conn);

	}
?>
</BODY></HTML>