<?php
/////////////////////////////////////////////////////////
//	
//	source/pref_filters.php
//
//	(C)Copyright 2003 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//		This file is part of IlohaMail.
//		IlohaMail is free software released under the GPL 
//		license.  See enclosed file COPYING for details,
//		or see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	AUTHOR: Ryo Chijiiwa <ryo@ilohamail.org>
	FILE: source/pref_filters.php
	PURPOSE:
		Create/edit/delete filters
	PRE-CONDITIONS:
		$user - Session ID
		
********************************************************/

	include("../include/super2global.inc");
	include("../include/header_main.inc");
	include("../include/langs.inc");
	include("../include/icl.inc");	
	include("../lang/".$my_prefs["lang"]."prefs.inc");
	include("../lang/".$my_prefs["lang"]."pref_identities.inc");
	include("../lang/".$my_prefs["lang"]."compose.inc");
	include("../lang/".$my_prefs["lang"]."filters.inc");
	include("../lang/".$my_prefs["lang"]."main.inc");
	//include("../conf/defaults.inc");
	include("../include/identities.inc");
	include("../include/data_manager.inc");
	include("../include/pref_header.inc");
	include("../include/cache.inc");
	include("../include/filters.inc");
	
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
	}else{
		echo "Authentication failed.";
		echo "</body></html>\n";
		exit;
	}
	
	//open DM connection
	$dm = new DataManager_obj;
	if ($dm->initialize($loginID, $host, $DB_FILTER_TABLE, $DB_TYPE)){
	}else{
		echo "Data Manager initialization failed:<br>\n";
		$dm->showError();
	}


	if (isset($add) || isset($edit)){
		//do add or edit
		$error = fltrCheckInput($name, $conditions, $actions, $params, $sort_order);
		if (empty($error)){
			$rule = fltrCompileFilter($conditions, $actions, $params);
			
			echo "<!-- $rule //-->\n";
			
			if (!$active && !ereg("[d]", $flags)) $flags.="d";
			else if ($active && ereg("[d]", $flags)) $flags = str_replace("d", "", $flags);
			if ($auto_apply && strpos($flags, "a")===false) $flags.="a";
			else if (!$auto_apply && strpos($flags, "a")!==false) $flags = str_replace("a","",$flags);
			
			if ($sort_order=="") $sort_order = 0;
			
			$entry["name"] = $name;
			$entry["rule"] = $rule;
			$entry["flags"] = $flags;
			$entry["sort_order"] = $sort_order;
			
			if ($edit_id>0){
				if ($dm->update($edit_id, $entry)){
					echo "<!-- Updated -->";
					$lastRun_a = cache_read($loginID, $host, "filter");
					unset($lastRun_a[$edit_id]);
					cache_write($loginID, $host, "filter", $lastRun_a);
				}else echo "<!-- Not updated //-->";
			}else{
				if ($dm->insert($entry)) echo "<!-- Inserted //-->";
				else echo "<!-- Not inserted //-->";
				$name = $conditions = $actions = $params = $sort_order = $auto_apply = "";
			}
			
		}
	}else if (isset($delete) && ($edit_id > 0)){
		//do delete
		$dm->delete($edit_id);
		$name = $conditions = $actions = $params = $sort_order = $auto_appply = "";
	}else if ($edit_id > 0){
		//if edit_id specified, fetch record and parse
		$a = $dm->fetch_id($edit_id);
		if ($a){
			extract($a);
			$parts = fltrParseFilter($rule);
			echo "<!--\n";
			print_r($parts);
			echo "//-->\n";
			extract($parts);
		}else{
			$error = $dm->error;
		}
	}	
	
	//fetch all filters, sort by sort_order
	$filters_a = $dm->sort("sort_order", "DESC");

	//display list
	if (is_array($filters_a) && count($filters_a)>0){
		echo '<p><table border="0" cellspacing="1" cellpadding="4" class="md" width="95%">';
		echo '<tr class="dk">';
		echo '<td colspan=5><span class="tblheader">'.$fltr["filters"].'</span>';
		echo '<span class="mainHeading">';
		echo '&nbsp;&nbsp;[<a href="pref_filters.php?user='.$user.'" class="mainHeading">'.$fltr["new"].'</a>]';
		echo '</span></td>';
		echo '</tr>';
		echo '<tr class="dk">';
			echo "<td valign=\"top\"><span class=tblheader>".$fltr["name"]."</span></td>";
			echo "<td valign=\"top\"><span class=tblheader>".$fltr["sort_order"]."</span></td>";
			echo "<td valign=\"top\"><span class=tblheader>".$fltr["status"]."</span></td>";
			echo "<td valign=\"top\"><span class=tblheader>".$fltr["edit"]."</span></td>";
		echo "</tr>\n";

		if ($my_prefs["compose_inside"]) $target="list2";
		else $target="_blank";

		reset($filters_a);
		while ( list($k, $v) = each($filters_a) ){
			echo '<tr class="lt">';
				echo "<td>".$v["name"]."</td>";
				echo "<td>".$v["sort_order"]."</td>";
				echo "<td>";
				if (ereg("[d]", $v["flags"])) echo $fltr["stat_dis"];
				else echo $fltr["stat_en"];
				echo "</td>";
				echo "<td><a href=\"pref_filters.php?user=$user&edit_id=".$v["id"]."#form\">".$fltr["edit"]."</a></td>";
			echo "</tr>\n";
		}
		echo "</table>";
	}
	?>
	
	<p>
			<a name="form">
			<form method="post" action="pref_filters.php">
			<input type="hidden" name="user" value="<?php echo $user?>">
			<input type="hidden" name="session" value="<?php echo $user?>">
			<input type="hidden" name="edit_id" value="<?php echo $edit_id?>">
			<table border="0" cellspacing="1" cellpadding="1" class="md" width="95%">
			<tr class="dk">
			<td align="center"><span class="tblheader"><?php echo $fltr["filter"] ?></span></td>
			</tr>
			<tr class="lt">
			<td>
			<?php
				if ($error){
					echo '<span class="error">'.$error.'</span>';
				}
			?>
			<p><b><?php echo $fltr["name"] ?></b>
			&nbsp;&nbsp;
			<input type="text" name="name" value="<?php echo $name?>">
			<input type="checkbox" name="active" value="a" <?php echo (!ereg("[d]", $flags)?"CHECKED":"") ?>>
			<?php echo $fltr["is_active"] ?>
			<input type="hidden" name="flags" value="<?php echo $flags?>">
			<p><b><?php echo $fltr["sort_order"] ?></b>
			&nbsp;&nbsp;
			<input type="text" name="sort_order" value="<?php echo $sort_order?>" size=3>
			<?php 
				echo $f_hlp["priority"];
				echo "<p><b>".$fltr["conditions"]."</b>";
				if (!isset($num_conditions) && count($conditions)>1) $num_conditions = count($conditions);
				if (!isset($num_conditions)) $num_conditions = 1;
				else if (isset($add_cond)) $num_conditions++;
				else if (isset($rem_cond) && $num_conditions>1) $num_conditions--;
				for ($i=0;$i<$num_conditions;$i++){
					fltrShowConditionForm($i, $conditions[$i], (($num_conditions-$i)==1));
				}
			?>
			<p><b><?php echo $fltr["actions"] ?></b>
			<table width="100%">
			<tr>
				<td valign="top" width="20">
					<input type="checkbox" name="auto_apply" value=1 <?php echo (strpos($flags,"a")!==false?"CHECKED":"") ?>>
				</td>
				<td valign="top">
				<?php echo $fltr["auto"] ?>
				</td>
			</tr>
			<tr>
				<td valign="top" width="20">
					<input type="checkbox" name="actions[d]" value=1 <?php echo ($actions['d']?"CHECKED":"") ?>>
				</td>
				<td valign="top">
				<?php echo $fltr["deletemsg"] ?>
				</td>
			</tr>

			<?php
			//fetch folder list
			if ($ICL_CAPABILITY["folders"]){
					$cached_folders = cache_read($loginID, $host, "folders");
					if (is_array($cached_folders)){
						$folderlist = $cached_folders;
					}else{
						if ($my_prefs["hideUnsubscribed"]) $folderlist = iil_C_ListSubscribed($conn, $my_prefs["rootdir"], "*");
						else $folderlist = iil_C_ListMailboxes($conn, $my_prefs["rootdir"], "*");
						$cache_result = cache_write($loginID, $host, "folders", $folderlist);
					}
			}

			?>
			<tr>
				<td valign="top" width="20">
					<input type="checkbox" name="actions[m]" value=1 <?php echo ($actions['m']?"CHECKED":"") ?>>
				</td>
				<td valign="top">
				<?php echo $fltr["move"] ?> 
				<select name="params[m]">
				<option value="">
				<?php
				FolderOptions2($folderlist, $params['m']);
				?>
				</select>
				</td>
			</tr>
			<tr>
				<td valign="top" width="20">
					<input type="checkbox" name="actions[c]" value=1 <?php echo ($actions['c']?"CHECKED":"") ?>>
				</td>
				<td valign="top">
				<?php echo $fltr["copy"] ?> 
				<select name="params[c]">
				<option value="">
				<?php
				FolderOptions2($folderlist, $params['c']);
				?>
				</select>
				</td>
			</tr>
			<tr>
				<td valign="top" width="20">
					<input type="checkbox" name="actions[f]" value=1 <?php echo ($actions['f']?"CHECKED":"") ?>>
				</td>
				<td valign="top">
				<?php
				$v = $params[f];
				$flag_buttons = '<select name="params[f]">'."\n";
				$flag_buttons.= '<option value="">--'."\n";
				$flag_buttons.= '<option value="Read" '.($v=='Read'?'SELECTED':'').'>'.$mainStrings[21]."\n";
				$flag_buttons.= '<option value="Unread" '.($v=='Unread'?'SELECTED':'').'>'.$mainStrings[22]."\n";
				$flag_buttons.= '<option value="Flagged" '.($v=='Flagged'?'SELECTED':'').'>'.$mainStrings[26]."\n";
				$flag_buttons.= '<option value="Unflagged" '.($v=='Unflagged'?'SELECTED':'').'>'.$mainStrings[32]."\n";
				$flag_buttons.= "</select>\n";
				$flag_buttons.= "";
				$flag_group = str_replace("%b", $flag_buttons, $mainStrings[20]);
				echo $flag_group."\n";
				?>
				</td>
			</tr>
			<tr>
				<td valign="top" width="20">
					<input type="checkbox" name="actions[n]" value=1 <?php echo ($actions['n']?"CHECKED":"") ?>>
				</td>
				<td valign="top">
				<?php echo $fltr["do_nothing"] ?>
				</td>
			</tr>
			</table>
			
				<input type="hidden" name="num_conditions" value="<?php echo $num_conditions ?>">
				
				<?php
				if ($edit_id>0){
					echo '<input type="submit" name="edit" value="'.$fltr["edit_filter"].'">';
					echo '<input type="submit" name="delete" value="'.$fltr["delete"].'">';
				}else{
					echo '<input type="submit" name="add" value="'.$fltr["add"].'">';
				}
				?>
			</td>
			</tr>
			</table>
			</form>
			
</BODY></HTML>
