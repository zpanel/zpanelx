<?php
/*
	File:		compose_package.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	package MIME message
*/

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
	if (!file_exists(realpath($uploadDir))){
		$error .= "Invlalid uploads directory<br>\n";
		return;
	}
	
	$tempID = ereg_replace("[/]","",$loginID).time();
	$boundary="RWP_PART_".$tempID;
		
	$temp_file = $uploadDir."/".$tempID;
	echo "Temp file: $temp_file <br>\n";
	
	$temp_fp = fopen($temp_file, "w");
	
	if (!$temp_fp){
		$error .= "Temp file could not be opened: $temp_file <br>\n";
		return;
	}
	
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
					$line = chop(fgets($ul_fp, 5000));
					fputs($temp_fp, $line)."\r\n";
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
			while (list($line_num,$line)=each($message_lines)){
				$line = chop($line);
				fputs($temp_fp, $line."\r\n");
			}
			//fputs($temp_fp, $part[$i]["data"]."\r\n");
		}
	}
	
	//write closing boundary
	fputs($temp_fp, "\r\n--".$boundary."--\r\n");
	
	//close temp file
	fclose($temp_fp);
	
	$message = $temp_file;
	$is_file = true;
}
