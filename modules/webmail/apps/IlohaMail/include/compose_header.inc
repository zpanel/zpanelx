<?php
/*
	File:		compose_header.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	compose MIME header
*/

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
	else $message.= "\r\n\r\n".$my_prefs["signature1"];
}	

/****  Attach Tag-line ***/

if ($userLevel < $TAG_LEVEL){
	$message .= "\r\n\r\n".$TAG_LINE;
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
if ($to==$from) $to_self = true;
else $to_self = false;

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

if (!$save&&$bcc_self&&!$to_self) $bcc.=($bcc?', ':'').$from;
if (!empty($bcc)){
	$bcc = stripslashes($bcc);
	$bcc = str_replace(";",",",$bcc);
	$bcc = RemoveDoubleAddresses($bcc);
	$bcc = LangEncodeAddressList($bcc, $my_charset);
}
/***********************/

/****  Add Recipients *********/
$headerx.="From: ".$from."\r\n";
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
?>