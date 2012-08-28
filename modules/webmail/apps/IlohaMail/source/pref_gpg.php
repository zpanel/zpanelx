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
	include("../include/gpg.inc");
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
	
	$gpg_home = str_replace("%h", $host, str_replace("%u", $loginID, $GPG_HOME_STR));
	if (file_exists(realpath($gpg_home."/pubring.gpg"))) $key_exists = true;
	else $key_exists = false;
	
	if (isset($genkey)){
		if (empty($name)) $error .= "Name not specified\n";
		if (empty($email)) $error .= "Password not specified\n";
		if (strlen($passphrase)<8) $error .= "Passphrase must be ATLEAST 8 characters long\n";
		
		if (empty($error)){
			//echo "Making dir: $gpg_home <br>";
			//create file containing key specifications
			if (!is_dir(realpath($gpg_home))){
				if (!mkdir($gpg_home, 0700)){
					$error .= "mkdir() failed <br>\n";
				}
			}
			
			if (empty($error)){
				//echo "Made dir <br>\n";
				$temp_file = $gpg_home."/".$user.".tmp";
				$fp = fopen($temp_file, "w");
				if ($fp){
					echo "Opened temp file: $temp_file <br>\n";
					$output = "Key-Type: DSA\n";
					$output.= "Key-Length: 1024\n";
					$output.= "Subkey-Type: ELG-E\n";
					$output.= "Subkey-Length: 1024\n";
					$output.= "Name-Real: ".$name."\n";
					$output.= "#Name-Comment: test\n";
					$output.= "Name-Email: ".$email."\n";
					$output.= "Expire-Date: 0\n";
					$output.= "Passphrase: ".$passphrase."\n";
					$output.= "%commit\n";
					fputs($fp, $output);
					fclose($fp);
					//echo "Wrote: <pre>$output</pre><br>\n";
				}else{
					$error .= "Temp file not created: $temp_file <br>\n";
				}
			}
			//generate key
			if (empty($error)){				
				$command = $GPG_PATH." --home=".realpath($gpg_home)." --batch --gen-key ".realpath($temp_file);
				//echo "Will run command: <tt>$command</tt><br>\n";
				$temp = exec($command, $result, $errorno);
				//echo "Got results: [$errorno]<pre>".implode("\n", $result)."</pre><br>\n";
				
				if ($errorno==0){
					/*
					$command = $GPG_PATH." --home=".realpath($gpg_home)." --export -a";
					//echo "Running: $command <br>\n";
					$temp = exec($command, $result, $errono);
					//echo "Got results: [$errorno]<pre>".implode("\n", $result)."</pre><br>\n";
					*/
					if ($errorno==0) $key_exists = true;
				}else{
					$error.= "Key generation failed.  Erro code: $errorno <br>\n";
				}
				unlink($temp_file);
			}
		}
	}
	
	if ($key_exists && isset($import)){
		if (empty($public_key)) $error .= "Public key not specified...";
		if (empty($error)){
			$temp_file = $gpg_home."/".$user.".import.tmp";
			$fp = fopen($temp_file, "w");
			if ($fp){
				fputs($fp, $public_key);
				fclose($fp);
				echo "Wrote: <pre>$output</pre><br>\n";
			}else{
				$error .= "Temp file not created: $temp_file <br>\n";
			}
		}
		if (empty($error)){
			$command = $GPG_PATH." --home=".realpath($gpg_home)." --import ".realpath($temp_file);
			$temp = exec($command, $result, $errorno);
			"Import: [$errorno] <pre>".implode("\n", $result)."</pre><br>\n";
			unlink($temp_file);
		}
	}
	
	if (isset($show_key) && !empty($person)){
		echo "Show key for $person <br>\n";
		$public_key = gpg_export($person);
	}
	
	$gpgStrings["genkey"] = "Generate Key";
	$gpgStrings["name"] = "Name";
	$gpgStrings["email"] = "Email";
	$gpgStrings["passphrase"] = "Passphrase";
	$gpgStrings["generate"] = "Generate Key";
	?>		
	<font color="red"><?php echo $error?></font>
	<p>
	
	<?php
		if ($key_exists){
	?>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
			<input type="hidden" name="user" value="<?php echo $user?>">
			<input type="hidden" name="session" value="<?php echo $user?>">
			<table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
			<tr bgcolor="<?php echo $my_colors["main_head_bg"]?>">
			<td align="center"><span class="tblheader"><?php echo $gpgStrings["genkey"]?></span></td>
			</tr><tr bgcolor="<?php echo $my_colors["main_bg"]?>">
			<td align="center">
				Show public key for:
				<?php
					$keys = gpg_list_keys();
					$options = "";
					if (is_array($keys) && count($keys)>0){
						while (list($k,$str)=each($keys)){
							$options.= "<option value=\"$k\">$str\n";
						}
					}
				?>
				<select name="person">
				<?php echo $options ?>
				</select>
				<input type="submit" name="show_key" value="Show Key">
				<p>Add public key:<br>
				<textarea cols=80 rows=15 name="public_key"><?php echo $public_key?></textarea><br>
				<input type="submit" name="import" value="Import Public Key">
			</td>
			</tr>
			</table>
			</form>
	<?php
		}else{
	?>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
			<input type="hidden" name="user" value="<?php echo $user?>">
			<input type="hidden" name="session" value="<?php echo $user?>">
			<table border="0" cellspacing="1" cellpadding="1" bgcolor="<?php echo $my_colors["main_hilite"]?>" width="95%">
			<tr bgcolor="<?php echo $my_colors["main_head_bg"]?>">
			<td align="center"><span class="tblheader"><?php echo $gpgStrings["genkey"]?></span></td>
			</tr><tr bgcolor="<?php echo $my_colors["main_bg"]?>">
			<td align="center">
				<table>
					<tr>
						<td align="right"><?php echo $gpgStrings["name"]?>:</td>
						<td><input type="text" name="name" value="<?php echo stripslashes($name)?>" size="45"></td>
					</tr>
					<tr>
						<td align="right"><?php echo $gpgStrings["email"]?>:</td>
						<td><input type="text" name="email" value="<?php echo $email ?>" size="45"></td>
					</tr>
					<tr>
						<td align="right"><?php echo $gpgStrings["passphrase"]?>:</td>
						<td><input type="password" name="passphrase" value="" size="45"></td>
					</tr>
				</table>
				CAUTION!  Generating a key could take a long time (a minute or more).  If you stop loading before a key is generated, this will screw things up big time (you will have to remove your GPG_HOME folder).
				<input type="submit" name="genkey" value="<?php echo $gpgStrings["generate"]?>">
			</td>
			</tr>
			</table>
			</form>
			
			<?php
			}
			?>
</BODY></HTML>
