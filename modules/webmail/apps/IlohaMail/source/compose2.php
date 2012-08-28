<?php
/////////////////////////////////////////////////////////
//	
//	source/compose2.php
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
include("../include/mod_base64.inc");
include("../conf/defaults.inc");
if ($GPG_ENABLE){
	include_once("../include/gpg.inc");
}

function RemoveDoubleAddresses($to) {
	$to_adr = iil_ExplodeQuotedString(",", $to);
	$adresses = array();
	$contacts = array();
	foreach($to_adr as $addr) {
		$addr = trim($addr);
		if (preg_match("/(.*<)?.*?([^\s\"\']+@[^\s>\"\']+)/", $addr, $email)) {
			$email = strtolower($email[2]);
			if (!in_array($email, $adresses)) {						//New adres
				array_push($adresses, $email);
				$contacts[$email] = $addr;
			} elseif (strlen($contacts[$email])<strlen($addr)) {				//Adres already in list and name is longer
				$contacts[$email] = trim($addr);
			}
		}
	}
	return implode(", ",$contacts);
}

function ResolveContactsGroup($str){
	global $contacts;
	
	$tokens = explode(" ", $str);
	if (!is_array($tokens)) return $str;
	
	while ( list($k,$token)=each($tokens) ){
		if (ereg("@contacts.group", $token)){
			if (ereg("^<", $token)) $token = substr($token, 1);
			list($group, $junk) = explode("@contacts.", $token);
			$group = base64_decode($group);
			$newstr = "";
			reset($contacts);
			while ( list($blah, $contact)=each($contacts) ){
				if ($contact["grp"]==$group && !empty($contact["email"])){
					$newstr.= (!empty($newstr)?", ":"");
					$newstr.= "\"".$contact["name"]."\" <".$contact["email"].">";
				}
			}
			if (ereg(",$", $token)) $newstr.= ",";
			$tokens[$k] = $newstr;
			if (ereg(str_replace(" ", "_", $group), $tokens[$k-1])) $tokens[$k-1] = "";
		}
	}
	
	return implode(" ", $tokens);
}


if (ini_get('file_uploads')!=1){
	echo "Error:  Make sure the 'file_uploads' directive is enabled (set to 'On' or '1') in your php.ini file";
}



/******* Init values *******/
if (!isset($attachments)) $attachments=0;
if (isset($change_contacts)) $show_contacts = $new_show_contacts;
if (isset($change_show_cc)) $show_cc = $new_show_cc;

//read alternate identities
include_once("../include/data_manager.inc");
$ident_dm = new DataManager_obj;
if ($ident_dm->initialize($loginID, $host, $DB_IDENTITIES_TABLE, $DB_TYPE)){
	$alt_identities = $ident_dm->read();
}

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

//generate user's name
$from_name = $my_prefs["user_name"];
$from_name = LangEncodeSubject($from_name, $my_charset);
if ((!empty($from_name)) && (count(explode(" ", $from_name)) > 1)) $from_name = "\"".$from_name."\"";

if ($TRUST_USER_ADDRESS){
    //Honor User Address
    //If email address is specified in prefs, use that in the "From"
    //field, and set the Sender field to an authenticated address
    $from_addr = (empty($my_prefs["email_address"]) ? $sender_addr : $my_prefs["email_address"] );
    $from = $from_name." <".$from_addr.">";
    $reply_to = "";
}else{
    //Default
    //Set "From" to authenticated user address
    //Set "Reply-To" to user specified address (if any)
	$from_addr = $sender_addr;
    $from = $from_name." <".$sender_addr.">";
    if (!empty($my_prefs["email_address"])) $reply_to = $from_name." <".$my_prefs["email_address"].">";
    else $reply_to = "";
}
$original_from = $from;

echo "\n<!-- FROM: $original_from //-->\n";


//resolve groups added from contacts selector
$to_has_group = $cc_has_group = $bcc_has_group = false;
if (!empty($to)) $to_has_group = ereg("@contacts.group", $to);
if (!empty($cc)) $cc_has_group = ereg("@contacts.group", $cc);
if (!empty($bcc)) $bcc_has_group = ereg("@contacts.group", $bcc);
if ($to_has_group || $cc_has_group || $bcc_has_group){
	$dm = new DataManager_obj;
	if ($dm->initialize($loginID, $host, $DB_CONTACTS_TABLE, $DB_TYPE)){
		if (empty($sort_field)) $sort_field = "grp,name";
		if (empty($sort_order)) $sort_order = "ASC";
		$contacts = $dm->sort($sort_field, $sort_order);
		
		if ($to_has_group) $to = ResolveContactsGroup($to);
		if ($cc_has_group) $cc = ResolveContactsGroup($cc);
		if ($bcc_has_group) $bcc = ResolveContactsGroup($bcc);
	}
}



/***
	CHECK UPLOADS DIR
***/
$uploadDir = $UPLOAD_DIR.ereg_replace("[\\/]", "", $loginID.".".$host);
if (!file_exists(realpath($uploadDir))) $error .= "Uploads dir not found: \"$uploadDir\"<br>";


/****
	SEND
****/
function cmp_send(){}
if (isset($send_1)||isset($send_2)){
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
		
		/**** alternate identity? ****/
		$reply_to = '';
		if ($sender_identity_id > 0){
			//format sender name
			$from_name = $alt_identities[$sender_identity_id]["name"];
			$from_name = LangEncodeSubject($from_name, $my_charset);
			if ((!empty($from_name)) && (count(explode(" ", $from_name)) > 1)) $from_name = "\"".$from_name."\"";
			
			//format from/reply-to addresses
			$from_addr = $alt_identities[$sender_identity_id]["email"];
			$reply_to_addr = $alt_identities[$sender_identity_id]["replyto"];
			
			//Assign to proper field, respecting TRUST_USER_ADDRESS
			if ($TRUST_USER_ADDRESS){
				$from = $from_name.' <'.$from_addr.'>';
				if ($reply_to_addr) $reply_to = $from_name.' <'.$reply_to_addr.'>';
			}else{
				$reply_to = $from_name.' <'.$from_addr.'>';
			}
		}
		
		/**** Check "from" ***/
		if (strlen($from)<7) $error .= $composeErrors[1]."<br>\n";
		
		/**** Check for recepient ***/
		$to = stripslashes($to);
		$to = str_replace(";",",",$to);
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
			$headerx = "Date: ".TZDate($my_prefs["timezone"])."\r\n";
			$headerx.= "X-Mailer: IlohaMail/".$version." (On: ".$_SERVER["SERVER_NAME"].")\r\n";
			$mt_str = microtime();
			$space_pos = strpos($mt_str, " ");
			$message_id = GenerateRandomString(8,"").".".substr($mt_str, $space_pos+1).substr($mt_str, 1, $space_pos - 2).".".$sender_addr;
			$headerx.= "Message-ID: <".$message_id.">\r\n";
			if (!empty($replyto_messageID)) $headerx.= "In-Reply-To: <".$replyto_messageID.">\r\n";
		

			/****  Attach Sig ****/
			if ($attach_sig==1){
				if ($sender_identity_id > 0) $message.="\n\n".$alt_identities[$sender_identity_id]["sig"];
				else $message.= "\n\n".$my_prefs["signature1"];
			}	

			/****  Attach Tag-line ***/
			
			if ($userLevel < $TAG_LEVEL){
				$message .= "\n\n".$TAG_LINE;
			}

			/******* GPG stuff *********/
			if(isset($keytouse) && $GPG_ENABLE){
				$gpg_encrypted = gpg_encrypt($loginID, $host, $keytouse, $message);
			}
			
			/****  smart wrap ****/
			$message = LangSmartWrap($message, 74);

			/****  Encode  ****/
			$subject=stripslashes($subject);
			$subject=LangEncodeSubject($subject, $my_charset);
			
			if (!$gpg_encrypted){
				$message=stripslashes($message);
				$part[0]=LangEncodeMessage($message, $my_charset);
			}else{
				$part[0]["data"] = $message;
			}
			/***********************/
				
			/****  Pre-process addresses */
			$from = stripslashes($from);
			$to = stripslashes($to);
			
			$to = RemoveDoubleAddresses($to);
			
			echo "To: ".htmlspecialchars($to)." <br>\n";
				
			$to = LangEncodeAddressList($to, $my_charset);
			$from = LangEncodeAddressList($from, $my_charset);
					
			if (!empty($cc)){
				$cc = stripslashes($cc);
				$cc = str_replace(";",",",$cc);
				$cc = RemoveDoubleAddresses($cc);
				$cc = LangEncodeAddressList($cc, $my_charset);
				
			}
			if (!empty($bcc)){
				$bcc = stripslashes($bcc);
				$bcc = str_replace(";",",",$bcc);
				$bcc = RemoveDoubleAddresses($bcc);
				$bcc = LangEncodeAddressList($bcc, $my_charset);
			}
			/***********************/

                    
			/****  Add Recipients *********/
			//$headerx.="Return-Path: ".$sender_addr."\n";
			$headerx.="From: ".$from."\r\n";
            //$headerx.="Sender: ".$sender_addr."\n";
			$headerx.="Bounce-To: ".$from."\r\n";
            $headerx.="Errors-To: ".$from."\r\n";
			if (!empty($reply_to)) $headerx.="Reply-To: ".stripslashes($reply_to)."\r\n";
			if ($cc){
				$headerx.="CC: ". stripslashes($cc)."\r\n";
			}
			if ($bcc && !$SMTP_SERVER){
				//add bcc to header only if sending through PHP's mail() => i.e. no SMTP_SERVER specified
				$headerx.="BCC: ".stripslashes($bcc)."\r\n";
			}
			/************************/
				
			/****  Prepare attachments *****/
			echo "Attachments: $attachments <br>\n";
			if (file_exists(realpath($uploadDir))){
				if (is_array($attach)){
					while ( list($file, $v) = each($attach) ){
						if ($v==1){
							//split up file name
							$file_parts = explode(".", $file);
							
							//put together full path
							$a_path = $uploadDir."/".$file;

							//get name and type
							$a_name=mod_base64_decode($file_parts[1]);
							$a_type=strtolower(mod_base64_decode($file_parts[2]));
							if ($a_type=="") $a_type="application/octet-stream";								

							//if data is good...
							if (($file_parts[0]==$user) && (file_exists(realpath($a_path)))){
								echo "Attachment $i is good <br>\n";
								$num_parts++;			
								
								//stick it in conent array
								$part[$num_parts]["type"]="Content-Type: ".$a_type."; name=\"".$a_name."\"\r\n";
								$part[$num_parts]["disposition"]="Content-Disposition: attachment; filename=\"".$a_name."\"\r\n";
								$part[$num_parts]["encoding"]="Content-Transfer-Encoding: base64\r\n";
								$part[$num_parts]["size"] = filesize($a_path);
								$attachment_size += $part[$num_parts]["size"];
								$part[$num_parts]["path"] = $a_path;
							}else if (strpos($file_parts[0], "fwd-")!==false){
							//forward an attachment
								//extract specs of attachment
								$fwd_specs = explode("-", $file_parts[0]);
								$fwd_folder = mod_base64_decode($fwd_specs[1]);
								$fwd_id = $fwd_specs[2];
								$fwd_part = mod_base64_decode($fwd_specs[3]);
								
								//get attachment content
								$fwd_content = iil_C_FetchPartBody($conn, $fwd_folder, $fwd_id, $fwd_part);

								//get attachment header
								$fwd_header = iil_C_FetchPartHeader($conn, $fwd_folder, $fwd_id, $fwd_part);
								
								//extract "content-transfer-encoding field
								$head_a = explode("\n", $fwd_header);
								if (is_array($head_a)){
									while ( list($k,$head_line)=each($head_a) ){
										$head_line = chop($head_line);
										if (strlen($head_line)>15){
											list($head_field,$head_val)=explode(":", $head_line);
											if (strcasecmp($head_field, "content-transfer-encoding")==0){
												$fwd_encoding = trim($head_val);
												echo $head_field.": ".$head_val."<br>\n";
											}
										}
									}
								}
									
								//create file in uploads dir
								$file = $user.".".$file_parts[1].".".$file_parts[2].".".$file_parts[3];
								$a_path = $uploadDir."/".$file;
								$fp = fopen($a_path, "w");
								if ($fp){
									fputs($fp, $fwd_content);
									fclose($fp);
								}else{
									echo "Error when saving fwd att to $a_path <br>\n";
								}
								$fwd_content = "";
									
								echo "Attachment $i is a forward <br>\n";
								$num_parts++;

								//stick it in conent array
								$part[$num_parts]["type"]="Content-Type: ".$a_type."; name=\"".$a_name."\"\r\n";
								$part[$num_parts]["disposition"]="Content-Disposition: attachment; filename=\"".$a_name."\"\r\n";
								if (!empty($fwd_encoding)) $part[$num_parts]["encoding"] = "Content-Transfer-Encoding: $fwd_encoding\r\n";
								$part[$num_parts]["size"] = filesize($a_path);
								$attachment_size += $part[$num_parts]["size"];
								$part[$num_parts]["path"] = $a_path;
								$part[$num_parts]["encoded"] = true;
								
							}
						}
					}
				}
			}

			
			/**** Put together MIME message *****/
			echo "Num parts: $num_parts <br>\n";
			
			//
			$received_header = "Received: from ".$_SERVER["REMOTE_ADDR"]." (auth. user $loginID@$host)\r\n";
			$received_header.= "          by ".$_SERVER["SERVER_NAME"]." with HTTP; ".TZDate($my_prefs["timezone"])."\r\n";
			$headerx = $received_header."To: ".$to."\r\n".(!empty($subject)?"Subject: ".$subject."\r\n":"").$headerx;
			
			if ($gpg_encrypted){
				//OpenPGP Compliance.  See RFC2015
				//create boundary
				$tempID = ereg_replace("[/]","",$loginID).time();
				$boundary="RWP_PART_".$tempID;

				//message header...
				$headerx.="Mime-Version: 1.0\r\n";
				$headerx.="Content-Type: multipart/encrypted; boundary=$boundary;\r\n";
				$headerx.="        protocol=\"application/pgp-encrypted\"\r\n";

				$body = "--".$boundary."\r\n";
				$body.= "Content-Type: application/pgp-encrypted\r\n\r\n";
				$body.= "Version: 1\r\n\r\n";
				
				$body.= "--".$boundary."\r\n";
				$body.= "Content-Type: application/octet-stream\r\n\r\n";
				$body.= $part[0]["data"];
				$body.= "\r\n";
				
				$body.= "--".$boundary."--\r\n";
				
				$message = $headerx."\r\n".$body;
				$is_file = false;
			}else if ($num_parts==0){
				//simple message, just store as string
				$headerx.="MIME-Version: 1.0 \r\n";
				$headerx.=$part[0]["type"];
				if (!empty($part[0]["encoding"])) $headerx.=$part[0]["encoding"];
				$body=$part[0]["data"];
				
				$message = $headerx."\r\n".$body;
				$is_file = false;
			}else{
				//for multipart message, we'll assemble it and dump it into a file
				
				echo "Uploads directory: $uploadDir <br>\n";
				if (file_exists(realpath($uploadDir))){
					$tempID = ereg_replace("[/]","",$loginID).time();
					$boundary="RWP_PART_".$tempID;
					

					$temp_file = $uploadDir."/".$tempID;
					echo "Temp file: $temp_file <br>\n";
					$temp_fp = fopen($temp_file, "w");
					if ($temp_fp){
						//setup header
						$headerx.="MIME-Version: 1.0 \r\n";
						$headerx.="Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n"; 

						//write header to temp file
						fputs($temp_fp, $headerx."\r\n");
					
						//write main body
						fputs($temp_fp, "This message is in MIME format.\r\n");
			
						//loop through attachments
						for ($i=0;$i<=$num_parts;$i++){
							//write boundary
							fputs($temp_fp, "\r\n--".$boundary."\r\n");
							
							//form part header
							$part_header = "";
							if ($part[$i]["type"]!="") $part_header .= $part[$i]["type"];
							if ($part[$i]["encoding"]!="") $part_header .= $part[$i]["encoding"];
							if ($part[$i]["disposition"]!="") $part_header .= $part[$i]["disposition"];
							
							//write part header
							fputs($temp_fp, $part_header."\r\n");
								
							//open uploaded attachment
							$ul_fp = false;
							if ((!empty($part[$i]["path"])) && (file_exists(realpath($part[$i]["path"])))){
								$ul_fp = fopen($part[$i]["path"], "rb");
							}
							if ($ul_fp){
								//transfer data in uploaded file to MIME message
								
								if ($part[$i]["encoded"]){
									//straight transfer if already encoded
									while(!feof($ul_fp)){
										$line = chop(fgets($ul_fp, 1024));
										fputs($temp_fp, $line."\r\n");
									}
								}else{
									//otherwisee, base64 encode
									while(!feof($ul_fp)){
										//read 57 bytes at a time
										$buffer = fread($ul_fp, 57);
										//base 64 encode and write (line len becomes 76 bytes)
										fputs($temp_fp, base64_encode($buffer)."\r\n");
									}
								}
								fclose($ul_fp);
								unlink($part[$i]["path"]);
							}else if (!empty($part[$i]["data"])){
								//write message (part is not an attachment)
								$message_lines = explode("\n", $part[$i]["data"]);
								while(list($line_num, $line)=each($message_lines)){
									$line = chop($line)."\r\n";
									fputs($temp_fp, $line);
								}
							}
						}
						
						//write closing boundary
						fputs($temp_fp, "\r\n--".$boundary."--\r\n");
						
						//close temp file
						fclose($temp_fp);
						
						$message = $temp_file;
						$is_file = true;
					}else{
						$error .= "Temp file could not be opened: $temp_file <br>\n";
					}
				}else{
					$error .= "Invlalid uploads directory<br>\n";
				}
			}
			
			/*** Clean up uploads directory ***/
			if (file_exists(realpath($uploadDir))){
				//open directory
				if ($handle = opendir($uploadDir)) {
					//loop through files
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
							//split up file name
							$file_parts = explode(".", $file);
				
							if ((count($file_parts)==4) && (strpos($file_parts[0], "fwd-")!==false)){
								$path = $uploadDir."/".$file;
								unlink($path);
							}
						} 
					}
					closedir($handle); 
				}
			}	
			


			/**** Send message *****/
			if (!empty($error)){
				echo $error;
				echo "</body></html>";
				exit;
			}
			
					
			echo "Sending...<br>";

			$sent = false;
			if (!empty($SMTP_SERVER)){
			//send thru SMTP server using cusotm SMTP library
				include("../include/smtp.inc");
				
				//connect to SMTP server
				$smtp_conn = smtp_connect($SMTP_SERVER, "25", $loginID, $password);
				
				if ($smtp_conn){
					//generate list of recipients
					$recipients = $to.", ".$cc.", ".$bcc;
					$recipient_list = smtp_expand($recipients);
					echo "Sending to: ".htmlspecialchars(implode(",", $recipient_list))."<br>\n";
				
					//send message
					$sent = smtp_mail($smtp_conn, $from_addr, $recipient_list, $message, $is_file);
				}else{
					echo "SMTP connection failed: $smtp_error \n";
				}
			}else{
			//send using PHP's mail() function
				include_once("../include/smtp.inc");
				$to = implode(",", smtp_expand($to));
				$to = ereg_replace("[<>]", "", $to);
				echo "Adjusted to: ".htmlspecialchars($to)."<br>";
				
				
				if ($is_file){
					//open file
					$fp = fopen($message, "r");
					
					//if file opened...
					if ($fp){
						//read header
						$header = "";
						do{
							$line = chop(iil_ReadLine($fp, 1024));

							if ((!empty($line))
								and (!iil_StartsWith($line, "Subject:"))
								and (!iil_StartsWith($line, "To:"))
								)
							{
								$header .= $line."\n";
							}							
						}while((!feof($fp)) && (!empty($line)));
						
						echo nl2br($header);
						
						//read body
						$body = "";
						while(!feof($fp)){
							$body .= chop(fgets($fp, 8192))."\n";
						}
						fclose($fp);
						
						echo "<br>From: $from_addr <br>\n";
						
						//send
						if (ini_get("safe_mode")=="1")
							$sent = mail($to,$subject,$body,$header);
						else
							$sent = mail($to, $subject, $body, $header, "-f $from_addr");
					}else{
						$error .= "Couldn't open temp file $message for reading<br>\n";
					}
				}else{
					//take out unnecessary header fields
					$header_a = explode("\n", $headerx);
					$header_a[2] = "X-IlohaMail-Blah: ".$sender_addr;
					$header_a[3] = "X-IlohaMail-Method: mail() [mem]";
					$header_a[4] = "X-IlohaMail-Dummy: moo";

					reset($header_a);
					while ( list($k,$line) = each($header_a) ) $header_a[$k] = chop($line);

					$headerx = implode("\r\n", $header_a);
					$body = str_replace("\r", "", $body);
					$body = str_replace("\n", "\r\n", $body);
					
					echo "<br>From: $from_addr <br>\n";

					//send
					if (ini_get("safe_mode")=="1")
						$sent = mail($to,$subject,$body,$headerx);
					else
						$sent = mail($to, $subject, $body, $headerx, "-f $from_addr");
				}
			}
			
			//send!!
			if ($sent){
				echo "Sent!<br>"; flush();
				$error = "";
				
				//save in send folder
				flush();
				if ($my_prefs["save_sent"]==1){
					echo "Moving to send folder...";
					if ($is_file) $saved = iil_C_AppendFromFile($conn, $my_prefs["sent_box_name"], $message);
					else $saved = iil_C_Append($conn, $my_prefs["sent_box_name"], $message);
					if (!$saved) $error .= "Couldn't save:".$conn->error."<br>\n";
					else echo "done.<br>\n";
				}
				
				//delete temp file, if necessary
				if ($is_file) unlink($message);
				
				//if replying, flag original message
				if (isset($in_reply_to)) $reply_id = $in_reply_to;
				else if (isset($forward_of)) $reply_id = $forward_of;
				if (($ICL_CAPABILITY["flags"]) && (isset($reply_id))){
					$pos = strrpos($reply_id, ":");
					$reply_uid = substr($reply_id, $pos+1);
					$reply_folder = substr($reply_id, 0, $pos);
					$reply_num = iil_C_UID2ID($conn, $reply_folder, $reply_uid);
					
					if ($reply_num !== false){
						if (iil_C_Flag($conn, $reply_folder, $reply_num, "ANSWERED") < 1){
							echo "Flagging failed:".$conn->error." ()<br>\n";
						}
					}else{
						echo "UID -> ID conversion failed.<br>\n";
					}
				}
				
				//update spam-prevention related records
				include("../include/as_update.inc");

				if ((empty($error))&&($my_prefs["closeAfterSend"]==1)){
					//clean up uploads dir
					$uploadDir = $UPLOAD_DIR.ereg_replace("[\\/]", "", $loginID.".".$host);

					if (file_exists(realpath($uploadDir))){
						if ($handle = opendir($uploadDir)) {
							while (false !== ($file = readdir($handle))) { 
								if ($file != "." && $file != "..") { 
									$file_path = $uploadDir."/".$file;
									unlink($file_path);
								} 
							}
							closedir($handle); 
						}
					}	
					
					//close window
					echo "<p>Message successfully sent.";
					echo "\n<script type=\"text/javascript\">\n";
					echo "   DoCloseWindow(\"main.php?user=$user&folder=".(empty($folder)?"INBOX":urlencode($folder))."\");\n";
					echo "</script>\n";
					echo "<br><br>"; flush();
				}else{
					echo $error;
				}
			}else{
				echo "<p><font color=\"red\">Send FAILED</font><br>$smtp_errornum : ".nl2br($smtp_error);
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
function upload(){}
if (isset($upload)){
	if (($userfile)&&($userfile!="none")){
		$i=$attachments;
		$newfile = $user.".".mod_base64_encode($userfile_name).".".mod_base64_encode($userfile_type).".".mod_base64_encode($userfile_size);
		$newpath=$uploadDir."/".$newfile;
		if (@move_uploaded_file($userfile, $newpath)){
			$attach[$newfile] = 1;
		}else{
			echo $userfile_name." : ".$composeErrors[3];
		}
	}else{
		echo $composeErrors[4];
	}
}


/****
	FETCH LIST OF UPLOADED FILES
****/
function fetchUploads(){}
if (file_exists(realpath($uploadDir))){
	//open directory
	if ($handle = opendir($uploadDir)) {
		//loop through files
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				//split up file name
				$file_parts = explode(".", $file);
				
				//make sure first part is session ID, and add to list
				if ((strcmp($file_parts[0], $user)==0)||(strpos($file_parts[0], "fwd-")!==false))
					$uploaded_files[] = $file;
			} 
		}
		closedir($handle); 
	}
}
if (is_array($fwd_att_list)){
	reset($fwd_att_list);
	while ( list($file, $v) = each($fwd_att_list) ){
		$uploaded_files[] = $file;
	}
}


/****
	REPLYING OR FORWARDING
****/
function replyOrForward(){}
if ((isset($replyto)) || (isset($forward))){
    // if REPLY, or FORWARD
	if ((isset($folder))&&(isset($id))){		
        include_once("../include/mime.inc");
        
		//connect
		$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);

		//get message
		$header=iil_C_FetchHeader($conn, $folder, $id);

		//check IMAP UID, if set
		if (($uid > 0) && ($header->uid!=$uid)){
			$temp_id = iil_C_UID2ID($conn, $folder, $uid);
			if ($temp_id) $header=iil_C_FetchHeader($conn, $folder, $temp_id);
			else{
				"UID - MID mismatch:  UID $uid not found.  Original message no longer exists in $folder <br>\n";
				exit;
			}
		}else{
			//echo "UID matched:  $uid <br>\n";
		}

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
			if (count($to_a)>1){
				$to = "";
				while ( list($k, $v) = each($to_a) ){
               		//remove user's own address from "to" list
              	  	if ((stristr($to_a[$k]["address"], $from_addr) === false) and
 					    (stristr($to_a[$k]["address"], $loginID."@".$host) === false) and
						($my_prefs["email_address"] != $to_a[$k]["address"])){
               	     $to .= (empty($to)?"":", ")."\"".LangDecodeSubject($to_a[$k]["name"], $my_prefs["charset"])."\" <".$to_a[$k]["address"].">";
               	 	}
            	}
				echo $to;
				$to = RemoveDoubleAddresses($to);
			}else if (count($to_a)==1){
				$to = "\"".LangDecodeSubject($to_a[0]["name"], $my_prefs["charset"])."\" <".$to_a[0]["address"].">";
			}
		}
		if (isset($cc)){
			echo "<!-- $cc //-->\n";

			$cc_a = LangParseAddressList($cc);
			$cc = "";
			while ( list($k, $v) = each($cc_a) ){
				echo "<!-- CC: ".$cc_a[$k]["address"]." //-->\n";
                //remove user's own address from "cc" list
                if ((stristr($cc_a[$k]["address"], $from_addr) === false) and
 				    (stristr($cc_a[$k]["address"], $loginID."@".$host) === false) and
					($my_prefs["email_address"] != $cc_a[$k]["address"])){
					echo "<!-- adding: ".$cc_a[$k]["address"]." //-->\n";
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
		
		//get message attachments
		if ($forward){
			$att_list = iml_GetPartList($structure, "");
			while ( list($i,$v) = each($att_list) ){
				if ((strcasecmp($att_list[$i]["disposition"], "inline")==0)
					or (strcasecmp($att_list[$i]["disposition"], "attachment")==0)
					or (!empty($att_list[$i]["name"]))){
					$file = "fwd-".mod_base64_encode($folder)."-$id-".base64_encode($i);
					$file .= ".".mod_base64_encode($att_list[$i]["name"]);
					$file .= ".".mod_base64_encode($att_list[$i]["typestring"]);
					$file .= ".".mod_base64_encode($att_list[$i]["size"]);
					if (!$fwd_att_list[$file]){
						$uploaded_files[] = $file;
						$fwd_att_list[$file] = 1;
						$attach[$file] = 1;
					}
				}
			}
		}

		//get message
        if (!empty($part)) $part.=".1";
        else{
            $part = iml_GetFirstTextPart($structure, "");
        }
        		
		$message=iil_C_FetchPartBody($conn, $folder, $id, $part);
				
		//decode message if necessary
        $encoding=iml_GetPartEncodingCode($structure, $part);        
		if ($encoding==3) $message = base64_decode($message);
		else if ($encoding==4){
            //if ($encoding == 3 ) $message = base64_decode($message);
            //else if ($encoding == 4) $message = quoted_printable_decode($message);
			//$message = quoted_printable_decode($message);
            $message = str_replace("=\n", "", $message);
            $message = quoted_printable_decode(str_replace("=\r\n", "", $message));
        }
		
		//check for HTML
		$type_str = iml_GetPartTypeString($structure, $part);
		if (stristr($type_str,'html')!==false){
			$message = strip_tags($message);
		}

        //add quote marks
		$message = str_replace("\r", "", $message);
		$charset=iml_GetPartCharset($structure, $part);


		$message=LangConvert($message, $my_prefs["charset"], $charset);
		if (isset($replyto)) $message=">".str_replace("\n","\n>",$message);
		$message = "\n".LangConvert($message_head, $my_prefs["charset"], $charset).$message;
		
		iil_Close($conn);			
	}
}else{
	$message = stripslashes($message);
}

function showForm(){}

if (($show_contacts) || ($my_prefs["showContacts"])) {
?>
<?php
}
?>

<FORM NAME="messageform" ENCTYPE="multipart/form-data" ACTION="compose2.php" METHOD="POST" onSubmit='DeselectAdresses(); close_popup(); return true;'>
	<input type="hidden" name="user" value="<?php echo $user?>">
	<input type="hidden" name="show_contacts" value="<?php echo $show_contacts?>">
	<input type="hidden" name="show_cc" value="<?php echo $show_cc?>">
	<?php
        if ($no_subject) echo '<input type="hidden" name="confirm_no_subject" value="1">';
    
		if (($replyto) || ($in_reply_to)){
			if (empty($in_reply_to)) $in_reply_to = $folder.":".$uid;
			echo "<input type=\"hidden\" name=\"in_reply_to\" value=\"$in_reply_to\">\n";
			echo "<input type=\"hidden\" name=\"replyto_messageID\" value=\"$replyto_messageID\">\n";
		}else if (($forward) || ($forward_of)){
			if (empty($forward_of)) $forward_of = $folder.":".$uid;
			echo "<input type=\"hidden\" name=\"forward_of\" value=\"$forward_of\">\n";
		}
		
		if (is_array($fwd_att_list)){
			reset($fwd_att_list);
			while ( list($file,$v) = each($fwd_att_list)){
				echo "<input type=\"hidden\" name=\"fwd_att_list[".$file."]\" value=\"1\">\n";
			}
		}
		if (!empty($folder)){
			echo '<input type="hidden" name="folder" value="'.$folder.'">';
		}
	?>
	
	<table border="0" width="100%" bgcolor="<?php echo $my_colors["main_head_bg"]?>">
	<tr>
		<td valign="bottom" align="left">
			<span class="bigTitle"><?php echo $composeStrings[0]; ?></span>
			&nbsp;&nbsp;&nbsp;
			<span class="mainHeadingSmall">
			<?php
			if (!$my_prefs["compose_inside"]){
				$jsclose="[<a href=\"javascript:window.close();\" class=\"mainHeadingSmall\">".$composeStrings[11]."</a>]";
				echo "<SCRIPT type=\"text/javascript\" language=\"JavaScript1.2\">\n";
				echo "document.write('$jsclose');\n</SCRIPT>";
			}
			?>
			</span>
		</td>
		<td valign="bottom" align="right">
						
		</td>
	</tr>
	</table>
    <?php
    if (!empty($error)) echo '<br><font color="red">'.$error.'</font>';
		$to = encodeUTFSafeHTML($to);
		$cc = encodeUTFSafeHTML($cc);
		$bcc = encodeUTFSafeHTML($bcc);
		
		// format sender's email address (i.e. "from" string)
        	$email_address = htmlspecialchars($original_from);

		// External table
		echo "<table border=0><tr valign=\"top\"><td>\n";
		
		echo "<table border=0>";
		//echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"".$my_colors["main_bg"]."\">\n";
		echo "<tr>";
		echo "<td align=right class=\"mainLight\">".$composeHStrings[0].":</td><td class=\"mainLight\">";
		echo '<input type=text name="subject" value="'.encodeUTFSafeHTML(stripslashes($subject)).'" size="60" onKeyUp="fixtitle(\''.$composeStrings["title"].'\');"></td>';
		echo "</td></tr>\n";
		
		//show from (identities)
		echo "<tr>\n";
		echo "<td align=right class=\"mainLight\">".$composeHStrings[1].":</td>\n<td class=\"mainLight\">\n";
		if (($alt_identities) && (count($alt_identities)>1)){
			echo "<!-- ident count: ".count($alt_identities)." //-->\n";
			echo "<select name=\"sender_identity_id\">\n";
				echo "<option value=\"-1\">".LangDecodeSubject($email_address, $my_prefs["charset"])."\n";
			while ( list($key,$ident_a) = each($alt_identities) ){
				if ($ident_a["name"]!=$my_prefs["user_name"] || $ident_a["email"]!=$my_prefs["email_address"]){
					echo "<option value=\"$key\" ".($key==$sender_identity_id?"SELECTED":"").">";
					echo "\"".$ident_a["name"]."\"&nbsp;&nbsp;&lt;".$ident_a["email"]."&gt;\n";
				}
			}
			echo "</select>\n";
		}else{
			echo LangDecodeSubject($email_address, $my_prefs["charset"]);
		}
		echo "</td></tr>\n";
  
		//if (($show_contacts) || ($my_prefs["showContacts"])){
		if ($show_contacts){
			echo "<tr>\n<td align=right valign=top>";
			echo "<select name=\"to_a_field\">\n";
			echo "<option value=\"to\">".$composeHStrings[2].":\n";
			echo "<option value=\"cc\">".$composeHStrings[3].":\n";
			echo "<option value=\"bcc\">".$composeHStrings[4].":\n";
			echo "</select>\n";
			echo"</td><td>";
		
			// display "select" box with contacts
			include_once("../include/data_manager.inc");
			$source_name = $DB_CONTACTS_TABLE;
			if (empty($source_name)) $source_name = "contacts";
			$dm = new DataManager_obj;
			if ($dm->initialize($loginID, $host, $source_name, $DB_TYPE)){
				if (empty($sort_field)) $sort_field = "grp,name";
				if (empty($sort_order)) $sort_order = "ASC";
				$contacts = $dm->sort($sort_field, $sort_order);
			}else{
				echo "Data Manager initialization failed:<br>\n";
				$dm->showError();
			}

			if ((is_array($contacts)) && (count($contacts) > 0)){
				echo "<select name=\"to_a[]\" MULTIPLE SIZE=7 onDblClick='CopyAdresses(); return true;'>\n";
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
				
				echo "<script type=\"text/javascript\" language=\"JavaScript1.2\">";
				echo "document.write('<input type=\"button\" name=\"add_contacts\" value=\"".$composeStrings[8]."\" onClick=\"CopyAdresses()\">');\n";
				echo "</script>\n";
				echo "<noscript><input type=\"submit\" name=\"add_contacts\" value=\"".$composeStrings[8]."\"><br></noscript>\n";
				if ($my_prefs["showContacts"]!=1){
					echo "<input type=\"hidden\" name=\"new_show_contacts\" value=0>\n";
					echo "<input type=\"submit\" name=\"change_contacts\" value=\"".$composeStrings[6]."\">\n";
				}
			}
			echo "</td></tr>\n";
			$contacts_shown = true;
		}else{
			$contacts_shown = false;
		}
		
		// display to field
		echo "<tr>\n<td align=right class=\"mainLight\">".$composeHStrings[2].":</td><td>";
		if (strlen($to) < 60)
            echo "<input type=text name=\"to\" value=\"".stripslashes($to)."\" size=60>";
        else
            echo "<textarea name=\"to\" cols=\"60\" rows=\"3\">".stripslashes($to)."</textarea>";
		if (!$contacts_shown){
			//"show contacts" button
			echo "<input type=\"hidden\" name=\"new_show_contacts\" value=1>\n";
			$popup_url = "contacts_popup.php?user=$user";
			$showcon_link = "<a href=\"javascript:open_popup('$popup_url')\" class=\"mainLight\">";
			$showcon_link.= "<img src=\"themes/".$my_prefs["theme"]."/images/addc.gif\">".$composeStrings[5]."</a>";
			$showcon_link = addslashes($showcon_link);
			echo "<script type=\"text/javascript\" language=\"JavaScript1.2\">\n";
			echo "document.write('$showcon_link');\n";
			echo "</script>\n";
			echo "<noscript>\n<input type=\"submit\" name=\"change_contacts\" value=\"".$composeStrings[5]."\">\n</noscript>\n";
			//echo "<input type=\"submit\" name=\"change_contacts\" value=\"".$composeStrings[5]."\">\n";
		}
		echo "</td></tr>\n";
		
		if ((!empty($cc)) || ($my_prefs["showCC"]==1) || ($show_cc)){
			// display cc box
			echo "<tr>\n<td align=right class=\"mainLight\">".$composeHStrings[3].":</td><td>";
        	if (strlen($cc) < 60)
            	echo "<input type=text name=\"cc\" value=\"".stripslashes($cc)."\" size=60>";
        	else
            	echo "<textarea name=\"cc\" cols=\"60\" rows=\"3\">".stripslashes($cc)."</textarea>";
			echo "</td></tr>\n";
			
			$cc_field_shown = true;
		}else{
			$cc_field_shown = false;
		}
		
		if ((!empty($bcc)) || ($my_prefs["showCC"]==1) || ($show_cc)){
			// display bcc box
			echo "<tr>\n<td align=right class=\"mainLight\">".$composeHStrings[4].":</td><td>";
        	if (strlen($bcc) < 60)
            	echo "<input type=text name=\"bcc\" value=\"".stripslashes($bcc)."\" size=60>";
			else
            	echo "<textarea name=\"bcc\" cols=\"60\" rows=\"3\">".stripslashes($bcc)."</textarea>\n";
			echo "</td></tr>\n";
			$bcc_field_shown = true;
		}else{
			$bcc_field_shown = false;
		}

		//show attachments
		echo "<tr>";
		echo "<td align=\"right\" valign=\"top\" class=\"mainLight\">".$composeStrings[4].":</td>";
		echo "<td valign=\"top\">";
		if ((is_array($uploaded_files)) && (count($uploaded_files)>0)){
			//echo "<table>";
			echo "<table border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"".$my_colors["main_head_bg"]."\">\n";
			reset($uploaded_files);
			while ( list($k,$file) = each($uploaded_files) ){
				$file_parts = explode(".", $file);
				echo "<tr bgcolor=\"".$my_colors["main_bg"]."\">";
				echo "<td valign=\"bottom\"><input type=\"checkbox\" name=\"attach[$file]\" value=\"1\" ".($attach[$file]==1?"CHECKED":"")."></td>";
				echo "<td valign=\"bottom\">".mod_base64_decode($file_parts[1])."&nbsp;</td>";
				echo "<td valign=\"bottom\" class=\"small\">".mod_base64_decode($file_parts[3])."bytes&nbsp;</td>";
				echo "<td valign=\"bottom\" class=\"small\">(".mod_base64_decode($file_parts[2]).")</td>";
				echo "</td></tr>\n";
			}
			echo "</table>";
		}

		if ($MAX_UPLOAD_SIZE) $max_file_size = $MAX_UPLOAD_SIZE;
		else $max_file_size = ini_get('upload_max_filesize');
		if (eregi("M$", $max_file_size)) $max_file_size = (int)$max_file_size * 1000000;
		else if (eregi("K$", $max_file_size)) $max_file_size = (int)$max_file_size * 1000;
		echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'">';
		echo "<INPUT NAME=\"userfile\" TYPE=\"file\">";
		echo '<INPUT TYPE="submit" NAME="upload" VALUE="'.$composeStrings[2].'">';
		echo "</td></tr>\n";
		
		echo "<tr>\n<td></td><td>";
		
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

	// External table
	echo "</td><td>\n";
	echo '<input type=submit name="send_1" value="'.$composeStrings[1].'">';
	echo "</td></tr><td colspan=2>\n";
	
	/***
		SPELL CHECK
	****/
	if ($check_spelling){
		include("../include/spellcheck.inc");

		//run spell check
		$result = splchk_check($message, $spell_dict_lang);
		
		//handle results
		if ($result){
			echo "<table><tr bgcolor=\"".$my_colors["main_bg"]."\"><td>\n";
			$words = $result["words"];
			$positions = $result["pos"];
			if (count($positions)>0){
				//show errors and possible corrections
				echo "<b>".$composeStrings[15]."</b><br>\n";
				echo str_replace("%s", $DICTIONARIES[$spell_dict_lang], $composeErrors[8]);

				$splstr["ignore"] = $composeStrings[17];
				$splstr["delete"] = $composeStrings[18];
				$splstr["correct"] = $composeStrings[13];
				$splstr["nochange"] = $composeStrings[14];
				$splstr["formname"] = "messageform";
			
				splchk_showform($positions, $words, $splstr);
			}else{
				//show "no changes needed"
				echo $composeErrors[6].str_replace("%s", $DICTIONARIES[$spell_dict_lang], $composeErrors[8]);
			}
			echo "</td></tr></table>\n";
			
		}else{
			echo $composeErrors[7];
		}
	}else if ($correct_spelling){
		//correct spelling
		include("../include/spellcheck.inc");

		//do some shifting here...
		while (list($num,$word)=each($words)){
			$correct_var = "correct".$num;
			$correct[$num] = $$correct_var;
		}
		
		echo "<table><tr bgcolor=\"".$my_colors["main_bg"]."\"><td>\n";
		echo "<b>".$composeStrings[16]."</b><br>\n";

		//do the actual corrections
		$message = splchk_correct($message, $words, $offsets, $suggestions, $correct);

		echo "</td></tr></table>\n";
	}
	
	/***
		SHOW TEXT BOX
	***/
	?>
	<br><span class="mainLight"><?php echo $composeStrings[7]?></span><br>
	<TEXTAREA NAME=message ROWS=20 COLS=77 WRAP=virtual><?php echo "\n".encodeUTFSafeHTML($message); ?></TEXTAREA>
	
	<?php
		//spell check controls
		if (is_array($DICTIONARIES) && count($DICTIONARIES)>0){
			echo "<br><select name='spell_dict_lang'>\n";
			reset($DICTIONARIES);
			while ( list($l,$n)=each($DICTIONARIES) ) echo "<option value='$l'>$n\n";
			echo "</select>\n";
			echo '<input type="submit" name="check_spelling" value="'.$composeStrings[12].'">';
		}
	?>

	<!-- External talbe -->
	</td><tr><td>
	
	
	<table border=0 width="100%">
	<?php
	//GPG stuff
	if ($GPG_ENABLE){
	?>
		<tr>
			<span class="mainLight">Whose public key to use? (this feature is still experimental)<br>
			<?php
				$keys = gpg_list_keys();
				$options = "";
				if (is_array($keys) && count($keys)>0){
					while (list($k,$str)=each($keys)){
						$options.= "<option value=\"$k\">$str\n";
					}
				}
			?>
			<select name="keytouse">
			<option value = "noencode">None</option>
			<?php echo $options ?>
			</select>
			</span>
		</tr>
	<?php
	} //end if $GPG_ENABLE
	?>
	<tr>
		<td>
		<input type=checkbox name="attach_sig" value=1 <?php  echo ($my_prefs["show_sig1"]==1?"CHECKED":""); ?> > 
		<span class="mainLight"><?php  echo $composeStrings[3]; ?></span>
		</td>
	</tr>
	</table>

	</td><td>
		<input type=submit name="send_2" value="<?php  echo  $composeStrings[1]; ?>">
	</td></tr></table><!-- End External table -->

</form>
	<script type=text/javascript>
		var _p = this.parent;
		if (_p==this){
			_p.document.title = "<?php echo $composeStrings["title"] ?>";
		}
	</script>
</BODY></HTML>
