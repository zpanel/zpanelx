<?php
/////////////////////////////////////////////////////////
//	
//	source/tool.php
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
	FILE: tool.php
	PURPOSE:
		This is the tool bar.  Provides global access to main functionality, including:
		1. Access to folder list, or folder contents (including INBOX)
		2. Access to message composition page (link to "source/compose.php")
		3. Access to search form (link to "source/search_form.php")
		4. Access to contacts list (link to "source/contacts.php")
		5. Access to preferences (link to "source/prefs.php")
		6. Logout
	PRE-CONDITIONS:
		$user - Session ID
	COMMENTS:
		Depending on whether or not "list_folders" preferences is enabled or not, this page
		may display a pop-up menu of all folders, or a link to source/folders.php.
		If the protocol does not support a given feature, it will not be displayed (e.g. "Folders"
		and "Search" links will not be shown for POP3 accounts).
		
********************************************************/

include("../include/super2global.inc");
include("../include/nocache.inc");

function showLink($a){
	echo $a[3]."<a href=\"".$a[0]."\" target=\"".$a[1]."\" class=\"menuText\">".$a[2]."</a>\n";
}

if (isset($user)){
	include_once("../include/ryosimap.inc");
	include_once("../include/encryption.inc");
	include_once("../include/session_auth.inc");
	include_once("../include/icl.inc");
	include_once("../lang/".$my_prefs["lang"]."tool.inc");
	
	$linkc=$my_colors["tool_link"];
	$bgc=$my_colors["tool_bg"];
	$font_size = $my_colors["menu_font_size"];
	$bodyString='<BODY  LEFTMARGIN=0 RIGHTMARGIN=0 MARGINWIDTH=0 MARGINHEIGHT=0 TOPMARGIN=0 BGCOLOR="'.$bgc.'" TEXT="'.$linkc.'" LINK="'.$linkc.'" ALINK="'.$linkc.'" VLINK="'.$linkc.'">';
}else{
	echo "User not specified.";
	exit;
}
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=<?php echo $my_prefs["charset"]; ?>">
<?php
/*** include CSS ***/
include("../include/css.inc");
?>
</HEAD>
<?php 
echo $bodyString; 

$div = "<span class=\"menuText\">&nbsp;&nbsp;|&nbsp;&nbsp;</span>";

if (($my_prefs["list_folders"])||(!$ICL_CAPABILITY["folders"])){
	$links[] = array("main.php?folder=INBOX&user=$user", "list2", $toolStrings["inbox"], "&nbsp;&nbsp;&nbsp;&nbsp;");
	//if ($ICL_CAPABILITY["folders"])
	//$links[] = array("folders.php?user=$user", "list1", $toolStrings["folders"], $div);
}
$target = ($my_prefs["compose_inside"]?"list2":"_blank");
$links[] = array("compose2.php?user=$user", $target, $toolStrings["compose"], $div);
if (($ICL_CAPABILITY["calendar"]) && (!$DISABLE_CALENDAR))
	$links[] = array("calendar.php?user=$user", "list2", $toolStrings["calendar"], $div);
$links[] = array("contacts.php?user=$user", "list2", $toolStrings["contacts"], $div);
if (!$DISABLE_BOOKMARKS)
	$links[] = array("bookmarks.php?user=$user", "list2", $toolStrings["bookmarks"], $div);
if ($ICL_CAPABILITY["search"])
	$links[] = array("search_form.php?user=$user", "list2", $toolStrings["search"], $div);
$links[] = array("prefs.php?user=$user", "list2", $toolStrings["prefs"], $div);

echo "\n<form method=POST action=\"main.php\" target=\"list2\">\n";
?>
<table width="100%"><tr class="menuText"><td valign="bottom">
<?php
if ((!$my_prefs["list_folders"]) && ($ICL_CAPABILITY["folders"])){
	echo "<input type=hidden name=\"sort_field\" value=\"".$my_prefs["sort_field"]."\">\n";
	echo "<input type=hidden name=\"sort_order\" value=\"".$my_prefs["sort_order"]."\">\n";
	echo "<input type=hidden name=\"user\" value=\"".$user."\">\n";
	
	$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if ($conn){
		include_once("../include/cache.inc");
		$cached_folders = cache_read($loginID, $host, "folders");
		if (is_array($cached_folders)){
			echo "<!-- Read cache! //-->\n";
			$folderlist = $cached_folders;
		}else{
			echo "<!-- No cache...";
			if ($my_prefs["hideUnsubscribed"]) $folderlist = iil_C_ListSubscribed($conn, $my_prefs["rootdir"], "*");
			else $folderlist = iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
			$cache_result = cache_write($loginID, $host, "folders", $folderlist);
			echo "write: $cache_result //-->\n";
		}
		//$folderlist=iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
		iil_Close($conn);
	}
	if ($my_prefs["list_folders"]!=1){
		include("../lang/".$my_prefs["lang"]."defaultFolders.inc");
		echo "<select name=folder>\n";
		RootedFolderOptions($folderlist, $defaults, $my_prefs["rootdir"]);
		echo "</select>";
		echo "<input type=submit value=\"".$toolStrings["go"]."\">";
	}
	$link = array("edit_folders.php?user=$user", "list2", $toolStrings["folders"]);
	showLink($link);
}
while ( list($k,$v) = each($links) ){
	//echo "<span class=\"menuText\">&nbsp;&nbsp;|&nbsp;&nbsp;</span>";
	showLink($links[$k]);
}
//echo "<span class=\"menuText\">&nbsp;&nbsp;|&nbsp;&nbsp;</span>";
?>
</td><td align="right" valign="bottom">
	<A HREF="login.php?logout=1&user=<?php echo $user?>" target="_parent" class="menuText"><?php echo $toolStrings["logout"]?></A>
</td></tr></table>
</form>
</BODY>
</HTML>
