<?php
/********************************************************
	lang/eng/search.inc
	(C)2001-2002 Ryo Chijiiwa <Ryo@IlohaMail.org>

	This file is part of IlohaMail, and released under GPL.
	See COPYING, or http://www.fsf.org/copyleft/gpl.html

********************************************************/

	$fields=array("select"=>"-", "From"=>"FROM", "To"=>"TO", "Subject"=>"SUBJECT","Message body"=>"BODY");
	while (list($key, $value) = each ($fields)) {
		$fieldOptions.="<option value=\"$value\">$key\n";
	}
	
	$dates=array("doesn't matter"=>"ignore", "is on"=>"ON", "is after"=>"SINCE", "is before"=>"BEFORE");
	while (list($key, $value) = each ($dates)) {
		$dateOptions.="<option value=\"$value\">$key\n";
	}
?>
<h2>Search</h2>
<form method="POST" action="main.php">
	<input type="hidden" name="user" value="<?php echo $sid; ?>">
	Search in  folder  
	<select name="folder">
			<?php 
				include("../lang/".$my_prefs["lang"]."defaultFolders.inc");
				$conn = iil_Connect($host, $loginID, $password);
				if ($conn){
					$folderlist=iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
					iil_Close($conn);
				}
				FolderOptions3($folderlist, $defaults);
			?>
	</select>
	<br>   where the field
	<select   name="field">
		<?php   echo   $fieldOptions;?>
	</select>
	 contains 
	<input type="text"  name="string" >
	<br> and the date 
	<select name="date_operand">
		<?php echo $dateOptions; ?>
	</select>
	<input type="text" name="month" value="mm" size=2>/
	<input type="text" name="day" value="dd" size=2>/
	<input type="text" name="year" value="yyyy" size=4>	
	<p><input type="submit" name="search" value="Search">
</form>
