<?php
/**
 * mailout.php
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * $Id: mailout.php 14088 2011-02-01 13:06:03Z avel $
 * @package plugins
 * @subpackage listcommands
 */

define('SM_PATH','../../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
include_once(SM_PATH . 'functions/page_header.php');
include_once(SM_PATH . 'include/load_prefs.php');
include_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'functions/identity.php');

displayPageHeader($color, null);

/* get globals */
sqgetGlobalVar('mailbox', $mailbox, SQ_GET);
sqgetGlobalVar('send_to', $send_to, SQ_GET);
sqgetGlobalVar('subject', $subject, SQ_GET);
sqgetGlobalVar('body',    $body,    SQ_GET);
sqgetGlobalVar('action',  $action,  SQ_GET);

switch ( $action ) {
case 'help':
    $out_string = _("This will send a message to %s requesting help for this list. You will receive an emailed response at the address below.");
    break;
case 'subscribe':
    $out_string = _("This will send a message to %s requesting that you will be subscribed to this list. You will be subscribed with the address below.");
    break;
case 'unsubscribe':
    $out_string = _("This will send a message to %s requesting that you will be unsubscribed from this list. It will try to unsubscribe the adress below.");
    break;
default:
    error_box(sprintf(_("Unknown action: %s"),htmlspecialchars($action)), $color);
    exit;
}

echo html_tag('p', '', 'left' ) .
html_tag( 'table', '', 'center', $color[0], 'border="0" width="75%"' ) . "\n" .
    html_tag( 'tr',
        html_tag( 'th', _("Mailinglist") . ' ' . _($action), '', $color[9] )
    ) .
    html_tag( 'tr' ) .
    html_tag( 'td', '', 'left' );


printf( $out_string, htmlspecialchars($send_to) );

echo '<form method="post" action="../../src/compose.php">'.
     '<input type="hidden" name="smtoken" value="' . sm_generate_security_token() . '" />';

$idents = get_identities();

echo html_tag('p', '', 'center' ) . _("From:") . ' ';

if (count($idents) > 1) {
    echo '<select name="identity">';
    foreach($idents as $nr=>$data) {
        echo '<option value="' . $nr . '">' .
            htmlspecialchars(
                $data['full_name'].' <'.
                $data['email_address'] . ">\n");
    }
    echo '</select>' . "\n" ;
} else {
    echo htmlspecialchars('"'.$idents[0]['full_name'].'" <'.$idents[0]['email_address'].'>');
}

echo '<br /><br />'
. '<input type="hidden" name="send_to" value="' . htmlspecialchars($send_to) . '">'
. '<input type="hidden" name="subject" value="' . htmlspecialchars($subject) . '">'
. '<input type="hidden" name="body" value="' . htmlspecialchars($body) . '">'
. '<input type="hidden" name="mailbox" value="' . htmlspecialchars($mailbox) . '">'
. '<input type="submit" name="send" value="' . _("Send Mail") . '"><br /><br />'
. '</form></td></tr></table></body></html>';

