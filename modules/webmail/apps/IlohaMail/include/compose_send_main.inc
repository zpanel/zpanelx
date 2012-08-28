<?php
/*
File:		compose_send_main.inc
Author: 	Ryo Chijiiwa
License:	GPL (part of IlohaMail)
Purpose: 	does everything needed to send
*/

$conn = iil_Connect($host, $loginID, $password, $AUTH_MODE);
if (!$conn){
	$error = "Failed: ".$iil_error;
	return;
}

$error = "";
	
/***** Perform Input Check ****/
include("../include/compose_checks.inc");
	
if ($error){
	iil_Close($conn);
	return;
}
	

$num_parts=0;
/****  Prepare header elements ***/
include("../include/compose_header.inc");

	
/****  Prepare attachments *****/
echo "Attachments: $attachments <br>\n";
if (file_exists(realpath($uploadDir)) && is_array($attach)){
	include("../include/compose_attachments.inc");
}


/**** Put together MIME message *****/
echo "Num parts: $num_parts <br>\n";
include("../include/compose_package.inc");			


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
	//echo '<span class="error">'.$error.'</span>';
	//echo "</body></html>";
	//exit;
	return;
}
if (!$save){
	echo "Sending...<br>";
	echo "from_addr: ".htmlspecialchars($from_addr)."<br>\n";
	include("../include/compose_send.inc");
	if ($sent) echo "Sent!<br>"; flush();
}else{
	$sent = true;
}

//bail if send failed
if (!$sent){
	echo '<p><span class="error">Send FAILED</span><br>'.$smtp_errornum.' : '.nl2br($smtp_error);
	exit;
}

$error = "";
	
//save in send folder
flush();
if ($my_prefs["save_sent"]==1||$save){
	if ($save) $save_to = $my_prefs['draft_box_name'];
	else $save_to = $my_prefs['sent_box_name'];
	echo "Saving to: ".$save_to;
	if ($is_file) $saved = iil_C_AppendFromFile($conn, $save_to, $message);
	else $saved = iil_C_Append($conn, $save_to, $message);
	if (!$saved) $error .= "Couldn't save:".$conn->error."<br>\n";
	else echo "done.<br>\n";
}

//delete if file
if ($is_file) unlink($message);
												
//if replying, flag original message
if (isset($in_reply_to)) $reply_id = $in_reply_to;
else if (isset($forward_of)) $reply_id = $forward_of;
echo 'reply_id='.$reply_id.';';
if (($ICL_CAPABILITY["flags"]) && (isset($reply_id))){
	$pos = strrpos($reply_id, ":");
	$reply_uid = substr($reply_id, $pos+1);
	$reply_folder = substr($reply_id, 0, $pos);
	$reply_num = iil_C_UID2ID($conn, $reply_folder, $reply_uid);
	
	if ($reply_num !== false){
		if ($is_draft){
			echo 'deleteing '.$reply_num.' from '.$folder.'<br>'."\n";
			if (iil_C_Delete($conn, $folder, $reply_num)){
				iil_C_Expunge($conn, $folder);
				echo 'Deleted original from drafts<br>';
			}else echo 'Delete failed<br>';			
		}else if (iil_C_Flag($conn, $reply_folder, $reply_num, "ANSWERED") < 1){
			echo "Flagging failed:".$conn->error." ()<br>\n";
		}
	}else{
		echo "UID -> ID conversion failed.<br>\n";
	}
}
	
//update spam-prevention related records
include("../include/as_update.inc");

if ((empty($error))&&($my_prefs["closeAfterSend"]==1)){
	
	//close window
	echo "<p>Message successfully sent.";
	echo "\n<script type=\"text/javascript\">\n";
	echo "   DoCloseWindow(\"main.php?user=$user\");\n";
	echo "</script>\n";
	echo "<br><br>"; flush();
}else{
	echo $error;
}

iil_Close($conn); 
exit;


?>