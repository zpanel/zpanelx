<?php
/////////////////////////////////////////////////////////
//
//	source/view.php
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
	FILE: view.php
	PURPOSE:
		Display message part data (whether it be text, images, or whatever).  Decode as necessary.
		Sets HTTP "Content-Type" header as appropriate, so that the browser will (hopefully) know
		what to do with the data.
	PRE-CONDITIONS:
		$user - Session ID
		$folder - Folder in which message to open is in
		$id - Message ID (not UID)
		$part - IMAP (or MIME?) part code to view.

********************************************************/

include_once("../include/super2global.inc");
include_once("../include/nocache.inc");
include_once('../include/ryosimap.inc');

if ((isset($user))&&(isset($folder))){
	include_once("../include/session_auth.inc");
	include_once("../include/icl.inc");

	$view_conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
	if ($iil_errornum==-11){
		for ($i=0; (($i<10)&&(!$view_conn)); $i++){
			sleep(1);
			$view_conn=iil_Connect($host, $loginID, $password, $AUTH_MODE);
		}
	}
	if (!$view_conn){
		echo "failed\n".$iil_error;
		flush();
	}else{

		// Let's look for MSIE as it needs special treatment
		if  (strpos (getenv('HTTP_USER_AGENT'), "MSIE"))
			$DISPOSITION_MODE="inline";
		else
			$DISPOSITION_MODE="attachment";

		//get basic info
		include_once("../include/mime.inc");
		$header = iil_C_FetchHeader($view_conn, $folder, $id);
		$structure_str=iil_C_FetchStructureString($view_conn, $folder, $id);
		$structure=iml_GetRawStructureArray($structure_str);

		//if part id not specified but content-id is, 
		//find corresponding part id
		if (!isset($part) && $cid){
			if (!ereg("^<", $cid)) $cid = "<".$cid;
			if (!ereg(">$", $cid)) $cid.= ">";
			
			//fetch parts list
			$parts_list = iml_GetPartList($structure, "");
			
			//search for cid
			if (is_array($parts_list)){
				reset($parts_list);
				while(list($part_id,$part_a)=each($parts_list)){
					if ($part_a["id"]==$cid){
						$part = $part_id;
					}
				}
			}
			
			//we couldn't find part with cid, die
			if (!isset($part)) exit;
		}
		
		if (isset($source)){
			//show source
			header("Content-type: text/plain");
			iil_C_PrintSource(&$view_conn, $folder, $id, $part);
		}else if ($show_header){
			//show header
			header("Content-Type: text/plain");
			$header = iil_C_FetchPartHeader($view_conn, $folder, $id, $part);
			//$header = str_replace("\r", "", $header);
			//$header = str_replace("\n", "\r\n", $header);
			echo $header;
		}else if ($printer_friendly){
			//show printer friendly version
			include_once("../include/ryosimap.inc");
			include("../lang/".$my_prefs["charset"].".inc");

			//get message info
			$conn = $view_conn;
			
			$num_parts=iml_GetNumParts($structure, $part);
			$parent_type=iml_GetPartTypeCode($structure, $part);
			$uid = $header->uid;

			//get basic header fields
			$subject = encodeHTML(LangDecodeSubject($header->subject, $my_prefs["charset"]));
			$from = LangShowAddresses($header->from,  $my_prefs["charset"], $user);
			$to = LangShowAddresses($header->to,  $my_prefs["charset"], $user);
			if (!empty($header->cc)) $cc = LangShowAddresses($header->cc,  $my_prefs["charset"], $user);
			else $cc = "";

			header("Content-type: text/html");

			//output
			?>
			<html>
			<head><title><?php echo $subject ?></title></head>
			<body>
			<?php
			echo "<b>Subject:&nbsp;</b>$subject<br>\n";
			echo "<b>Date:&nbsp;</b>".htmlspecialchars($header->date)."<br>\n";
			echo "<b>From:&nbsp;</b>".$from."<br>\n";
			echo "<b>To:&nbsp;</b>".$to."<br>\n";
			if (!empty($cc)) echo "<b>CC:&nbsp;</b>".$cc."<br>\n";
//20094
			include("../include/read_message_handler.inc");
			?>
			</body>
			</html>
			<?php

		}else if(isset($tneffid)){
			//show ms-tnef

			include("../include/tnef_decoder.inc");			
			$type=iml_GetPartTypeCode($structure, $part);
			$typestring=iml_GetPartTypeString($structure, $part);
			list($type, $subtype) = explode("/", $typestring);
			$body=iil_C_FetchPartBody($view_conn, $folder, $id, $part);
			$encoding=iml_GetPartEncodingCode($structure, $part);
			if ($encoding == 3 ) $body=base64_decode($body);
			else if ($encoding == 4) $body=quoted_printable_decode($body);
			$charset=iml_GetPartCharset($structure, $part);
			if (strcasecmp($charset, "utf-8")==0){
				include_once("../include/utf8.inc");
				$is_unicode = true;
				$body = utf8ToUnicodeEntities($body);
			}else{
				$is_unicode = false;
			}
			//$body=LangConvert($body, $my_charset, $charset);
			$tnef_files=tnef_decode($body);
			header("Content-type: ".$tnef_files[$tneffid]['type0']."/".$tnef_files[$tneffid]['type1']."; name=\"".$tnef_files[$tneffid]['name']."\"");
			header("Content-Disposition: ".$DISPOSITION_MODE."; filename=\"".$tnef_files[$tneffid]['name']."\"");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Pragma: public");
			echo($tnef_files[$tneffid]['stream']);
		}else{			
			$header_obj = $header;
			$type=iml_GetPartTypeCode($structure, $part);
			if ($is_html) $typestr = "text/html";
			else if (empty($part) || $part==0) $typestr = $header_obj->ctype;
			else $typestr = iml_GetPartTypeString($structure, $part);
			list($majortype, $subtype) = explode("/", $typestr);

			// structure string
			if ($show_struct){
				echo $structure_str;
					exit;
			}

			// format and send HTTP header
			if ($type==$MIME_APPLICATION){
				$name = str_replace("/",".",iml_GetPartName($structure, $part));
				header("Content-type: $typestr; name=\"".$name."\"");
				header("Content-Disposition: ".$DISPOSITION_MODE."; filename=\"".$name."\"");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: public");
			}else if ($type==$MIME_MESSAGE){
				$name=str_replace("/",".", iml_GetPartName($structure, $part));
				header("Content-Type: text/plain; name=\"".$name."\"");
			}else if ($type != $MIME_INVALID){
				$charset=iml_GetPartCharset($structure, $part);
				$name=str_replace("/",".", iml_GetPartName($structure, $part));
				$header="Content-type: $typestr";
				if (!empty($charset)) $header.="; charset=\"".$charset."\"";
				if (!empty($name)) $header.="; name=\"".$name."\"";
				header($header);
				if ($type!=$MIME_TEXT && $type!=$MIME_IMAGE){
					header("Content-Disposition: ".$DISPOSITION_MODE."; filename=\"".$name."\"");
					header("Expires: 0");
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					header("Pragma: public");
				}else if (!empty($name)){
					header("Content-Disposition: inline; filename=\"".$name."\"");
				}
			}else{
				if ($debug) echo "Invalid type code!\n";
			}
			if ($debug) echo "Type code = $type ;\n";
			
			//check if text/html
			if ($type==$MIME_TEXT && strcasecmp($subtype, "html")==0){
				$is_html = true;
				$img_url = "view.php?user=$user&folder=$folder&id=$id&cid=";
				//echo "IS HTML<br>\n";
			}else{
				$is_html = false;
				//echo "IS NOT HTML $type $subtype <br>\n";
			}

			// send actual output
			if ($print){
				// straight output, no processing
				iil_C_PrintPartBody($view_conn, $folder, $id, $part);
				if ($debug) echo $view_conn->error;
			}else{
				// process as necessary, based on encoding
				$encoding=iml_GetPartEncodingCode($structure, $part);
				if ($debug) echo "Part code = $encoding;\n";

				if ($raw){
					iil_C_PrintPartBody($view_conn, $folder, $id, $part);
				}else if ($encoding==3){
					// base 64
					if ($debug) echo "Calling iil_C_PrintBase64Body\n"; flush();
					if ($is_html){
						$body = iil_C_FetchPartBody($view_conn, $folder, $id, $part);
						$body = ereg_replace("[^a-zA-Z0-9\/\+]", "", $body);
						$body = base64_decode($body);
						$body = eregi_replace("src=\"cid:", "src=\"".$img_url, $body);
						sanitizeHTML($body);
						echo $body;
					}else{
						iil_C_PrintBase64Body($view_conn, $folder, $id, $part);
					}
				}else if ($encoding == 4){
					// quoted printable
					$body = iil_C_FetchPartBody($view_conn, $folder, $id, $part);
					if ($debug) echo "Read ".strlen($body)." bytes\n";
					$body=quoted_printable_decode(str_replace("=\r\n","",$body));
					$charset=iml_GetPartCharset($structure, $part);
					if (strcasecmp($charset, "utf-8")==0){
						include_once("../include/utf8.inc");
						$body = utf8ToUnicodeEntities($body);
					}
					if ($is_html){
						sanitizeHTML($body);
						$body = eregi_replace("src=\"cid:", "src=\"".$img_url, $body);
					}
					echo $body;
				}else{
					// otherwise, just dump it out
					if ($is_html){
						$body = iil_C_FetchPartBody($view_conn, $folder, $id, $part);
						sanitizeHTML($body);
						$body = eregi_replace("src=\"cid:", "src=\"".$img_url, $body);
						echo $body;
					}else{
						iil_C_PrintPartBody($view_conn, $folder, $id, $part);
					}
				}
				if ($debug) echo $view_conn->error;
			}
		}
		iil_Close($view_conn);
	}
}
?>
