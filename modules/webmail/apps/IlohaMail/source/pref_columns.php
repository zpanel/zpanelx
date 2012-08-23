<?php
/////////////////////////////////////////////////////////
//	
//	source/pref_columns.php
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
	FILE: source/pref_columns.php
	PURPOSE:
		Interface for reordering message listing columns
	PRE-CONDITIONS:
		$user - Session ID
		
********************************************************/

	include("../include/super2global.inc");
	include("../include/header_main.inc");
	include("../include/icl.inc");	
	include("../lang/".$my_prefs["lang"]."prefs.inc");
	include("../lang/".$my_prefs["lang"]."main.inc");
    include("../lang/".$my_prefs["lang"]."contacts.inc");

	
	//authenticate
	$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if ($conn){
		if ($ICL_CAPABILITY["folders"]){
			if ($my_prefs["hideUnsubscribed"]){
				$mailboxes = iil_C_ListSubscribed($conn, $my_prefs["rootdir"], "*");
			}else{
				$mailboxes = iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
			}
			sort($mailboxes);
		}
		iil_Close($conn);
	}else{
		echo "Authentication failed.";
		echo "</body></html>\n";
		exit;
	}
	
	
	/*
		c: check boxes
		a: attachment
		m: flags
		f: sender/recipient
		s: subject
		d: date
		z: size
	*/
	$col_codes = "camfsdz";
	$col_count = strlen($col_codes);
	$col_required = "cfsd";
	$col_label['c'] = $pref_col_label['c'];
	$col_label['a'] = $pref_col_label['a'];
	$col_label['m'] = $pref_col_label['m'];
	$col_label['f'] = $mainStrings[8]."/".$mainStrings[7];
	$col_label['s'] = $mainStrings[6];
	$col_label['d'] = $mainStrings[9];
	$col_label['z'] = $mainStrings[14];

	//do stuff
	if (isset($apply)){
		asort($col_order);
		reset($col_order);
		$new_order = "";
		while(list($code,$order)=each($col_order))
			if ($order>0) $new_order.=$code;
		
		$my_prefs["main_cols"] = $new_order;
		include("../include/save_prefs.inc");
	}else if (isset($revert)){
		$my_prefs["main_cols"] = $col_codes;
	}
	
	//show title heading
	echo "\n<table width=\"100%\" cellpadding=2 cellspacing=0><tr bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
	echo "<td align=left valign=bottom>\n";
	echo "<span class=\"bigTitle\">".$pref_col_title."</span>\n";
	echo "&nbsp;&nbsp;&nbsp;";
	echo '<span class="mainHeadingSmall">';
	echo '[<a href="javascript:close();" onClick="window.close();" class="mainHeadingSmall">'.$cStrings["close"].'</a>]';
	echo '</span>';
	echo "</td></tr></table>\n";
	
?>

<center>
	<form method="POST" action="pref_columns.php">
	<input type="hidden" name="user" value="<?php echo $user?>">
	<p>
	<table border="0" cellspacing="1" cellpadding="1" bgcolor="#b1b1b9">
		<tr bgcolor="#222244">
			<td><span class="tblheader"><?php echo $pref_colstr["order"]?></span></td>
			<td><span class="tblheader"><?php echo $pref_colstr["field"]?></span></td>
		</tr>
	<?php
		for($i=0;$i<$col_count;$i++){
			$col_code = $col_codes[$i];
			$cur_order = strpos($my_prefs["main_cols"], $col_code);
			if ($cur_order!==false) $cur_order++;
			echo "<tr bgcolor=\"#f0f0f0\">\n";
			echo "\t<td>\n";
			echo "\t\t<select name=\"col_order[$col_code]\">\n";
			if (!ereg("[".$col_code."]", $col_required))
				echo "\t\t\t<option value=\"\">--\n";
			for ($order=1;$order<=$col_count;$order++)
				echo "\t\t\t<option value=\"$order\" ".($order==$cur_order?"SELECTED":"").">$order\n";
			echo "\t\t</select>\n";
			echo "\t</td>\n";
			echo "\t<td>\n".$col_label[$col_code]."\n\t</td>\n";
			echo "</tr>\n";
		}

	?>
	</table>
	<?php
		//show preview
		include("../include/main.inc");
		$num_cols = strlen($my_prefs["main_cols"]);
		echo "<p><table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"".$my_colors["main_hilite"]."\">\n";
		echo "<tr bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
			$check_link="<SCRIPT type=\"text/javascript\" language=JavaScript1.2><!-- Make old browsers think this is a comment.\n";
			$check_link.="document.write(\"<a href=javascript:SelectAllMessages(true) class='tblheader'><b>+</b></a><span class=tblheader>|</span><a href=javascript:SelectAllMessages(false) class=tblheader><b>-</b></a>\")";
			$check_link.="\n--></SCRIPT><NOSCRIPT>";
			$check_link.="<a href=\"main.php?folder=".urlencode($folder)."&start=$start&user=$user&sort_field=$sort_field&sort_order=$sort_order&check_all=1\"><b>+</b></a>|";
 			$check_link.="<a href=\"main.php?folder=".urlencode($folder)."&start=$start&user=$user&sort_field=$sort_field&sort_order=$sort_order&uncheck_all=1\"><b>-</b></a>";
			$check_link.="</NOSCRIPT>";
			$tbl_header["c"] = "\n<td>$check_link</td>";
			$tbl_header["s"] = "\n<td>".FormFieldHeader("subject", $mainStrings[6])."</td>";
			$tbl_header["f"] = "\n<td>".FormFieldHeader("from", $mainStrings[8])."</td>";
			$tbl_header["d"] = "\n<td>".FormFieldHeader("date", $mainStrings[9])."</td>";
			$tbl_header["z"] = "\n<td>".FormFieldHeader("size", $mainStrings[14])."</td>";
			$tbl_header["a"] = "<td><img src=\"themes/".$my_prefs["theme"]."/images/att.gif\"></td>";
			$tbl_header["m"] = "<td><img src=\"themes/".$my_prefs["theme"]."/images/reply.gif\"></td>";
			for ($i=0;$i<$num_cols;$i++) echo $tbl_header[$my_prefs["main_cols"][$i]];
		echo "\n</tr>\n";
		echo "</table>\n";
	?>
	<input type="submit" name="apply" value="<?php echo $prefsButtonStrings[1]?>">
	<input type="submit" name="revert" value="<?php echo $prefsButtonStrings[3]?>">
	</form>
	
</center>

</BODY></HTML>
