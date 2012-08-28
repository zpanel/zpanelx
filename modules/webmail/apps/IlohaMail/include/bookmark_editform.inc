<?php
/*
	File:     bookmark_editform.inc
	Author:   Ryo Chijiiwa, ilohamail.org
	License:  GPL, part of IlohaMail
	Purpose:  Form for adding bookmark entry
*/
?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?user=".$user?>">
		<input type="hidden" name="user" value="<?php echo $user ?>">
		<input type="hidden" name="edit_id" value="<?php echo $edit_id ?>">
		<table border="0" cellspacing="1" cellpadding="1" class="md" width="95%">
		<tr class="dk">
			<td aling="center"><span class="tblheader"><?php echo $bmStrings["edit_url"]?></span></td>
		</tr>
		<tr class="lt">
			<td align="center">
				<table>
					<tr>
						<td align="right"><?php echo $bmStrings["name"]?>:</td>
						<td><input type="text" name="edit_name" value="<?php echo $v["name"]?>" size="25">
						<?php echo $bmStrings["category"]?>:
						<select name="edit_grp">
						<?php 
							echo "<option value=\"\">".$bmStrings["other"]."\n";
							if (is_array($groups) && count($groups)>0){
								while ( list($k,$grp) = each($groups) ){
									echo "<option value=\"$grp\" ".($grp==$v["grp"]?"SELECTED":"").">$grp\n";
								}
							}
						?>
						</select>
						<input type="text" name="edit_grp_other" value="" size="15">
						</td>
					</tr>
					<tr>
						<td align="right"><?php echo $bmStrings["url"]?>:</td>
						<td><input type="text" name="edit_url" value="<?php echo $v["url"] ?>" size="60"></td>
					</tr>
					<tr>
						<td align="right"><?php echo $bmStrings["rss"]?>:</td>
						<td><input type="text" name="edit_rss" value="<?php echo $v["rss"] ?>" size="60"></td>
					</tr>
					<tr>
						<td align="right" valign="top"><?php echo $bmStrings["comments"]?>:</td>
						<td>
						<input type="text" name="edit_comments" value="<?php echo htmlspecialchars(stripslashes($v["comments"]))?>" size="60">
						</td>
					</tr>
					<tr>
						<td align="right" valign="top"></td>
						<td>
						<label>
						<input type="checkbox" name="edit_private" value="1" <?php echo ($v['is_private']?'CHECKED':'')?>><?php echo $bmStrings['private'] ?>
						</label>
						</td>
					</tr>
				</table>
				<input type="submit" name="edit" value="<?php echo $bmStrings["edit"]?>">
				<input type="submit" name="delete" value="<?php echo $bmStrings["delete"]?>">
			</td>
		</tr>
		</table>
		</form>