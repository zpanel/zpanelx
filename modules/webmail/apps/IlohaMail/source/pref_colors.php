<?php
/////////////////////////////////////////////////////////
//	
//	source/pref_colors.php
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
	FILE: source/pref_colors.php
	PURPOSE:
		Provide interface for customizing display colors.
	PRE-CONDITIONS:
		$user - Session ID
	COMMENTS:
		File include "include/write_sinc.inc" for storing preferences to back-end, and update
		per-session settings.
		
********************************************************/

function pc_ShowField($label, $field){
	global $my_colors, $THEME_OVERRIDES;
	
	$value = $my_prefs[$field];
	if ($THEME_OVERRIDES[$field]){
		echo $label."&nbsp;".$my_colors[$field]."<br>\n";
	}else{
		echo $label.'<input type="text" name="'.$field.'" value="'.$my_colors[$field].'" size=7>';
		echo "<br>\n";
	}
}

	include("../include/super2global.inc");
	include("../include/header_main.inc");
	include("../include/icl.inc");
	include("../lang/".$my_prefs["lang"]."prefs.inc");
	include("../lang/".$my_prefs["lang"]."pref_colors.inc");
	include("../conf/defaults.inc");
	include("themes/".$my_prefs["theme"]."/info.inc");
	
	//check for SID
	if (!isset($user)){
		echo "Session ID not specified";
		exit;
	}
	
	include("../include/pref_header.inc");
	
	//authenticate
	$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if ($conn){
		iil_Close($conn);
	}else{
		echo "Authentication failed.";
		echo "</body></html>\n";
		exit;
	}

	?>
		<span class="mainLight">
        <?php echo $pcStrings["0"]."  ".$pcStrings["0.1"]?>
        <a href="cp.php?user=<?php echo $user?>" class="mainLight" target=_blank>
        <?php echo $pcStrings["0.2"]?></a><?php echo $pcStrings["0.3"]?><br>
		<?php echo $pcStrings["0.4"]?>
		</span>
		
		<form method="post" action="index.php" target="_parent">
		<input type="hidden" name="do_pref_colors" value="1">
		<input type="hidden" name="user" value="<?php echo $user?>">
		<input type="hidden" name="session" value="<?php echo $user?>">
		<table width="100%">
			<tr valign=top>
			<td width="50%">
                <table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
                <tr bgcolor="<?php echo $my_colors["main_head_bg"]?>"><td><b class="tblheader"><?php echo $pcStrings["1.0"]?></b></td></tr>
                <tr bgcolor="<?php echo $my_colors["main_bg"]?>"><td>
					<?php
					pc_ShowField($pcPortions[0], "tool_bg");
					pc_ShowField($pcPortions[1], "tool_link");
					?>
					<!--
					<?php echo $pcPortions[0]?><input type="text" name="tool_bg" value="<?php echo $my_colors["tool_bg"]; ?>" size=7>
					<br><?php echo $pcPortions[1]?><input type="text" name="tool_link" value="<?php echo $my_colors["tool_link"]; ?>" size=7>
					//-->
				</td></tr></table>
			</td>
			<td width="50%">
				<?php
				if ($ICL_CAPABILITY["folders"]){
				?>
                <table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
                <tr bgcolor="<?php echo $my_colors["main_head_bg"]?>"><td><b class="tblheader"><?php echo $pcStrings["2.0"]?></b></td></tr>
                <tr bgcolor="<?php echo $my_colors["main_bg"]?>"><td>
					<?php
					pc_ShowField($pcPortions[0], "folder_bg");
					pc_ShowField($pcPortions[1], "folder_link");
					?>
					<!--
					<?php echo $pcPortions[0]?><input type="text" name="folder_bg" value="<?php echo $my_colors["folder_bg"]; ?>" size=7>
					<br><?php echo $pcPortions[1]?><input type="text" name="folder_link" value="<?php echo $my_colors["folder_link"]; ?>" size=7>
					//-->
				</td></tr></table>
				<?php
				}
				?>
			</td>
			</tr>

			<tr valign=top>
			<td width="50%">
                <table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
                <tr bgcolor="<?php echo $my_colors["main_head_bg"]?>"><td><b class="tblheader"><?php echo $pcStrings["3.0"]?></b></td></tr>
                <tr bgcolor="<?php echo $my_colors["main_bg"]?>"><td>
					<?php
					pc_ShowField($pcPortions[0], "main_bg");
					pc_ShowField($pcPortions[1], "main_link");
					pc_ShowField($pcPortions[2], "main_text");
					pc_ShowField($pcPortions[3], "main_hilite");
					pc_ShowField($pcStrings["3.1"], "main_head_bg");
					pc_ShowField($pcStrings["3.2"], "main_head_txt");
					pc_ShowField($pcStrings["3.3"], "main_darkbg");
					pc_ShowField($pcStrings["3.4"], "main_light_txt");
					?>
					<!--
					<?php echo $pcPortions[0]?><input type="text" name="main_bg" value="<?php echo $my_colors["main_bg"]; ?>" size=7>
					<br><?php echo $pcPortions[1]?><input type="text" name="main_link" value="<?php echo $my_colors["main_link"]; ?>" size=7>
					<br><?php echo $pcPortions[2]?><input type="text" name="main_text" value="<?php echo $my_colors["main_text"]; ?>" size=7>
					<br><?php echo $pcPortions[3]?><input type="text" name="main_hilite" value="<?php echo $my_colors["main_hilite"]; ?>" size=7>
					<br><?php echo $pcStrings["3.1"]?>:<input type="text" name="main_head_bg" value="<?php echo $my_colors["main_head_bg"]; ?>" size=7>
					<br><?php echo $pcStrings["3.2"]?>:<input type="text" name="main_head_txt" value="<?php echo $my_colors["main_head_txt"]; ?>" size=7>
					<br>main_darkbg:<input type="text" name="main_darkbg" value="<?php echo $my_colors["main_darkbg"]; ?>" size=7>
					<br>main_light_txt:<input type="text" name="main_light_txt" value="<?php echo $my_colors["main_light_txt"]; ?>" size=7>
					//-->
				</td></tr></table>
			</td>
			<td width="50%">
                <table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
                <tr bgcolor="<?php echo $my_colors["main_head_bg"]?>"><td><b class="tblheader"><?php echo $pcStrings["4.0"]?></b></td></tr>
                <tr bgcolor="<?php echo $my_colors["main_bg"]?>"><td>
					<?php
					pc_ShowField($pcStrings["4.1"], "quotes");
					?>
					<!--
					<?php echo $pcStrings["4.1"]?>: <input type="text" name="quotes" value="<?php echo $my_colors["quotes"]; ?>" size=7>
					//-->
				</td></tr></table>
			</td>
			</tr>

			<tr valign=top>
			<td width="50%">
                <table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
                <tr bgcolor="<?php echo $my_colors["main_head_bg"]?>"><td><b class="tblheader"><?php echo $pcStrings["5.0"]?></b></td></tr>
                <tr bgcolor="<?php echo $my_colors["main_bg"]?>"><td>
					<?php echo $pcStrings["5.1"]?>: 
					<select name="font_family">
					<option></option>
					<option value="other"><?php echo $prefsStrings["3.8"] ?>
					<?php
					$fonts = array("Arial, Helvetica, sans-serif","Times New Roman, Times, serif","Courier New, Courier, mono",
							"Georgia, Times New Roman, Times, serif","Verdana, Arial, Helvetica, sans-serif",
							"Geneva, Arial, Helvetica, sans-serif", "Bitstream Vera Sans");
					while ( list($k, $font) = each($fonts) ){
						echo "<option ".($font==$my_colors["font_family"]?"SELECTED":"").">$font\n";
					}
					?>
					</select><br>
					<?php
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					if (!in_array($my_colors["font_family"], $fonts)) $show_other_font = $my_colors["font_family"];
					else $show_other_font="";
					echo $prefsStrings["3.8"].": <input type=\"text\" name=\"font_family_other\" value=\"$show_other_font\" size=20><p>\n";
					pc_ShowField($pcStrings["5.2"], "font_size");
					pc_ShowField($pcStrings["5.3"], "small_font_size");
					pc_ShowField($pcStrings["5.4"], "menu_font_size");
					pc_ShowField($pcStrings["5.5"], "folderlist_font_size");
					?>
					<!--
					<br><?php echo $pcStrings["5.2"]?>: <input type="text" name="font_size" value="<?php echo $my_colors["font_size"]?>" size=7>
					<br><?php echo $pcStrings["5.3"]?>: <input type="text" name="small_font_size" value="<?php echo $my_colors["small_font_size"]?>" size=7>
					<br><?php echo $pcStrings["5.4"]?>: <input type="text" name="menu_font_size" value="<?php echo $my_colors["menu_font_size"]?>" size=7>
					//-->
				</td></tr></table>
			</td>
			<td width="50%">
				<!--
                <table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
                <tr bgcolor="<?php echo $my_colors["main_head_bg"]?>"><td><b class="tblheader"><?php echo $pcStrings["4.0"]?></b></td></tr>
                <tr bgcolor="<?php echo $my_colors["main_bg"]?>"><td>
					<?php echo $pcStrings["4.1"]?>: <input type="text" name="quotes" value="<?php echo $my_colors["quotes"]; ?>" size=7>
				</td></tr></table>
				//-->
			</td>
			</tr>

		</table>
			<!--
			<input type="submit" name="update" value="<?php echo $prefsButtonStrings[0]?>">
			//-->
			<input type="submit" name="apply" value="<?php echo $prefsButtonStrings[1]?>">
			<input type="submit" name="cancel" value="<?php echo $prefsButtonStrings[2]?>">
			<input type="submit" name="revert" value="<?php echo $prefsButtonStrings[3]?>">
		</form>
		
</BODY></HTML>
