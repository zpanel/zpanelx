<?php

/**
 * vcard.php
 *
 * This file shows an attched vcard
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: vcard.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the vcard page */
define('PAGE_NAME', 'vcard');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
Define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/date.php');
require_once(SM_PATH . 'functions/page_header.php');
require_once(SM_PATH . 'functions/mime.php');
require_once(SM_PATH . 'include/load_prefs.php');

/* globals */
sqgetGlobalVar('username', $username, SQ_SESSION);
sqgetGlobalVar('key', $key, SQ_COOKIE);
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);

sqgetGlobalVar('passed_id', $passed_id, SQ_GET);
sqgetGlobalVar('mailbox', $mailbox, SQ_GET);
sqgetGlobalVar('ent_id', $ent_id, SQ_GET);
sqgetGlobalVar('startMessage', $startMessage, SQ_GET);
/* end globals */

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
sqimap_mailbox_select($imapConnection, $mailbox);


displayPageHeader($color, 'None');

echo '<br /><table width="100%" border="0" cellspacing="0" cellpadding="2" ' .
        'align="center">' . "\n" .
     '<tr><td bgcolor="' . $color[0] . '">' .
     '<b><center>' .
     _("Viewing a Business Card") . " - ";

$msg_url = 'read_body.php?mailbox='.urlencode($mailbox).
    '&amp;startMessage='.urlencode($startMessage).
    '&amp;passed_id='.urlencode($passed_id);

$msg_url = set_url_var($msg_url, 'ent_id', 0);

echo '<a href="'.$msg_url.'">'. _("View message") . '</a>';

echo '</center></b></td></tr>';

$message = sqimap_get_message($imapConnection, $passed_id, $mailbox);

$entity_vcard = getEntity($message,$ent_id);

$vcard = mime_fetch_body ($imapConnection, $passed_id, $ent_id);
$vcard = decodeBody($vcard, $entity_vcard->header->encoding);
$vcard = explode ("\n",$vcard);
foreach ($vcard as $l) {
    $k = substr($l, 0, strpos($l, ':'));
    $v = substr($l, strpos($l, ':') + 1);
    $attributes = explode(';', $k);
    $k = strtolower(array_shift($attributes));
    foreach ($attributes as $attr)     {
        if ($attr == 'quoted-printable')
        $v = quoted_printable_decode($v);
        else
            $k .= ';' . strtolower($attr);
    }

    $v = str_replace(';', "\n", $v);
    $vcard_nice[$k] = $v;
}

if ($vcard_nice['version'] == '2.1') {
    // get firstname and lastname for sm addressbook
    $vcard_nice['firstname'] = substr($vcard_nice['n'],
    strpos($vcard_nice['n'], "\n") + 1, strlen($vcard_nice['n']));
    $vcard_nice['lastname'] = substr($vcard_nice['n'], 0,
        strpos($vcard_nice['n'], "\n"));
    // workaround for Outlook, should be fixed in a better way,
    // maybe in new 'vCard' class.
    if (isset($vcard_nice['email;pref;internet'])) {
       $vcard_nice['email;internet'] = $vcard_nice['email;pref;internet'];
    }
} else {
    echo '<tr><td align="center">' .
         sprintf(_("vCard Version %s is not supported. Some information might not be converted correctly."),
                 htmlspecialchars($vcard_nice['version'])) .
         "</td></tr>\n";
    $vcard_nice['firstname'] = '';
    $vcard_nice['lastname'] = '';
}

foreach ($vcard_nice as $k => $v) {
    $v = htmlspecialchars($v);
    $v = trim($v);
    $vcard_safe[$k] = trim(nl2br($v));
}

$ShowValues = array(
    'fn' =>             _("Name"),
    'title' =>          _("Title"),
    'email;internet' => _("E-mail"),
    'url' =>            _("Web Page"),
    'org' =>            _("Organization / Department"),
    'adr' =>            _("Address"),
    'tel;work' =>       _("Work Phone"),
    'tel;home' =>       _("Home Phone"),
    'tel;cell' =>       _("Cellular Phone"),
    'tel;fax' =>        _("Fax"),
    'note' =>           _("Note"));

echo '<tr><td><br />' .
     '<table border="0" cellpadding="2" cellspacing="0" align="center">' . "\n";

if (isset($vcard_safe['email;internet'])) {
    $vcard_safe['email;internet'] = '<a href="../src/compose.php?send_to=' .
        $vcard_safe['email;internet'] . '">' . $vcard_safe['email;internet'] .
        '</a>';
}

if (isset($vcard_safe['url'])) {
    $vcard_safe['url'] = '<a href="' . $vcard_safe['url'] . '">' .
        $vcard_safe['url'] . '</a>';
}

foreach ($ShowValues as $k => $v) {
    if (isset($vcard_safe[$k]) && $vcard_safe[$k])     {
        echo "<tr><td align=\"right\"><b>$v:</b></td><td>" . $vcard_safe[$k] .
                "</td><tr>\n";
    }
}

echo '</table>' .
     '<br />' .
     '</td></tr></table>' .
     '<table width="100%" border="0" cellspacing="0" cellpadding="2" ' .
        'align="center">' .
     '<tr>' .
     '<td bgcolor="' . $color[0] . '">' .
     '<b><center>' .
     _("Add to address book") .
     '</td></tr>' .
     '<tr><td align="center">' .
     '<form action="../src/addressbook.php" method="post" name="f_add">' .
     '<input type="hidden" name="smtoken" value="' . sm_generate_security_token() . '" />' .
     '<table border="0" cellpadding="2" cellspacing="0" align="center">' .
     '<tr><td align="right"><b>' . _("Nickname") . ':</b></td>' .
     '<td>' .
     '<input type="text" name="addaddr[nickname]" size="20" value="' .
     $vcard_safe['firstname'] . '-' . $vcard_safe['lastname'] . '" />' .
     '</td></tr>' .
     '<tr><td align="right"><b>' . _("Additional info") . ':</b></td><td>' .
     '<select name="addaddr[label]">';

if (isset($vcard_nice['url'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['url']) .
        '">' . _("Web Page") . "</option>\n";
}
if (isset($vcard_nice['adr'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['adr']) .
        '">' . _("Address") . "</option>\n";
}
if (isset($vcard_nice['title'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['title']) .
        '">' . _("Title") . "</option>\n";
}
if (isset($vcard_nice['org'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['org']) .
        '">' . _("Organization / Department") . "</option>\n";
}
if (isset($vcard_nice['title'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['title']) .
        '; ' . htmlspecialchars($vcard_nice['org']) .
        '">' . _("Title &amp; Org. / Dept.") . "</option>\n";
}
if (isset($vcard_nice['tel;work'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['tel;work']) .
        '">' . _("Work Phone") . "</option>\n";
}
if (isset($vcard_nice['tel;home'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['tel;home']) .
        '">' . _("Home Phone") . "</option>\n";
}
if (isset($vcard_nice['tel;cell'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['tel;cell']) .
        '">' . _("Cellular Phone") . "</option>\n";
}
if (isset($vcard_nice['tel;fax'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['tel;fax']) .
        '">' . _("Fax") . "</option>\n";
}
if (isset($vcard_nice['note'])) {
    echo '<option value="' . htmlspecialchars($vcard_nice['note']) .
        '">' . _("Note") . "</option>\n";
}
echo '</select>';
?>
</td></tr>
<tr><td colspan="2" align="center">
<?php
echo '<input name="addaddr[email]" type="hidden" value="' .
        htmlspecialchars(!empty($vcard_nice['email;internet'])?$vcard_nice['email;internet']:'') . '" />' .
     '<input name="addaddr[firstname]" type="hidden" value="' .
        $vcard_safe['firstname'] . '" />' .
     '<input name="addaddr[lastname]" type="hidden" value="' .
        $vcard_safe['lastname'] . '" />' .
     '<input type="submit" name="addaddr[SUBMIT]" ' .
        'value="'._("Add to address book").'" />';

?>
</td></tr>
</table>
</form>
</td></tr>
<tr><td align="center">
<?php
echo '<a href="../src/download.php?absolute_dl=true&amp;passed_id='.
     urlencode($passed_id) . '&amp;mailbox=' . urlencode($mailbox) .
     '&amp;ent_id=' . urlencode($ent_id) .'">'.
     _("Download this as a file") . '</a>';
?>
</td></tr></table>

<table border="0" cellspacing="0" cellpadding="2" align="center">
<tr><td bgcolor="<?php echo $color[4]; ?>">
</td></tr></table>
</body></html>
