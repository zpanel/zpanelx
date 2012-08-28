<?php
/////////////////////////////////////////////////////////
//	
//	source/pref_identities.php
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
	FILE: source/pref_identities.php
	PURPOSE:
		Create/edit/delete identities
	PRE-CONDITIONS:
		$user - Session ID
		
********************************************************/

	include("../include/super2global.inc");
	include("../include/header_main.inc");
	include("../include/langs.inc");
	include("../include/icl.inc");	
	include("../lang/".$my_prefs["lang"]."prefs.inc");
	include("../lang/".$my_prefs["lang"]."pref_identities.inc");
	//include("../conf/defaults.inc");
	include("../include/identities.inc");
	include("../include/data_manager.inc");
	include("../include/pref_header.inc");
    
	
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
	
	//open DM connection
	$dm = new DataManager_obj;
	if ($dm->initialize($loginID, $host, $DB_IDENTITIES_TABLE, $DB_TYPE)){
	}else{
		echo "Data Manager initialization failed:<br>\n";
		$dm->showError();
	}


	if (isset($add)){
		if ((empty($new_name)) || (empty($new_email))) $error .= $piError[1];
		else{
			$new_email = eregi_replace('[^a-zA-Z0-9@_.-]', '', $new_email);
			$new_replyto = eregi_replace('[^a-zA-Z0-9@_.-]', '', $new_replyto);
			
			$new_ident["name"] = $new_name;
			$new_ident["email"] = $new_email;
			$new_ident["replyto"] = $new_replyto;
			$new_ident["sig"] = $new_sig;
			
			if ($dm->insert($new_ident)) echo "<!-- Inserted //-->";
			else echo "<!-- Not inserted //-->";
			
			if ($new_default){
				$my_prefs["user_name"] = $new_name;
				$my_prefs["email_address"] = $new_email;
				$my_prefs["signature1"] = $new_sig;
				include("../include/save_prefs.inc");
			}
			
			$new_name = $new_email = $new_replyto = $new_sig = "";
		}
	}
	if (isset($edit) && ($edit_id > 0)){
		//do update
		$edit_email = eregi_replace('[^a-zA-Z0-9@_.-]', '', $edit_email);
		$edit_replyto = eregi_replace('[^a-zA-Z0-9@_.-]', '', $edit_replyto);
		
		$new_ident["name"] = $edit_name;
		$new_ident["email"] = $edit_email;
		$new_ident["replyto"] = $edit_replyto;
		$new_ident["sig"] = $edit_sig;
			
		if ($dm->update($edit_id, $new_ident)) echo "<!-- Updated! //-->";
		else echo "<!-- Not updated //-->";
		
		if ($new_default){
			$my_prefs["user_name"] = $edit_name;
			$my_prefs["email_address"] = $edit_email;
			$my_prefs["signature1"] = $edit_sig;
			include("../include/save_prefs.inc");
		}

		$edit_id = 0;
	}
	if (isset($delete) && ($edit_id > 0)){
		//do delete
		if ($dm->delete($edit_id)){
			$edit_id = 0;
			
			//if default, clear it
			if (strcmp($edit_name, $my_prefs["user_name"])==0
				&& strcmp($edit_email, $my_prefs["email_address"])==0
				&& strcmp($edit_sig, $my_prefs["signature1"])==0
			){
				$my_prefs["user_name"] = "";
				$my_prefs["email_address"] = "";
				$my_prefs["signature1"] = "";
				include("../include/save_prefs.inc");				
			}
			
		}else $error .= "Deletion failed<br>\n";
	}
	
	$identities_a = $dm->read();
	
	$error .= $dm->error;
	
	echo "<center>\n";
	if (is_array($identities_a) && count($identities_a)>0){
		echo '<p><table border="0" cellspacing="1" cellpadding="4" bgcolor="'.$my_colors["main_hilite"].'" width="95%">';
		echo '<tr bgcolor="'.$my_colors["main_head_bg"].'">';
		echo '<td colspan=5><span class="tblheader">'.$piStrings["identities"].'</span>';
		echo '<span class="mainHeading">';
		echo '&nbsp;&nbsp;[<a href="pref_identities.php?user='.$user.'" class="mainHeading">'.$piStrings["new"].'</a>]';
		echo '</span></td>';
		echo '</tr>';
		echo '<tr bgcolor="'.$my_colors["main_head_bg"].'">';
			echo "<td></td>";
			echo "<td valign=\"top\"><span class=tblheader>".$piStrings["name"]."</span></td>";
			echo "<td valign=\"top\"><span class=tblheader>".$piStrings["email"]."</span></td>";
			echo "<td valign=\"top\"><span class=tblheader>".$piStrings["replyto"]."</span></td>";
			echo "<td valign=\"top\"><span class=tblheader>".$piStrings["sig"]."</span></td>";
		echo "</tr>\n";

		if ($my_prefs["compose_inside"]) $target="list2";
		else $target="_blank";

		reset($identities_a);
		while ( list($k, $v) = each($identities_a) ){
			$v = $identities_a[$k];
			if ($my_prefs["user_name"]==$v["name"] 
				&& $my_prefs["email_address"]==$v["email"] 
				&& $my_prefs["signature1"]==$v["sig"])
					$v["default"] = true;
			echo '<tr bgcolor="'.$my_colors["main_bg"].'">';
				echo "<td valign=\"middle\"><a href=\"pref_identities.php?user=$user&edit_id=".$v["id"]."\">".$piStrings["edit"]."</a></td>";
				$url = "compose2.php?user=$user&sender_identity_id=".$v["id"];
				echo "<td valign=\"middle\"><nobr><a href=\"$url\" target=\"$target\">";
					echo $v["name"]." ".($v["default"]?$piStrings["isdef"]:"");
					echo "</a></nobr></td>";
				echo "<td valign=\"middle\">".$v["email"]."</td>";
				echo "<td valign=\"middle\">".$v["replyto"]."</td>";
				echo "<td valign=\"middle\">".nl2br(stripslashes($v["sig"]))."</td>";
			echo "</tr>\n";
		}
		echo "</table>";
	}
	?>
	
			
	<font color="red"><?php echo $error?></font>
	<p>
	
	<?php
		if ($edit_id>0){
			reset($identities_a);
			while ( list($k,$foo) = each($identities_a) ){
				if ($identities_a[$k]["id"]==$edit_id){
					$v = $identities_a[$k];
				}
			}
			
			if ($my_prefs["user_name"]==$v["name"] 
				&& $my_prefs["email_address"]==$v["email"] 
				&& $my_prefs["signature1"]==$v["sig"])
					$v["default"] = true;
	?>
			<form method="post" action="pref_identities.php">
			<input type="hidden" name="user" value="<?php echo $user ?>">
			<input type="hidden" name="edit_id" value="<?php echo $edit_id ?>">
			<table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
			<tr bgcolor="<?php echo $my_colors["main_head_bg"]?>">
			<td aling="center">
				<span class="tblheader">
					<?php echo $piStrings["edit_ident"]?>
				</span>&nbsp;
			</td>
			</tr><tr bgcolor="<?php echo $my_colors["main_bg"]?>">
			<td align="center">
				<table>
					<tr>
						<td align="right"><?php echo $piStrings["name"]?>:</td>
						<td><input type="text" name="edit_name" value="<?php echo $v["name"]?>" size="45"></td>
					</tr>
					<tr>
						<td align="right"><?php echo $piStrings["email"]?>:</td>
						<td><input type="text" name="edit_email" value="<?php echo $v["email"] ?>" size="45"></td>
					</tr>
					<tr>
						<td align="right"><?php echo $piStrings["replyto"]?>:</td>
						<td><input type="text" name="edit_replyto" value="<?php echo $v["replyto"] ?>" size="45"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><?php echo $piStrings["sig"]?>:</td><td><textarea name="edit_sig" cols=50 rows=5><?php echo htmlspecialchars(stripslashes($v["sig"]))?></textarea></td>
					</tr>
					<tr>
						<td align="right" valign="top"></td>
						<td>
							<input type="checkbox" name="new_default" value="1" <?php echo ($v["default"]?"checked":"") ?>> 
							<?php echo $piStrings["setdef"]?>
						</td>
					</tr>
				</table>
				<input type="submit" name="edit" value="<?php echo $piStrings["edit"]?>">
				<input type="submit" name="delete" value="<?php echo $piStrings["delete"]?>">
			</td>
			</tr>
			</table>
			</form>
	<?php
		}else{
	?>
			<form method="post" action="pref_identities.php">
			<input type="hidden" name="user" value="<?php echo $user?>">
			<input type="hidden" name="session" value="<?php echo $user?>">
			<table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
			<tr bgcolor="<?php echo $my_colors["main_head_bg"]?>">
			<td align="center"><span class="tblheader"><?php echo $piStrings["new"]?></span></td>
			</tr><tr bgcolor="<?php echo $my_colors["main_bg"]?>">
			<td align="center">
				<table>
					<tr>
						<td align="right"><?php echo $piStrings["name"]?>:</td>
						<td><input type="text" name="new_name" value="<?php echo stripslashes($new_name)?>" size="45"></td>
					</tr>
					<tr>
						<td align="right"><?php echo $piStrings["email"]?>:</td>
						<td><input type="text" name="new_email" value="<?php echo $new_email ?>" size="45"></td>
					</tr>
					<tr>
						<td align="right"><?php echo $piStrings["replyto"]?>:</td>
						<td><input type="text" name="new_replyto" value="<?php echo $new_replyto ?>" size="45"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><?php echo $piStrings["sig"]?>:</td><td><textarea name="new_sig" cols=50 rows=5><?php echo htmlspecialchars(stripslashes($new_sig))?></textarea></td>
					</tr>
					<tr>
						<td align="right" valign="top"></td>
						<td>
							<input type="checkbox" name="new_default" value="1"> <?php echo $piStrings["setdef"]?>
						</td>
					</tr>
				</table>
				<input type="submit" name="add" value="<?php echo $piStrings["add"]?>">
			</td>
			</tr>
			</table>
			</form>
			
			<?php
			}
			?>
</BODY></HTML>
