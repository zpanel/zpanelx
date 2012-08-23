<?php
/////////////////////////////////////////////////////////
//	
//	POP3.inc
//
//	(C)Copyright 2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
//
/////////////////////////////////////////////////////////

/********************************************************

	FILE: include/pop3.inc
	PURPOSE:
		POP3 equivalence of imap.inc
	USEAGE:
		Function containing "_C_" in name require connection handler to be
		passed as one of the parameters.  To obtain connection handler, use
		iil_Connect()

********************************************************/

include_once("../include/icl_commons.inc");

$iil_error;
$iil_errornum;
$iil_selected;

class iilConnection{
	var $fp;
	var $login;
	var $password;
	var $host;
	var $error;
	var $errorNum;
	var $selected;
	var $cacheFP;
	var $cacheMode;
}

class iilBasicHeader{
	var $id;
	var $uid;
	var $subject;
	var $from;
	var $to;
	var $cc;
	var $replyto;
	var $date;
	var $messageID;
	var $size;
	var $encoding;
	var	$ctype;
	var $flags;
	var $timestamp;
	var $seen;
	var $deleted;
	var $recent;
	var $answered;
}

function iil_xor($string, $string2){
    $result = "";
    $size = strlen($string);
    for ($i=0; $i<$size; $i++) $result .= chr(ord($string[$i]) ^ ord($string2[$i]));
        
    return $result;
}

function iil_ConnectionOpen($fp){
	$status = socket_get_status($fp);
	if (($status['timed_out']) || ($status['blocked']) || ($status['eof'])) return false;
	else return true;
}

function iil_ReadLine($fp, $size){
	$line="";
	if (($fp)&&(!feof($fp))){
		do{
			$buffer = fgets($fp, 1024);
			$endID = strlen($buffer) - 1;
			$end = (($buffer[$endID] == "\n")||(feof($fp)));
			$line.=$buffer;
		}while(!$end);
	}
	//if (!empty($line)) $line.="\n";
	return $line;
}

function iil_C_ReadLine(&$conn){
	if ($conn->cacheMode == "x"){
		socket_set_timeout($conn->fp, 10);
		return iil_ReadLine($conn->fp, 300);
	}else if ($conn->cacheMode == "r"){
		$line = iil_ReadLine($conn->cacheFP, 300);
		//echo $line."<br>\n"; flush();
		return $line;
	}else if ($conn->cacheMode == "w"){
		socket_set_timeout($conn->fp, 10);
		$line = iil_ReadLine($conn->fp, 300);
		fputs($conn->cacheFP, $line);
		return $line;
	}
}

function iil_ReadReply($fp){
	do{
		$line = chop(trim(iil_ReadLine($fp, 1024)));
	}while($line[0]=="*");
	
	return $line;
}

function iil_ParseResult($string){
	if ((($string[0]=="+") || ($string[0]=="-")) && ($string[1]!=" ")){
		$string = $string[0]." ".substr($string, 1);
	}
	$a=explode(" ", $string);
	if (count($a) > 2){
		if (strcasecmp($a[1], "OK")==0) return 0;
		else if (strcasecmp($a[1], "NO")) return -1;
		else if (strcasecmp($a[1], "BAD")) return -2;
	}else return -3;
}

// check if $string starts with $match
function iil_StartsWith($string, $match){
	if ((empty($string)) || (empty($match))) return false;
	
	if ($string[0]==$match[0]){
		$pos=strpos($string, $match);
		if ( $pos === false) return false;
		else if ( $pos == 0) return true;
		else return false;
	}else{
		return false;
	}
}

function iil_Close(&$conn){
	if (@fputs($conn->fp, "QUIT\r\n")){
		fgets($conn->fp, 1024);
		fclose($conn->fp);
	}
}

function iil_C_CramMD5(&$conn, $user, $pass){
	$conn->message .= "Doing CRAM-MD5\n";
    fputs($conn->fp, "AUTH CRAM-MD5\r\n");
	$line = chop(iil_ReadLine($conn->fp, 1024));
	//echo "<!-- got reply: $line //-->\n";
	if ($line[0]=="+"){
		$encChallenge = substr($line, 2);
		//echo "<!-- challenge: \"$encChallenge\" //-->\n";
	}else{
		//didn't get valid challenge
		$conn->message .= 'Didn\'t get challenge, got: "'.htmlspecialchars($line)."\"\n";
		return false;
	}
	
    // initialize ipad, opad
    for ($i=0;$i<64;$i++){
        $ipad.=chr(0x36);
        $opad.=chr(0x5C);
    }
    // pad $pass so it's 64 bytes
    $padLen = 64 - strlen($pass);
    for ($i=0;$i<$padLen;$i++) $pass .= chr(0);
    // generate hash
    $hash = md5(iil_xor($pass,$opad).pack("H*",md5(iil_xor($pass, $ipad).base64_decode($encChallenge))));
    // generate reply
    $reply = base64_encode($user." ".$hash);
    
    // send result, get reply
    fputs($conn->fp, $reply."\r\n");
    $line = iil_ReadLine($conn->fp, 1024);
    
    // process result
    if (iil_ParseResult($line)==0){
        $conn->error = "";
        $conn->errorNum = 0;
		$conn->message .= "CRAM-MD5 returning true\n";
        return true;
    }else{
        $conn->error = "Authentication failed (AUTH): <br>\"".htmlspecialchars($line)."\"";
        $conn->errorNum = -2;
		$conn->message .= "CRAM-MD5 failed: ".htmlspecialchars($line)."\n";
		//echo "<!-- ".$conn->error." //-->\n";
        return false;
    }
}

function iil_C_APOP($conn, $user, $password, $timestamp){
	
	$digest = md5($timestamp.$password);
	
	fputs($conn->fp, "APOP $user $digest\r\n");
	$line = iil_ReadLine($conn->fp, 1024);
	
	if ($line[0]=="+") return true;
	else{
		return false;
	}
}

function iil_Connect($host, $user, $password){	
    global $iil_error, $iil_errornum;
	
	$result = false;

	//strip slashes
	$user = stripslashes($user);
	$password = stripslashes($password);
	
	//set auth method
	$auth_method = "plain";
	if (func_num_args() >= 4){
		$auth_array = func_get_arg(3);
		if (is_array($auth_array)) $auth_method = $auth_array["pop3"];
		if (empty($auth_method)) $auth_method = "plain";
	}
	
	//initialize connection object
	$conn = new iilConnection;
	$conn->error="";
	$conn->errorNum=0;
	$conn->login = $user;
	$conn->password = $password;
	$conn->host = $host;
	$conn->selected="";
	
	//initiate connection
	$conn->fp=@fsockopen($host, 110);
	if ($conn->fp){
		$iil_error.="Socket connection established\r\n";
		$line=iil_ReadLine($conn->fp, 300);
		
		//check for time stamp
		//it's the last "word" and has the form:
		//    <somestring>
		$sig_a = explode(" ", chop($line));
		$last = count($sig_a)-1;
		$timestamp = $sig_a[$last];
		//echo "<!-- last word: \"$timestamp\" //-->\n";
		$last_letter = strlen($timestamp)-1;
		if (($timestamp[0]=="<") && ($timestamp[$last_letter]==">")){
			$try_apop = true;
		}else{
			$try_apop = false;
			$timestamp = "";
		}
		
		if ($auth_method != "plain"){
			fputs($conn->fp, "AUTH\r\n");
			$line = iil_ReadLine($conn->fp, 300);
			if ($line[0]=="+"){
				//auth command succeeded
				//get list of auth methods
				do{
					$line = iil_ReadLine($conn->fp, 300);
					$new_auth = strtolower(chop($line));
					$auth_methods[$new_auth] = 1;
				}while($line[0]!=".");
				
				//echo "<!-- got auth methods //-->\n";
				
				if ($auth_method == "check") $auth_method = "auth";
			}else{
				//auth command failed
				//revert to plain text
				$auth_method = "plain";
			}
		}
		
		//try AUTH CRAM-MD5
		if (($auth_method=="auth") && ($auth_methods["cram-md5"])){
			//echo "<!-- doing CRAM-MD5 //-->\n";
			$result = iil_C_CramMD5($conn, $user, $password);
			$conn->message .= "CRAM_MD5: $result\n";
		}
		
		//try APOP?
		if ((!$result) && ($try_apop) && ($timestamp) && $auth_method!="plain"){
			$result = iil_C_APOP($conn, $user, $password, $timestamp);
			$conn->message .= "\nAPOP: $try_apop $result\n";
		}

		if (!$result){
			//do plain auth
        	fputs($conn->fp, "USER $user\r\n");
        	$line = trim(chop(iil_ReadLine($conn->fp, 1024)));
			if (iil_StartsWith($line, "+OK")){
				fputs($conn->fp, "PASS $password\r\n");
				$line = trim(chop(iil_ReadLine($conn->fp, 1024)));
				if (iil_StartsWith($line, "+OK"))
					$result = true;
				else{
					$iil_errornum = -11;
					$iil_error = htmlspecialchars($line);
					iil_Close($conn);
					return false;
				}
			}else{
				$iil_errornum = -2;
				$iil_error = "Unknown user: $user \"".htmlspecialchars($line)."\"<br>Messages:".$conn->message;
				iil_Close($conn);
				return false;
			}
		}
    }else{
        $iil_error = "Could not connect to $host at port 110";
        $iil_errornum = -1;
		return false;
	}
	
	if ($result) return $conn;
	else return false;
}

function iil_ExplodeQuotedString($delimiter, $string){
	$quotes=explode("\"", $string);
	while ( list($key, $val) = each($quotes))
		if (($key % 2) == 1) 
			$quotes[$key] = str_replace($delimiter, "_!@!_", $quotes[$key]);
	$string=implode("\"", $quotes);
	
	$result=explode($delimiter, $string);
	while ( list($key, $val) = each($result) )
		$result[$key] = str_replace("_!@!_", $delimiter, $result[$key]);
	
	return $result;
}

function iil_CheckForRecent($host, $user, $password, $mailbox){
	return 0;
}

function iil_C_Select(&$conn, $mailbox){
	return true;
}

function iil_C_CountMessages(&$conn, $mailbox){
	$num=-1;
	$fp = $conn->fp;
	
	if (@fputs($fp, "STAT\r\n")){
		$line=chop(fgets($fp, 300));
		$a=explode(" ", $line);
		$num=(int)$a[1];
		$size = (int)$a[2];
	}
	
	return $num;
}

function iil_SplitHeaderLine($string){
	$pos=strpos($string, ":");
	if ($pos>0){
		$res[0]=substr($string, 0, $pos);
		$res[1]=substr($string, $pos+2);
		return $res;
	}else{
		return $string;
	}
}

function iil_StrToTime($str){
	//replace double spaces with single space
	$str = trim($str);
	$str = str_replace("  ", " ", $str);
	
	//strip off day of week
	$pos=strpos($str, " ");
	$word = substr($str, 0, $pos);
	if (!is_numeric($word)) $str = substr($str, $pos+1);

	//explode, take good parts
	$a=explode(" ",$str);
	$month_a=array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
	$month_str=$a[1];
	$month=$month_a[$month_str];
	$day=$a[0];
	$year=$a[2];
	$time=$a[3];
	$tz_str = $a[4];
	$tz = substr($tz_str, 0, 3);
	$ta=explode(":",$time);
	$hour=(int)$ta[0]-(int)$tz;
	$minute=$ta[1];
	$second=$ta[2];

	//make UNIX timestamp
	return mktime($hour, $minute, $second, $month, $day, $year);
}

function iil_F_List($fp){
	fputs($fp, "LIST\r\n");
	$line = fgets($fp, 128);
	if ($line[0]=="+"){
		do{
			$line = fgets($fp, 128);
			if ($line[0]!="."){
				$a = explode(" ", $line);
				$id = (int)$a[0];
				$size = (int)$a[1];
				$index[$id] = $size;
			}
		}while($line[0]!=".");
	}
	return $index;
}

function iil_F_GetMessageID($fp, $id){
	$messageID = "";
	
	fputs($fp, "TOP $id 0\r\n");
	$line = fgets($fp, 128);
	if ($line[0]=="+"){
		do{
			//go through headers...
			$line = chop(iil_ReadLine($fp, 300));
			$a = iil_SplitHeaderLine($line);
			if (strcasecmp($a[0], "message-id")==0){
				$messageID = trim(chop($a[1]));
				$messageID = substr(substr($messageID, 1), 0, strlen($messageID)-2);
			}
		}while($line[0]!=".");
	}
	return $messageID;
}

function iil_C_FetchHeaderIndex(&$conn, $mailbox, $message_set, $index_field){
	return false;
	
	/*
		NOTE:
		Following code is a complete functional implementation of this particular function,
		compatible with it's IMAP equivalence.  Only problem is, with POP3, it's less efficient
		because the entire header for all messages have to be read in. 
	*/
	
	$fp = $conn->fp;
	$result = array();
	$index_field = strtoupper($index_field);
	
	//get index list
	$index = iil_F_List($fp);

	if (strcmp($index_field, "SIZE")==0){
		return $index;
	}else{
		reset($index);
		//loop through all items
		while ( list($id, $val) = each($index) ){
			//fetch header
			fputs($fp, "TOP $id 0\r\n");
			$line = fgets($fp, 128);
			if ($line[0]=="+"){
				do{
					//go through headers...
					$line = chop(iil_ReadLine($fp, 300));
					$a = iil_SplitHeaderLine($line);
					//look for line containg field we want
					if (strcasecmp($index_field,"date")==0) $a[1] = iil_StrToTime($a[1]);
					if (strcasecmp($a[0], $index_field)==0) $result[$id] = $a[1];
				}while($line[0]!=".");
			}
		}
	}
	
	return $result;
}

function iil_C_FetchHeaders(&$conn, $mailbox, $message_set){

    //echo microtime()."....starting iil_C_FetchHeaders $message_set <br>\n"; flush();

	if (strpos($message_set, ":")>0){
		$a = explode(":", $message_set);
		$from_i = (int)$a[0];
		$to_i = (int)$a[1];
	}else if (strpos($message_set, ",")>0){
		$a = explode(",", $message_set);
		$n = count($a);
		$from_i = (int)$a[0];
		$to_i = (int)$a[$n-1];
	}else{
		$from_i = $to_i = $message_set;
	}
		
	$fp = $conn->fp;
	$index = iil_F_List($fp);
	
	$c = 0;
	$lines = array();
	for ($id=$from_i;(($id<=$to_i)&&($fp));$id++){
        //echo "[".microtime().":begin $id]"; flush();
        
		fputs($fp, "TOP $id 0\r\n");
		$line = fgets($fp, 128);
		
        //echo "[".microtime().":requested]"; flush();
        //echo "Requested $id ..."; flush();
		if ($line[0]=="+"){

			//initialize new iilBasicHeader object
			$result[$id] = new iilBasicHeader;
			$result[$id]->size = $index[$id];
			$result[$id]->id = $id;
			$result[$id]->subject = "";
			$result[$id]->seen = false;
			$result[$id]->recent = false;
			$result[$id]->deleted = false;
			$result[$id]->answered = false;
			
			//fetch header into array
			do{
                socket_set_timeout($fp, 10);
				$line = chop(iil_ReadLine($fp, 300));
				if (strlen($line)>2){
					if (ord($line[0]) <= 32) $lines[$c] .= (empty($lines[$c])?"":"\n").trim($line);
					else{
						$c++;
						$lines[$c]=$line;
					}
				}
			}while(($line[0]!=".")&&($fp));
			
			//echo "[".microtime().":fetched header]"; flush();
			
			//process header, fill iilBasicHeader obj.
			//	initialize
			if (is_array($header_fields)){
				reset($header_fields);
				while ( list($k, $bar) = each($header_fields) ) $header_fields[$k] = "";
			}

			//	create array with header field:data
			$numlines = count($lines);
            while (list($k,$string) = each($lines)){
                $pos=strpos($string, ":");
                if ($pos>0){
                    $field = strtolower(substr($string, 0, $pos));
                    $string = substr($string, $pos+2);
                }else{
                    $field = "";
                }

                $header_fields[$field] = $string;
			}
			
            //	fill in object
            $result[$id]->date = $header_fields["date"];
            $result[$id]->timestamp = iil_StrToTime($result[$id]->date);
            $result[$id]->from = $header_fields["from"];
            $result[$id]->to = $header_fields["to"];
            $result[$id]->subject = $header_fields["subject"];
            $result[$id]->replyto = $header_fields["reply-to"];
            $result[$id]->cc = $header_fields["cc"];
            $result[$id]->messageID = substr(substr($header_fields["message-id"], 1), 0, strlen($header_fields["message-id"])-2);
            $result[$id]->encoding = $header_fields["content-transfer-encoding"];
            $result[$id]->ctype = $header_fields["content-type"];

            //echo "[".microtime().":filled]<br>\n"; flush();
		}
	}
    //echo microtime().".....done"; flush();
    
	return $result;
}


function iil_C_FetchHeader(&$conn, $mailbox, $id){
	$fp = $conn->fp;

	$index = iil_F_List($fp);

	fputs($fp, "TOP $id 0\r\n");
	$line = fgets($fp, 128);
	if ($line[0]=="+"){
		//initialize new iilBasicHeader object
		$result = new iilBasicHeader;
		$result->size = $index[$id];
		$result->id = $id;
		
		//fetch header into array
		do{
			$line = chop(iil_ReadLine($fp, 300));
			if (strlen($line)>2){
				if (ord($line[0]) <= 32) $lines[$c] .= (empty($lines[$c])?"":"\n").trim($line);
				else{
					$c++;
					$lines[$c]=$line;
				}
			}
		}while($line[0]!=".");
		
		//process header, fill iilBasicHeader obj.
		$numlines = count($lines);
		for ($i=0;$i<$numlines;$i++){
            //echo $lines[$i]."<br>\n";
			list($field, $string) = iil_SplitHeaderLine($lines[$i]);
			
			if (strcasecmp($field, "date")==0){
				$result->date = $string;
				$result->timestamp = iil_StrToTime($string);
			}
			else if (strcasecmp($field, "from")==0) $result->from = str_replace("\n", " ", $string);
			else if (strcasecmp($field, "to")==0) $result->to = $string;
			else if (strcasecmp($field, "subject")==0) $result->subject = str_replace("\n", "", $string);
			else if (strcasecmp($field, "reply-to")==0) $result->replyto=$string;
			else if (strcasecmp($field, "cc")==0) $result->cc = str_replace("\n", " ", $string);
			else if (strcasecmp($field, "Content-Transfer-Encoding")==0) $result->encoding=$string;
			else if (strcasecmp($field, "message-id")==0)
				$result->messageID = substr(substr($string, 1), 0, strlen($string)-2);
		}
		return $result;
	}else{
		return false;
	}
	/*
	$a=iil_C_FetchHeaders($conn, $mailbox, $id);
	if (is_array($a)) return $a[$id];
	else return false;
	*/
}


function iil_SortHeaders($a, $field, $flag){
	if (empty($field)) $field="uid";
	$field=strtolower($field);
	if ($field=="date") $field="timestamp";
	if (empty($flag)) $flag="ASC";
	$flag=strtoupper($flag);
	
	$c=count($a);
	if ($c>0){
		/*
			Strategy:
			First, we'll create an "index" array.
			Then, we'll use sort() on that array, 
			and use that to sort the main array.
		*/
                
                // create "index" array
		$index=array();
		reset($a);
		while (list($key, $val)=each($a)){
			$data=$a[$key]->$field;
			if (is_string($data)) $data=strtoupper(str_replace("\"", "", $data));
			$index[$key]=$data;
		}
		
		// sort index
		$i=0;
		if ($flag=="ASC") asort($index);
		else arsort($index);
		
		// form new array based on index 
		$result=array();
		reset($index);
		while (list($key, $val)=each($index)){
			$result[$i]=$a[$key];
			$i++;
		}
	}
	
	return $result;
}

function iil_C_Expunge(&$conn, $mailbox){
	return 0;
}

function iil_C_Flag(&$conn, $mailbox, $messages, $flag){
	return 0;
}

function iil_C_Delete(&$conn, $mailbox, $messages){
	$fp = $conn->fp;

	$c = 0;
	if (strpos($messages, ":")!==false){
		$a = explode(":", $messages);
		$start = $a[0];
		$end = $a[1];
		for ($i=$start;$i<=$end;$i++) $messages .= (empty($messages)?"":",").$i;
	}
	$message_list = explode(",", $messages);
	while ( list($key, $val) = each($message_list) ){
		fputs($fp, "DELE $val\r\n");
		$line = iil_ReadLine($fp, 300);
		if ($line[0]=="+"){
			$c++;
		}
	}
	
	//disconnect and reconnect to expunge
	iil_Close($conn);
	$conn = iil_Connect($conn->host, $conn->login, $conn->password);
	
	return $c;
}

function iil_C_Undelete(&$conn, $mailbox, $messages){
	return -1;
}

function iil_C_Unseen(&$conn, $mailbox, $messages){
	return -1;
}

function iil_C_Copy(&$conn, $messages, $from, $to){
	return 0;
}

function iil_FormatSearchDate($month, $day, $year){
	$months=array(
			1=>"Jan", 2=>"Feb", 3=>"Mar", 4=>"Apr", 
			5=>"May", 6=>"Jun", 7=>"Jul", 8=>"Aug", 
			9=>"Sep", 10=>"Oct", 11=>"Nov", 12=>"Dec"
			);
	return $day."-".$months[$month]."-".$year;
}

function iil_C_Search(&$conn, $folder, $criteria){
	return false;
}

function iil_C_Move(&$conn, $messages, $from, $to){
	return 0;
}

function iil_C_GetHierarchyDelimiter(&$conn){
	return "/";
}

function iil_C_ListMailboxes(&$conn, $ref, $mailbox){
	return array("INBOX");
}

function iil_ActionPrintBody($line){
	echo chop($line)."\n"; flush();
	return "";
}

function iil_ActionFetchBody($line){
	return chop($line)."\n";
}

function iil_ActionBase64Decode($line){
	echo base64_decode(chop($line)); flush();
	return "";
}

function iil_ActionFetchHeader($line){
	return "";
}

function iil_ClearCache($login, $host){
	global $CACHE_DIR;
	
	$cacheDir = $CACHE_DIR;
	if (empty($cacheDir)) $cacheDir = "../cache/";
	if (!file_exists(realpath($cacheDir))) $conn->errorNum = -2;
	else{
		$cacheDir = $cacheDir.ereg_replace("[\\/]", "", $login.".".$host);
		if (!file_exists(realpath($cacheDir))) mkdir($cacheDir, 0700);
	}
	if (file_exists(realpath($cacheDir))){
		if ($handle = opendir($cacheDir)) {
			while (false !== ($file = readdir($handle))) { 
				if ($file != "." && $file != "..") { 
					$file_path = $cacheDir."/".$file;
					//echo $file_path."<br>\n";
					unlink($file_path);
				} 
			}
			closedir($handle); 
		}
	}	
}

function iil_C_OpenMessage(&$conn, $id){
	/*
		POST-CONDITIONS:
			TRUE: if cache file has been opened
			FALSE: if cache file is not open
			$conn->cacheMode:
				x: failed
				w: open cache for writing
				r: cache opened for reading
	*/
	global $CACHE_DIR;
	
	$conn->messageID = iil_F_GetMessageID($conn->fp, $id);
	$messageID = $conn->messageID;
	
	$conn->errorNum = 0;
	$conn->cacheMode = "x";
	if (!empty($messageID)){
		$cacheDir = $CACHE_DIR;
		if (empty($cacheDir)) $cacheDir = "../cache/";
		if (!file_exists(realpath($cacheDir))) $conn->errorNum = -2;
		else{
			$cacheDir = $cacheDir."/".ereg_replace("[\\/]", "", $conn->login.".".$conn->host);
			if (!file_exists(realpath($cacheDir))) mkdir($cacheDir, 0700);
		}
		if (file_exists(realpath($cacheDir))){
			$cachePath = $cacheDir."/".urlencode($messageID);
			if (file_exists(realpath($cachePath))) $mode = "r";
			else $mode = "w";
			
			$conn->cacheFP = fopen($cachePath, $mode);
			if ($conn->cacheFP){
				$conn->cacheMode = $mode;
				return true;
			}else{
				$conn->errorNum = -3;
				$conn->error = "Couldn't open cache for reading";
			}
		}else{
			$conn->errorNum = -2;
			$conn->error = "Invalid cache directory";
		}
	}else{
		$conn->errorNum = -1;
		$conn->error = "Invalid messageID";
	}
	return false;
}

function iil_C_CloseMessage(&$conn){
	if (($conn->cacheMode=="r") || ($conn->cacheMode=="w")) fclose($conn->cacheFP);
	$conn->cacheMode = "x";
	$conn->messageID = "";
}

function iil_C_FetchBodyPart(&$conn, $boundary, &$last_line, $the_part, &$part, $action, $bytes_total, &$bytes_read){
	if ($the_part==0) $the_part="";
	$original_boundary = $boundary; 
	$raw_header = "";
	$raw_text = "";
	
	if ($conn->cacheMode=="r") $fp = $conn->cacheFP;
	else $fp = $conn->fp;

	// read headers from file
	$lines = array();
	$count = 0;
	do{
		$line = iil_C_ReadLine($conn);
		$bytes_read+=strlen($line);
		$raw_header .= $line;
		$line = chop($line);
		if (!empty($line)){
			//echo "Read: ".$line."<br>\n";
			$c = 0;
			if (ord($line[0]) <= 32) $lines[$count].=" ".trim($line);
			else{
				$count++;
				$lines[$count] = $line;
				//echo "\t".$count.":".$line."\n";
			}
		}
	}while(!empty($line));
	if ((strcmp($part, $the_part)==0)&&(strcmp($action, "FetchHeader")==0)) $str=$raw_header;
	
	//echo "Read header: ".count($lines)." lines\n"; flush();
	
	// parse header into associative array
	$header = iil_ContentHeaderArray($lines);
	
	//echo "Parsed headers\n"; flush();
	//echo implode("\n", $lines)."\n\n";
	
	//echo $header["content-type"]["major"]." vs multipart";
	
	// generate bodystructure string(s)
	if (strcasecmp($header["content-type"]["major"], "multipart")==0){
		//echo "Parsing multipart\n"; flush();
		
		$params = $header["content-type"]["parameters"];
		while ( list($k, $v) = each($params) ) if (strcasecmp($v, "\"boundary\"")==0) $boundary = "--".str_replace("\"","",$params[$k+1]);
		//echo "Boundary: ".$boundary."<br>\n";
		do{
			$line = iil_C_ReadLine($conn);
			$bytes_read+=strlen($line);
		}while(!iil_StartsWith($line, $boundary));

		//parse body parts
		$part_num = 0;
		do{
			$part_num++;
			$next_part = $part.(!empty($part)?".":"").$part_num;
			$str .= iil_C_FetchBodyPart($conn, $boundary, $last_line, $the_part, $next_part, $action, $bytes_total, $bytes_read);
			$end = (((strlen($last_line) - strlen($boundary)) > 0) || (chop($last_line)=="."));
		}while((!$end) && (!feof($fp))&&(chop($last_line)!="."));

		//read up to next message boundary
		if (chop($last_line)!="."){
			do{
				$line = iil_C_ReadLine($conn);
				$bytes_read += strlen($line);
				$end = ((iil_StartsWith($line, $original_boundary)) || (chop($last_line)=="."));
			}while((!$end)&&(!feof($fp))&&(chop($line)!="."));
			$last_line = chop($line);
		}
	}else if (strcasecmp($header["content-type"]["major"], "message")==0){
		//read blank lines (up to and including first line, which hopefully isn't important)
		do{
			$line = iil_C_ReadLine($conn);
		}while(iil_StartsWith($line, "\n"));
		
		$str .= iil_C_FetchBodyPart($conn, $boundary, $last_line, $the_part, $part, $action, $bytes_total, $bytes_read);
	}else{
		// read actual data
		//echo "Will do action: $action <br>\n"; flush();
		if (strcmp($part, $the_part)==0){
			$this_is_it=ture;
			$handler = "iil_Action".$action;
		}else $this_is_it = false;
		do{
			$line = iil_C_ReadLine($conn);
			$bytes_read += strlen($line);
			if (($this_is_it)&&(!iil_StartsWith($line, $boundary))&&(chop($line)!=".")) $str.=$handler($line);
			$line = chop($line);
			//echo "Read $bytes_read of $bytes_total bytes<br>\n"; flush();
		}while((!iil_StartsWith($line, $boundary)) && ((!feof($fp))&&($line!=".")));
		$last_line = $line;
	}
	
	//echo "Read $bytes_read out of $bytes_total <br>\n"; flush();

	return $str;
}

function iil_C_HandlePartBody(&$conn, $id, $part, $action){
		
	iil_C_OpenMessage($conn, $id);
	
	//echo "Message opened\n"; flush();
	
	if (($conn->cacheMode=="x") || ($conn->cacheMode=="w")){
		$fp = $conn->fp;
		fputs($fp, "RETR $id\r\n");
		$line = fgets($fp, 100);
		if ($line[0]!="+"){
			$conn->errorNum = -10;
			$conn->error = "POP3 error:".$line;
		}
	}

	//echo "FP opened\n";

	if ($conn->fp){
		//echo "Going ingot iil_C_FetchBodyPart\n";
		$result =  iil_C_FetchBodyPart($conn, "", $line, $part, $part_blah, $action, $total_size, $bytes);
	}else{
		$conn->error = "Bad fp";
	}
	iil_C_CloseMessage($conn);
	
	return $result;
}

function iil_C_FetchPartBody(&$conn, $mailbox, $id, $part){
	return iil_C_HandlePartBody($conn, $id, $part, "FetchBody");
}

function iil_C_PrintPartBody(&$conn, $mailbox, $id, $part){
	return iil_C_HandlePartBody($conn, $id, $part, "PrintBody");
}

function iil_C_PrintBase64Body(&$conn, $mailbox, $id, $part){
	return iil_C_HandlePartBody($conn, $id, $part, "Base64Decode");
}

function iil_C_FetchPartHeader(&$conn, $mailbox, $id, $part){
	return iil_C_HandlePartBody($conn, $id, $part, "FetchHeader");
}

function iil_C_CreateFolder(&$conn, $folder){
	return false;
}

function iil_C_RenameFolder(&$conn, $from, $to){
	return false;
}

function iil_C_DeleteFolder(&$conn, $folder){
	return false;
}

function iil_C_Append(&$conn, $folder, $message){
	return false;
}

function iil_ReadHeader($fp){
	$lines = array();
	$c = 0;
	//echo "++++<br>\n";
	do{
		$line = chop(iil_ReadLine($fp, 300));
		if (!empty($line)){
			//echo "Read: ".$line."<br>\n";
			if (ord($line[0]) <= 32) $lines[$c].=" ".trim($line);
			else{
				$c++;
				$lines[$c] = $line;
			}
		}
	}while(!empty($line));
	//echo "++++<br>\n";
	
	return $lines;
}

function iil_ParseContentHeader($data){
	$parameters = array();

	$pos = strpos($data, ";");
	if ($pos === false){
		//no';'? then no parameters, all we have is main data
		$major_data = $data;
	}else{
		//every thing before first ';' is main data
		$major_data = substr($data, 0, $pos);
		$data = substr($data, $pos+1);
		
		//go through parameter list (delimited by ';')
		$parameters_a = explode(";", $data);
		while ( list($key, $val) = each($parameters_a) ){
			// split param name from param data
			$val = trim(chop($val));
			$eqpos = strpos($val, "=");
			$p_field = substr($val, 0, $eqpos);
			$p_data = substr($val, $eqpos+1);
			$field = trim(chop($p_field));
			$p_data = trim(chop($p_data));
			// add quotes
			if ($p_data[0]!="\"") $p_data = "\"".$p_data."\"";
			$p_field = "\"".$p_field."\"";
			// add to array
			array_push($parameters, $p_field);
			array_push($parameters, $p_data);
		}
	}
	$result["data"] = trim(chop($major_data));
	if (count($parameters) > 0) $result["parameters"] = $parameters;
	else $result["parameters"] = "NIL";
	
	return $result;
}

function iil_ContentHeaderArray($lines){
	//echo "---<br>\n";
	$num_lines = count($lines);
	//initialize header variables with default (fall back) values
	$header["content-type"]["major"] = "text";
	$header["content-type"]["minor"] = "plain";
	$header["content-transfer-encoding"]["data"] = "8bit";
	while ( list($key, $line) = each($lines) ){
		//echo $line."<br>\n";
		list($field, $data) = iil_SplitHeaderLine($line);
		// is this a content header?
		if (iil_StartsWith($field, "Content")){
			$field = strtolower($field);
			// parse line, add "data" and "parameters" to header[]
			$header[$field] = iil_ParseContentHeader($data);
			// need some special care for "content-type" header line
			if (strcasecmp($field, "content-type")==0){
				$typeStr = $header["content-type"]["data"];
				//split major and minor content types
				$slashPos = strpos($typeStr,"/");
				$major_type = substr($typeStr, 0, $slashPos);
				$minor_type = substr($typeStr, $slashPos+1);
				$header["content-type"]["major"] = strtolower($major_type);
				$header["content-type"]["minor"] = strtolower($minor_type);
			}
		}
	}
	return $header;
}

function iil_C_ReadNParse($conn, $boundary, &$last_line){

	$original_boundary = $boundary; 
	
	if ($conn->cacheMode=="r") $fp = $conn->cacheFP;
	else $fp = $conn->fp;

	// read headers from file
	$lines = iil_ReadHeader($fp);
	if (count($lines) == 0) return "";
	if ($conn->cacheMode=="w") fputs($conn->cacheFP, implode("\n", $lines)."\n\n");	
		
	// parse header into associative array
	$header = iil_ContentHeaderArray($lines);
	
	// generate bodystructure string(s)
	if (strcasecmp($header["content-type"]["major"], "multipart")==0){
		$params = $header["content-type"]["parameters"];
		while ( list($k, $v) = each($params) ) if (strcasecmp($v, "\"boundary\"")==0) $boundary = "--".str_replace("\"","",$params[$k+1]);
		do{
			$line = iil_C_ReadLine($conn);
		}while(!iil_StartsWith($line, $boundary));
		$str = "(";
		//parse body parts
		do{
			$str .= iil_C_ReadNParse($conn, $boundary, $last_line);
			$end = (((strlen($last_line) - strlen($boundary)) > 0) || (chop($last_line)=="."));
		}while((!$end) && (!feof($fp))&&($line!="."));
		
		$str .=" \"".$header["content-type"]["minor"]."\" (".implode(" ", $params).") NIL NIL)";

		//if next boundary encountered
		if ((chop($line)!=".") && (chop($last_line)!=".")){
			//read up to next message boundary
			do{
				$line = iil_C_ReadLine($conn);
				$end = ((iil_StartsWith($line, $original_boundary)) || (chop($last_line)=="."));
			}while((!$end)&&(!feof($fp))&&(chop($line)!=".")) ;
			$last_line = chop($line);
		}
	}else if (strcasecmp($header["content-type"]["major"], "message")==0){
		//read blank lines (up to and including first line, which hopefully isn't important)
		do{
			$line = iil_C_ReadLine($conn);
		}while(iil_StartsWith($line, "\n"));
		
		//format structure string
		$str = '("'.$header["content-type"]["major"].'" "'.$header["content-type"]["minor"].'"';
		$str.= ' NIL NIL NIL';
		$str.= ' "'.$header["content-transfer-encoding"]["data"].'"';
		$byte_count = 'NIL';
		$str.= " $byte_count NIL ";
		
		//recursively parse content
		$str.= iil_C_ReadNParse($conn, $boundary, $last_line);
		
		//more structure stuff
		$line_count = 'NIL';
		$str.= " $line_count NIL ";
		if (!empty($header["content-disposition"]["data"])){
			$param_a = $header["content-disposition"]["parameters"];
			$str .= "(\"".$header["content-disposition"]["data"]."\" ";
			if ((is_array($param_a)) && (count($param_a) > 0))
				$str .="(".implode(" ", $param_a).")";
			else $str .="NIL";
			$str .= ") ";
		}else $str .= "NIL ";
		$str.= ' NIL)';
	}else{
		// read actual data
		$content_size = 0;
		$num_lines = 0;
		do{
			$line = iil_C_ReadLine($conn);
			$content_size += strlen($line);
			$num_lines++;
			$line = chop($line);
		}while((!iil_StartsWith($line, $boundary)) && ((!feof($fp))&&($line!=".")));
		$last_line = $line;
				
		// generate bodystructure string
		$str = "(";
		$str .= "\"".$header["content-type"]["major"]."\" ";
		$str .= "\"".$header["content-type"]["minor"]."\" ";
		if ((is_array($header["content-type"]["parameters"]))&&(count($header["content-type"]["parameters"]) > 0))
			$str .="(".implode(" ", $header["content-type"]["parameters"]).") ";
		else
			$str .= "NIL ";
		if ($header["content-id"]["data"])
			$str .= "\"".$header["content-id"]["data"]."\" ";
		else
			$str .= "NIL ";
		$str .= "NIL ";
		$str .= "\"".$header["content-transfer-encoding"]["data"]."\" ";
		$str .= $content_size." ";
		if (strcasecmp($header["content-type"]["major"], "text")==0)
			$str .= $num_lines." ";
		$str .= "NIL ";
		if (!empty($header["content-disposition"]["data"])){
			$param_a = $header["content-disposition"]["parameters"];
			$str .= "(\"".$header["content-disposition"]["data"]."\" ";
			if ((is_array($param_a)) && (count($param_a) > 0))
				$str .="(".implode(" ", $param_a).")";
			else $str .="NIL";
			$str .= ") ";
		}else $str .= "NIL ";
		$str .= "NIL ";
		$str = substr($str, 0, strlen($str)-1);
		$str .= ")";
	}
	
	return $str;
}

function iil_C_FetchStructureString(&$conn, $folder, $id){
	$fp = $conn->fp;
		
	iil_C_OpenMessage($conn,$id);
	
	if (($conn->cacheMode=="x") || ($conn->cacheMode=="w")){
		fputs($fp, "RETR $id\r\n");
		$line = fgets($fp, 100);
		if ($line[0]!="+"){
			$conn->errorNum = -10;
			$conn->error = "POP3 error:".$line;
		}
	}

	if ($conn->fp){
		$str =  iil_C_ReadNParse($conn, "", $line);
	}
	
	iil_C_CloseMessage($conn);
	
	return $str;
}

function iil_C_PrintSource(&$conn, $folder, $id, $part){
	iil_C_OpenMessage($conn,$id);
	
	if ($conn->cacheMode=="r") $fp = $conn->cacheFP;
	else $fp = $conn->fp;
	
	if (($conn->cacheMode=="x") || ($conn->cacheMode=="w")){
		fputs($fp, "RETR $id\r\n");
		$line = fgets($fp, 100);
		if ($line[0]!="+"){
			$conn->errorNum = -10;
			$conn->error = "POP3 error:".$line;
		}
	}

	if ($fp){
		do{
			$line = iil_ReadLine($fp, 300);
			echo $line;
		}while((chop($line)!=".")&&(!feof($fp)));
	}
	iil_C_CloseMessage($conn);
}

function iil_C_GetQuota($conn){
	global $POP_QUOTA;
	
	$index = iil_F_List($conn->fp);
	$used = 0;
	if (is_array($index)) while ( list($k,$size) = each($index) ) $used += $size;
	else $used = 0;
	$total = $POP_QUOTA[$conn->host];
	
	$used = (int)($used/1024);
	$result["used"] = $used;
	$result["total"] = (empty($total)?"??":$total);
	$result["percent"] = (empty($total)?"??":round(($used/$total)*100));
	$result["free"] = 100 - $result["percent"];
	
	return $result;
}

?>
