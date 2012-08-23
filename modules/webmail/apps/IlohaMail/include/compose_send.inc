<?php
/*
	File:		compose_send.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	sends mime message
*/


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
		if (!$fp){
			$error .= "Couldn't open temp file $message for reading<br>\n";
			return;
		}
		
		//read header
		$header = "";
		do{
			$line = chop(iil_ReadLine($fp, 1024),"\r\n");
			if ((!empty($line))
				and (!iil_StartsWith($line, "Subject:"))
				and (!iil_StartsWith($line, "To:"))
				)
			{
				$header .= $line."\r\n";
			}	
		}while((!feof($fp)) && (!empty($line)));
		
		echo nl2br($header);
		
		//read body
		$body = "";
		while(!feof($fp)){
			$body .= chop(fgets($fp, 8192),"\r\n")."\r\n";
		}
		fclose($fp);
		
		echo "<br>From: $from_addr <br>\n";
		echo "safe_mode: ".ini_get("safe_mode")."<br>\n";
		
		//send
		if (ini_get("safe_mode")=="1")
			$sent = mail($to,$subject,$body,$header);
		else
			$sent = mail($to, $subject, $body, $header, "-f $from_addr");
		
	}else{
		//take out unnecessary header fields
		$header_a = explode("\n", $headerx);
		unset($header_a[4]);
		unset($header_a[3]);
		$header_a[2] = 'X-IlohaMail-Method: mail() [mem]';

		reset($header_a);
		while ( list($k,$line) = each($header_a) ) $header_a[$k] = chop($line, "\r\n");

		$headerx =  implode("\r\n", $header_a);
		$body = str_replace("\r", "", $body);
		$body = str_replace("\n", "\r\n", $body);
		
		echo "<br>From: $from_addr <br>\n";
		echo "safe_mode: ".ini_get("safe_mode")."<br>\n";

		//send
		if (ini_get("safe_mode")=="1")
			$sent = mail($to,$subject,$body,$headerx);
		else
			$sent = mail($to, $subject, $body, $headerx, "-f $from_addr");
	}
}
?>