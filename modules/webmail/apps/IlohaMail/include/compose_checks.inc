<?php
/*
	File:		compose_checks.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	package MIME message
*/
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
?>