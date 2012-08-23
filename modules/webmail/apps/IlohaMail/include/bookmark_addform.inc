<?php
/*
	File:     bookmark_addform.inc
	Author:   Ryo Chijiiwa, ilohamail.org
	License:  GPL, part of IlohaMail
	Purpose:  Bookmark entry editing form
*/
?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?user=".$user?>">
		<input type="hidden" name="user" value="<?php echo $user?>">
		<input type="hidden" name="session" value="<?php echo $user?>">
		<table border="0" cellspacing="1" cellpadding="1" class="md" width="95%">
			<tr class="dk">
				<td align="center"><span class="tblheader"><?php echo $bmStrings["new"]?></span></td>
			</tr>
			<tr class="lt">
				<td align="center">
					<table>
						<tr>
							<td align="right"><?php echo $bmStrings["name"]?>:</td>
							<td><input type="text" name="new_name" value="<?php echo htmlspecialchars(stripslashes($new_name))?>" size="25">
							<?php echo $bmStrings["category"]?>:
							<select name="new_grp">
							<?php
								echo "<option value=\"\">".$bmStrings["other"]."\n";
								if (is_array($groups) && count($groups)>0){
									while ( list($k,$v) = each($groups) ){
										echo "<option value=\"$v\">$v\n";
									}
								}
							?>
							</select>
							<input type="text" name="new_grp_other" value="<?php echo $new_grp_other?>" size="15">
							</td>
						</tr>
						<tr>
							<td align="right"><?php echo $bmStrings["url"]?>:</td>
							<td><input type="text" name="new_url" value="<?php echo $new_url ?>" size="60"></td>
						</tr>
						<tr>
							<td align="right"><?php echo $bmStrings["rss"]?>:</td>
							<td><input type="text" name="new_rss" value="<?php echo $new_rss ?>" size="60"></td>
						</tr>
						<tr>
							<td align="right" valign="top"><?php echo $bmStrings["comments"]?>:</td>
							<td>
							<input type="text" name="new_comments" value="<?php echo htmlspecialchars(stripslashes($new_comments))?>" size="60">
							</td>
						</tr>
						<tr>
							<td align="right" valign="top"></td>
							<td>
							<label>
							<input type="checkbox" name="new_private" value="1" <?php echo ($new_private?'CHECKED':'')?>><?php echo $bmStrings['private'] ?>
							</label>
							</td>
						</tr>
					</table>
					<input type="submit" name="add" value="<?php echo $bmStrings["add"]?>">
				</td>
			</tr>
		</table>
		</form>		
