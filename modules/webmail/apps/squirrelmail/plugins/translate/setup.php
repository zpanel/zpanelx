<?php

/**
 * setup.php
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * @version $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage translate
 */

/* Easy plugin that sends the body of the message to a new browser
window using the specified translator.  It can also translate your
outgoing message if you send it to someone in a different country.

  Languages from i18n, incorporated in the auto-language selection:
    en - English
    no - Norwegian (Bokm&aring;l)
    no_NO_ny - Norwegian (Nynorsk)
    de - Deutsch
    ru - Russian KOI8-R
    pl - Polish
    sv - Swedish
    nl - Dutch
    pt_BR - Portuguese (Brazil)
    fr - French
    it - Italian
    cs - Czech
    es - Spanish
    ko - Korean
*/

/** Initialize the translation plugin */
function squirrelmail_plugin_init_translate() {
    global $squirrelmail_plugin_hooks;

    $squirrelmail_plugin_hooks['read_body_bottom']['translate'] = 'translate_read_form';
    $squirrelmail_plugin_hooks['optpage_register_block']['translate'] = 'translate_optpage_register_block';
    $squirrelmail_plugin_hooks['loading_prefs']['translate'] = 'translate_pref';
    $squirrelmail_plugin_hooks['compose_button_row']['translate'] = 'translate_button';
}

/** Show the translation for a message you're reading */
function translate_read_form() {
    global $color, $translate_server;
    global $message, $translate_dir;
    global $translate_show_read;
    global $imapConnection, $wrap_at, $passed_id, $mailbox;

    if (!$translate_show_read) {
        return;
    }

    $translate_dir = 'to';

    $trans_ar = $message->findDisplayEntity(array(), array('text/plain'));
    $body = '';
    if ( !empty($trans_ar[0]) ) {
        for ($i = 0; $i < count($trans_ar); $i++) {
            $body .= formatBody($imapConnection, $message, $color, $wrap_at, $trans_ar[$i], $passed_id, $mailbox, true);
        }
        $hookResults = do_hook('message_body', $body);
        $body = $hookResults[1];
    } else {
        $body = 'Message can\'t be translated';
    }

    $new_body = $body;

    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans[' '] = '&nbsp;';
    $trans = array_flip($trans);
    $new_body = strtr($new_body, $trans);

    $new_body = urldecode($new_body);
    $new_body = strip_tags($new_body);

    /* I really don't like this next part ... */
    $new_body = str_replace('"', "''", $new_body);
    $new_body = strtr($new_body, "\n", ' ');

    $function = 'translate_form_' . $translate_server;
    $function($new_body);
}

/** Closes translation engine form */
function translate_table_end() {
    ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
  <?php
}

/** Unimplemented. Translation in compose*/
function translate_button() {
    global $translate_show_send;

    if (! $translate_show_send) {
        return;
    }
}

/** Registers translation option block */
function translate_optpage_register_block() {
    global $optpage_blocks;
    $optpage_blocks[] = array(
        'name' => _("Translation Options"),
        'url'  => '../plugins/translate/options.php',
        'desc' => _("Which translator should be used when you get messages in a different language?"),
        'js'   => false
    );
}

/** gets translation preferences */
function translate_pref() {
    global $username, $data_dir;
    global $translate_server, $translate_location;
    global $translate_show_send, $translate_show_read;
    global $translate_same_window;

    $translate_server = getPref($data_dir, $username, 'translate_server');
    if ($translate_server == '') {
        $translate_server = 'babelfish';
    }

    $translate_location = getPref($data_dir, $username, 'translate_location');
    if ($translate_location == '') {
        $translate_location = 'center';
    }

    $translate_show_send = getPref($data_dir, $username, 'translate_show_send');
    $translate_show_read = getPref($data_dir, $username, 'translate_show_read');
    $translate_same_window = getPref($data_dir, $username, 'translate_same_window');
}


/**
 * This function could be sped up.
 * It basically negates the process if a ! is found in the beginning and
 * matches a * at the end with 0 or more characters.
 */
function translate_does_it_match_language($test) {
    global $squirrelmail_language;
    $true = 1;
    $false = 0;
    $index = 0;
    $smindex = 0;

    if (! $test || ! $squirrelmail_language) {
        return $false;
    }

    if ($test[$index] == '!') {
        $index ++;
        $true = 0;
        $false = 1;
    }

    if (($index == 0) && ($test == $squirrelmail_language)) {
        return $true;
    }

    while (isset($test[$index]) && $test[$index]) {
        if ($test[$index] == '*') {
            return $true;
        }
        if ($test[$index] != $squirrelmail_language[$smindex]) {
            return $false;
        }
        $index ++;
        $smindex ++;
    }

    return $false;
}

/** creates translation engine language selection */
function translate_lang_opt($from, $to, $value, $text) {
    global $translate_dir;

    $ret = '  <option value="' . $value . '"';

    if (translate_does_it_match_language($to) && ($translate_dir == 'to')) {
        $ret .= ' selected';
    }

    if (translate_does_it_match_language($from) && ($translate_dir == 'from')) {
        $ret .= ' selected';
    }

    $ret .= '>' . $text . "</option>\n";

    return( $ret );
}

/**
 * Starts translation box
 *
 * @param string $action url that has to recieve message for translation
 * @param string $charset (since sm 1.5.1 and 1.4.9) character set, that
 * should be used to submit 8bit information.
 * @access private
 */
function translate_new_form($action,$charset=null) {
    global $translate_dir, $translate_new_window, $translate_location;
    global $color, $translate_same_window;

    echo '<form action="';

    if ($translate_dir == 'to') {
        echo $action;
    } else {
        echo 'translate.php';
    }

    echo '" method="post"';

    if (!$translate_same_window) {
        echo ' target="_blank"';
    }

    if (! is_null($charset))
        echo ' accept-charset="'.htmlspecialchars($charset).'"';

    echo ">\n";

    ?><table align="<?php echo $translate_location ?>" cellpadding="3"
        cellspacing="0" border="0" bgcolor="<?php echo $color[10] ?>">
    <tr>
      <td>
        <table cellpadding="2" cellspacing="1" border="0" bgcolor="<?php echo $color[5] ?>">
          <tr>
            <td><?php
}

/**
 * Babelfish translation engine functions
 *
 * @param string $message text that has to be translated.
 * @access private
 */
function translate_form_babelfish($message) {
    translate_new_form('http://babelfish.altavista.com/babelfish/tr');
?>
    <input type="hidden" name="doit" value="done" />
    <input type="hidden" name="intl" value="1" />
    <input type="hidden" name="tt" value="urltext" />
    <input type="hidden" name="urltext" value="<?php echo htmlspecialchars($message); ?>" />
    <select name="lp"><?php
        echo translate_lang_opt('zh_CN',  '',     'zh_en',
                            sprintf( _("%s to %s"),_("Chinese, Simplified"),_("English"))) .
         translate_lang_opt('zh_TW',  '',     'zt_en',
                            sprintf( _("%s to %s"),_("Chinese, Traditional"),_("English"))) .
         translate_lang_opt('en_US', 'zh_CN', 'en_zh',
                            sprintf( _("%s to %s"),_("English"),_("Chinese, Simplified"))) .
         translate_lang_opt('en_US', 'zh_TW', 'en_zt',
                            sprintf( _("%s to %s"),_("English"),_("Chinese, Traditional"))) .
         translate_lang_opt('en_US', 'nl_NL',  'en_nl',
                            sprintf( _("%s to %s"),_("English"),_("Dutch"))) .
         translate_lang_opt('en_US', 'fr_FR',  'en_fr',
                            sprintf( _("%s to %s"),_("English"),_("French"))) .
         translate_lang_opt('en_US', 'de_DE', 'en_de',
                            sprintf( _("%s to %s"),_("English"),_("German"))) .
         translate_lang_opt('en_US', 'el_GR',  'en_el',
                            sprintf( _("%s to %s"),_("English"),_("Greek"))) .
         translate_lang_opt('en_US', 'it_IT', 'en_it',
                            sprintf( _("%s to %s"),_("English"),_("Italian"))) .
         translate_lang_opt('en_US', 'ja_JP', 'en_ja',
                            sprintf( _("%s to %s"),_("English"),_("Japanese"))) .
         translate_lang_opt('en_US', 'ko_KR', 'en_ko',
                            sprintf( _("%s to %s"),_("English"),_("Korean"))) .
         translate_lang_opt('en_US', 'pt*',   'en_pt',
                            sprintf( _("%s to %s"),_("English"),_("Portuguese"))) .
         translate_lang_opt('en_US', 'ru_RU',  'en_ru',
                            sprintf( _("%s to %s"),_("English"),_("Russian"))) .
         translate_lang_opt('en_US', 'es_ES', 'en_es',
                            sprintf( _("%s to %s"),_("English"),_("Spanish"))) .
         translate_lang_opt('nl_NL', '',      'nl_en',
                            sprintf( _("%s to %s"),_("Dutch"),_("English"))) .
         translate_lang_opt('nl_NL', '',      'nl_fr',
                            sprintf( _("%s to %s"),_("Dutch"),_("French"))) .
         translate_lang_opt('fr_FR', '',      'fr_en',
                            sprintf( _("%s to %s"),_("French"),_("English"))) .
         translate_lang_opt('fr_FR',  '',     'fr_de',
                            sprintf( _("%s to %s"),_("French"),_("German"))) .
         translate_lang_opt('fr_FR',  '',     'fr_el',
                            sprintf( _("%s to %s"),_("French"),_("Greek"))) .
         translate_lang_opt('fr_FR',  '',     'fr_it',
                            sprintf( _("%s to %s"),_("French"),_("Italian"))) .
         translate_lang_opt('fr_FR',  '',     'fr_pt',
                            sprintf( _("%s to %s"),_("French"),_("Portuguese"))) .
         translate_lang_opt('fr_FR',  '',     'fr_nl',
                            sprintf( _("%s to %s"),_("French"),_("Dutch"))) .
         translate_lang_opt('fr_FR',  '',     'fr_es',
                            sprintf( _("%s to %s"),_("French"),_("Spanish"))) .
         translate_lang_opt('de_DE', 'en_US', 'de_en',
                            sprintf( _("%s to %s"),_("German"),_("English"))) .
         translate_lang_opt('de_DE',  '',     'de_fr',
                            sprintf( _("%s to %s"),_("German"),_("French"))) .
         translate_lang_opt('el_GR', '',      'el_en',
                            sprintf( _("%s to %s"),_("Greek"),_("English"))) .
         translate_lang_opt('el_GR', '',      'el_fr',
                            sprintf( _("%s to %s"),_("Greek"),_("French"))) .
         translate_lang_opt('it_IT', '',      'it_en',
                            sprintf( _("%s to %s"),_("Italian"),_("English"))) .
         translate_lang_opt('it_IT', '',      'it_fr',
                            sprintf( _("%s to %s"),_("Italian"),_("French"))) .
         translate_lang_opt('ja_JP',  '',     'ja_en',
                            sprintf( _("%s to %s"),_("Japanese"),_("English"))) .
         translate_lang_opt('ko_KR',  '',     'ko_en',
                            sprintf( _("%s to %s"),_("Korean"),_("English"))) .
         translate_lang_opt('pt*',    '',     'pt_en',
                            sprintf( _("%s to %s"),_("Portuguese"),_("English"))) .
         translate_lang_opt('pt*',    '',     'pt_fr',
                            sprintf( _("%s to %s"),_("Portuguese"),_("French"))) .
         translate_lang_opt('ru_RU',  '',     'ru_en',
                            sprintf( _("%s to %s"),_("Russian"),_("English"))) .
         translate_lang_opt('es_ES',  '',     'es_en',
                            sprintf( _("%s to %s"),_("Spanish"),_("English"))) .
         translate_lang_opt('es_ES',  '',     'es_fr',
                            sprintf( _("%s to %s"),_("Spanish"),_("French")));
    echo '</select>'.
         'Babelfish: <input type="Submit" value="' . _("Translate") . '" />';

    translate_table_end();
}

/**
 * go.com translation engine (disabled)
 *
 * @param string $message text that has to be translated
 * @access private
 */
function translate_form_go($message) {
    translate_new_form('http://translator.go.com/cb/trans_entry');
?>
    <input type="hidden" name="input_type" value="text" />
    <select name="lp"><?php
        echo translate_lang_opt('en', 'es', 'en_sp',
                                sprintf( _("%s to %s"),
                                         _("English"),
                                         _("Spanish"))) .
             translate_lang_opt('',   'fr', 'en_fr',
                                sprintf( _("%s to %s"),
                                         _("English"),
                                         _("French"))) .
             translate_lang_opt('',   'de', 'en_ge',
                                sprintf( _("%s to %s"),
                                         _("English"),
                                         _("German"))) .
             translate_lang_opt('',   'it', 'en_it',
                                sprintf( _("%s to %s"),
                                         _("English"),
                                         _("Italian"))) .
             translate_lang_opt('',   'pt', 'en_pt',
                                sprintf( _("%s to %s"),
                                         _("English"),
                                         _("Portuguese"))) .
             translate_lang_opt('es', 'en', 'sp_en',
                                sprintf( _("%s to %s"),
                                         _("Spanish"),
                                         _("English"))) .
             translate_lang_opt('fr', '',   'fr_en',
                                sprintf( _("%s to %s"),
                                         _("French"),
                                         _("English"))) .
             translate_lang_opt('de', '',   'ge_en',
                                sprintf( _("%s to %s"),
                                         _("German"),
                                         _("English"))) .
             translate_lang_opt('it', '',   'it_en',
                                sprintf( _("%s to %s"),
                                         _("Italian"),
                                         _("English"))) .
             translate_lang_opt('pt', '',   'pt_en',
                                sprintf( _("%s to %s"),
                                         _("Portuguese"),
                                         _("English")));
    echo '</select>'.
         "<input type=\"hidden\" name=\"text\" value=\"$message\" />".
         'Go.com: <input type="submit" value="' . _("Translate") . '" />';

    translate_table_end();
}

/**
 * intertran translation engine
 *
 * @param string $message text that has to be translated
 * @access private
 */
function translate_form_intertran($message) {
    translate_new_form('http://intertran.tranexp.com/Translate/result.shtml');
    echo '<input type="hidden" name="topframe" value="yes" />'.
         '<input type="hidden" name="type" value="text" />'.
         '<input type="hidden" name="keyb" value="non" />'.
         "<input type=\"hidden\" name=\"text\" value=\"$message\" />";

    $left = '<select name="from">' .
        translate_lang_opt('pt_BR', '',    'pob', _("Brazilian Portuguese")).
        translate_lang_opt('bg_BG', '',    'bul', _("Bulgarian") . ' (CP 1251)').
        translate_lang_opt('hr_HR', '',    'cro', _("Croatian") . ' (CP 1250)').
        translate_lang_opt('cs_CZ', '',    'che', _("Czech") . ' (CP 1250)').
        translate_lang_opt('da_DK', '',    'dan', _("Danish")).
        translate_lang_opt('nl_NL', '',    'dut', _("Dutch")).
        translate_lang_opt('en_US', '!en', 'eng', _("English")).
        translate_lang_opt('tl_PH', '',    'tag', _("Filipino (Tagalog)")).
        translate_lang_opt('fi_FI', '',    'fin', _("Finnish")).
        translate_lang_opt('fr_FR', '',    'fre', _("French")).
        translate_lang_opt('de_DE', '',    'ger', _("German")).
        translate_lang_opt('el_GR', '',    'grk', _("Greek")).
        translate_lang_opt('hu_HU', '',    'hun', _("Hungarian") . ' (CP 1250)').
        translate_lang_opt('is_IS', '',    'ice', _("Icelandic")).
        translate_lang_opt('it_IT', '',    'ita', _("Italian")).
        translate_lang_opt('ja_JP', '',    'jpn', _("Japanese") . ' (Shift JIS)').
        translate_lang_opt('la',    '',    'ltt', _("Latin")).
        translate_lang_opt('es*',   '',    'spl', _("Latin American Spanish")).
        translate_lang_opt('no*',   '',    'nor', _("Norwegian")).
        translate_lang_opt('pl_PL', '',    'pol', _("Polish") . ' (ISO 8859-2)').
        translate_lang_opt('pt*',   '',    'poe', _("Portuguese")).
        translate_lang_opt('ro_RO', '',    'rom', _("Romanian") . ' (CP 1250)').
        translate_lang_opt('ru_RU', '',    'rus', _("Russian") . ' (CP 1251)').
        translate_lang_opt('sr_YU', '',    'sel', _("Serbian") . ' (CP 1250)').
        translate_lang_opt('sl_SI', '',    'slo', _("Slovenian") . ' (CP 1250)').
        translate_lang_opt('es_ES', '',    'spa', _("Spanish")).
        translate_lang_opt('sv_SE', '',    'swe', _("Swedish")).
        translate_lang_opt('tr_TR', '',    'tur', _("Turkish") . ' (CP 1254)').
        translate_lang_opt('cy_GB', '',    'wel', _("Welsh")).
        '</select>';

    $right = '<select name="to">'.
        translate_lang_opt('',    'pt_BR', 'pob', _("Brazilian Portuguese")).
        translate_lang_opt('',    'bg_BG', 'bul', _("Bulgarian") . ' (CP 1251)').
        translate_lang_opt('',    'hr_HR', 'cro', _("Croatian") . ' (CP 1250)').
        translate_lang_opt('',    'cs_CZ', 'che', _("Czech") . ' (CP 1250)').
        translate_lang_opt('',    'da_DK', 'dan', _("Danish")).
        translate_lang_opt('',    'nl_NL', 'dut', _("Dutch")).
        translate_lang_opt('!en', 'en_US', 'eng', _("English")).
        translate_lang_opt('',    'tl_PH', 'tag', _("Filipino (Tagalog)")).
        translate_lang_opt('',    'fi_FI', 'fin', _("Finnish")).
        translate_lang_opt('',    'fr_FR', 'fre', _("French")).
        translate_lang_opt('',    'de_DE', 'ger', _("German")).
        translate_lang_opt('',    'el_GR', 'grk', _("Greek")).
        translate_lang_opt('',    'hu_HU', 'hun', _("Hungarian") . ' (CP 1250)').
        translate_lang_opt('',    'is_IS', 'ice', _("Icelandic")).
        translate_lang_opt('',    'it_IT', 'ita', _("Italian")).
        translate_lang_opt('',    'ja_JP', 'jpn', _("Japanese") . ' (Shift JIS)').
        translate_lang_opt('',    'la',    'ltt', _("Latin")).
        translate_lang_opt('',    'es*',   'spl', _("Latin American Spanish")).
        translate_lang_opt('',    'no*',   'nor', _("Norwegian")).
        translate_lang_opt('',    'pl_PL', 'pol', _("Polish") . ' (ISO 8859-2)').
        translate_lang_opt('',    'pt_PT', 'poe', _("Portuguese")).
        translate_lang_opt('',    'ro_RO', 'rom', _("Romanian") . ' (CP 1250)').
        translate_lang_opt('',    'ru_RU', 'rus', _("Russian") . ' (CP 1251)').
        translate_lang_opt('',    'sr_YU', 'sel', _("Serbian") . ' (CP 1250)').
        translate_lang_opt('',    'sl_SI', 'slo', _("Slovenian") . ' (CP 1250)').
        translate_lang_opt('',    'es_ES', 'spa', _("Spanish")).
        translate_lang_opt('',    'sv_SE', 'swe', _("Swedish")).
        translate_lang_opt('',    'tr_TR', 'tur', _("Turkish") . ' (CP 1254)').
        translate_lang_opt('',    'cy_GB', 'wel', _("Welsh")).
        '</select>';
    printf( _("%s to %s"), $left, $right );
    echo 'InterTran: <input type="submit" value="' . _("Translate") . '" />';

    translate_table_end();
}

/**
 * gpltrans translation engine
 *
 * @param string $message text that has to be translated
 * @access private
 */
function translate_form_gpltrans($message) {
    translate_new_form('http://www.translator.cx/cgi-bin/gplTrans');
    echo '<select name="language">'.
        translate_lang_opt('', 'nl_NL', 'dutch_dict',      _("Dutch")).
        translate_lang_opt('', 'fr_FR', 'french_dict',     _("French")).
        translate_lang_opt('', 'de_DE', 'german_dict',     _("German")).
        translate_lang_opt('', 'id_ID', 'indonesian_dict', _("Indonesian")).
        translate_lang_opt('', 'it_IT', 'italian_dict',    _("Italian")).
        translate_lang_opt('', 'la',    'latin_dict',      _("Latin")).
        translate_lang_opt('', 'pt*',   'portuguese_dict', _("Portuguese")).
        translate_lang_opt('', 'es_ES', 'spanish_dict',    _("Spanish")).
        '</select>';
    echo '<select name="toenglish">';
    echo '<option value="yes" >'. _("to English") . '</option>';
    echo '<option value="no" selected>' . _("from English") . '</option></select>';
    echo "<input type=hidden name=text value=\"$message\" />".
        'GPLTrans: <input type="submit" value="' . _("Translate") . '" />';

    translate_table_end();
}

/**
 * reference.com (dictionary) translation engine
 *
 * @param string $message text that has to be translated
 * @access private
 */
function translate_form_dictionary($message) {
    translate_new_form('http://dictionary.reference.com/translate/text.html');
    list($usec, $sec) = explode(" ",microtime());
    $time = $sec . (float)$usec*100000000;
    echo "<input type=\"hidden\" name=\"text\" value=\"$message\" />".
         "<input type=\"hidden\" name=\"r\" value=\"$time\" />".
         '<select name="lp">'.
         translate_lang_opt('en_US', 'zh_CN', 'en_zh',
                            sprintf( _("%s to %s"),_("English"),_("Chinese, Simplified"))) .
         translate_lang_opt('en_US', 'zh_TW', 'en_zt',
                            sprintf( _("%s to %s"),_("English"),_("Chinese, Traditional"))) .
         translate_lang_opt('en_US', 'nl_NL', 'en_nl',
                            sprintf( _("%s to %s"),_("English"),_("Dutch"))) .
         translate_lang_opt('en_US', 'fr_FR', 'en_fr',
                            sprintf( _("%s to %s"),_("English"),_("French"))) .
         translate_lang_opt('en_US', 'de_DE', 'en_ge',
                            sprintf( _("%s to %s"),_("English"),_("German"))) .
         translate_lang_opt('en_US', 'el_GR', 'en_el',
                            sprintf( _("%s to %s"),_("English"),_("Greek"))) .
         translate_lang_opt('en_US', 'it_IT', 'en_it',
                            sprintf( _("%s to %s"),_("English"),_("Italian"))) .
         translate_lang_opt('en_US', 'ja_JP', 'en_ja',
                            sprintf( _("%s to %s"),_("English"),_("Japanese"))) .
         translate_lang_opt('en_US', 'ko_KR', 'en_ko',
                            sprintf( _("%s to %s"),_("English"),_("Korean"))) .
         translate_lang_opt('en_US', 'pt*',   'en_pt',
                            sprintf( _("%s to %s"),_("English"),_("Portuguese"))) .
         translate_lang_opt('en_US', 'ru_RU', 'en_ru',
                            sprintf( _("%s to %s"),_("English"),_("Russian"))) .
         translate_lang_opt('en_US', 'es_ES', 'en_es',
                            sprintf( _("%s to %s"),_("English"),_("Spanish"))) .
         translate_lang_opt('zh_CN',  '',     'zh_en',
                            sprintf( _("%s to %s"),_("Chinese, Simplified"),_("English"))) .
         translate_lang_opt('zh_TW',  '',     'zt_en',
                            sprintf( _("%s to %s"),_("Chinese, Traditional"),_("English"))) .
         translate_lang_opt('nl_NL',  '',     'nl_en',
                            sprintf( _("%s to %s"),_("Dutch"),_("English"))) .
         translate_lang_opt('fr_FR',  '',     'fr_en',
                            sprintf( _("%s to %s"),_("French"),_("English"))) .
         translate_lang_opt('de_DE', 'en_US', 'ge_en',
                            sprintf( _("%s to %s"),_("German"),_("English"))) .
         translate_lang_opt('el_GR', '',      'el_en',
                            sprintf( _("%s to %s"),_("Greek"),_("English"))) .
         translate_lang_opt('it_IT',  '',     'it_en',
                            sprintf( _("%s to %s"),_("Italian"),_("English"))) .
         translate_lang_opt('ja_JP',  '',     'ja_en',
                            sprintf( _("%s to %s"),_("Japanese"),_("English"))) .
         translate_lang_opt('ko_KR',  '',     'ko_en',
                            sprintf( _("%s to %s"),_("Korean"),_("English"))) .
         translate_lang_opt('pt*',    '',     'pt_en',
                            sprintf( _("%s to %s"),_("Portuguese"),_("English"))) .
         translate_lang_opt('ru_RU',  '',     'ru_en',
                            sprintf( _("%s to %s"),_("Russian"),_("English"))) .
         translate_lang_opt('es_ES',  '',     'es_en',
                            sprintf( _("%s to %s"),_("Spanish"),_("English"))) .
         '</select>'.
         'Dictionary.com: <input type="submit" value="'._("Translate").'" />';

  translate_table_end();
}

/**
 * otenet translation engine
 *
 * @param string $message text that has to be translated
 * @access private
 */
function translate_form_otenet($message) {
    translate_new_form('http://trans.otenet.gr/systran/box');
?>
    <input type="hidden" name="doit" value="done" />
    <input name="partner" value="OTEnet-en" type="hidden" />
    <input type="hidden" name="urltext" value="<?php echo $message; ?>" />
    <select name="lp" size="1"><?php
         echo translate_lang_opt('en_US', 'el_GR', 'en_el',
                                sprintf( _("%s to %s"),_("English"),_("Greek"))) .
         translate_lang_opt('el_GR', 'en_US', 'el_en',
                            sprintf( _("%s to %s"),_("Greek"),_("English"))) .
         translate_lang_opt('fr_FR', '',      'fr_el',
                                sprintf( _("%s to %s"),_("French"),_("Greek"))) .
         translate_lang_opt('el_GR', 'fr_FR', 'el_fr',
                            sprintf( _("%s to %s"),_("Greek"),_("French"))) .
         translate_lang_opt('#',  '',  '', '----------------') .
         translate_lang_opt('en_US', '',      'en_fr',
                            sprintf( _("%s to %s"),_("English"),_("French"))) .
         translate_lang_opt('fr_FR', '',      'fr_en',
                            sprintf( _("%s to %s"),_("French"),_("English"))) .
         translate_lang_opt('en_US', 'de_DE', 'en_de',
                            sprintf( _("%s to %s"),_("English"),_("German"))) .
         translate_lang_opt('de_DE', '',      'de_en',
                            sprintf( _("%s to %s"),_("German"),_("English"))) .
         translate_lang_opt('en_US', 'es_ES', 'en_es',
                            sprintf( _("%s to %s"),_("English"),_("Spanish"))) .
         translate_lang_opt('es_ES', '',      'es_en',
                            sprintf( _("%s to %s"),_("Spanish"),_("English"))) .
         translate_lang_opt('en_US', 'it_IT', 'en_it',
                            sprintf( _("%s to %s"),_("English"),_("Italian"))) .
         translate_lang_opt('it_IT', '',      'it_en',
                            sprintf( _("%s to %s"),_("Italian"),_("English"))) .
         translate_lang_opt('en_US', 'pt*',   'en_pt',
                            sprintf( _("%s to %s"),_("English"),_("Portuguese"))) .
         translate_lang_opt('pt*',   '',      'pt_en',
                            sprintf( _("%s to %s"),_("Portuguese"),_("English"))) .
         translate_lang_opt('fr_FR', '',      'fr_de',
                            sprintf( _("%s to %s"),_("French"),_("German"))) .
         translate_lang_opt('de_DE', '',      'de_fr',
                            sprintf( _("%s to %s"),_("German"),_("French"))) .
         translate_lang_opt('fr_FR', '',      'fr_es',
                            sprintf( _("%s to %s"),_("French"),_("Spanish"))) .
         translate_lang_opt('es_ES', '',      'es_fr',
                            sprintf( _("%s to %s"),_("Spanish"),_("French"))) .
         translate_lang_opt('fr_FR', 'nl_NL', 'fr_nl',
                            sprintf( _("%s to %s"),_("French"),_("Dutch"))) .
         translate_lang_opt('nl_NL', '',      'nl_fr',
                            sprintf( _("%s to %s"),_("Dutch"),_("French"))) ;
    echo '</select>'.
         'OTEnet: <input type="submit" value="' . _("Translate") . '" />';
    translate_table_end();

}

/**
 * promt translation engine
 *
 * @param string $message text that has to be translated
 * @access private
 */
function translate_form_promt($message) {
    translate_new_form('http://www.online-translator.com/text.asp#tr_form');
    echo '<input type="hidden" name="status" value="translate" />';
    echo "<input type=\"hidden\" name=\"source\" value=\"$message\" />";
    echo _("Interface language")." : ";
    echo "<select size=\"1\" name=\"lang\">\n";
    echo "<option value=\"en\">" . _("English") . "</option>\n";
    echo "<option value=\"ru\">" . _("Russian") . "</option>\n";
    echo "<option value=\"de\">" . _("German") . "</option>\n";
    echo "<option value=\"fr\">" . _("French") . "</option>\n";
    echo "<option value=\"es\">" . _("Spanish") . "</option>\n";
    echo "</select><br>\n";
    echo _("Translation direction")." : ";
    echo '<select size="1" id="direction" name="direction">';
        echo translate_lang_opt('en_US', 'ru_RU', 'er',
                                sprintf( _("%s to %s"),_("English"),_("Russian"))) .
            translate_lang_opt('ru_RU', 'en_US', 're',
                               sprintf( _("%s to %s"),_("Russian"),_("English"))) .
            translate_lang_opt('de_DE', '',      'gr',
                               sprintf( _("%s to %s"),_("German"),_("Russian"))) .
            translate_lang_opt('ru_RU', 'de_DE', 'rg',
                               sprintf( _("%s to %s"),_("Russian"),_("German"))) .
            translate_lang_opt('fr_FR',  '',     'fr',
                               sprintf( _("%s to %s"),_("French"),_("Russian"))) .
            translate_lang_opt('ru_RU', 'fr_FR', 'rf',
                               sprintf( _("%s to %s"),_("Russian"),_("French"))) .
            translate_lang_opt('es_ES', '',      'sr',
                               sprintf( _("%s to %s"),_("Spanish"),_("Russian"))) .
            translate_lang_opt('ru_RU', 'es_ES', 'rs',
                               sprintf( _("%s to %s"),_("Russian"),_("Spanish"))) .
            translate_lang_opt('it_IT', '',      'ir',
                               sprintf( _("%s to %s"),_("Italian"),_("Russian"))) .
            translate_lang_opt('en_US', '',      'eg',
                               sprintf( _("%s to %s"),_("English"),_("German"))) .
            translate_lang_opt('de_DE', '',      'ge',
                               sprintf( _("%s to %s"),_("German"),_("English"))) .
            translate_lang_opt('en_US', '',      'es',
                               sprintf( _("%s to %s"),_("English"),_("Spanish"))) .
            translate_lang_opt('es_ES', '',  'se',
                               sprintf( _("%s to %s"),_("Spanish"),_("English"))) .
            translate_lang_opt('en_US', '',  'ef',
                               sprintf( _("%s to %s"),_("English"),_("French"))) .
            translate_lang_opt('fr_FR', '',  'fe',
                               sprintf( _("%s to %s"),_("French"),_("English"))) .
            translate_lang_opt('en_US', '',  'ep',
                               sprintf( _("%s to %s"),_("English"),_("Portuguese")));
    echo "</select><br>\n";
    echo "<input type=\"hidden\" name=\"template\" value=\"General\" />\n";
    echo 'PROMT: <input type="submit" value="' . _("Translate") . '" />';

    translate_table_end();
}

/**
 * google translation engine
 *
 * @param string $message text that has to be translated
 * @access private
 */
function translate_form_google($message) {
    translate_new_form('http://www.google.com/translate_t','utf-8');
    echo '<input type="hidden" name="text" value="' . $message . '" />';
    echo '<select name="langpair">'.
         translate_lang_opt('en_US', 'de_DE', 'en|de',
                            sprintf( _("%s to %s"),_("English"),_("German"))) .
         translate_lang_opt('en_US', 'es_ES',  'en|es',
                            sprintf( _("%s to %s"),_("English"),_("Spanish"))) .
         translate_lang_opt('en_US', 'fr_FR', 'en|fr',
                            sprintf( _("%s to %s"),_("English"),_("French"))) .
         translate_lang_opt('en_US', 'it_IT', 'en|it',
                            sprintf( _("%s to %s"),_("English"),_("Italian"))) .
         translate_lang_opt('en_US', 'pt*',   'en|pt',
                            sprintf( _("%s to %s"),_("English"),_("Portuguese"))) .
         translate_lang_opt('en_US', 'ar',    'en|ar',
                            sprintf( _("%s to %s"),_("English"),_("Arabic"))) .
         translate_lang_opt('en_US', 'ja_JP', 'en|ja',
                            sprintf( _("%s to %s"),_("English"),_("Japanese"))) .
         translate_lang_opt('en_US', 'ko_KR', 'en|ko',
                            sprintf( _("%s to %s"),_("English"),_("Korean"))) .
         translate_lang_opt('en_US', 'zh_CN', 'en|zh-CN',
                            sprintf( _("%s to %s"),_("English"),_("Chinese, Simplified"))) .
         translate_lang_opt('de_DE', 'en_US', 'de|en',
                            sprintf( _("%s to %s"),_("German"),_("English"))) .
         translate_lang_opt('de_DE', '', 'de|fr',
                            sprintf( _("%s to %s"),_("German"),_("French"))) .
         translate_lang_opt('es_ES', '', 'es|en',
                            sprintf( _("%s to %s"),_("Spanish"),_("English"))) .
         translate_lang_opt('fr_FR', '', 'fr|en',
                            sprintf( _("%s to %s"),_("French"),_("English"))) .
         translate_lang_opt('fr_FR', '', 'fr|de',
                            sprintf( _("%s to %s"),_("French"),_("German"))) .
         translate_lang_opt('it_IT', '', 'it|en',
                            sprintf( _("%s to %s"),_("Italian"),_("English"))) .
         translate_lang_opt('pt*',   '', 'pt|en',
                            sprintf( _("%s to %s"),_("Portuguese"),_("English"))).
         translate_lang_opt('ar',    '', 'ar|en',
                            sprintf( _("%s to %s"),_("Arabic"),_("English"))).
         translate_lang_opt('ja_JP', '', 'ja|en',
                            sprintf( _("%s to %s"),_("Japanese"),_("English"))).
         translate_lang_opt('ko_KR', '', 'ko|en',
                            sprintf( _("%s to %s"),_("Korean"),_("English"))).
         translate_lang_opt('zh_CN', '', 'zh-CN|en',
                            sprintf( _("%s to %s"),_("Chinese, Simplified"),_("English")));
    echo '</select>'.
        '<input type="hidden" name="hl" value="en" />' .
        '<input type="hidden" name="ie" value="UTF8" />' .
        '<input type="hidden" name="oe" value="UTF8" />' .
        'Google: <input type="submit" value="' . _("Translate") . '" />';

    translate_table_end();
}
