<?php

/**
 * image.php
 *
 * This file shows an attached image
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: image.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the image page */
define('PAGE_NAME', 'image');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/date.php');
require_once(SM_PATH . 'functions/page_header.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'include/load_prefs.php');

displayPageHeader($color, 'None');

/* globals */
if ( sqgetGlobalVar('passed_id', $temp, SQ_GET) ) {
  $passed_id = (int) $temp;
}
sqgetGlobalVar('mailbox',       $mailbox,       SQ_GET);
sqgetGlobalVar('ent_id',        $ent_id,        SQ_GET);
sqgetGlobalVar('QUERY_STRING',  $QUERY_STRING,  SQ_SERVER);
/* end globals */

echo '<br />' . 
    '<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">' .
    "\n" .
    '<tr><td bgcolor="' . $color[0] . '">' .
    '<b><center>' .
    _("Viewing an image attachment") . " - ";

$msg_url = 'read_body.php?' . $QUERY_STRING;
$msg_url = set_url_var($msg_url, 'ent_id', 0);
echo '<a href="'.$msg_url.'">'. _("View message") . '</a>';


$DownloadLink = '../src/download.php?passed_id=' . $passed_id .
               '&amp;mailbox=' . urlencode($mailbox) . 
               '&amp;ent_id=' . urlencode($ent_id) . '&amp;absolute_dl=true';
?>
</b></td></tr>
<tr><td align="center">
<a href="<?php echo $DownloadLink; ?>"><?php echo _("Download this as a file"); ?></a>
<br />&nbsp;</td></tr></table>

<table border="0" cellspacing="0" cellpadding="2" align="center">
<tr><td bgcolor="<?php echo $color[4]; ?>">
<img src="<?php echo $DownloadLink; ?>" />

</td></tr></table>
</body></html>