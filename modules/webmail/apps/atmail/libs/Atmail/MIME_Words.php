<?php
// +----------------------------------------------------------------+
// | MIME_Words.php													|
// +----------------------------------------------------------------+
// | AtMail Open - Licensed under the Apache 2.0 Open-source License|
// | http://opensource.org/licenses/apache2.0.php                   |
// +----------------------------------------------------------------+
// | Date: January 2006												|
// +----------------------------------------------------------------+

### Nonprintables (controls + x7F + 8bit):
define('NONPRINT', "\\x00-\\x1F\\x7F-\\xFF");

class MIME_Words
{

	function encode_mimeword($word, $encoding='Q', $charset='ISO-8859-1')
	{
		$encfunc  = ($encoding == 'Q') ? '_encode_Q' : '_encode_B';
		MIME_Words::$encfunc($word);
    	return "=?$charset?$encoding?$word?=";
	}

	function encode_mimewords($rawstr, $params)
	{
		$charset  = isset($params['Charset']) ? $params['Charset'] : 'ISO-8859-1';
		$encoding = isset($params['Encoding']) ? strtolower($params['Encoding']) : 'q';

	    /*
	     * Encode any "words" with unsafe characters.
	     * We limit such words to 18 characters, to guarantee that the
	     * worst-case encoding give us no more than 54 + ~10 < 75 characters
	     */

	    $rawstr = preg_replace('/([a-zA-Z0-9\x7F-\xFF]{1,18})/e', "MIME_Words::encode_mimeword('$1', $encoding, $charset)", $rawstr);
		return $rawstr;
	}

	# _decode_Q STRING
	#     Private: used by _decode_header() to decode "Q" encoding, which is
	#     almost, but not exactly, quoted-printable.  :-P
	function _decode_Q(&$str)
	{
	    $str = str_replace('_', "\x20", $str);                              # RFC-1522, Q rule 2
	    $str = preg_replace('/=([\da-fA-F]{2})/e', "pack('C', hexdec('$1'))", $str);   # RFC-1522, Q rule 1
	}

	# _encode_Q STRING
	#     Private: used by _encode_header() to decode "Q" encoding, which is
	#     almost, but not exactly, quoted-printable.  :-P
	function _encode_Q(&$str)
	{
	    $str = preg_replace('/([_\?\='.NONPRINT.'])/', sprintf("=%02X", ord('$1')), $str);
	}

	# _decode_B STRING
	#     Private: used by _decode_header() to decode "B" encoding.
	function _decode_B(&$str)
	{
	    $str = base64_decode($str);
	}

	# _encode_B STRING
	#     Private: used by _decode_header() to decode "B" encoding.
	function _encode_B(&$str)
	{
	    $str = base64_encode($str);
	}

	function decode_mimeword($word, $encoding='Q', $charset='ISO-8859-1')
	{
		$encfunc  = ($encoding == 'Q') ? '_decode_Q' : '_decode_B';
		MIME_Words::$encfunc($word);
    	return "$word";
	}

//	function decode_mimewords($rawstr, $params)
//	{
//		$charset  = isset($params['Charset']) ? $params['Charset'] : 'ISO-8859-1';
//		$encoding = isset($params['Encoding']) ? strtolower($params['Encoding']) : 'q';
//
//	    /*
//	     * Encode any "words" with unsafe characters.
//	     * We limit such words to 18 characters, to guarantee that the
//	     * worst-case encoding give us no more than 54 + ~10 < 75 characters
//	     */
//
//	    $rawstr = preg_replace('/([a-zA-Z0-9\x7F-\xFF]{1,18})/e', "MIME_Words::decode_mimeword('$1', $encoding, $charset)", $rawstr);
//		return $rawstr;
//	}
	/*
	function decode_mimewords($encstr, $params)
	{
	    $tokens = array();

	    ### Collapse boundaries between adjacent encoded words:
	    $encstr = preg_replace('/(\?\=)\s*(\=\?)/', '$1$2', $encstr);

	    ### Decode:
	    while (1)
		{
			if (pos($encstr) >= length($encstr))
				continue;

			$pos = pos($encstr);               ### save it

		### Case 1: are we looking at "=?..?..?="?
		if ($encstr =~    m{\G             # from where we left off..
				    =\?([^?]*)     # "=?" + charset +
				     \?([bq])      #  "?" + encoding +
				     \?([^?]+)     #  "?" + data maybe with spcs +
				     \?=           #  "?="
				    }xgi) {
		    ($charset, $encoding, $enc) = ($1, lc($2), $3);
		    $dec = (($encoding eq 'q') ? _decode_Q($enc) : _decode_B($enc));
		    push @tokens, [$dec, $charset];
		    next;
		}

		### Case 2: are we looking at a bad "=?..." prefix?
		### We need this to detect problems for case 3, which stops at "=?":
		pos($encstr) = $pos;               # reset the pointer.
		if ($encstr =~ m{\G=\?}xg) {
		    $@ .= qq|unterminated "=?..?..?=" in "$encstr" (pos $pos)\n|;
		    push @tokens, ['=?'];
		    next;
		}

		### Case 3: are we looking at ordinary text?
		pos($encstr) = $pos;               # reset the pointer.
		if ($encstr =~ m{\G                # from where we left off...
				 ([\x00-\xFF]*?    #   shortest possible string,
				  \n*)             #   followed by 0 or more NLs,
			         (?=(\Z|=\?))      # terminated by "=?" or EOS
				}xg) {
		    length($1) or die "MIME::Words: internal logic err: empty token\n";
		    push @tokens, [$1];
		    next;
		}

		### Case 4: bug!
		die "MIME::Words: unexpected case:\n($encstr) pos $pos\n\t".
		    "Please alert developer.\n";
	    }
	    return (wantarray ? @tokens : join('',map {$_->[0]} @tokens));
	}
	*/
}

?>