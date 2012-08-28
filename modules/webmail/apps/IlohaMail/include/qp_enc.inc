<?php
/////////////////////////////
//	include/qp_enc.inc
//		
//		Author: pob@medienrecht.org
//		Source: http://www.php.net/manual/en/function.quoted-printable-decode.php
//		Description:
//				quoted printable encoding function
//		License: 
//				Public domain?
//				Used with implicit permission
//
////////////////////////////


function qp_enc($input = "quoted-printable encoding test string", $line_max = 76) {

	$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
	$lines = preg_split("/(?:\r\n|\r|\n)/", $input);
	$eol = "\r\n";
	$escape = "=";
	$output = "";

	while( list(, $line) = each($lines) ) {
		//$line = rtrim($line); // remove trailing white space -> no =20\r\nnecessary
		$linlen = strlen($line);
		$newline = "";
		for($i = 0; $i < $linlen; $i++) {
			$c = substr($line, $i, 1);
			$dec = ord($c);
			if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
				$c = "=20"; 
			} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { //always encode "\t", which is *not* required
				$h2 = floor($dec/16); $h1 = floor($dec%16); 
				$c = $escape.$hex["$h2"].$hex["$h1"]; 
			}
			if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is notcounted
				$output .= $newline.$escape.$eol; // soft line break; "=\r\n" is okay
				$newline = "";
			}
			$newline .= $c;
		} // end of for
		$output .= $newline.$eol;
	}
	return trim($output);

}
?>