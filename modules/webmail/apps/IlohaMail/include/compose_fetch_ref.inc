<?php
/*
	File:		compose_fetch_ref.inc
	Author: 	Ryo Chijiiwa
	License:	GPL (part of IlohaMail)
	Purpose: 	fetch info about message being replied to or forwarded
*/


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
		if (!$draft){
			$lookfor=(isset($replyto)?"Re:":"Fwd:");
			$pos = strpos ($subject, $lookfor);
        	if ($pos===false) {
				$pos = strpos ($subject, strtoupper($lookfor));
        		if ($pos===false) $subject=$lookfor." ".$subject;
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
		if ($replyto_all||$draft){
			if ($replyto_all) $to = $header->from;
			if (!empty($header->replyto)) $to.= ",".$header->replyto;
			if (!empty($header->to)) $to .= (empty($to)?"":", ").$header->to;
			if (!empty($header->cc)) $cc .= (empty($cc)?"":", ").$header->cc;
		}
		$original_recipients = $header->to.','.$header->cc;
		if ($draft) $original_recipients.=$header->from; //help identity auto detector
		
		//mime decode "to," "cc," and "from" fields
		if (!$draft && isset($to)){
			$to_a = LangParseAddressList($to);
			if (count($to_a)>1){
				$to = "";
				while ( list($k, $v) = each($to_a) ){
               		//remove user's own address from "to" list
              	  	if ((stristr($to_a[$k]["address"], $from_addr) === false) and
 					    (stristr($to_a[$k]["address"], $loginID."@".$host) === false) and
						($my_prefs["email_address"] != $to_a[$k]["address"])){
               	     $to .= (empty($to)?"":", ")."\"".LangDecodeSubject($to_a[$k]["name"], $my_prefs["charset"])."\" <".$to_a[$k]["address"].">";
               	 		echo "<!-- to: ".$to_a[$k]["name"]." ".LangDecodeSubject($to_a[$k]["name"], $my_prefs["charset"])." //-->\n";
					}
            	}
				$to = RemoveDoubleAddresses($to);
			}else if (count($to_a)==1){
				$to = "\"".LangDecodeSubject($to_a[0]["name"], $my_prefs["charset"])."\" <".$to_a[0]["address"].">";
			}
		}
		if (!$draft && isset($cc)){
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
		if ($forward||$draft){
			$att_list = iml_GetPartList($structure, "");
			while ( list($i,$v) = each($att_list) ){
				if ((strcasecmp($att_list[$i]["disposition"], "inline")==0)
					or (strcasecmp($att_list[$i]["disposition"], "attachment")==0)
					or (!empty($att_list[$i]["name"]))){
					$file = "fwd-".mod_base64_encode($folder)."-$id-".mod_base64_encode($i);
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
?>