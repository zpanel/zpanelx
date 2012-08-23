<?php
/////////////////////////////
//	common.inc
//	(C)2002 Ryo Chijiiwa <Ryo@IlohaMail.org>
//		
//		Description:
//			Charset agnostic functions related
//			to the lang/charset.inc libraries.
//
//	This file is part of IlohaMail. IlohaMail is free software released 
//	under the GPL license.  See enclosed file COPYING for details, or 
//	see http://www.fsf.org/copyleft/gpl.html
////////////////////////////

function LangFormatIntTime($time, $system, $ampm, $format){
	//purpose: take "930" and format as "9:30am" or "0930" as necessary
	$min_pos = strlen($time) - 2;
	$hours = substr($time, 0, $min_pos);
	$minutes = substr($time, $min_pos);
	if ($system==12){
		if ($hours >= 12){
			if ($hours>12) $hours-=12;
			$a = "pm";
		}else{
			$a = "am";
		}
	}
	$result = $format;
	if (!$hours) $hours = "00";
	if (!$minutes) $minutes = "00";
	$result = str_replace("%h", $hours, $result);
	$result = str_replace("%m", $minutes, $result);
	$result = str_replace("%a", $ampm[$a], $result);
	
	return $result;
}

function LangInsertStringsFromAK($dest, $source_a){
	if (!is_array($source_a)) return $dest;
	else{
		while ( list($key, $val) = each($source_a) ){
			$place_holder = "%".$key;
			$dest = str_replace($place_holder, $val, $dest);
		}
	}
	return $dest;
}

function LangDecodeMimeString($str, $charset){
	$a=explode("?", $str);
	$count = count($a);
	if ($count >= 3){			//should be in format "charset?encoding?base64_string"
		for ($i=2; $i<$count; $i++) $rest.=$a[$i];
		
		if (($a[1]=="B")||($a[1]=="b")) $rest = base64_decode($rest);
		else if (($a[1]=="Q")||($a[1]=="q")){
			$rest = str_replace("_", " ", $rest);
			$rest = quoted_printable_decode($rest);
		}
		if (strcasecmp($a[0], "utf-8")==0){
			include_once("../include/utf8.inc");
			return utf8ToUnicodeEntities($rest);
		}else{
			return LangConvert($rest, $charset, $a[0]);
		}
	}else{
		return $str;		//we dont' know what to do with this
	}
}

function LangDecodeSubject($input, $charset){
	$out = "";
	//echo "Received: $input <br>\n";
	$pos = strpos($input, "=?");
	if ($pos !== false){
		$out = substr($input, 0, $pos);

		$end_cs_pos = strpos($input, "?", $pos+2);
		$end_en_pos = strpos($input, "?", $end_cs_pos+1);
		$end_pos = strpos($input, "?=", $end_en_pos+1);

		$encstr = substr($input, $pos+2, ($end_pos-$pos-2));
		//echo "encstr: $encstr <br>\n";
		$rest = substr($input, $end_pos+2);
		//echo "rest: $rest <br>\n";
		$out.=LangDecodeMimeString($encstr, $charset);
		$out.=LangDecodeSubject($rest, $charset);
		//echo "returning: $out <br>\n";
		return $out;
	}else{
		return LangConvert($input, $charset, $charset);
	}
}

function LangDisableHTML($str){
	$result = $str;
	$result = str_replace("<", "&lt;", $result);
	$result = str_replace(">", "&gt;", $result);
	
	return $result;
}

function LangFormAddressHTML($user, $name, $address, $charset){
	global $my_prefs;
	
	if ($my_prefs["compose_inside"]) $target="list2";
	else $target="_blank";
	
	if (empty($name)) $name=$address;
	$decoded_name = LangDecodeSubject($name, $charset);
	if (strpos($decoded_name, " ")!==false) $q_decoded_name = "\"".$decoded_name."\"";
	else $q_decoded_name = $decoded_name;
	
	$url = "compose2.php?user=".$user."&to=".urlencode($q_decoded_name." <".$address.">");
	
	$res="";
	$res.="<a href=\"$url\" target=\"$target\">".LangDisableHTML($decoded_name)."</a>";
	$res.="[<a href=\"edit_contact.php?user=$user&name=".urlencode($decoded_name)."&email=".urlencode($address)."&edit=-1\">+</a>]";
	return $res;
}

function LangExplodeQuotedString($delimiter, $string){
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

function LangParseAddressList($str){
	$a=LangExplodeQuotedString(",", $str);
	$result=array();
	reset($a);
	while( list($key, $val) = each($a) ){
		$val = str_replace("\"<", "\" <", $val);
		$sub_a = LangExplodeQuotedString(" ", $val);
		reset($sub_a);
		while ( list($k, $v) = each($sub_a) ){
			if ((strpos($v, "@") > 0) && (strpos($v, ".") > 0)) 
				$result[$key]["address"] = str_replace("<", "", str_replace(">", "", $v));
			else $result[$key]["name"] .= (empty($result[$key]["name"])?"":" ").str_replace("\"","",stripslashes($v));
		}
		if (empty($result[$key]["name"])) $result[$key]["name"] = $result[$key]["address"];
	}
	
	return $result;
}

function LangEncodeAddressList($str, $charset){
	$str = str_replace(", ", ",", $str);
	$str = str_replace("," , ", ", $str);
	$str = str_replace("; ", ";", $str);
	$str = str_replace(";", "; ", $str);
	
	$a = LangExplodeQuotedString(" ", $str);
	if (is_array($a)){
		$c = count($a);
		for ($i=0;$i<$c;$i++){
			if ((strpos($a[$i],"@") > 0) && (strpos($a[$i], ".") > 0)){
				//probably an email address, leave it alone
			}else{
				//some string, encode
				$word = stripslashes($a[$i]);
				$len = strlen($word);
				$enc = LangEncodeSubject(str_replace("\"", "", $word), $charset);
				if (($word[0]=="\"")&&($word[$len-1]=="\"")) $enc = "\"".$enc."\"";
				$a[$i] = $enc;
			}
		}
		return implode(" ", $a);
	}else{
		return $str;
	}
}

function LangDecodeAddressList($str, $charset, $user){
	$a=LangParseAddressList($str);
	if (is_array($a)){
		$c=count($a);
        $j=0;
		reset($a);
		while( list($i, $val) = each($a) ){
            $j++;
			$address=$a[$i]["address"];
			$name=str_replace("\"", "", $a[$i]["name"]);
			$res.=LangFormAddressHTML($user, $name, $address, $charset);
			if ((($j % 3)==0) && (($c-$j)>1)) $res.=",<br>&nbsp;&nbsp;&nbsp;";
			else if ($c>$j) $res.=",&nbsp;";
			//$res.=(($c>1)&&($j<$c)?",<br>&nbsp;&nbsp;&nbsp;":"");
		}
	}
	
	return $res;
}

function LangShowAddresses($str, $charset){
	$a=LangParseAddressList($str);
	if (is_array($a)){
		$c=count($a);
        $j=0;
		reset($a);
		while( list($i, $val) = each($a) ){
            $j++;
			$address=$a[$i]["address"];
			$name=str_replace("\"", "", $a[$i]["name"]);
			
			$res.=htmlspecialchars("\"$name\" <$address>");
			if ((($j % 3)==0) && (($c-$j)>1)) $res.=",<br>&nbsp;&nbsp;&nbsp;";
			else if ($c>$j) $res.=",&nbsp;";
		}
	}
	
	return $res;
}

function LangFormatDate($timestamp, $format){
	$date = getdate($timestamp);
	
	$result = $format;
	$result = str_replace("%d", $date["mday"], $result);
	$result = str_replace("%m", $date["mon"], $result);
	$result = str_replace("%y", $date["year"], $result);
	$result = str_replace("%t", $date["hour"].":".$date["minutes"], $result);
	$result = str_replace("%S", date('S', $timestamp), $result);

	return $result;
}


function LangWrapLine($line, $width){
	$line_len = strlen($line);
	$i = 0;
	
	//if line is less than width, we're good
	if ($line_len <= $width) return $line;
	
	for ($prev_i=0,$i=$width;$i<$line_len;$prev_i=$i,$i+=$width){
		//extract last segment that is $width wide
		$chunk = substr($line, $prev_i, ($i-$prev_i))."\n";
		
		//find last space in this chunk
		$last_space = strrpos($chunk, " ");
		$last_space = $prev_i + $last_space;
		
		if ($last_space==$prev_i){
			//no space found in this chunk
			$next_space = strpos($line, " ", $i);
			if ($next_space!==false){
				$i = $next_space;
				$line[$next_space] = "\n";
			}
		}else{
			//replace last space before width with newline
			$line[$last_space]="\n";
			$i = $last_space;
		}
	}
	
	return $line;
}

function LangSmartWrap($text, $len){
	$lines = explode("\n", $text);
	
	if (!is_array($lines)) return "";
	
	while ( list($i,$line)=each($lines) ){
		if (!ereg("^>", $line)) $lines[$i] = LangWrapLine(chop($line), $len);
	}
	
	return implode("\n", $lines);
}

function LangEncode8bitLatin($str){
	//following code inspired by SquirrelMail's
	//charset_decode_utf8 in utf-8.php
	
    /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
    if (! ereg("[\200-\237]", $str) and ! ereg("[\241-\377]", $str))
        return $str;

    // encode 8-bit ISO-8859-1 into HTML entities 
	// works because Unicode uses ISO-8859-1 for those ranges
    $str = preg_replace("/([\200-\377])/e", "'&#'.(ord('\\1')).';'",$str);

    return $str;
}


?>