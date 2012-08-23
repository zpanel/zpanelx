<?php
/////////////////////////////////////////////////////////
//	
//	source/search_form.php
//
//	(C)Copyright 2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//	
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	PURPOSE:
		Search form.  This file is a shell for lang/[lang]/search.inc where the actual search
		form code is.  Actual search request is processed by source/main.php
	PRE-CONDITIONS:
		$user - Session ID

********************************************************/

include("../include/super2global.inc");
include("../include/nocache.inc");
include("../include/header_main.inc");
include("../include/icl.inc");


	$userName=$loginID;
	
	include("../lang/".$my_prefs["lang"]."search.inc");
	
	//form field list string
	$fieldOptions = "<select name=\"field\">\n";
	while (list($key, $value) = each ($search_fields)) {
		$fieldOptions.="<option value=\"$value\">$key\n";
	}
	$fieldOptions .= "</select>\n";
	
	//form date options string
	$dateOptions = "<select name=\"date_operand\">\n";
	while (list($key, $value) = each ($search_dates)) {
		$dateOptions.="<option value=\"$value\">$key\n";
	}
	$dateOptions.="</select>\n";

	//form folder list
	include("../lang/".$my_prefs["lang"]."defaultFolders.inc");
	$conn = iil_Connect($host, $loginID, $password);
	if ($conn){
		$folders=iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
		sort($folders);
		iil_Close($conn);
	}
	$folderList = "<select name=\"folder\">\n";
	while ( list($folder, $label)=each($defaults) ){
		$folderList .= "<option value=\"$folder\">$label\n";
	}
	while ( list($k, $folder)=each($folders) ){
		if (empty($defaults[$folder])) $folderList .= "<option value=\"$folder\">".iil_utf7_decode($folder)."\n";
	}
	$folderList .= "</select>\n";
	//FolderOptions3($folderlist, $defaults);

	//generate output
	$str = $search_str["str"];
	$str = str_replace("%folders", $folderList, $str);
	$str = str_replace("%fields", $fieldOptions, $str);
	$str = str_replace("%dateops", $dateOptions, $str);
	$str = str_replace("%value", '<input type="text"  name="string" >', $str);
	$str = str_replace("%m", '<input type="text" name="month" value="mm" size=2>', $str);
	$str = str_replace("%d", '<input type="text" name="day" value="dd" size=2>', $str);
	$str = str_replace("%y", '<input type="text" name="year" value="yyyy" size=4>', $str);
?>
<form method="POST" action="main.php">
	<input type="hidden" name="user" value="<?php echo $sid; ?>">

<table width="100%" cellpadding=2 cellspacing=0>
<tr bgcolor="<?php echo $my_colors["main_head_bg"]?>"><td>
<span class="bigTitle"><?php echo $search_str["title"]?></span>
</td></tr>
<tr bgcolor="<?php echo $my_colors["main_bg"]?>"><td>

	<?php
	echo $str;
	?>
	<p><input type="submit" name="search" value="<?php echo $search_str["submit"]?>">
</tr></td>
</table>

</form>

</body></html>
