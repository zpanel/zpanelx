<?php

/**
 * SquirrelMail internationalization functions
 *
 * This file contains variuos functions that are needed to do
 * internationalization of SquirrelMail.
 *
 * Internally the output character set is used. Other characters are
 * encoded using Unicode entities according to HTML 4.0.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: i18n.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage i18n
 */

/** @ignore */
if (!defined('SM_PATH')) define('SM_PATH','../');

/** Everything uses global.php... */
require_once(SM_PATH . 'functions/global.php');

/**
 * Wrapper for textdomain(), bindtextdomain() and
 * bind_textdomain_codeset() primarily intended for
 * plugins when changing into their own text domain
 * and back again.
 *
 * Note that if plugins using this function have
 * their translation files located in the SquirrelMail
 * locale directory, the second argument is optional.
 *
 * @param string $domain_name The name of the text domain
 *                            (usually the plugin name, or
 *                            "squirrelmail") being switched to.
 * @param string $directory   The directory that contains
 *                            all translations for the domain
 *                            (OPTIONAL; default is SquirrelMail
 *                            locale directory).
 *
 * @return string The name of the text domain that was set
 *                *BEFORE* it is changed herein - NOTE that
 *                this differs from PHP's textdomain()
 *
 * @since 1.4.10 and 1.5.2
 */
function sq_change_text_domain($domain_name, $directory='') {
    global $use_gettext;
    static $domains_already_seen = array();

    $return_value = textdomain(NULL);

    // empty domain defaults to "squirrelmail"
    //
    if (empty($domain_name)) $domain_name = 'squirrelmail';

    // only need to call bindtextdomain() once unless
    // $use_gettext is turned on
    //
    if (!$use_gettext && in_array($domain_name, $domains_already_seen)) {
        textdomain($domain_name);
        return $return_value;
    }

    $domains_already_seen[] = $domain_name;

    if (empty($directory)) $directory = SM_PATH . 'locale/';

    sq_bindtextdomain($domain_name, $directory);
    textdomain($domain_name);

    return $return_value;
}

/**
 * Gettext bindtextdomain wrapper.
 *
 * Wrapper solves differences between php versions in order to provide
 * ngettext support. Should be used if translation uses ngettext
 * functions.
 *
 * This also provides a bind_textdomain_codeset call to make sure the
 * domain's encoding will not be overridden.
 *
 * @since 1.4.10 and 1.5.1
 * @param string $domain gettext domain name
 * @param string $dir directory that contains all translations (OPTIONAL;
 *                    if not specified, defaults to SquirrelMail locale
 *                    directory)
 * @return string path to translation directory
 */
function sq_bindtextdomain($domain,$dir='') {
    global $languages, $sm_notAlias;

    if (empty($dir)) $dir = SM_PATH . 'locale/';

    $dir = bindtextdomain($domain, $dir);

    // set codeset in order to avoid gettext charset conversions
    if (function_exists('bind_textdomain_codeset')
     && isset($languages[$sm_notAlias]['CHARSET'])) {

        // Japanese translation uses different internal charset
        if ($sm_notAlias == 'ja_JP') {
            bind_textdomain_codeset ($domain, 'EUC-JP');
        } else {
            bind_textdomain_codeset ($domain, $languages[$sm_notAlias]['CHARSET']);
        }

    }

    return $dir;
}

/**
 * php setlocale function wrapper
 *
 * From php 4.3.0 it is possible to use arrays in order to set locale.
 * php gettext extension works only when locale is set. This wrapper
 * function allows to use more than one locale name.
 *
 * @param int $category locale category name. Use php named constants
 *     (LC_ALL, LC_COLLATE, LC_CTYPE, LC_MONETARY, LC_NUMERIC, LC_TIME)
 * @param mixed $locale option contains array with possible locales or string with one locale
 * @return string name of set locale or false, if all locales fail.
 * @since 1.4.5 and 1.5.1
 * @see http://php.net/setlocale
 */
function sq_setlocale($category,$locale) {
    if (is_string($locale)) {
        // string with only one locale
        $ret = setlocale($category,$locale);
    } elseif (! check_php_version(4,3)) {
        // older php version (second setlocale argument must be string)
        $ret=false;
        $index=0;
        while ( ! $ret && $index<count($locale)) {
            $ret=setlocale($category,$locale[$index]);
            $index++;
        }
    } else {
        // php 4.3.0 or better, use entire array
        $ret=setlocale($category,$locale);
    }

    /* safety checks */
    if (preg_match("/^.*\/.*\/.*\/.*\/.*\/.*$/",$ret)) {
        /**
         * Welcome to We-Don't-Follow-Own-Fine-Manual department
         * OpenBSD 3.8, 3.9-current and maybe later versions
         * return invalid response to setlocale command.
         * SM bug report #1427512.
         */
        $ret = false;
    }
    return $ret;
}

/**
 * Converts string from given charset to charset, that can be displayed by user translation.
 *
 * Function by default returns html encoded strings, if translation uses different encoding.
 * If Japanese translation is used - function returns string converted to euc-jp
 * If $charset is not supported - function returns unconverted string.
 *
 * sanitizing of html tags is also done by this function.
 *
 * @param string $charset
 * @param string $string Text to be decoded
 * @param boolean $force_decode converts string to html without $charset!=$default_charset check.
 * Argument is available since 1.4.5 and 1.5.1.
 * @param boolean $save_html disables htmlspecialchars() in order to preserve
 *  html formating. Use with care. Available since 1.4.6 and 1.5.1
 * @return string decoded string
 */
function charset_decode ($charset, $string, $force_decode=false, $save_html=false) {
    global $languages, $squirrelmail_language, $default_charset;

    if (isset($languages[$squirrelmail_language]['XTRA_CODE']) &&
        function_exists($languages[$squirrelmail_language]['XTRA_CODE'])) {
        $string = $languages[$squirrelmail_language]['XTRA_CODE']('decode', $string);
    }

    /* All HTML special characters are 7 bit and can be replaced first */
    if (! $save_html) $string = htmlspecialchars ($string);
    $charset = strtolower($charset);

    set_my_charset();

    // Don't do conversion if charset is the same.
    if ( ! $force_decode && $charset == strtolower($default_charset) )
        return $string;

    /* controls cpu and memory intensive decoding cycles */
    global $aggressive_decoding;
    $aggressive_decoding = false;

    $decode=fixcharset($charset);
    $decodefile=SM_PATH . 'functions/decode/' . $decode . '.php';
    if ($decode != 'index' && file_exists($decodefile)) {
      include_once($decodefile);
      $ret = call_user_func('charset_decode_'.$decode, $string, $save_html);
    } else {
      $ret = $string;
    }

    return( $ret );
}

/**
 * Converts html string to given charset
 * @since 1.4.4 and 1.5.1
 * @param string $string
 * @param string $charset
 * @param boolean $htmlencode keep htmlspecialchars encoding
 * @return string
 */
function charset_encode($string,$charset,$htmlencode=true) {
    global $default_charset;

    $encode=fixcharset($charset);
    $encodefile=SM_PATH . 'functions/encode/' . $encode . '.php';
    if ($encode != 'index' && file_exists($encodefile)) {
        include_once($encodefile);
        $ret = call_user_func('charset_encode_'.$encode, $string);
    } elseif(file_exists(SM_PATH . 'functions/encode/us_ascii.php')) {
        // function replaces all 8bit html entities with question marks.
        // it is used when other encoding functions are unavailable
        include_once(SM_PATH . 'functions/encode/us_ascii.php');
        $ret = charset_encode_us_ascii($string);
    } else {
        /**
         * fix for yahoo users that remove all us-ascii related things
         */
        $ret = $string;
    }

    /**
     * Undo html special chars, some places (like compose form) have
     * own sanitizing functions and don't need html symbols.
     * Undo chars only after encoding in order to prevent conversion of
     * html entities in plain text emails.
     */
    if (! $htmlencode ) {
        $ret = str_replace(array('&amp;','&gt;','&lt;','&quot;'),array('&','>','<','"'),$ret);
    }
    return( $ret );
}

/**
 * Combined decoding and encoding functions
 *
 * If conversion is done to charset different that utf-8, unsupported symbols
 * will be replaced with question marks.
 * @since 1.4.4 and 1.5.1
 * @param string $in_charset initial charset
 * @param string $string string that has to be converted
 * @param string $out_charset final charset
 * @param boolean $htmlencode keep htmlspecialchars encoding
 * @return string converted string
 */
function charset_convert($in_charset,$string,$out_charset,$htmlencode=true) {
    $string=charset_decode($in_charset,$string,true);
    $string=charset_encode($string,$out_charset,$htmlencode);
    return $string;
}

/**
 * Makes charset name suitable for decoding cycles
 *
 * ks_c_5601_1987, x-euc-* and x-windows-* charsets are supported
 * since 1.4.6 and 1.5.1.
 *
 * @since 1.4.4 and 1.5.0
 * @param string $charset Name of charset
 * @return string $charset Adjusted name of charset
 */
function fixcharset($charset) {

    /* Remove minus and characters that might be used in paths from charset
     * name in order to be able to use it in function names and include calls.
     * Also make sure it's in lower case (ala "UTF" --> "utf")
     */
    $charset=preg_replace("/[-:.\/\\\]/",'_', strtolower($charset));

    // OE ks_c_5601_1987 > cp949
    $charset=str_replace('ks_c_5601_1987','cp949',$charset);
    // Moz x-euc-tw > euc-tw
    $charset=str_replace('x_euc','euc',$charset);
    // Moz x-windows-949 > cp949
    $charset=str_replace('x_windows_','cp',$charset);

    // windows-125x and cp125x charsets
    $charset=str_replace('windows_','cp',$charset);

    // ibm > cp
    $charset=str_replace('ibm','cp',$charset);

    // iso-8859-8-i -> iso-8859-8
    // use same cycle until I'll find differences
    $charset=str_replace('iso_8859_8_i','iso_8859_8',$charset);

    return $charset;
}

/**
 * Set up the language to be output
 * if $do_search is true, then scan the browser information
 * for a possible language that we know
 *
 * Function sets system locale environment (LC_ALL, LANG, LANGUAGE),
 * gettext translation bindings and html header information.
 *
 * Function returns error codes, if there is some fatal error.
 *  0 = no error,
 *  1 = mbstring support is not present,
 *  2 = mbstring support is not present, user's translation reverted to en_US.
 *
 * @param string $sm_language  Translation used by user's interface
 * @param bool   $do_search    Use browser's preferred language detection functions.
 *                             Defaults to false.
 * @param bool   $default      Set $sm_language to $squirrelmail_default_language if
 *                             language detection fails or language is not set.
 *                             Defaults to false.
 * @return int function execution error codes.
 *
 */
function set_up_language($sm_language, $do_search = false, $default = false) {

    static $SetupAlready = 0;
    global $use_gettext, $languages, $squirrelmail_language,
           $squirrelmail_default_language, $default_charset, $sm_notAlias;

    if ($SetupAlready) {
        return;
    }

    $SetupAlready = TRUE;
    sqgetGlobalVar('HTTP_ACCEPT_LANGUAGE',  $accept_lang, SQ_SERVER);

    /**
     * If function is asked to detect preferred language
     *  OR SquirrelMail default language is set to empty string
     *    AND
     * SquirrelMail language ($sm_language) is empty string
     * (not set in user's prefs and no cookie with language info)
     *    AND
     * browser provides list of preferred languages
     *  THEN
     * get preferred language from HTTP_ACCEPT_LANGUAGE header
     */
    if (($do_search || empty($squirrelmail_default_language)) &&
        ! $sm_language &&
        isset($accept_lang)) {
        // TODO: use more than one language, if first language is not available
        // FIXME: function assumes that string contains two or more characters.
        // FIXME: some languages use 5 chars
        $sm_language = substr($accept_lang, 0, 2);
    }

    /**
     * If language preference is not set OR script asks to use default language
     *  AND
     * default SquirrelMail language is not set to empty string
     *  THEN
     * use default SquirrelMail language value from configuration.
     */
    if ((!$sm_language||$default) &&
        ! empty($squirrelmail_default_language)) {
        $squirrelmail_language = $squirrelmail_default_language;
        $sm_language = $squirrelmail_default_language;
    }

    /** provide failsafe language when detection fails */
    if (! $sm_language) $sm_language='en_US';

    $sm_notAlias = $sm_language;

    // Catching removed translation
    // System reverts to English translation if user prefs contain translation
    // that is not available in $languages array
    if (!isset($languages[$sm_notAlias])) {
        $sm_notAlias="en_US";
    }

    while (isset($languages[$sm_notAlias]['ALIAS'])) {
        $sm_notAlias = $languages[$sm_notAlias]['ALIAS'];
    }

    if ( isset($sm_language) &&
         $use_gettext &&
         $sm_language != '' &&
         isset($languages[$sm_notAlias]['CHARSET']) ) {
        bindtextdomain( 'squirrelmail', SM_PATH . 'locale/' );
        textdomain( 'squirrelmail' );
        if (function_exists('bind_textdomain_codeset')) {
            if ($sm_notAlias == 'ja_JP') {
                bind_textdomain_codeset ("squirrelmail", 'EUC-JP');
            } else {
                bind_textdomain_codeset ("squirrelmail", $languages[$sm_notAlias]['CHARSET'] );
            }
        }

        // Use LOCALE key, if it is set.
        if (isset($languages[$sm_notAlias]['LOCALE'])){
            $longlocale=$languages[$sm_notAlias]['LOCALE'];
        } else {
            $longlocale=$sm_notAlias;
        }

        // try setting locale
        $retlocale=sq_setlocale(LC_ALL, $longlocale);

        // check if locale is set and assign that locale to $longlocale
        // in order to use it in putenv calls.
        if (! is_bool($retlocale)) {
            $longlocale=$retlocale;
        } elseif (is_array($longlocale)) {
            // setting of all locales failed.
            // we need string instead of array used in LOCALE key.
            $longlocale=$sm_notAlias;
        }

        if ( !((bool)ini_get('safe_mode')) &&
             getenv( 'LC_ALL' ) != $longlocale ) {
            putenv( "LC_ALL=$longlocale" );
            putenv( "LANG=$longlocale" );
            putenv( "LANGUAGE=$longlocale" );
            putenv( "LC_NUMERIC=C" );
            if ($sm_notAlias=='tr_TR') putenv( "LC_CTYPE=C" );
        }
        // Workaround for plugins that use numbers with floating point
        // It might be removed if plugins use correct decimal delimiters
        // according to locale settings.
        setlocale(LC_NUMERIC, 'C');
        // Workaround for specific Turkish strtolower/strtoupper rules.
        // Many functions expect English conversion rules.
        if ($sm_notAlias=='tr_TR') setlocale(LC_CTYPE,'C');

        $squirrelmail_language = $sm_notAlias;
        if ($squirrelmail_language == 'ja_JP') {
            header ('Content-Type: text/html; charset=EUC-JP');
            if (!function_exists('mb_internal_encoding')) {

                // don't display mbstring warning when user isn't logged
                // in because the user may not be using SM for Japanese;
                // also don't display on webmail page so user has the
                // chance to go back and revert their language setting
                // until admin can get their act together
                if (sqGetGlobalVar('user_is_logged_in', $user_is_logged_in, SQ_SESSION)
                 && $user_is_logged_in && PAGE_NAME != 'webmail') {
                    echo _("You need to have PHP installed with the multibyte string function enabled (using configure option --enable-mbstring).");
                    // Revert to English link has to be added.
                    // stop further execution in order not to get php errors on mb_internal_encoding().
                }
                return;
            }
            if (function_exists('mb_language')) {
                mb_language('Japanese');
            }
            mb_internal_encoding('EUC-JP');
            mb_http_output('pass');
        } elseif ($squirrelmail_language == 'en_US') {
            header( 'Content-Type: text/html; charset=' . $default_charset );
        } else {
            header( 'Content-Type: text/html; charset=' . $languages[$sm_notAlias]['CHARSET'] );
        }
        /**
         * mbstring.func_overload fix (#929644).
         *
         * php mbstring extension can replace standard string functions with their multibyte
         * equivalents. See http://php.net/ref.mbstring#mbstring.overload. This feature
         * was added in php v.4.2.0
         *
         * Some SquirrelMail functions work with 8bit strings in bytes. If interface is forced
         * to use mbstring functions and mbstring internal encoding is set to multibyte charset,
         * interface can't trust regular string functions. Due to mbstring overloading design
         * limits php scripts can't control this setting.
         *
         * This hack should fix some issues related to 8bit strings in passwords. Correct fix is
         * to disable mbstring overloading. Japanese translation uses different internal encoding.
         */
        if ($squirrelmail_language != 'ja_JP' &&
            function_exists('mb_internal_encoding') &&
            check_php_version(4,2,0) &&
            (int)ini_get('mbstring.func_overload')!=0) {
            mb_internal_encoding('pass');
        }
    }
}

/**
 * Sets default_charset variable according to the one that is used by user's
 * translations.
 *
 * Function changes global $default_charset variable in order to be sure, that
 * it contains charset used by user's translation. Sanity of
 * $squirrelmail_language and $default_charset combination provided in the
 * SquirrelMail configuration is also tested.
 *
 * There can be a $default_charset setting in the
 * config.php file, but the user may have a different language
 * selected for a user interface. This function checks the
 * language selected by the user and tags the outgoing messages
 * with the appropriate charset corresponding to the language
 * selection. This is "more right" (tm), than just stamping the
 * message blindly with the system-wide $default_charset.
 */
function set_my_charset(){
    global $data_dir, $username, $default_charset, $languages, $squirrelmail_language;

    $my_language = getPref($data_dir, $username, 'language');
    if (!$my_language) {
        $my_language = $squirrelmail_language ;
    }
    // Catch removed translation
    if (!isset($languages[$my_language])) {
        $my_language="en_US";
    }
    while (isset($languages[$my_language]['ALIAS'])) {
        $my_language = $languages[$my_language]['ALIAS'];
    }
    $my_charset = $languages[$my_language]['CHARSET'];
    if ($my_language!='en_US') {
        $default_charset = $my_charset;
    }
}

/**
 * Function informs if it is safe to convert given charset to the one that is used by user.
 *
 * It is safe to use conversion only if user uses utf-8 encoding and when
 * converted charset is similar to the one that is used by user.
 *
 * @param string $input_charset Charset of text that needs to be converted
 * @return bool is it possible to convert to user's charset
 */
function is_conversion_safe($input_charset) {
    global $languages, $sm_notAlias, $default_charset, $lossy_encoding;

    if (isset($lossy_encoding) && $lossy_encoding )
        return true;

    // convert to lower case
    $input_charset = strtolower($input_charset);

    // Is user's locale Unicode based ?
    if ( $default_charset == "utf-8" ) {
        return true;
    }

    // Charsets that are similar
    switch ($default_charset) {
    case "windows-1251":
        if ( $input_charset == "iso-8859-5" ||
                $input_charset == "koi8-r" ||
                $input_charset == "koi8-u" ) {
            return true;
        } else {
            return false;
        }
    case "windows-1257":
        if ( $input_charset == "iso-8859-13" ||
             $input_charset == "iso-8859-4" ) {
            return true;
        } else {
            return false;
        }
    case "iso-8859-4":
        if ( $input_charset == "iso-8859-13" ||
             $input_charset == "windows-1257" ) {
            return true;
        } else {
            return false;
        }
    case "iso-8859-5":
        if ( $input_charset == "windows-1251" ||
             $input_charset == "koi8-r" ||
             $input_charset == "koi8-u" ) {
            return true;
        } else {
            return false;
        }
    case "iso-8859-13":
        if ( $input_charset == "iso-8859-4" ||
             $input_charset == "windows-1257" ) {
            return true;
        } else {
            return false;
        }
    case "koi8-r":
        if ( $input_charset == "windows-1251" ||
             $input_charset == "iso-8859-5" ||
             $input_charset == "koi8-u" ) {
            return true;
        } else {
            return false;
        }
    case "koi8-u":
        if ( $input_charset == "windows-1251" ||
             $input_charset == "iso-8859-5" ||
             $input_charset == "koi8-r" ) {
            return true;
        } else {
            return false;
        }
    default:
        return false;
    }
}

/* ---- extra code functions ----*/
/**
 * Japanese charset extra function
 */
function japanese_charset_xtra() {
    $ret = func_get_arg(1);  /* default return value */
    if (function_exists('mb_detect_encoding')) {
        switch (func_get_arg(0)) { /* action */
        case 'decode':
            $detect_encoding = @mb_detect_encoding($ret);
            if ($detect_encoding == 'JIS' ||
                $detect_encoding == 'EUC-JP' ||
                $detect_encoding == 'SJIS' ||
                $detect_encoding == 'UTF-8') {

                $ret = mb_convert_kana(mb_convert_encoding($ret, 'EUC-JP', 'AUTO'), "KV");
            }
            break;
        case 'encode':
            $detect_encoding = @mb_detect_encoding($ret);
            if ($detect_encoding == 'JIS' ||
                $detect_encoding == 'EUC-JP' ||
                $detect_encoding == 'SJIS' ||
                $detect_encoding == 'UTF-8') {

                $ret = mb_convert_encoding(mb_convert_kana($ret, "KV"), 'JIS', 'AUTO');
            }
            break;
        case 'strimwidth':
            $width = func_get_arg(2);
            $ret = mb_strimwidth($ret, 0, $width, '...');
            break;
        case 'encodeheader':
            /**
             * First argument ($ret) contains header string.
             * SquirrelMail ja_JP translation uses euc-jp as internal encoding.
             * euc-jp stores Japanese letters in 0xA1-0xFE block (source:
             * JIS X 0208 unicode.org mapping. see euc_jp.php in extra decoding
             * library). Standard SquirrelMail 8bit test should detect if text
             * is in euc or in ascii.
             */
            if (sq_is8bit($ret)) {
                /**
                 * Minimize dependency on mb_mime_encodeheader(). PHP 4.4.1 bug
                 * and maybe other bugs.
                 *
                 * Convert text from euc-jp (internal encoding) to iso-2022-jp
                 * (commonly used Japanese encoding) with mbstring functions.
                 *
                 * Use SquirrelMail internal B encoding function. 'encodeheader'
                 * XTRA_CODE is executed in encodeHeader() function, so
                 * functions/mime.php (encodeHeaderBase64) and functions/strings.php
                 * (sq_is8bit) are already loaded.
                 */
                $ret = encodeHeaderBase64(mb_convert_encoding($ret,'ISO-2022-JP','EUC-JP'),
                                          'iso-2022-jp');
            }
            /**
             * if text is in ascii, we leave it unchanged. If some ASCII
             * chars must be encoded, add code here in else statement.
             */
            break;
        case 'decodeheader':
            $ret = str_replace("\t", "", $ret);
            if (preg_match('/=\?([^?]+)\?(q|b)\?([^?]+)\?=/i', $ret))
                $ret = @mb_decode_mimeheader($ret);
            $ret = @mb_convert_encoding($ret, 'EUC-JP', 'AUTO');
            break;
        case 'downloadfilename':
            $useragent = func_get_arg(2);
            if (strstr($useragent, 'Windows') !== false ||
                strstr($useragent, 'Mac_') !== false) {
                $ret = mb_convert_encoding($ret, 'SJIS', 'AUTO');
            } else {
                $ret = mb_convert_encoding($ret, 'EUC-JP', 'AUTO');
}
            break;
        case 'wordwrap':
            $no_begin = "\x21\x25\x29\x2c\x2e\x3a\x3b\x3f\x5d\x7d\xa1\xf1\xa1\xeb\xa1" .
                "\xc7\xa1\xc9\xa2\xf3\xa1\xec\xa1\xed\xa1\xee\xa1\xa2\xa1\xa3\xa1\xb9" .
                "\xa1\xd3\xa1\xd5\xa1\xd7\xa1\xd9\xa1\xdb\xa1\xcd\xa4\xa1\xa4\xa3\xa4" .
                "\xa5\xa4\xa7\xa4\xa9\xa4\xc3\xa4\xe3\xa4\xe5\xa4\xe7\xa4\xee\xa1\xab" .
                "\xa1\xac\xa1\xb5\xa1\xb6\xa5\xa1\xa5\xa3\xa5\xa5\xa5\xa7\xa5\xa9\xa5" .
                "\xc3\xa5\xe3\xa5\xe5\xa5\xe7\xa5\xee\xa5\xf5\xa5\xf6\xa1\xa6\xa1\xbc" .
                "\xa1\xb3\xa1\xb4\xa1\xaa\xa1\xf3\xa1\xcb\xa1\xa4\xa1\xa5\xa1\xa7\xa1" .
                "\xa8\xa1\xa9\xa1\xcf\xa1\xd1";
            $no_end = "\x5c\x24\x28\x5b\x7b\xa1\xf2\x5c\xa1\xc6\xa1\xc8\xa1\xd2\xa1" .
                "\xd4\xa1\xd6\xa1\xd8\xa1\xda\xa1\xcc\xa1\xf0\xa1\xca\xa1\xce\xa1\xd0\xa1\xef";
            $wrap = func_get_arg(2);

            if (strlen($ret) >= $wrap &&
                substr($ret, 0, 1) != '>' &&
                strpos($ret, 'http://') === FALSE &&
                strpos($ret, 'https://') === FALSE &&
                strpos($ret, 'ftp://') === FALSE) {

                $ret = mb_convert_kana($ret, "KV");

                $line_new = '';
                $ptr = 0;

                while ($ptr < strlen($ret) - 1) {
                    $l = mb_strcut($ret, $ptr, $wrap);
                    $ptr += strlen($l);
                    $tmp = $l;

                    $l = mb_strcut($ret, $ptr, 2);
                    while (strlen($l) != 0 && mb_strpos($no_begin, $l) !== FALSE ) {
                        $tmp .= $l;
                        $ptr += strlen($l);
                        $l = mb_strcut($ret, $ptr, 1);
                    }
                    $line_new .= $tmp;
                    if ($ptr < strlen($ret) - 1)
                        $line_new .= "\n";
                }
                $ret = $line_new;
            }
            break;
        case 'utf7-imap_encode':
            $ret = mb_convert_encoding($ret, 'UTF7-IMAP', 'EUC-JP');
            break;
        case 'utf7-imap_decode':
            $ret = mb_convert_encoding($ret, 'EUC-JP', 'UTF7-IMAP');
            break;
        }
    }
    return $ret;
}


/*
 * Korean charset extra function
 * Hangul(Korean Character) Attached File Name Fix.
 */
function korean_charset_xtra() {

    $ret = func_get_arg(1);  /* default return value */
    if (func_get_arg(0) == 'downloadfilename') { /* action */
        $ret = str_replace("\x0D\x0A", '', $ret);  /* Hanmail's CR/LF Clear */
        for ($i=0;$i<strlen($ret);$i++) {
            if ($ret[$i] >= "\xA1" && $ret[$i] <= "\xFE") {   /* 0xA1 - 0XFE are Valid */
                $i++;
                continue;
            } else if (($ret[$i] >= 'a' && $ret[$i] <= 'z') || /* From Original ereg_replace in download.php */
                       ($ret[$i] >= 'A' && $ret[$i] <= 'Z') ||
                       ($ret[$i] == '.') || ($ret[$i] == '-')) {
                continue;
            } else {
                $ret[$i] = '_';
            }
        }

    }

    return $ret;
}

/* ------------------------------ main --------------------------- */

global $squirrelmail_language, $languages, $use_gettext;

if (! sqgetGlobalVar('squirrelmail_language',$squirrelmail_language,SQ_COOKIE)) {
    $squirrelmail_language = '';
}

/**
 * This array specifies the available translations.
 *
 * Structure of array:
 * $languages['language']['variable'] = 'value'
 *
 * Possible 'variable' names:
 *  NAME      - Translation name in English
 *  CHARSET   - Encoding used by translation
 *  ALIAS     - used when 'language' is only short name and 'value' should provide long language name
 *  ALTNAME   - Native translation name. Any 8bit symbols must be html encoded.
 *  LOCALE    - Full locale name (in xx_XX.charset format). It can use array with more than one locale name since 1.4.5 and 1.5.1
 *  DIR       - Text direction. Used to define Right-to-Left languages. Possible values 'rtl' or 'ltr'. If undefined - defaults to 'ltr'
 *  XTRA_CODE - translation uses special functions. See http://squirrelmail.org/docs/devel/devel-3.html
 *
 * Each 'language' definition requires NAME+CHARSET or ALIAS variables.
 *
 * @name $languages
 * @global array $languages
 */
$languages['bg_BG']['NAME']    = 'Bulgarian';
$languages['bg_BG']['CHARSET'] = 'windows-1251';
$languages['bg_BG']['LOCALE']  = 'bg_BG.CP1251';
$languages['bg']['ALIAS']      = 'bg_BG';

$languages['bn_BD']['NAME']    = 'Bengali (Bangladesh)';
//$languages['bn_BD']['ALTNAME'] = 'Bangla';
$languages['bn_BD']['ALTNAME'] = '&#x09AC;&#x09BE;&#x0982;&#x09B2;&#x09BE;';
$languages['bn_BD']['CHARSET'] = 'utf-8';
$languages['bn_BD']['LOCALE']  = array('bn_BD.UTF-8', 'bn_BD.UTF8', 'bn_BD', 'bn.UTF-8', 'bn.UTF8', 'bn');
$languages['bn']['ALIAS'] = 'bn_BD';

$languages['bn_IN']['NAME']    = 'Bengali (India)';
$languages['bn_IN']['CHARSET'] = 'utf-8';
$languages['bn_IN']['LOCALE']  = array('bn_IN.UTF-8', 'bn_IN.UTF8');

$languages['ca_ES']['NAME']    = 'Catalan';
$languages['ca_ES']['CHARSET'] = 'iso-8859-1';
$languages['ca_ES']['LOCALE']  = array('ca_ES.ISO8859-1','ca_ES.ISO-8859-1','ca_ES');
$languages['ca']['ALIAS']      = 'ca_ES';

$languages['cs_CZ']['NAME']    = 'Czech';
$languages['cs_CZ']['ALTNAME'] = '&#268;e&scaron;tina';
$languages['cs_CZ']['CHARSET'] = 'utf-8';
$languages['cs_CZ']['LOCALE']  = array('cs_CZ.UTF-8', 'cs_CZ.UTF8', 'cs_CZ');
$languages['cs']['ALIAS']      = 'cs_CZ';

$languages['cy_GB']['NAME']    = 'Welsh';
$languages['cy_GB']['CHARSET'] = 'iso-8859-1';
$languages['cy_GB']['LOCALE']  = array('cy_GB.ISO8859-1','cy_GB.ISO-8859-1','cy_GB');
$languages['cy']['ALIAS'] = 'cy_GB';

$languages['da_DK']['NAME']    = 'Danish';
$languages['da_DK']['CHARSET'] = 'iso-8859-1';
$languages['da_DK']['LOCALE']  = array('da_DK.ISO8859-1','da_DK.ISO-8859-1','da_DK');
$languages['da']['ALIAS']      = 'da_DK';

$languages['de_DE']['NAME']    = 'German';
$languages['de_DE']['ALTNAME'] = 'Deutsch';
$languages['de_DE']['CHARSET'] = 'iso-8859-1';
$languages['de_DE']['LOCALE']  = array('de_DE.ISO8859-1','de_DE.ISO-8859-1','de_DE');
$languages['de']['ALIAS']      = 'de_DE';

$languages['el_GR']['NAME']    = 'Greek';
$languages['el_GR']['CHARSET'] = 'iso-8859-7';
$languages['el_GR']['LOCALE']  = array('el_GR.ISO8859-7','el_GR.ISO-8859-7','el_GR');
$languages['el']['ALIAS']      = 'el_GR';

/* This translation is disabled because it contains less than 50%
 * translated strings. In those cases where British and American English are
 * spelled the same, the translation should indicate that by copy the "msgid" to
 * the "msgstr" instead of leaving the "msgstr" blank.
$languages['en_GB']['NAME']    = 'British';
$languages['en_GB']['CHARSET'] = 'iso-8859-15';
$languages['en_GB']['LOCALE']  = array('en_GB.ISO8859-15','en_GB.ISO-8859-15','en_GB');
*/

$languages['en_US']['NAME']    = 'English';
$languages['en_US']['CHARSET'] = 'iso-8859-1';
$languages['en_US']['LOCALE']  = 'en_US.ISO8859-1';
$languages['en']['ALIAS']      = 'en_US';

$languages['es_ES']['NAME']    = 'Spanish';
$languages['es_ES']['CHARSET'] = 'iso-8859-1';
$languages['es_ES']['LOCALE']  = array('es_ES.ISO8859-1','es_ES.ISO-8859-1','es_ES');
$languages['es']['ALIAS']      = 'es_ES';

$languages['et_EE']['NAME']    = 'Estonian';
$languages['et_EE']['CHARSET'] = 'iso-8859-15';
$languages['et_EE']['LOCALE']  = array('et_EE.ISO8859-15','et_EE.ISO-8859-15','et_EE');
$languages['et']['ALIAS']      = 'et_EE';

$languages['eu_ES']['NAME']    = 'Basque';
$languages['eu_ES']['CHARSET'] = 'iso-8859-1';
$languages['eu_ES']['LOCALE']  = array('eu_ES.ISO8859-1','eu_ES.ISO-8859-1','eu_ES');
$languages['eu']['ALIAS']      = 'eu_ES';

$languages['fi_FI']['NAME']    = 'Finnish';
$languages['fi_FI']['CHARSET'] = 'iso-8859-1';
$languages['fi_FI']['LOCALE']  = array('fi_FI.ISO8859-1','fi_FI.ISO-8859-1','fi_FI');
$languages['fi']['ALIAS']      = 'fi_FI';

$languages['fo_FO']['NAME']    = 'Faroese';
$languages['fo_FO']['CHARSET'] = 'iso-8859-1';
$languages['fo_FO']['LOCALE']  = array('fo_FO.ISO8859-1','fo_FO.ISO-8859-1','fo_FO');
$languages['fo']['ALIAS']      = 'fo_FO';

$languages['fr_FR']['NAME']    = 'French';
$languages['fr_FR']['CHARSET'] = 'iso-8859-1';
$languages['fr_FR']['LOCALE']  = array('fr_FR.ISO8859-1','fr_FR.ISO-8859-1','fr_FR');
$languages['fr']['ALIAS']      = 'fr_FR';

$languages['fy']['NAME']       = 'Frisian';
$languages['fy']['CHARSET']    = 'utf-8';
$languages['fy']['LOCALE']     = array('fy.UTF-8', 'fy.UTF8', 'fy_NL.UTF-8', 'fy_NL.UTF8');

$languages['hr_HR']['NAME']    = 'Croatian';
$languages['hr_HR']['CHARSET'] = 'iso-8859-2';
$languages['hr_HR']['LOCALE']  = array('hr_HR.ISO8859-2','hr_HR.ISO-8859-2','hr_HR');
$languages['hr']['ALIAS']      = 'hr_HR';

$languages['hu_HU']['NAME']    = 'Hungarian';
$languages['hu_HU']['ALTNAME'] = 'Magyar';
$languages['hu_HU']['CHARSET'] = 'utf-8';
$languages['hu_HU']['LOCALE']  = array('hu_HU.UTF-8', 'hu_HU.UTF8', 'hu_HU');
$languages['hu']['ALIAS']      = 'hu_HU';

$languages['id_ID']['NAME']    = 'Bahasa Indonesia';
$languages['id_ID']['CHARSET'] = 'iso-8859-1';
$languages['id_ID']['LOCALE']  = array('id_ID.ISO8859-1','id_ID.ISO-8859-1','id_ID');
$languages['id']['ALIAS']      = 'id_ID';

$languages['is_IS']['NAME']    = 'Icelandic';
$languages['is_IS']['CHARSET'] = 'iso-8859-1';
$languages['is_IS']['LOCALE']  = array('is_IS.ISO8859-1','is_IS.ISO-8859-1','is_IS');
$languages['is']['ALIAS']      = 'is_IS';

$languages['it_IT']['NAME']    = 'Italian';
$languages['it_IT']['ALTNAME'] = 'Italiano';
$languages['it_IT']['CHARSET'] = 'utf-8';
$languages['it_IT']['LOCALE']  = array('it_IT.UTF-8','it_IT-UTF8','it_IT');
$languages['it']['ALIAS']      = 'it_IT';

$languages['ja_JP']['NAME']    = 'Japanese';
$languages['ja_JP']['CHARSET'] = 'iso-2022-jp';
$languages['ja_JP']['XTRA_CODE'] = 'japanese_charset_xtra';
$languages['ja']['ALIAS']      = 'ja_JP';

$languages['ka']['NAME']       = 'Georgian';
$languages['ka']['CHARSET']    = 'utf-8';
$languages['ka']['LOCALE']     = array('ka_GE.UTF-8', 'ka_GE.UTF8', 'ka_GE', 'ka');
$languages['ka_GE']['ALIAS']   = 'ka';

$languages['km']['NAME']       = 'Khmer';
$languages['km']['ALTNAME']    = '&#6017;&#6098;&#6040;&#6082;&#6042;';
$languages['km']['CHARSET']    = 'utf-8';
$languages['km']['LOCALE']     = array('km.UTF-8', 'km.UTF8', 'km_KH.UTF-8', 'km_KH.UTF8', 'km', 'km_KH');
$languages['km_KH']['ALIAS']   = 'km';

$languages['ko_KR']['NAME']    = 'Korean';
$languages['ko_KR']['CHARSET'] = 'euc-KR';
// Function does not provide all needed options
// $languages['ko_KR']['XTRA_CODE'] = 'korean_charset_xtra';
$languages['ko']['ALIAS']      = 'ko_KR';

$languages['lv_LV']['NAME']    = 'Latvian';
$languages['lv_LV']['ALTNAME'] = 'Latvi&#371;';
$languages['lv_LV']['CHARSET'] = 'utf-8';
$languages['lv_LV']['LOCALE'] = array('lv_LV.UTF-8', 'lv_LV.UTF8');
$languages['lv']['ALIAS'] = 'lv_LV';

$languages['lt_LT']['NAME']    = 'Lithuanian';
$languages['lt_LT']['CHARSET'] = 'utf-8';
$languages['lt_LT']['LOCALE']  = array('lt_LT.UTF-8', 'lt_LT.UTF8');
$languages['lt']['ALIAS']      = 'lt_LT';

$languages['mk']['NAME']       = 'Macedonian';
$languages['mk']['CHARSET']    = 'utf-8';
$languages['mk']['LOCALE']     = array('mk.UTF-8', 'mk.UTF8', 'mk_MK.UTF-8', 'mk_MK.UTF8');

$languages['ms_MY']['NAME']    = 'Bahasa Melayu';
$languages['ms_MY']['CHARSET'] = 'iso-8859-1';
$languages['ms_MY']['LOCALE']  = array('ms_MY.ISO8859-1','ms_MY.ISO-8859-1','ms_MY');
$languages['my']['ALIAS']      = 'ms_MY';

$languages['nl_NL']['NAME']    = 'Dutch';
$languages['nl_NL']['CHARSET'] = 'iso-8859-1';
$languages['nl_NL']['LOCALE']  = array('nl_NL.ISO8859-1','nl_NL.ISO-8859-1','nl_NL');
$languages['nl']['ALIAS']      = 'nl_NL';

$languages['nb_NO']['NAME']    = 'Norwegian (Bokm&aring;l)';
$languages['nb_NO']['CHARSET'] = 'utf-8';
$languages['nb_NO']['LOCALE']  = array('nb_NO.UTF-8', 'nb_NO.UTF8', 'nb_NO');
$languages['nb']['ALIAS']      = 'nb_NO';

$languages['nn_NO']['NAME']    = 'Norwegian (Nynorsk)';
$languages['nn_NO']['CHARSET'] = 'iso-8859-1';
$languages['nn_NO']['LOCALE']  = array('nn_NO.ISO8859-1','nn_NO.ISO-8859-1','nn_NO');

$languages['pl_PL']['NAME']    = 'Polish';
$languages['pl_PL']['CHARSET'] = 'iso-8859-2';
$languages['pl_PL']['LOCALE']  = array('pl_PL.ISO8859-2','pl_PL.ISO-8859-2','pl_PL');
$languages['pl']['ALIAS']      = 'pl_PL';

$languages['pt_PT']['NAME']    = 'Portuguese (Portugal)';
$languages['pt_PT']['CHARSET'] = 'iso-8859-1';
$languages['pt_PT']['LOCALE']  = array('pt_PT.ISO8859-1','pt_PT.ISO-8859-1','pt_PT');
$languages['pt']['ALIAS']      = 'pt_PT';

$languages['pt_BR']['NAME']    = 'Portuguese (Brazil)';
$languages['pt_BR']['CHARSET'] = 'iso-8859-1';
$languages['pt_BR']['LOCALE']  = array('pt_BR.ISO8859-1','pt_BR.ISO-8859-1','pt_BR');

$languages['ro_RO']['NAME']    = 'Romanian';
$languages['ro_RO']['CHARSET'] = 'utf-8';
$languages['ro_RO']['LOCALE']  = array('ro_RO.UTF-8', 'ro_RO.UTF8', 'ro_RO');
$languages['ro']['ALIAS']      = 'ro_RO';

$languages['ru_RU']['NAME']    = 'Russian';
$languages['ru_RU']['CHARSET'] = 'utf-8';
$languages['ru_RU']['LOCALE']  = array('ru_RU.UTF-8', 'ru_RU.UTF8');
$languages['ru']['ALIAS']      = 'ru_RU';

/* This translation is disabled because it is supposedly
 * Russian slang and is in need of updating.
$languages['ru_UA']['NAME']    = 'Russian (Ukrainian)';
$languages['ru_UA']['CHARSET'] = 'koi8-r';
$languages['ru_UA']['LOCALE']  = 'ru_UA.KOI8-R';
*/

/* This translation is disabled because it contains less than 50%
 * translated strings
$languages['si_LK']['NAME']    = 'Sinhala';
$languages['si_LK']['ALTNAME'] = '&#3523;&#3538;&#3458;&#3524;&#3517;';
$languages['si_LK']['CHARSET'] = 'utf-8';
$languages['si_LK']['LOCALE']  = array('si_LK.UTF-8', 'si_LK.UTF8');
$languages['si']['ALIAS'] = 'si_LK';
*/

$languages['sk_SK']['NAME']    = 'Slovak';
$languages['sk_SK']['CHARSET'] = 'utf-8';
$languages['sk_SK']['LOCALE']  = array('sk_SK.UTF-8', 'sk_SK.UTF8', 'sk_SK');
$languages['sk']['ALIAS']      = 'sk_SK';

$languages['sl_SI']['NAME']    = 'Slovenian';
$languages['sl_SI']['CHARSET'] = 'iso-8859-2';
$languages['sl_SI']['LOCALE']  = array('sl_SI.ISO8859-2','sl_SI.ISO-8859-2','sl_SI');
$languages['sl']['ALIAS']      = 'sl_SI';

$languages['sr_YU']['NAME']    = 'Serbian';
$languages['sr_YU']['CHARSET'] = 'iso-8859-2';
$languages['sr_YU']['LOCALE']  = array('sr_YU.ISO8859-2','sr_YU.ISO-8859-2','sr_YU');
$languages['sr']['ALIAS']      = 'sr_YU';

$languages['sv_SE']['NAME']    = 'Swedish';
$languages['sv_SE']['CHARSET'] = 'utf-8';
$languages['sv_SE']['LOCALE']  = array('sv_SE.UTF-8', 'sv_SE.UTF8', 'sv_SE');
$languages['sv']['ALIAS']      = 'sv_SE';

$languages['ta_LK']['NAME']    = 'Tamil';
$languages['ta_LK']['ALTNAME'] = '&#2980;&#2990;&#3007;&#2996;&#3021;';
$languages['ta_LK']['CHARSET'] = 'utf-8';
$languages['ta_LK']['LOCALE']  = array('ta_LK.UTF-8', 'ta_LK.UTF8', 'ta_LK', 'ta.UTF-8', 'ta.UTF8', 'ta');
$languages['ta']['ALIAS'] = 'ta_LK';

/* This translation is disabled because it contains less than 50%
 * translated strings
$languages['th_TH']['NAME']    = 'Thai';
$languages['th_TH']['CHARSET'] = 'tis-620';
$languages['th_TH']['LOCALE']  = 'th_TH.TIS-620';
$languages['th']['ALIAS'] = 'th_TH';
*/

/* This translation is disabled because it contains less than 50%
 * translated strings
$languages['tl_PH']['NAME']    = 'Tagalog';
$languages['tl_PH']['CHARSET'] = 'iso-8859-1';
$languages['tl_PH']['LOCALE']  = array('tl_PH.ISO8859-1','tl_PH.ISO-8859-1','tl_PH');
$languages['tl']['ALIAS'] = 'tl_PH';
*/

$languages['tr_TR']['NAME']    = 'Turkish';
$languages['tr_TR']['CHARSET'] = 'iso-8859-9';
$languages['tr_TR']['LOCALE']  = array('tr_TR.ISO8859-9','tr_TR.ISO-8859-9','tr_TR');
$languages['tr']['ALIAS']      = 'tr_TR';

$languages['zh_TW']['NAME']    = 'Chinese Trad';
$languages['zh_TW']['CHARSET'] = 'utf-8';
$languages['zh_TW']['LOCALE']  = array('zh_TW.UTF-8', 'zh_TW.UTF8');
$languages['tw']['ALIAS']      = 'zh_TW';

$languages['zh_CN']['NAME']    = 'Chinese Simp';
$languages['zh_CN']['CHARSET'] = 'gb2312';
$languages['zh_CN']['LOCALE']  = 'zh_CN.GB2312';
$languages['cn']['ALIAS']      = 'zh_CN';

$languages['uk_UA']['NAME']    = 'Ukrainian';
$languages['uk_UA']['CHARSET'] = 'utf-8';
$languages['uk_UA']['LOCALE']  = array('uk_UA.UTF-8', 'uk_UA.UTF8', 'uk_UA', 'uk');
$languages['uk']['ALIAS'] = 'uk_UA';

$languages['vi_VN']['NAME']    = 'Vietnamese';
$languages['vi_VN']['CHARSET'] = 'utf-8';
$languages['vi']['ALIAS'] = 'vi_VN';

// Right to left languages

$languages['ar']['NAME']    = 'Arabic';
$languages['ar']['CHARSET'] = 'windows-1256';
$languages['ar']['DIR']     = 'rtl';

$languages['fa_IR']['NAME']    = 'Persian';
$languages['fa_IR']['CHARSET'] = 'utf-8';
$languages['fa_IR']['DIR']     = 'rtl';
$languages['fa_IR']['LOCALE']  = array('fa_IR.UTF-8', 'fa_IR.UTF8');
$languages['fa']['ALIAS']      = 'fa_IR';

$languages['he_IL']['NAME']    = 'Hebrew';
$languages['he_IL']['CHARSET'] = 'windows-1255';
$languages['he_IL']['DIR']     = 'rtl';
$languages['he']['ALIAS']      = 'he_IL';

$languages['ug']['NAME']    = 'Uighur';
$languages['ug']['CHARSET'] = 'utf-8';
$languages['ug']['DIR']     = 'rtl';

/* Detect whether gettext is installed. */
$gettext_flags = 0;
if (function_exists('_')) {
    $gettext_flags += 1;
}
if (function_exists('bindtextdomain')) {
    $gettext_flags += 2;
}
if (function_exists('textdomain')) {
    $gettext_flags += 4;
}

/* If gettext is fully loaded, cool */
if ($gettext_flags == 7) {
    $use_gettext = true;
}
/* If we can fake gettext, try that */
elseif ($gettext_flags == 0) {
    $use_gettext = true;
    include_once(SM_PATH . 'functions/gettext.php');
} else {
    /* Uh-ho.  A weird install */
    if (! $gettext_flags & 1) {
      /**
       * Function is used as replacement in broken installs
       * @ignore
       */
        function _($str) {
            return $str;
        }
    }
    if (! $gettext_flags & 2) {
      /**
       * Function is used as replacement in broken installs
       * @ignore
       */
        function bindtextdomain() {
            return;
        }
    }
    if (! $gettext_flags & 4) {
      /**
       * Function is used as replacemet in broken installs
       * @ignore
       */
        function textdomain() {
            return;
        }
    }
}
