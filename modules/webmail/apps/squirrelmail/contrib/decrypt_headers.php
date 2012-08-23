<?php

/**
 * Script provides form to decode encrypted header information.
 *
 * @copyright 2005-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: decrypt_headers.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/**
 * Set constant to path of your SquirrelMail install. 
 * @ignore
 */
define('SM_PATH','../');

/**
 * include SquirrelMail string and generic functions
 * script needs OneTimePadDecrypt() (functions/strings.php)
 * and sqgetGlobalVar() (functions/global.php)
 */
include_once(SM_PATH.'functions/global.php');
include_once(SM_PATH.'functions/strings.php');

/**
 * converts hex string to ip address
 * @param string $hex hexadecimal string created with squirrelmail ip2hex 
 *  function in delivery class.
 * @return string ip address
 * @since 1.5.1 and 1.4.5
 */
function hex2ip($hex) {
    if (strlen($hex)==8) {
        $ret=hexdec(substr($hex,0,2)).'.'
            .hexdec(substr($hex,2,2)).'.'
            .hexdec(substr($hex,4,2)).'.'
            .hexdec(substr($hex,6,2));
    } elseif (strlen($hex)==32) {
        $ret=substr($hex,0,4).':'
            .substr($hex,4,4).':'
            .substr($hex,8,4).':'
            .substr($hex,12,4).':'
            .substr($hex,16,4).':'
            .substr($hex,20,4).':'
            .substr($hex,24,4).':'
            .substr($hex,28,4);
    } else {
        $ret=$hex;
    }
    return $ret;
}

/** create page headers */
header('Content-Type: text/html');

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'
    ."\n<head>\n<meta name=\"robots\" content=\"noindex,nofollow\">\n"
    ."</head><body>";

if (sqgetGlobalVar('submit',$submit,SQ_POST)) {
    $continue = TRUE;
    if (! sqgetGlobalVar('secret',$secret,SQ_POST) ||
        empty($secret)) {
        $continue = FALSE;
        echo "<p>You must enter an encryption key.</p>\n";
    }
    if (! sqgetGlobalVar('enc_string',$enc_string,SQ_POST) ||
        empty($enc_string)) {
        $continue = FALSE;
        echo "<p>You must enter an encrypted string.</p>\n";
    }

    if ($continue) {
        if (isset($enc_string) && ! base64_decode($enc_string)) {
            echo "<p>Encrypted string should be BASE64 encoded.<br />\n"
                ."Please enter all characters that are listed after header name.</p>\n";
        } elseif (isset($secret)) {
            $string=OneTimePadDecrypt($enc_string,base64_encode($secret));

            if (sqgetGlobalVar('ip_addr',$is_addr,SQ_POST)) {
                $string=hex2ip($string);
            }
            echo "<p>Decoded string: ".htmlspecialchars($string)."</p>\n";
        }
    }
    echo "<hr />";
}
?>
<form action="<?php echo $PHP_SELF ?>" method="post" >
<p>
Secret key: <input type="password" name="secret"><br />
Encrypted string: <input type="text" name="enc_string"><br />
<label for="ip_addr">Check here if you are decoding an address string (FromHash/ProxyHash): </label><input type="checkbox" name="ip_addr" id="ip_addr" /><br />
<button type="submit" name="submit" value="submit">Submit</button>
</p>
</form>
</body></html>
