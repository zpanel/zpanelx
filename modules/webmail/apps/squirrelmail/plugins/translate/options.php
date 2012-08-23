<?php

/**
 * options.php
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Pick your translator to translate the body of incoming mail messages
 *
 * @version $Id: options.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage translate
 */

/* Path for SquirrelMail required files. */
define('SM_PATH','../../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/strings.php');
require_once(SM_PATH . 'functions/page_header.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'include/load_prefs.php');

displayPageHeader($color, 'None');

if (isset($_POST['submit_translate']) && $_POST['submit_translate'] ) {
    if (isset($_POST['translate_translate_server'])) {
        setPref($data_dir, $username, 'translate_server', $_POST['translate_translate_server']);
    } else {
        setPref($data_dir, $username, 'translate_server', 'babelfish');
    }

    if (isset($_POST['translate_translate_location'])) {
        setPref($data_dir, $username, 'translate_location', $_POST['translate_translate_location']);
    } else {
        setPref($data_dir, $username, 'translate_location', 'center');
    }

    if (isset($_POST['translate_translate_show_read'])) {
        setPref($data_dir, $username, 'translate_show_read', '1');
    } else {
        setPref($data_dir, $username, 'translate_show_read', '');
    }

    if (isset($_POST['translate_translate_show_send'])) {
        setPref($data_dir, $username, 'translate_show_send', '1');
    } else {
        setPref($data_dir, $username, 'translate_show_send', '');
    }

    if (isset($_POST['translate_translate_same_window'])) {
        setPref($data_dir, $username, 'translate_same_window', '1');
    } else {
        setPref($data_dir, $username, 'translate_same_window', '');
    }
}

$translate_server = getPref($data_dir, $username, 'translate_server');
if ($translate_server == '') {
    $translate_server = 'babelfish';
}
$translate_location = getPref($data_dir, $username, 'translate_location');
if ($translate_location == '') {
    $translate_location = 'center';
}
$translate_show_read = getPref($data_dir, $username, 'translate_show_read');
$translate_show_send = getPref($data_dir, $username, 'translate_show_send');
$translate_same_window = getPref($data_dir, $username, 'translate_same_window');


function ShowOption($Var, $value, $Desc) {
    $Var = 'translate_' . $Var;

    global $$Var;

    echo '<option value="' . $value . '"';
    if ($$Var == $value) {
            echo ' selected';
    }
    echo '>' . $Desc . "</option>\n";
}

function ShowTrad( $tit, $com, $url ) {
    echo "<li><b>$tit</b> - ".
        $com .
        "[ <a href=\"$url\" target=\"_blank\">$tit</a> ]</li>";
}

?>
<table width="95%" align="center" border="0" cellpadding="1" cellspacing="0">
<tr><td bgcolor="<?php echo $color[0] ?>">
<center><b><?php echo _("Options") . ' - '. _("Translator"); ?></b></center>
</td></tr></table>

<?php if (isset($_POST['submit_translate']) && $_POST['submit_translate'] ) {
    print "<center><h4>"._("Saved Translation Options")."</h4></center>\n";
}?>

<p><?php echo _("Your server options are as follows:"); ?></p>

<ul>
<?php
ShowTrad( 'Babelfish',
          _("Maximum of 150 words translated, powered by Systran").
          '<br />'.sprintf(_("Number of supported language pairs: %s"),'36').' ' ,
          'http://babelfish.altavista.com/' );
/** engine is disabled, because it is not available
    ShowTrad( 'Translator.Go.com',
              _("Maximum of 25 kilobytes translated, powered by Systran").
              '<br />'.sprintf(_("Number of supported language pairs: %s"),'10').' ' ,
              'http://translator.go.com/' );
*/
ShowTrad( 'Dictionary.com',
          _("No known limits, powered by Systran").
          '<br />'.sprintf(_("Number of supported language pairs: %s"),'24').' ' ,
          'http://www.dictionary.com/translate' );
ShowTrad( 'InterTran',
          _("No known limits, powered by Translation Experts' InterTran").
          '<br />'.sprintf(_("Number of supported languages: %s"),'29').' ' ,
          'http://www.tranexp.com/' );
/** engine is disabled, because it is not available
 ** correct way of implementing it, is to provide engine url configuration option.
 ** implemented in 1.5.1cvs.
    ShowTrad( 'GPLTrans',
              _("No known limits, powered by GPLTrans (free, open source)").
              '<br />'.sprintf(_("Number of supported language pairs: %s"),'8').' ' ,
              'http://www.translator.cx/' );
*/
ShowTrad( 'OTEnet',
          _("Hellenic translations, no known limits, powered by Systran").
          '<br />'.sprintf(_("Number of supported language pairs: %s"),'20').' ' ,
          'http://systran.otenet.gr/' );
ShowTrad( 'PROMT',
          _("Russian translations, maximum of 500 characters translated").
          '<br />'.sprintf(_("Number of supported language pairs: %s"),'16').' ' ,
          'http://www.online-translator.com/' );
ShowTrad( 'Google Translate',
          _("No known limits, powered by Systran").
          '<br />'.sprintf(_("Number of supported language pairs: %s"),'20').' ' ,
          'http://www.google.com/translate' );
?>
</ul>
<p>
<?php
echo _("You also decide if you want the translation box displayed, and where it will be located.") .
    "<form action=\"$PHP_SELF\" method=\"post\">".
    '<table border="0" cellpadding="0" cellspacing="2">'.
    '<tr><td align="right" nowrap>' .
    _("Select your translator:") .
    '</td>'.
    '<td><select name="translate_translate_server">';

ShowOption('server', 'babelfish', 'Babelfish');
// ShowOption('server', 'go', 'Go.com');
ShowOption('server', 'dictionary', 'Dictionary.com');
ShowOption('server', 'intertran', 'Intertran');
// ShowOption('server', 'gpltrans', 'GPLTrans');
ShowOption('server', 'otenet', 'OTEnet');
ShowOption('server', 'promt', 'PROMT');
ShowOption('server', 'google', 'Google');
echo '</select>' .
    '</td></tr>' .
    '<tr><td align="right" nowrap>' .
    _("When reading:") .
    '</td>'.
    '<td><input type="checkbox" name="translate_translate_show_read"';
if ($translate_show_read) echo " checked";
echo ' /> - ' . _("Show translation box") .
    ' <select name="translate_translate_location">';
ShowOption('location', 'left', _("to the left"));
ShowOption('location', 'center', _("in the center"));
ShowOption('location', 'right', _("to the right"));
echo '</select><br>'.
    '<input type="checkbox" name="translate_translate_same_window"';
if ($translate_same_window) echo " checked";
echo ' /> - ' . _("Translate inside the SquirrelMail frames").
    '</td></tr>';
// compose option is disabled in stable, because it is not implemented.
/*
    echo '<tr><td align="right" nowrap>'.
    _("When composing:") . '</td>';
echo '<td><input type="checkbox" name="translate_translate_show_send"';
if ($translate_show_send)
     echo " checked";
     echo ' /> - ' . _("Not yet functional, currently does nothing") .
        '</td></tr>';
*/
echo '<tr><td></td><td>'.
    '<input type="submit" value="' . _("Submit") . '" name="submit_translate" />'.
    '</td></tr>'.
    '</table>'.
    '</form>';
?>
</body></html>