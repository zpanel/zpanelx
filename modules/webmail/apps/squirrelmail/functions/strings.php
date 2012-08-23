<?php

/**
 * strings.php
 *
 * This code provides various string manipulation functions that are
 * used by the rest of the SquirrelMail code.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: strings.php 14123 2011-07-12 19:10:50Z pdontthink $
 * @package squirrelmail
 */

/**
 * SquirrelMail version number -- DO NOT CHANGE
 */
global $version;
$version = '1.4.22';

/**
 * SquirrelMail internal version number -- DO NOT CHANGE
 * $sm_internal_version = array (release, major, minor)
 */
global $SQM_INTERNAL_VERSION;
$SQM_INTERNAL_VERSION = array(1,4,22);

/**
 * There can be a circular issue with includes, where the $version string is
 * referenced by the include of global.php, etc. before it's defined.
 * For that reason, bring in global.php AFTER we define the version strings.
 */
require_once(SM_PATH . 'functions/global.php');

if (file_exists(SM_PATH . 'plugins/compatibility/functions.php')) {
    include_once(SM_PATH . 'plugins/compatibility/functions.php');
}

/**
 * Wraps text at $wrap characters
 *
 * Has a problem with special HTML characters, so call this before
 * you do character translation.
 *
 * Specifically, &#039 comes up as 5 characters instead of 1.
 * This should not add newlines to the end of lines.
 */
function sqWordWrap(&$line, $wrap, $charset=null) {
    global $languages, $squirrelmail_language;

    if (isset($languages[$squirrelmail_language]['XTRA_CODE']) &&
        function_exists($languages[$squirrelmail_language]['XTRA_CODE'])) {
        if (mb_detect_encoding($line) != 'ASCII') {
            $line = $languages[$squirrelmail_language]['XTRA_CODE']('wordwrap', $line, $wrap);
            return;
        }
    }

    preg_match('/^([\t >]*)([^\t >].*)?$/', $line, $regs);
    $beginning_spaces = $regs[1];
    if (isset($regs[2])) {
        $words = explode(' ', $regs[2]);
    } else {
        $words = array();
    }

    $i = 0;
    $line = $beginning_spaces;

    while ($i < count($words)) {
        /* Force one word to be on a line (minimum) */
        $line .= $words[$i];
        $line_len = strlen($beginning_spaces) + sq_strlen($words[$i],$charset) + 2;
        if (isset($words[$i + 1]))
            $line_len += sq_strlen($words[$i + 1],$charset);
        $i ++;

        /* Add more words (as long as they fit) */
        while ($line_len < $wrap && $i < count($words)) {
            $line .= ' ' . $words[$i];
            $i++;
            if (isset($words[$i]))
                $line_len += sq_strlen($words[$i],$charset) + 1;
            else
                $line_len += 1;
        }

        /* Skip spaces if they are the first thing on a continued line */
        while (!isset($words[$i]) && $i < count($words)) {
            $i ++;
        }

        /* Go to the next line if we have more to process */
        if ($i < count($words)) {
            $line .= "\n";
        }
    }
}

/**
 * Does the opposite of sqWordWrap()
 * @param string body the text to un-wordwrap
 * @return void
 */
function sqUnWordWrap(&$body) {
    global $squirrelmail_language;

    if ($squirrelmail_language == 'ja_JP') {
        return;
    }

    $lines = explode("\n", $body);
    $body = '';
    $PreviousSpaces = '';
    $cnt = count($lines);
    for ($i = 0; $i < $cnt; $i ++) {
        preg_match("/^([\t >]*)([^\t >].*)?$/", $lines[$i], $regs);
        $CurrentSpaces = $regs[1];
        if (isset($regs[2])) {
            $CurrentRest = $regs[2];
        } else {
            $CurrentRest = '';
        }

        if ($i == 0) {
            $PreviousSpaces = $CurrentSpaces;
            $body = $lines[$i];
        } else if (($PreviousSpaces == $CurrentSpaces) /* Do the beginnings match */
                   && (strlen($lines[$i - 1]) > 65)    /* Over 65 characters long */
                   && strlen($CurrentRest)) {          /* and there's a line to continue with */
            $body .= ' ' . $CurrentRest;
        } else {
            $body .= "\n" . $lines[$i];
            $PreviousSpaces = $CurrentSpaces;
        }
    }
    $body .= "\n";
}

/**
  * Truncates the given string so that it has at
  * most $max_chars characters.  NOTE that a "character"
  * may be a multibyte character, or (optionally), an
  * HTML entity, so this function is different than
  * using substr() or mb_substr().
  * 
  * NOTE that if $elipses is given and used, the returned
  *      number of characters will be $max_chars PLUS the
  *      length of $elipses
  * 
  * @param string  $string    The string to truncate
  * @param int     $max_chars The maximum allowable characters
  * @param string  $elipses   A string that will be added to
  *                           the end of the truncated string
  *                           (ONLY if it is truncated) (OPTIONAL;
  *                           default not used)
  * @param boolean $html_entities_as_chars Whether or not to keep
  *                                        HTML entities together
  *                                        (OPTIONAL; default ignore
  *                                        HTML entities)
  *
  * @return string The truncated string
  *
  * @since 1.4.20 and 1.5.2 (replaced truncateWithEntities())
  *
  */
function sm_truncate_string($string, $max_chars, $elipses='',
                            $html_entities_as_chars=FALSE)
{

   // if the length of the string is less than
   // the allowable number of characters, just
   // return it as is (even if it contains any
   // HTML entities, that would just make the
   // actual length even smaller)
   //
   $actual_strlen = sq_strlen($string, 'auto');
   if ($max_chars <= 0 || $actual_strlen <= $max_chars)
      return $string;


   // if needed, count the number of HTML entities in
   // the string up to the maximum character limit,
   // pushing that limit up for each entity found
   //
   $adjusted_max_chars = $max_chars;
   if ($html_entities_as_chars)
   {

      // $loop_count is needed to prevent an endless loop
      // which is caused by buggy mbstring versions that
      // return 0 (zero) instead of FALSE in some rare
      // cases.  Thanks, PHP.
      // see: http://bugs.php.net/bug.php?id=52731
      // also: tracker $3053349
      //
      $loop_count = 0;
      $entity_pos = $entity_end_pos = -1;
      while ($entity_end_pos + 1 < $actual_strlen
          && ($entity_pos = sq_strpos($string, '&', $entity_end_pos + 1)) !== FALSE
          && ($entity_end_pos = sq_strpos($string, ';', $entity_pos)) !== FALSE
          && $entity_pos <= $adjusted_max_chars
          && $loop_count++ < $max_chars)
      {
         $adjusted_max_chars += $entity_end_pos - $entity_pos;
      }


      // this isn't necessary because sq_substr() would figure this
      // out anyway, but we can avoid a sq_substr() call and we
      // know that we don't have to add an elipses (this is now
      // an accurate comparison, since $adjusted_max_chars, like
      // $actual_strlen, does not take into account HTML entities)
      //
      if ($actual_strlen <= $adjusted_max_chars)
         return $string;

   }


   // get the truncated string
   //
   $truncated_string = sq_substr($string, 0, $adjusted_max_chars);


   // return with added elipses
   //
   return $truncated_string . $elipses;

}

/**
 * If $haystack is a full mailbox name and $needle is the mailbox
 * separator character, returns the last part of the mailbox name.
 *
 * @param string haystack full mailbox name to search
 * @param string needle the mailbox separator character
 * @return string the last part of the mailbox name
 */
function readShortMailboxName($haystack, $needle) {

    if ($needle == '') {
        $elem = $haystack;
    } else {
        $parts = explode($needle, $haystack);
        $elem = array_pop($parts);
        while ($elem == '' && count($parts)) {
            $elem = array_pop($parts);
        }
    }
    return( $elem );
}

/**
 * php_self
 *
 * Attempts to determine the path and filename and any arguments
 * for the currently executing script.  This is usually found in
 * $_SERVER['REQUEST_URI'], but some environments may differ, so
 * this function tries to standardize this value.
 *
 * @since 1.2.3
 * @return string The path, filename and any arguments for the
 *                current script
 */
function php_self() {

    $request_uri = '';

    // first try $_SERVER['PHP_SELF'], which seems most reliable
    // (albeit it usually won't include the query string)
    //
    $request_uri = '';
    if (!sqgetGlobalVar('PHP_SELF', $request_uri, SQ_SERVER)
     || empty($request_uri)) {

        // well, then let's try $_SERVER['REQUEST_URI']
        //
        $request_uri = '';
        if (!sqgetGlobalVar('REQUEST_URI', $request_uri, SQ_SERVER)
         || empty($request_uri)) {

            // TODO: anyone have any other ideas?  maybe $_SERVER['SCRIPT_NAME']???
            //
            return '';
        }

    }

    // we may or may not have any query arguments, depending on
    // which environment variable was used above, and the PHP
    // version, etc., so let's check for it now
    //
    $query_string = '';
    if (strpos($request_uri, '?') === FALSE
     && sqgetGlobalVar('QUERY_STRING', $query_string, SQ_SERVER)
     && !empty($query_string)) {

        $request_uri .= '?' . $query_string;
    }

    return $request_uri;

}


/**
 * Find out where squirrelmail lives and try to be smart about it.
 * The only problem would be when squirrelmail lives in directories
 * called "src", "functions", or "plugins", but people who do that need
 * to be beaten with a steel pipe anyway.
 *
 * @return string the base uri of squirrelmail installation.
 */
function sqm_baseuri(){
    global $base_uri, $PHP_SELF;
    /**
     * If it is in the session, just return it.
     */
    if (sqgetGlobalVar('base_uri',$base_uri,SQ_SESSION)){
        return $base_uri;
    }
    $dirs = array('|src/.*|', '|plugins/.*|', '|functions/.*|');
    $repl = array('', '', '');
    $base_uri = preg_replace($dirs, $repl, $PHP_SELF);
    return $base_uri;
}

/**
 * get_location
 *
 * Determines the location to forward to, relative to your server.
 * This is used in HTTP Location: redirects.
 * If set, it uses $config_location_base as the first part of the URL,
 * specifically, the protocol, hostname and port parts. The path is
 * always autodetected.
 *
 * @return string the base url for this SquirrelMail installation
 */
function get_location () {

    global $imap_server_type, $config_location_base,
           $is_secure_connection, $sq_ignore_http_x_forwarded_headers;

    /* Get the path, handle virtual directories */
    if(strpos(php_self(), '?')) {
        $path = substr(php_self(), 0, strpos(php_self(), '?'));
    } else {
        $path = php_self();
    }
    $path = substr($path, 0, strrpos($path, '/'));

    // proto+host+port are already set in config:
    if ( !empty($config_location_base) ) {
        // register it in the session just in case some plugin depends on this
        sqsession_register($config_location_base . $path, 'sq_base_url');
        return $config_location_base . $path ;
    }
    // we computed it before, get it from the session:
    if ( sqgetGlobalVar('sq_base_url', $full_url, SQ_SESSION) ) {
        return $full_url . $path;
    }
    // else: autodetect

    /* Check if this is a HTTPS or regular HTTP request. */
    $proto = 'http://';
    if ($is_secure_connection)
        $proto = 'https://';

    /* Get the hostname from the Host header or server config. */
    if ($sq_ignore_http_x_forwarded_headers
     || !sqgetGlobalVar('HTTP_X_FORWARDED_HOST', $host, SQ_SERVER)
     || empty($host)) {
        if ( !sqgetGlobalVar('HTTP_HOST', $host, SQ_SERVER) || empty($host) ) {
            if ( !sqgetGlobalVar('SERVER_NAME', $host, SQ_SERVER) || empty($host) ) {
                $host = '';
            }
        }
    }

    $port = '';
    if (strpos($host, ':') === FALSE) {
        // Note: HTTP_X_FORWARDED_PROTO could be sent from the client and
        //       therefore possibly spoofed/hackable - for now, the
        //       administrator can tell SM to ignore this value by setting
        //       $sq_ignore_http_x_forwarded_headers to boolean TRUE in
        //       config/config_local.php, but in the future we may
        //       want to default this to TRUE and make administrators
        //       who use proxy systems turn it off (see 1.5.2+).
        global $sq_ignore_http_x_forwarded_headers;
        if ($sq_ignore_http_x_forwarded_headers
         || !sqgetGlobalVar('HTTP_X_FORWARDED_PROTO', $forwarded_proto, SQ_SERVER))
            $forwarded_proto = '';
        if (sqgetGlobalVar('SERVER_PORT', $server_port, SQ_SERVER)) {
            if (($server_port != 80 && $proto == 'http://') ||
                ($server_port != 443 && $proto == 'https://' &&
                 strcasecmp($forwarded_proto, 'https') !== 0)) {
                $port = sprintf(':%d', $server_port);
            }
        }
    }

   /* this is a workaround for the weird macosx caching that
      causes Apache to return 16080 as the port number, which causes
      SM to bail */

   if ($imap_server_type == 'macosx' && $port == ':16080') {
        $port = '';
   }

   /* Fallback is to omit the server name and use a relative */
   /* URI, although this is not RFC 2616 compliant.          */
   $full_url = ($host ? $proto . $host . $port : '');
   sqsession_register($full_url, 'sq_base_url');
   return $full_url . $path;
}


/**
 * Encrypts password
 *
 * These functions are used to encrypt the password before it is
 * stored in a cookie. The encryption key is generated by
 * OneTimePadCreate();
 *
 * @param string string the (password)string to encrypt
 * @param string epad the encryption key
 * @return string the base64-encoded encrypted password
 */
function OneTimePadEncrypt ($string, $epad) {
    $pad = base64_decode($epad);

    if (strlen($pad)>0) {
        // make sure that pad is longer than string
        while (strlen($string)>strlen($pad)) {
            $pad.=$pad;
        }
    } else {
        // FIXME: what should we do when $epad is not base64 encoded or empty.
    }

    $encrypted = '';
    for ($i = 0; $i < strlen ($string); $i++) {
        $encrypted .= chr (ord($string[$i]) ^ ord($pad[$i]));
    }

    return base64_encode($encrypted);
}

/**
 * Decrypts a password from the cookie
 *
 * Decrypts a password from the cookie, encrypted by OneTimePadEncrypt.
 * This uses the encryption key that is stored in the session.
 *
 * @param string string the string to decrypt
 * @param string epad the encryption key from the session
 * @return string the decrypted password
 */
function OneTimePadDecrypt ($string, $epad) {
    $pad = base64_decode($epad);

    if (strlen($pad)>0) {
        // make sure that pad is longer than string
        while (strlen($string)>strlen($pad)) {
            $pad.=$pad;
        }
    } else {
        // FIXME: what should we do when $epad is not base64 encoded or empty.
    }

    $encrypted = base64_decode ($string);
    $decrypted = '';
    for ($i = 0; $i < strlen ($encrypted); $i++) {
        $decrypted .= chr (ord($encrypted[$i]) ^ ord($pad[$i]));
    }

    return $decrypted;
}


/**
 * Randomizes the mt_rand() function.
 *
 * Toss this in strings or integers and it will seed the generator
 * appropriately. With strings, it is better to get them long.
 * Use md5() to lengthen smaller strings.
 *
 * @param mixed val a value to seed the random number generator
 * @return void
 */
function sq_mt_seed($Val) {
    /* if mt_getrandmax() does not return a 2^n - 1 number,
       this might not work well.  This uses $Max as a bitmask. */
    $Max = mt_getrandmax();

    if (! is_int($Val)) {
            $Val = crc32($Val);
    }

    if ($Val < 0) {
        $Val *= -1;
    }

    if ($Val == 0) {
        return;
    }

    mt_srand(($Val ^ mt_rand(0, $Max)) & $Max);
}


/**
 * Init random number generator
 *
 * This function initializes the random number generator fairly well.
 * It also only initializes it once, so you don't accidentally get
 * the same 'random' numbers twice in one session.
 *
 * @return void
 */
function sq_mt_randomize() {
    static $randomized;

    if ($randomized) {
        return;
    }

    /* Global. */
    sqgetGlobalVar('REMOTE_PORT', $remote_port, SQ_SERVER);
    sqgetGlobalVar('REMOTE_ADDR', $remote_addr, SQ_SERVER);
    sq_mt_seed((int)((double) microtime() * 1000000));
    sq_mt_seed(md5($remote_port . $remote_addr . getmypid()));

    /* getrusage */
    if (function_exists('getrusage')) {
        /* Avoid warnings with Win32 */
        $dat = @getrusage();
        if (isset($dat) && is_array($dat)) {
            $Str = '';
            foreach ($dat as $k => $v)
                {
                    $Str .= $k . $v;
                }
            sq_mt_seed(md5($Str));
        }
    }

    if(sqgetGlobalVar('UNIQUE_ID', $unique_id, SQ_SERVER)) {
        sq_mt_seed(md5($unique_id));
    }

    $randomized = 1;
}

/**
 * Creates encryption key
 *
 * Creates an encryption key for encrypting the password stored in the cookie.
 * The encryption key itself is stored in the session.
 *
 * @param int length optional, length of the string to generate
 * @return string the encryption key
 */
function OneTimePadCreate ($length=100) {
    sq_mt_randomize();

    $pad = '';
    for ($i = 0; $i < $length; $i++) {
        $pad .= chr(mt_rand(0,255));
    }

    return base64_encode($pad);
}

/**
 * Returns a string showing the size of the message/attachment.
 *
 * @param int bytes the filesize in bytes
 * @return string the filesize in human readable format
 */
function show_readable_size($bytes) {
    $bytes /= 1024;
    $type = 'k';

    if ($bytes / 1024 > 1) {
        $bytes /= 1024;
        $type = 'M';
    }

    if ($bytes < 10) {
        $bytes *= 10;
        settype($bytes, 'integer');
        $bytes /= 10;
    } else {
        settype($bytes, 'integer');
    }

    return $bytes . '<small>&nbsp;' . $type . '</small>';
}

/**
 * Generates a random string from the caracter set you pass in
 *
 * @param int size the size of the string to generate
 * @param string chars a string containing the characters to use
 * @param int flags a flag to add a specific set to the characters to use:
 *     Flags:
 *       1 = add lowercase a-z to $chars
 *       2 = add uppercase A-Z to $chars
 *       4 = add numbers 0-9 to $chars
 * @return string the random string
 */
function GenerateRandomString($size, $chars, $flags = 0) {
    if ($flags & 0x1) {
        $chars .= 'abcdefghijklmnopqrstuvwxyz';
    }
    if ($flags & 0x2) {
        $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    if ($flags & 0x4) {
        $chars .= '0123456789';
    }

    if (($size < 1) || (strlen($chars) < 1)) {
        return '';
    }

    sq_mt_randomize(); /* Initialize the random number generator */

    $String = '';
    $j = strlen( $chars ) - 1;
    while (strlen($String) < $size) {
        $String .= $chars{mt_rand(0, $j)};
    }

    return $String;
}

/**
 * Escapes special characters for use in IMAP commands.
 *
 * @param string the string to escape
 * @return string the escaped string
 */
function quoteimap($str) {
    // FIXME use this performance improvement (not changing because this is STABLE branch): return str_replace(array('\\', '"'), array('\\\\', '\\"'), $str);
    return preg_replace("/([\"\\\\])/", "\\\\$1", $str);
}

/**
 * Trims array
 *
 * Trims every element in the array, ie. remove the first char of each element
 * Obsolete: will probably removed soon
 * @param array array the array to trim
 * @obsolete
 */
function TrimArray(&$array) {
    foreach ($array as $k => $v) {
        global $$k;
        if (is_array($$k)) {
            foreach ($$k as $k2 => $v2) {
                $$k[$k2] = substr($v2, 1);
            }
        } else {
            $$k = substr($v, 1);
        }

        /* Re-assign back to array. */
        $array[$k] = $$k;
    }
}

/**
 * Removes slashes from every element in the array
 */
function RemoveSlashes(&$array) {
    foreach ($array as $k => $v) {
        global $$k;
        if (is_array($$k)) {
            foreach ($$k as $k2 => $v2) {
                $newArray[stripslashes($k2)] = stripslashes($v2);
            }
            $$k = $newArray;
        } else {
            $$k = stripslashes($v);
        }

        /* Re-assign back to the array. */
        $array[$k] = $$k;
    }
}

/**
 * Create compose link
 *
 * Returns a link to the compose-page, taking in consideration
 * the compose_in_new and javascript settings.
 * @param string url the URL to the compose page
 * @param string text the link text, default "Compose"
 * @return string a link to the compose page
 */
function makeComposeLink($url, $text = null, $target='')
{
    global $compose_new_win,$javascript_on;

    if(!$text) {
        $text = _("Compose");
    }


    // if not using "compose in new window", make
    // regular link and be done with it
    if($compose_new_win != '1') {
        return makeInternalLink($url, $text, $target);
    }


    // build the compose in new window link...


    // if javascript is on, use onClick event to handle it
    if($javascript_on) {
        sqgetGlobalVar('base_uri', $base_uri, SQ_SESSION);
        return '<a href="javascript:void(0)" onclick="comp_in_new(\''.$base_uri.$url.'\')">'. $text.'</a>';
    }


    // otherwise, just open new window using regular HTML
    return makeInternalLink($url, $text, '_blank');

}

/**
 * Print variable
 *
 * sm_print_r($some_variable, [$some_other_variable [, ...]]);
 *
 * Debugging function - does the same as print_r, but makes sure special
 * characters are converted to htmlentities first.  This will allow
 * values like <some@email.address> to be displayed.
 * The output is wrapped in <<pre>> and <</pre>> tags.
 *
 * @return void
 */
function sm_print_r() {
    ob_start();  // Buffer output
    foreach(func_get_args() as $var) {
        print_r($var);
        echo "\n";
    }
    $buffer = ob_get_contents(); // Grab the print_r output
    ob_end_clean();  // Silently discard the output & stop buffering
    print '<pre>';
    print htmlentities($buffer);
    print '</pre>';
}

/**
 * version of fwrite which checks for failure
 */
function sq_fwrite($fp, $string) {
        // write to file
        $count = @fwrite($fp,$string);
        // the number of bytes written should be the length of the string
        if($count != strlen($string)) {
                return FALSE;
        }

        return $count;
}
/**
 * Tests if string contains 8bit symbols.
 *
 * If charset is not set, function defaults to default_charset.
 * $default_charset global must be set correctly if $charset is
 * not used.
 * @param string $string tested string
 * @param string $charset charset used in a string
 * @return bool true if 8bit symbols are detected
 * @since 1.5.1 and 1.4.4
 */
function sq_is8bit($string,$charset='') {
    global $default_charset;

    if ($charset=='') $charset=$default_charset;

    /**
     * Don't use \240 in ranges. Sometimes RH 7.2 doesn't like it.
     * Don't use \200-\237 for iso-8859-x charsets. This ranges
     * stores control symbols in those charsets.
     * Use preg_match instead of ereg in order to avoid problems
     * with mbstring overloading
     */
    if (preg_match("/^iso-8859/i",$charset)) {
        $needle='/\240|[\241-\377]/';
    } else {
        $needle='/[\200-\237]|\240|[\241-\377]/';
    }
    return preg_match("$needle",$string);
}

/**
 * Function returns number of characters in string.
 *
 * Returned number might be different from number of bytes in string,
 * if $charset is multibyte charset. Detection depends on mbstring
 * functions. If mbstring does not support tested multibyte charset,
 * vanilla string length function is used.
 * @param string $str string
 * @param string $charset charset
 * @since 1.5.1 and 1.4.6
 * @return integer number of characters in string
 */
function sq_strlen($string, $charset=NULL){

   // NULL charset?  Just use strlen()
   //
   if (is_null($charset))
      return strlen($string);


   // use current character set?
   //
   if ($charset == 'auto')
   {
//FIXME: this may or may not be better as a session value instead of a global one
      global $sq_string_func_auto_charset;
      if (!isset($sq_string_func_auto_charset))
      {
         global $default_charset, $squirrelmail_language;
         set_my_charset();
         $sq_string_func_auto_charset = $default_charset;
         if ($squirrelmail_language == 'ja_JP') $sq_string_func_auto_charset = 'euc-jp';
      }
      $charset = $sq_string_func_auto_charset;
   }


   // standardize character set name
   //
   $charset = strtolower($charset);


/* ===== FIXME: this list is not used in 1.5.x, but if we need it, unless this differs between all our string function wrappers, we should store this info in the session
   // only use mbstring with the following character sets
   //
   $sq_strlen_mb_charsets = array(
      'utf-8',
      'big5',
      'gb2312',
      'gb18030',
      'euc-jp',
      'euc-cn',
      'euc-tw',
      'euc-kr'
   );


   // now we can use mb_strlen() if needed
   //
   if (in_array($charset, $sq_strlen_mb_charsets)
    && in_array($charset, sq_mb_list_encodings()))
===== */
//FIXME: is there any reason why this cannot be a static global array used by all string wrapper functions?
   if (in_array($charset, sq_mb_list_encodings()))
      return mb_strlen($string, $charset);


   // else use normal strlen()
   //
   return strlen($string);

}

/**
  * This is a replacement for PHP's strpos() that is
  * multibyte-aware.
  *
  * @param string $haystack The string to search within
  * @param string $needle   The substring to search for
  * @param int    $offset   The offset from the beginning of $haystack
  *                         from which to start searching
  *                         (OPTIONAL; default none)
  * @param string $charset  The charset of the given string.  A value of NULL
  *                         here will force the use of PHP's standard strpos().
  *                         (OPTIONAL; default is "auto", which indicates that
  *                         the user's current charset should be used).
  *
  * @return mixed The integer offset of the next $needle in $haystack,
  *               if found, or FALSE if not found
  *
  */
function sq_strpos($haystack, $needle, $offset=0, $charset='auto')
{

   // NULL charset?  Just use strpos()
   //
   if (is_null($charset))
      return strpos($haystack, $needle, $offset);


   // use current character set?
   //
   if ($charset == 'auto')
   {
//FIXME: this may or may not be better as a session value instead of a global one
      global $sq_string_func_auto_charset;
      if (!isset($sq_string_func_auto_charset))
      {
         global $default_charset, $squirrelmail_language;
         set_my_charset();
         $sq_string_func_auto_charset = $default_charset;
         if ($squirrelmail_language == 'ja_JP') $sq_string_func_auto_charset = 'euc-jp';
      }
      $charset = $sq_string_func_auto_charset;
   }


   // standardize character set name
   //
   $charset = strtolower($charset);


/* ===== FIXME: this list is not used in 1.5.x, but if we need it, unless this differs between all our string function wrappers, we should store this info in the session
   // only use mbstring with the following character sets
   //
   $sq_strpos_mb_charsets = array(
      'utf-8',
      'big5',
      'gb2312',
      'gb18030',
      'euc-jp',
      'euc-cn',
      'euc-tw',
      'euc-kr'
   );


   // now we can use mb_strpos() if needed
   //
   if (in_array($charset, $sq_strpos_mb_charsets)
    && in_array($charset, sq_mb_list_encodings()))
===== */
//FIXME: is there any reason why this cannot be a static global array used by all string wrapper functions?
   if (in_array($charset, sq_mb_list_encodings()))
       return mb_strpos($haystack, $needle, $offset, $charset);


   // else use normal strpos()
   //
   return strpos($haystack, $needle, $offset);

}

/**
  * This is a replacement for PHP's substr() that is
  * multibyte-aware.
  *
  * @param string $string  The string to operate upon
  * @param int    $start   The offset at which to begin substring extraction
  * @param int    $length  The number of characters after $start to return
  *                        NOTE that if you need to specify a charset but
  *                        want to achieve normal substr() behavior where
  *                        $length is not specified, use NULL (OPTIONAL;
  *                        default from $start to end of string)
  * @param string $charset The charset of the given string.  A value of NULL
  *                        here will force the use of PHP's standard substr().
  *                        (OPTIONAL; default is "auto", which indicates that
  *                        the user's current charset should be used).
  *
  * @return string The desired substring
  *
  * Of course, you can use more advanced (e.g., negative) values
  * for $start and $length as needed - see the PHP manual for more
  * information:  http://www.php.net/manual/function.substr.php
  *
  */
function sq_substr($string, $start, $length=NULL, $charset='auto')
{

   // if $length is NULL, use the full string length...
   // we have to do this to mimick the use of substr()
   // where $length is not given
   //
   if (is_null($length))
      $length = sq_strlen($length, $charset);

   
   // NULL charset?  Just use substr()
   //
   if (is_null($charset))
      return substr($string, $start, $length);


   // use current character set?
   //
   if ($charset == 'auto')
   {
//FIXME: this may or may not be better as a session value instead of a global one
      global $sq_string_func_auto_charset;
      if (!isset($sq_string_func_auto_charset))
      {
         global $default_charset, $squirrelmail_language;
         set_my_charset();
         $sq_string_func_auto_charset = $default_charset;
         if ($squirrelmail_language == 'ja_JP') $sq_string_func_auto_charset = 'euc-jp';
      }
      $charset = $sq_string_func_auto_charset;
   }


   // standardize character set name
   //
   $charset = strtolower($charset);


/* ===== FIXME: this list is not used in 1.5.x, but if we need it, unless this differs between all our string function wrappers, we should store this info in the session
   // only use mbstring with the following character sets
   //
   $sq_substr_mb_charsets = array(
      'utf-8',
      'big5',
      'gb2312',
      'gb18030',
      'euc-jp',
      'euc-cn',
      'euc-tw',
      'euc-kr'
   );


   // now we can use mb_substr() if needed
   //
   if (in_array($charset, $sq_substr_mb_charsets)
    && in_array($charset, sq_mb_list_encodings()))
===== */
//FIXME: is there any reason why this cannot be a global array used by all string wrapper functions?
   if (in_array($charset, sq_mb_list_encodings()))
      return mb_substr($string, $start, $length, $charset);


   // else use normal substr()
   //
   return substr($string, $start, $length);

}

/**
  * This is a replacement for PHP's substr_replace() that is
  * multibyte-aware.
  *
  * @param string $string      The string to operate upon
  * @param string $replacement The string to be inserted
  * @param int    $start       The offset at which to begin substring replacement
  * @param int    $length      The number of characters after $start to remove
  *                            NOTE that if you need to specify a charset but
  *                            want to achieve normal substr_replace() behavior
  *                            where $length is not specified, use NULL (OPTIONAL;
  *                            default from $start to end of string)
  * @param string $charset     The charset of the given string.  A value of NULL
  *                            here will force the use of PHP's standard substr().
  *                            (OPTIONAL; default is "auto", which indicates that
  *                            the user's current charset should be used).
  *
  * @return string The manipulated string
  *
  * Of course, you can use more advanced (e.g., negative) values
  * for $start and $length as needed - see the PHP manual for more
  * information:  http://www.php.net/manual/function.substr-replace.php
  *
  */
function sq_substr_replace($string, $replacement, $start, $length=NULL,
                           $charset='auto')
{

   // NULL charset?  Just use substr_replace()
   //
   if (is_null($charset))
      return is_null($length) ? substr_replace($string, $replacement, $start)
                              : substr_replace($string, $replacement, $start, $length);


   // use current character set?
   //
   if ($charset == 'auto')
   {
//FIXME: this may or may not be better as a session value instead of a global one
      $charset = $auto_charset;
      global $sq_string_func_auto_charset;
      if (!isset($sq_string_func_auto_charset))
      {
         global $default_charset, $squirrelmail_language;
         set_my_charset();
         $sq_string_func_auto_charset = $default_charset;
         if ($squirrelmail_language == 'ja_JP') $sq_string_func_auto_charset = 'euc-jp';
      }
      $charset = $sq_string_func_auto_charset;
   }


   // standardize character set name
   //
   $charset = strtolower($charset);


/* ===== FIXME: this list is not used in 1.5.x, but if we need it, unless this differs between all our string function wrappers, we should store this info in the session
   // only use mbstring with the following character sets
   //
   $sq_substr_replace_mb_charsets = array(
      'utf-8',
      'big5',
      'gb2312',
      'gb18030',
      'euc-jp',
      'euc-cn',
      'euc-tw',
      'euc-kr'
   );


   // now we can use our own implementation using
   // mb_substr() and mb_strlen() if needed
   //
   if (in_array($charset, $sq_substr_replace_mb_charsets)
    && in_array($charset, sq_mb_list_encodings()))
===== */
//FIXME: is there any reason why this cannot be a global array used by all string wrapper functions?
   if (in_array($charset, sq_mb_list_encodings()))
   {

      $string_length = mb_strlen($string, $charset);

      if ($start < 0)
         $start = max(0, $string_length + $start);

      else if ($start > $string_length)
         $start = $string_length;

      if ($length < 0)
         $length = max(0, $string_length - $start + $length);

      else if (is_null($length) || $length > $string_length)
         $length = $string_length;

      if ($start + $length > $string_length)
         $length = $string_length - $start;

      return mb_substr($string, 0, $start, $charset)
           . $replacement
           . mb_substr($string,
                       $start + $length,
                       $string_length, // FIXME: I can't see why this is needed:  - $start - $length,
                       $charset);

   }


   // else use normal substr_replace()
   //
   return is_null($length) ? substr_replace($string, $replacement, $start)
                           : substr_replace($string, $replacement, $start, $length);

}

/**
 * Replacement of mb_list_encodings function
 *
 * This function provides replacement for function that is available only
 * in php 5.x. Function does not test all mbstring encodings. Only the ones
 * that might be used in SM translations.
 *
 * Supported strings are stored in session in order to reduce number of
 * mb_internal_encoding function calls.
 *
 * If mb_list_encodings() function is present, code uses it. Main difference
 * from original function behaviour - array values are lowercased in order to
 * simplify use of returned array in in_array() checks.
 *
 * If you want to test all mbstring encodings - fill $list_of_encodings
 * array.
 * @return array list of encodings supported by php mbstring extension
 * @since 1.5.1 and 1.4.6
 */
function sq_mb_list_encodings() {

    // if it's already in the session, don't need to regenerate it
    if (sqgetGlobalVar('mb_supported_encodings',$mb_supported_encodings,SQ_SESSION)
     && is_array($mb_supported_encodings))
        return $mb_supported_encodings;

    // check if mbstring extension is present
    if (! function_exists('mb_internal_encoding')) {
        $supported_encodings = array();
        sqsession_register($supported_encodings, 'mb_supported_encodings');
        return $supported_encodings;
    }

    // php 5+ function
    if (function_exists('mb_list_encodings')) {
        $supported_encodings = mb_list_encodings();
        array_walk($supported_encodings, 'sq_lowercase_array_vals');
        sqsession_register($supported_encodings, 'mb_supported_encodings');
        return $supported_encodings;
    }

    // save original encoding
    $orig_encoding=mb_internal_encoding();

    $list_of_encoding=array(
        'pass',
        'auto',
        'ascii',
        'jis',
        'utf-8',
        'sjis',
        'euc-jp',
        'iso-8859-1',
        'iso-8859-2',
        'iso-8859-7',
        'iso-8859-9',
        'iso-8859-15',
        'koi8-r',
        'koi8-u',
        'big5',
        'gb2312',
        'gb18030',
        'windows-1251',
        'windows-1255',
        'windows-1256',
        'tis-620',
        'iso-2022-jp',
        'euc-cn',
        'euc-kr',
        'euc-tw',
        'uhc',
        'utf7-imap');

    $supported_encodings=array();

    foreach ($list_of_encoding as $encoding) {
        // try setting encodings. suppress warning messages
        if (@mb_internal_encoding($encoding))
            $supported_encodings[]=$encoding;
    }

    // restore original encoding
    mb_internal_encoding($orig_encoding);

    // register list in session
    sqsession_register($supported_encodings, 'mb_supported_encodings');

    return $supported_encodings;
}

/**
 * Callback function used to lowercase array values.
 * @param string $val array value
 * @param mixed $key array key
 * @since 1.5.1 and 1.4.6
 */
function sq_lowercase_array_vals(&$val,$key) {
    $val = strtolower($val);
}

/**
 * Callback function to trim whitespace from a value, to be used in array_walk
 * @param string $value value to trim
 * @since 1.5.2 and 1.4.7
 */
function sq_trim_value ( &$value ) {
    $value = trim($value);
}

/**
  * Gathers the list of secuirty tokens currently
  * stored in the user's preferences and optionally
  * purges old ones from the list.
  *
  * @param boolean $purge_old Indicates if old tokens
  *                           should be purged from the
  *                           list ("old" is 2 days or
  *                           older unless the administrator
  *                           overrides that value using
  *                           $max_token_age_days in
  *                           config/config_local.php)
  *                           (OPTIONAL; default is to always
  *                           purge old tokens)
  *
  * @return array The list of tokens
  *
  * @since 1.4.19 and 1.5.2
  *
  */
function sm_get_user_security_tokens($purge_old=TRUE)
{

   global $data_dir, $username, $max_token_age_days;

   $tokens = getPref($data_dir, $username, 'security_tokens', '');
   if (($tokens = unserialize($tokens)) === FALSE || !is_array($tokens))
      $tokens = array();

   // purge old tokens if necessary
   //
   if ($purge_old)
   {
      if (empty($max_token_age_days)) $max_token_age_days = 2;
      $now = time();
      $discard_token_date = $now - ($max_token_age_days * 86400);
      $cleaned_tokens = array();
      foreach ($tokens as $token => $timestamp)
         if ($timestamp >= $discard_token_date)
            $cleaned_tokens[$token] = $timestamp;
      $tokens = $cleaned_tokens;
   }

   return $tokens;

}

/**
  * Generates a security token that is then stored in
  * the user's preferences with a timestamp for later
  * verification/use.
  *
  * WARNING: If the administrator has turned the token system
  *          off by setting $disable_security_tokens to TRUE in
  *          config/config.php or the configuration tool, this
  *          function will not store tokens in the user
  *          preferences (but it will still generate and return
  *          a random string).
  *
  * @return string A security token
  *
  * @since 1.4.19 and 1.5.2
  *
  */
function sm_generate_security_token()
{

   global $data_dir, $username, $disable_security_tokens;
   $max_generation_tries = 1000;

   $tokens = sm_get_user_security_tokens();

   $new_token = GenerateRandomString(12, '', 7);
   $count = 0;
   while (isset($tokens[$new_token]))
   {
      $new_token = GenerateRandomString(12, '', 7);
      if (++$count > $max_generation_tries)
      {
         logout_error(_("Fatal token generation error; please contact your system administrator or the SquirrelMail Team"));
         exit;
      }
   }

   // is the token system enabled?  CAREFUL!
   //
   if (!$disable_security_tokens)
   {
      $tokens[$new_token] = time();
      setPref($data_dir, $username, 'security_tokens', serialize($tokens));
   }

   return $new_token;

}

/**
  * Validates a given security token and optionally remove it
  * from the user's preferences if it was valid.  If the token
  * is too old but otherwise valid, it will still be rejected.
  *
  * "Too old" is 2 days or older unless the administrator
  * overrides that value using $max_token_age_days in
  * config/config_local.php
  *
  * WARNING: If the administrator has turned the token system
  *          off by setting $disable_security_tokens to TRUE in
  *          config/config.php or the configuration tool, this
  *          function will always return TRUE.
  *
  * @param string  $token           The token to validate
  * @param int     $validity_period The number of seconds tokens are valid
  *                                 for (set to zero to remove valid tokens
  *                                 after only one use; use 3600 to allow
  *                                 tokens to be reused for an hour)
  *                                 (OPTIONAL; default is to only allow tokens
  *                                 to be used once)
  * @param boolean $show_error      Indicates that if the token is not
  *                                 valid, this function should display
  *                                 a generic error, log the user out
  *                                 and exit - this function will never
  *                                 return in that case.
  *                                 (OPTIONAL; default FALSE)
  *
  * @return boolean TRUE if the token validated; FALSE otherwise
  *
  * @since 1.4.19 and 1.5.2
  *
  */
function sm_validate_security_token($token, $validity_period=0, $show_error=FALSE)
{

   global $data_dir, $username, $max_token_age_days,
          $disable_security_tokens;

   // bypass token validation?  CAREFUL!
   //
   if ($disable_security_tokens) return TRUE;

   // don't purge old tokens here because we already
   // do it when generating tokens
   //
   $tokens = sm_get_user_security_tokens(FALSE);

   // token not found?
   //
   if (empty($tokens[$token]))
   {
      if (!$show_error) return FALSE;
      logout_error(_("This page request could not be verified and appears to have expired."));
      exit;
   }

   $now = time();
   $timestamp = $tokens[$token];

   // whether valid or not, we want to remove it from
   // user prefs if it's old enough
   //
   if ($timestamp < $now - $validity_period)
   {
      unset($tokens[$token]);
      setPref($data_dir, $username, 'security_tokens', serialize($tokens));
   }

   // reject tokens that are too old
   //
   if (empty($max_token_age_days)) $max_token_age_days = 2;
   $old_token_date = $now - ($max_token_age_days * 86400);
   if ($timestamp < $old_token_date)
   {
      if (!$show_error) return FALSE;
      logout_error(_("The current page request appears to have originated from an untrusted source."));
      exit;
   }

   // token OK!
   //
   return TRUE;

}

$PHP_SELF = php_self();
