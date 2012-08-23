<?php
	include('../include/super2global.inc');
	include('../include/header_main.inc');
	include('../include/langs.inc');
	include('../include/icl.inc');	
	include('../lang/'.$my_prefs["lang"].'prefs.inc');
	include('../lang/'.$my_prefs["lang"].'filters.inc');
	include('../lang/'.$my_prefs['lang'].'defaultFolders.inc');
	include_once('../conf/plugins.php');
	include(PLUGIN_DIR.'/whitelist/strings.php');
	include(PLUGIN_DIR.'/whitelist/whitelist.php');
	include('../include/pref_header.inc');
	include_once('../include/cache.inc');
	
	//authenticate & get folder list
	$conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if ($conn){
		if ($ICL_CAPABILITY['folders']){
			if ($my_prefs['hideUnsubscribed']){
				$mailboxes = iil_C_ListSubscribed($conn, $my_prefs['rootdir'], "*");
			}else{
				$mailboxes = iil_C_ListMailboxes($conn, $my_prefs['rootdir'], "*");
			}
			sort($mailboxes);
		}
		iil_Close($conn);
	}else{
		echo "Authentication failed.";
		echo "</body></html>\n";
		exit;
	}
	
	//do stuff
	$data = whitelist_read();
	echo '<!--';
	print_r($data);
	print_r($_POST);
	echo '//-->';
	if ($wl_address && $wl_add) whitelist_add($data, $wl_address, 'wl');
	if ($bl_address && $bl_add) whitelist_add($data, $bl_address, 'bl');
	if ($wl_update) whitelist_wlactions($data, $wl_move, $wl_moveto, $wl_flag);
	if ($bl_update) whitelist_blactions($data, $bl_move, $bl_moveto, $bl_delete);
	if ($wl_rem) whitelist_remove($data, $wl_list, 'wl');
	if ($bl_rem) whitelist_remove($data, $bl_list, 'bl');
	if ($wl_enable) whitelist_enable($data, 1, 'wl');
	else if ($wl_disable) whitelist_enable($data, 0, 'wl');
	if ($bl_enable) whitelist_enable($data, 1, 'bl');
	else if ($bl_disable) whitelist_enable($data, 0, 'bl');
	extract($data,EXTR_OVERWRITE);
	echo '<!--';
	print_r($data);
	echo '//-->';
	whitelist_write($data);
	?>
<center>
<form method="POST" action="">
<p><table border="0" cellspacing="1" cellpadding="4" class="md" width="95%">
<tr class="lt">
	<td width="50%" valign="top">
		<b><?php echo wblstr('whitelist')?></b>  
		<?php
		if ($wl_enabled) echo '<input type="submit" name="wl_disable" value="'.wblstr('disable').'">('.wblstr('curon').')';
		else  echo '<input type="submit" name="wl_enable" value="'.wblstr('enable').'">('.wblstr('curoff').')';
		?>
		<p><?php echo wblstr('emailad')?>:<input type="text" name="wl_address" value="">
			<input type="submit" name="wl_add" value="<?php echo wblstr('add')?>">
		<br>
		<?php
			$num_wl = count($data['wl_addresses']);
			if ($num_wl>0){
				$size = ($num_wl>15?15:$num_wl);
				echo '<p><select name="wl_list[]" size='.$size.' width="20" MULTIPLE>';
				echo whitelist_addressOpts($data, 'wl');
				echo '</select>';
				echo '<br><input type="submit" name="wl_rem" value="'.wblstr('remove').'">';
			}else{
				echo '<p>'.wblstr('emptywl');
			}
		?>
		
		
		<p><b><?php echo wblstr('wlaction')?></b>
		<br><input type="checkbox" name="wl_move" value="1" <?php echo ($wl_move?'checked':'')?>><?php echo wblstr('moveto1')?>
		<select name="wl_moveto">
		<option value="">
		<?php
		FolderOptions2($mailboxes, $wl_moveto);
		?>
		</select><?php echo wblstr('moveto2') ?>
		<br><input type="checkbox" name="wl_flag" value="1" <?php echo ($wl_flag?'checked':'') ?>> <?php echo wblstr('flag')?>
		<br><input type="submit" name="wl_update" value="<?php echo wblstr('update')?>">
	</td>
	<td width="50%" valign="top">
		<b><?php echo wblstr('blacklist')?></b>
		<?php
		if ($bl_enabled) echo '<input type="submit" name="bl_disable" value="'.wblstr('disable').'">('.wblstr('curon').')';
		else  echo '<input type="submit" name="bl_enable" value="'.wblstr('enable').'">('.wblstr('curoff').')';
		?>
		<p><?php echo wblstr('emailad')?>:<input type="text" name="bl_address" value="">
			<input type="submit" name="bl_add" value="<?php echo wblstr('add')?>">
		<br>
		<?php
			$num_bl = count($data['bl_addresses']);
			if ($num_bl>0){
				$size = ($num_bl>15?15:$num_bl);
				echo '<p><select name="bl_list[]" size='.$size.' MULTIPLE>';
				echo whitelist_addressOpts($data, 'bl');
				echo '</select>';
				echo '<br><input type="submit" name="bl_rem" value="',wblstr('remove').'">';
			}else{
				echo '<p>'.wblstr('emptybl');
			}
		?>
		
		<p><b><?php echo wblstr('blaction')?></b>
		<br><input type="checkbox" name="bl_move" value="1" <?php echo ($bl_move?'checked':'')?>><?php echo wblstr('moveto1')?>
		<select name="bl_moveto">
		<option value="">
		<?php
		FolderOptions2($mailboxes, $bl_moveto);
		?>
		</select><?php echo wblstr('moveto2')?>
		<br><input type="checkbox" name="bl_delete" value="1" <?php echo ($bl_delete?'checked':'') ?>> <?php echo wblstr('delete')?>
		<br><input type="submit" name="bl_update" value="<?php echo wblstr('update')?>">
	</td>
</tr>
</table>
</form>
</body>
</html>