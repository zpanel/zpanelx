<?php
/////////////////////////////////////////////////////////
//	
//	source/compose.php
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
	FILE:  source/compose.php
	PURPOSE:
		1.  Provide interface for creating messages
		2.  Provide interface for uploading attachments
		3.  Form MIME format (RFC822) compliant messages
		4.  Send message
		5.  Save to "sent items" folder if so specified
	PRE-CONDITIONS:
		$user - Session ID for session validation and user preference retreaval
	POST-CONDITIONS:
		Displays standard message composition interface by default
		If "upload" button pressed, displays all inputted text and attachment info
		If "send" button pressed, sends, files, and displays status
	COMMENTS:
	
********************************************************/

include("../include/super2global.inc");
include("../include/header_main.inc");
include("../lang/".$my_prefs["lang"]."compose.inc");
include("../lang/".$my_prefs["lang"]."dates.inc");
include("../include/icl.inc");
include("../include/version.inc");
include("../conf/defaults.inc");
/******* Init values *******/
if (!isset($attachments)) $attachments=0;

if (isset($change_contacts)) $show_contacts = $new_show_contacts;
if (isset($change_show_cc)) $show_cc = $new_show_cc;

//Handle ddresses submitted from contacts list 
//(in contacts window)
if (is_array($contact_to)) $to .= (empty($to)?"":", ").urldecode(implode(", ", $contact_to));
if (is_array($contact_cc)) $cc .= (empty($cc)?"":", ").urldecode(implode(", ", $contact_cc));
if (is_array($contact_bcc)) $bcc .= (empty($bcc)?"":", ").urldecode(implode(", ", $contact_bcc));
//(in compose window)
if ((isset($to_a)) && (is_array($to_a))){
    reset($to_a);
    while ( list($key, $val) = each($to_a)) $$to_a_field .= ($$to_a_field!=""?", ":"").stripslashes($val);
}

//generate authenticated email address
if (empty($init_from_address)){
	$sender_addr = $loginID.( strpos($loginID, "@")>0 ? "":"@".$host );
}else{
	$sender_addr = str_replace("%u", $loginID, str_replace("%h", $host, $init_from_address));
}

/***
	generate user's name
***/
$from_name = (empty($my_prefs["user_name"])?"":"\"".LangEncodeSubject($my_prefs["user_name"], $my_charset)."\" ");

if ($TRUST_USER_ADDRESS){
    //Honor User Address
    //If email address is specified in prefs, use that in the "From"
    //field, and set the Sender field to an authenticated address
    $from_addr = (empty($my_prefs["email_address"]) ? $sender_addr : $my_prefs["email_address"] );
    $from = $from_name."<".$from_addr.">";
    $reply_to = "";
}else{
    //Default
    //Set "From" to authenticated user address
    //Set "Reply-To" to user specified address (if any)
	$from_addr = $sender_addr;
    $from = $from_name."<".$sender_addr.">";
    if (!empty($my_prefs["email_address"])) $reply_to = $from_name."<".$my_prefs["email_address"].">";
    else $reply_to = "";
}
$original_from = $from;

/***
	CHECK UPLOADS DIR
***/
$uploadDir = $UPLOAD_DIR.ereg_replace("[\\/]", "", $loginID.".".$host);
if (!is_dir($uploadDir)) $error .= "Invalid uploads dir<br>";

/****
	SEND
****/
if (isset($send)){
	$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if (!$conn)
		echo "failed";
	else{
		//echo "Composing...<br>\n"; flush();
		
		$error = "";
		
		/**** Check for subject ***/
        $no_subject = false;
		if ((strlen($subject)==0)&&(!$confirm_no_subject)){
            $error .= $composeErrors[0]."<br>\n";
            $no_subject = true;
        }
		
		/**** Check "from" ***/
		if (strlen($from)<7) $error .= $composeErrors[1]."<br>\n";
		
		/**** Check for recepient ***/
		$to = stripslashes($to);
		if ((strcasecmp($to, "self")==0) || (strcasecmp($to, "me")==0)) $to=$my_prefs["email_address"];
		if ((strlen($to) < 7) || (strpos($to, "@")===false))
			$error .= $composeErrors[2]."<br>\n";
			
		/**** Anti-Spam *****/
		$as_ok = true;
		//echo "lastSend: $lastSend <br> numSent: $numSent <br>\n";
		//echo "$max_rcpt_message $max_rcpt_session $min_send_interval <br>";
		if ((isset($max_rcpt_message)) && ((isset($max_rcpt_session))) && (isset($min_send_interval))){
			$num_recepients = substr_count($to.$cc.$bcc, "@");
			if ($num_recepients > $max_rcpt_message) $as_ok = false;
			if (($num_recepients + $numSent) > $max_rcpt_session) $as_ok = false;
			if ((time() - $lastSend) < $min_send_interval) $as_ok = false;
		}else{
			echo "Bypassing anti-spam<br>\n";
		}
		if (!$as_ok){
			$as_error = $composeErrors[5];
			$as_error = str_replace("%1", $max_rcpt_message, $as_error);
			$as_error = str_replace("%2", $max_rcpt_session, $as_error);
			$as_error = str_replace("%3", $min_send_interval, $as_error);
			$error .= $as_error;
		}
		/**********************/

		if ($error){
			//echo "<font color=\"red\">".$error."</font><br><br>\n";
		}else{
			//echo "<p>Sending....";
			//flush();
			
			$num_parts=0;
	
			/*** Initialize header ***/
			$headerx = "Date: ".TZDate($my_prefs["timezone"])."\n";
			$headerx.= "X-Mailer: IlohaMail/".$version." (On: ".$_SERVER["SERVER_NAME"].")\n";
			if (!empty($replyto_messageID)) $headerx.= "In-Reply-To: <".$replyto_messageID.">\n";
		
			/****  Attach Sig ****/
				
			if ($attach_sig==1) $message.="\n\n".$my_prefs["signature1"];
				
			/****  Attach Tag-line ***/
			
			if ($userLevel < $TAG_LEVEL){
				$message .= "\n\n".$TAG_LINE;
			}

			/****  Encode  ****/
			$subject=stripslashes($subject);
			$message=stripslashes($message);
			$subject=LangEncodeSubject($subject, $my_charset);
			$part[0]=LangEncodeMessage($message, $my_charset);

			/***********************/
				
			/****  Pre-process addresses */
			$from = stripslashes($from);
			$to = stripslashes($to);
				
			$to = LangEncodeAddressList($to, $my_charset);
			$from = LangEncodeAddressList($from, $my_charset);
					
			if (!empty($cc)){
				$cc= stripslashes($cc);
				$cc = LangEncodeAddressList($cc, $my_charset);
			}
			if (!empty($bcc)){
				$bcc = stripslashes($bcc);
				$bcc = LangEncodeAddressList($bcc, $my_charset);
			}
			/***********************/

                    
			/****  Add Recipients *********/
			//$headerx.="Return-Path: ".$sender_addr."\n";
			$headerx.="From: ".$from."\n";
            //$headerx.="Sender: ".$sender_addr."\n";
			$headerx.="Bounce-To: ".$from."\n";
            $headerx.="Errors-To: ".$from."\n";
			if (!empty($reply_to)) $headerx.="Reply-To: ".stripslashes($reply_to)."\n";
			if ($cc){
				$headerx.="CC: ". stripslashes($cc)."\n";
			}
			if ($bcc){
				$headerx.="BCC: ".stripslashes($bcc)."\n";
			}
			/************************/
				
			/****  Prepare attachments *****/
			echo "Attachments: $attachments <br>\n";
			if (is_dir($uploadDir)){
				//open directory
				if ($handle = opendir($uploadDir)) {
					//loop through files
					while (false !== ($file = readdir($handle))) {
						if (($file != "." && $file != "..") && ($attach[$file]==1)) {
							//split up file name
							$file_parts = explode(".", $file);
							
							//put together full path
							$path = $uploadDir."/".$file;

							//read data
							$fp=fopen($path,"r");
							$a_data=fread($fp,filesize($path));
							fclose($fp);
	
							//if data is good...
							if ($a_data!=""){
								echo "Attachment $i is good <br>\n";
								$num_parts++;
									
								//get name and type
								$a_name=base64_decode($file_parts[1]);
								$a_type=strtolower(base64_decode($file_parts[2]));
								if ($a_type=="") $a_type="application/octet-stream";								
								
								//stick it in conent array
								$part[$num_parts]["type"]="Content-Type: ".$a_type."; name=\"".$a_name."\"\n";
								$part[$num_parts]["disposition"]="Content-Disposition: attachment; filename=\"".$a_name."\"\n";
								$part[$num_parts]["encoding"]="Content-Transfer-Encoding: base64\n";
								$part[$num_parts]["data"]=myWordWrap(base64_encode($a_data),76);
							}


							//delete file
							unlink($path);
						}
					}
					closedir($handle); 
				}
			}

			
			/**** Put together MIME message *****/
			echo "Num parts: $num_parts <br>\n";
			if ($num_parts==0){
				$headerx.=$part[0]["type"];
				if (!empty($part[0]["encoding"])) $headerx.=$part[0]["encoding"];
				$body=$part[0]["data"];
			}else{
				$boundary="RWP_PART_".$loginID.time();
				$headerx.="MIME-Version: 1.0 \n";
				$headerx.="Content-Type: multipart/mixed; boundary=\"$boundary\"\n"; 
					
				$body="This message is in MIME format.\n";
				for ($i=0;$i<=$num_parts;$i++){
					$body.="\n--".$boundary."\n";
					if ($part[$i]["type"]!="") $body.=$part[$i]["type"];
					if ($part[$i]["encoding"]!="") $body.=$part[$i]["encoding"];
					if ($part[$i]["disposition"]!="") $body.=$part[$i]["disposition"];
					$body.="\n";
					$body.=$part[$i]["data"]."\n";
				}
				$body.="\n--".$boundary."--";
			}


			/**** Send message *****/
			echo "Sending...<br>";
			if (mail($to,$subject,$body,$headerx, "-f$from_addr")){
				echo "Sent.<br>"; flush();
				$error = "";
				//save in send folder
				$append_str="To: ".$to."\n".(!empty($subject)?"Subject: ".$subject:"")."\n".$headerx."\n\n".$body;
				if ($my_prefs["save_sent"]==1){
					$append_str="To: ".$to."\n"."Subject: ".$subject."\n".$headerx."\n\n".$body;
					if (!iil_C_Append($conn, $my_prefs["sent_box_name"], $append_str)){
						$error .= "Couldn't save in ".$my_prefs["sent_box_name"].":".$conn->error."<br>\n";
					}else{
						echo "Saved in ".$my_prefs["sent_box_name"]."<br>\n";
					}
				}
				//echo nl2br($append_str);
				
				//if replying, flag original message
				if (isset($in_reply_to)) $reply_id = $in_reply_to;
				else if (isset($forward_of)) $reply_id = $forward_of;
				if (isset($reply_id)){
					$pos = strrpos($reply_id, ":");
					$reply_num = substr($reply_id, $pos+1);
					$reply_folder = substr($reply_id, 0, $pos);
					
					if (iil_C_Flag($conn, $reply_folder, $reply_num, "ANSWERED") < 1){
						$error .= "Flagging failed:".$conn->error."<br>\n";
					}
				}
				
				//update spam-prevention related records
				include("../include/as_update.inc");

				//close window
				if ((empty($error))&&($my_prefs["closeAfterSend"]==1)){
					echo "<p>Message successfully sent.";
					echo "<script type=\"text/javascript\"><!--\nwindow.close();\n--></script>";
					echo "<br><br>"; flush();
				}else{
					echo $error;
				}
			}else{
				echo "<p><font color=\"red\">Send FAILED</font>";
			}

			iil_Close($conn); 
			exit;
		}
	iil_Close($conn);
	}
}


/****
	HANDLE UPLOADED FILE
****/
if (isset($upload)){
	if (($userfile)&&($userfile!="none")){
		$i=$attachments;
		$newfile = base64_encode($userfile_name).".".base64_encode($userfile_type).".".base64_encode($userfile_size);
		$newpath=$uploadDir."/".$user.".".$newfile;
		if (!move_uploaded_file($userfile, $newpath)){
			echo $userfile_name." : ".$composeErrors[3];
		}
	}else{
		echo $composeErrors[4];
	}
}


/****
	FETCH LIST OF UPLOADED FILES
****/
if (is_dir($uploadDir)){
	//open directory
	if ($handle = opendir($uploadDir)) {
		//loop through files
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				//split up file name
				$file_parts = explode(".", $file);
				
				//make sure first part is session ID, and add to list
				if (strcmp($file_parts[0], $user)==0) $uploaded_files[] = $file;
			} 
		}
		closedir($handle); 
	}
}	


/****
	REPLYING OR FORWARDING
****/
if ((isset($replyto)) || (isset($forward))){
    // if REPLY, or FORWARD
	if ((isset($folder))&&(isset($id))){
        include_once("../include/mime.inc");
        
		$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
		$header=iil_C_FetchHeader($conn, $folder, $id);
        $structure_str=iil_C_FetchStructureString($conn, $folder, $id);
        $structure=iml_GetRawStructureArray($structure_str);
		
		$subject=LangDecodeSubject($header->subject, $my_prefs["charset"]);
		$lookfor=(isset($replyto)?"Re:":"Fwd:");
		$pos = strpos ($subject, $lookfor);
        if ($pos===false) {
			$pos = strpos ($subject, strtoupper($lookfor));
        	if ($pos===false) {
				$subject=$lookfor." ".$subject;
			}
        }
		
		//get messageID
		$replyto_messageID = $header->messageID;
		
		//get "from";
		$from = $header->from;
		//replace to "reply-to" if specified
		if ($replyto){
			$to = $from;
			if (!empty($header->replyto)) $to = $header->replyto;
		}
		if ($replyto_all){
			if (!empty($header->to)) $to .= (empty($to)?"":", ").$header->to;
			if (!empty($header->cc)) $cc .= (empty($cc)?"":", ").$header->cc;
		}
		
		//mime decode "to," "cc," and "from" fields
		if (isset($to)){
			$to_a = LangParseAddressList($to);
			$to = "";
			while ( list($k, $v) = each($to_a) ){
			
                //remove user's own address from "to" list
                if ((stristr($to_a[$k]["address"], $from_addr) === false) and
 				    (stristr($to_a[$k]["address"], $loginID."@".$host) === false) and
					(stristr($to_a[$k]["address"], $my_prefs["email_address"])===false)){
                    $to .= (empty($to)?"":", ")."\"".LangDecodeSubject($to_a[$k]["name"], $my_prefs["charset"])."\" <".$to_a[$k]["address"].">";
                }
            }
		}
		if (isset($cc)){
			$cc_a = LangParseAddressList($cc);
			$cc = "";
			while ( list($k, $v) = each($cc_a) ){
                //remove user's own address from "cc" list
                if ((stristr($cc_a[$k]["address"], $from_addr) === false) and
 				    (stristr($cc_a[$k]["address"], $loginID."@".$host) === false) and
					(stristr($to_a[$k]["address"], $my_prefs["email_address"])===false)){
                    $cc .= (empty($cc)?"":", ")."\"".LangDecodeSubject($cc_a[$k]["name"], $my_prefs["charset"])."\" <".$cc_a[$k]["address"].">";
                }
            }
		}
		$from_a = LangParseAddressList($from);
		$from = "\"".LangDecodeSubject($from_a[0]["name"], $my_prefs["charset"])."\" <".$from_a[0]["address"].">";
		
		//format headers for reply/forward
		if (isset($replyto)){
			$message_head = $composeStrings[9];
			$message_head = str_replace("%d", LangFormatDate($header->timestamp, $lang_datetime["prevyears"]), $message_head);
			$message_head = str_replace("%s", $from, $message_head);
		}else if (isset($forward)){
			if ($show_header){
				$message_head = iil_C_FetchPartHeader($conn, $folder, $id, 0);
			}else{
				$message_head = $composeStrings[10];
				$message_head .= $composeHStrings[5].": ".ShowDate2($header->date,"","short")."\n";
				$message_head .= $composeHStrings[1].": ". LangDecodeSubject($from, $my_prefs["charset"])."\n";
				$message_head .= $composeHStrings[0].": ".LangDecodeSubject($header->subject, $my_prefs["charset"])."\n\n";
			}
		}
		if (!empty($message_head)) $message_head = "\n".$message_head."\n";
		
		//get message
        if (!empty($part)) $part.=".1";
        else{
            $part = iml_GetFirstTextPart($structure, "");
        }
        
		$message=iil_C_FetchPartBody($conn, $folder, $id, $part);
		if (empty($message)){
            $part = 0;
            $message = iil_C_FetchPartBody($conn, $folder, $id, $part);
		}
        
		//decode message if necessary
        $encoding=iml_GetPartEncodingCode($structure, $part);        
		if ($encoding==3) $message = base64_decode($message);
		else if ($encoding==4){
            if ($encoding == 3 ) $message = base64_decode($message);
            else if ($encoding == 4) $message = quoted_printable_decode($message);					
            //$message = str_replace("=\n", "\n", $message);
            //$message = quoted_printable_decode(str_replace("=\r\n", "\n", $message));
        }
		
        //add quote marks
		$message = str_replace("\r", "", $message);
		$charset=iml_GetPartCharset($structure, $part);
		$message=LangConvert($message, $my_prefs["charset"], $charset);
		if (isset($replyto)) $message=">".str_replace("\n","\n>",$message);
		$message = "\n".LangConvert($message_head, $my_prefs["charset"]).$message;

		iil_Close($conn);			
	}
}
?>
<FORM ENCTYPE="multipart/form-data" ACTION="compose.php" METHOD=POST>
	<input type="hidden" name="user" value="<?php echo $user?>">
	<input type="hidden" name="show_contacts" value="<?php echo $show_contacts?>">
	<input type="hidden" name="show_cc" value="<?php echo $show_cc?>">
	<?php
        if ($no_subject) echo '<input type="hidden" name="confirm_no_subject" value="1">';
    
		if ($replyto){
			$in_reply_to = $folder.":".$id;
			echo "<input type=\"hidden\" name=\"in_reply_to\" value=\"$in_reply_to\">\n";
			echo "<input type=\"hidden\" name=\"replyto_messageID\" value=\"$replyto_messageID\">\n";
		}else if ($forward){
			$forward_of = $folder.":".$id;
			echo "<input type=\"hidden\" name=\"forward_of\" value=\"$forward_of\">\n";
		}
	?>
		<table border="0" width="100%">
	<tr>
		<td valign="bottom" align="left">
			<font size=+1><b><?php echo $composeStrings[0]; ?></b></font>
			&nbsp;&nbsp;&nbsp;
			<font size="-1">[<a href="" onClick="window.close();"><?php echo $composeStrings[11]?></a>]</font>
		</td>
		<td valign="bottom" align="right">
								</td>
	</tr>
	</table>
        <?php
    if (!empty($error)) echo '<br><font color="red">'.$error.'</font>';
    ?>
		<p><?php echo $composeHStrings[0]?>:<input type=text name="subject" value="<?php echo htmlspecialchars(stripslashes($subject))?>" size="60">
	<input type=submit name=send value="<?php echo  $composeStrings[1]?>">
	<?php
	
		$to = htmlspecialchars($to);
		$cc = htmlspecialchars($cc);
		$bcc = htmlspecialchars($bcc);
	
		// format sender's email address (i.e. "from" string)
        $email_address = htmlspecialchars($original_from);
		echo "<table>";
		echo "<tr><td align=right>".$composeHStrings[1].":</td><td>".LangDecodeSubject($email_address, $my_prefs["charset"])."</td></tr>\n";
  
		if (($show_contacts) || ($my_prefs["showContacts"])){
			echo "<tr><td align=right valign=top>";
			echo "<select name=\"to_a_field\">\n";
			echo "<option value=\"to\">".$composeHStrings[2].":\n";
			echo "<option value=\"cc\">".$composeHStrings[3].":\n";
			echo "<option value=\"bcc\">".$composeHStrings[4].":\n";
			echo "</select>\n";
			echo"</td><td>";
		
			// display "select" box with contacts
			include("../include/read_contacts.inc");
			if ((is_array($contacts)) && (count($contacts) > 0)){
				echo "<select name=\"to_a[]\" MULTIPLE SIZE=5>\n";
				while ( list($key, $foobar) = each($contacts) ){
					$contact = $contacts[$key];
					if (!empty($contact["email"])){
						$line = "\"".$contact["name"]."\" <".$contact["email"].">";
						echo "<option>".htmlspecialchars($line)."\n";
					}
					if (!empty($contact["email2"])){
						$line = "\"".$contact["name"]."\" <".$contact["email2"].">";
						echo "<option>".htmlspecialchars($line)."\n";
					}
				}
				echo "</select>"; 
				echo "<input type=\"submit\" name=\"add_contacts\" value=\"".$composeStrings[8]."\"><br>\n";
			}
			echo "</td></tr>\n";
			$contacts_shown = true;
		}else{
			$contacts_shown = false;
		}
		
		// display to box
		if (strlen($to) < 60)
            echo "<tr><td align=right>".$composeHStrings[2].":</td><td><input type=text name=\"to\" value=\"".stripslashes($to)."\" size=60></td></tr>\n";
        else
            echo "<tr><td align=right>".$composeHStrings[2].":</td><td><textarea name=\"to\" cols=\"60\" rows=\"3\">".stripslashes($to)."</textarea></td></tr>\n";

		if ((!empty($cc)) || ($my_prefs["showCC"]==1) || ($show_cc)){
			// display cc box
        	if (strlen($cc) < 60)
            	echo "<tr><td align=right>".$composeHStrings[3].":</td><td><input type=text name=\"cc\" value=\"".stripslashes($cc)."\" size=60></td></tr>\n";
        	else
            	echo "<tr><td align=right>".$composeHStrings[3].":</td><td><textarea name=\"cc\" cols=\"60\" rows=\"3\">".stripslashes($cc)."</textarea></td></tr>\n";
			$cc_field_shown = true;
		}else{
			$cc_field_shown = false;
		}
		
		if ((!empty($bcc)) || ($my_prefs["showCC"]==1) || ($show_cc)){
			// display bcc box
        	if (strlen($bcc) < 60)
            	echo "<tr><td align=right>".$composeHStrings[4].":</td><td><input type=text name=\"bcc\" value=\"".stripslashes($bcc)."\" size=60></td></tr>\n";
			else
            	echo "<tr><td align=right>".$composeHStrings[4].":</td><td><textarea name=\"bcc\" cols=\"60\" rows=\"3\">".stripslashes($bcc)."</textarea></td></tr>\n";
			$bcc_field_shown = true;
		}else{
			$bcc_field_shown = false;
		}
		
		
		//show attachments
		echo "<tr>";
		echo "<td align=\"right\" valign=\"top\">".$composeStrings[4].":</td>";
		echo "<td valign=\"top\">";
		if ((is_array($uploaded_files)) && (count($uploaded_files)>0)){
			echo "<table>";
			reset($uploaded_files);
			while ( list($k,$file) = each($uploaded_files) ){
				$file_parts = explode(".", $file);
				echo "<tr><td valign=\"bottom\"><input type=\"checkbox\" name=\"attach[$file]\" value=\"1\" ".($attach[$file]==1?"CHECKED":"")."></td>";
				echo "<td valign=\"bottom\" bgcolor=\"".$my_colors["main_hilite"]."\">".base64_decode($file_parts[1])."&nbsp;</td>";
				echo "<td valign=\"bottom\" bgcolor=\"".$my_colors["main_hilite"]."\" class=\"small\">".base64_decode($file_parts[3])."bytes&nbsp;</td>";
				echo "<td valign=\"bottom\" bgcolor=\"".$my_colors["main_hilite"]."\" class=\"small\">(".base64_decode($file_parts[2]).")</td>";
				echo "</td></tr>\n";
			}
			echo "</table>";
		}
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="2000000">';
		echo "<INPUT NAME=\"userfile\" TYPE=\"file\">";
		echo '<INPUT TYPE="submit" NAME="upload" VALUE="'.$composeStrings[2].'">';
		echo "</td></tr>\n";
		
		echo "<tr><td></td><td>";
		if (!$contacts_shown){
			//"show contacts" button
			echo "<input type=\"hidden\" name=\"new_show_contacts\" value=1>\n";
			echo "<input type=\"submit\" name=\"change_contacts\" value=\"".$composeStrings[5]."\">\n";
		}else{
			//"hide contacts" button, if not specified in prefs
			if ($my_prefs["showContacts"]!=1){
				echo "<input type=\"hidden\" name=\"new_show_contacts\" value=0>\n";
				echo "<input type=\"submit\" name=\"change_contacts\" value=\"".$composeStrings[6]."\">\n";
			}
		}
		
		if ((!$cc_field_shown) || (!$bcc_field_shown)){
			//"show cc/bcc field" button
			include("../lang/".$my_prefs["lang"]."prefs.inc");
			echo '<input type="hidden" name="new_show_cc" value="1">';
			echo '<input type="submit" name="change_show_cc" value="'.$prefsStrings["6.2"].'">';
		}else{
			echo '<input type="hidden" name="new_show_cc" value="'.$show_cc.'">';
		}
		echo "</td></tr>\n";
        echo "</table>";
	?>
	<br><?php echo $composeStrings[7]?><br>
	<TEXTAREA NAME=message ROWS=20 COLS=77 WRAP=virtual><?php echo htmlspecialchars(stripslashes($message)); ?></TEXTAREA>
	<br><input type=checkbox name="attach_sig" value=1 <?php echo ($my_prefs["show_sig1"]==1?"CHECKED":""); ?> ><?php echo $composeStrings[3]."\n"; ?>
	<?php

	?>
	<input type="hidden" name="attachments" value="<?php echo $attachments; ?>">

</form>
<?php
?>
</BODY></HTML>
