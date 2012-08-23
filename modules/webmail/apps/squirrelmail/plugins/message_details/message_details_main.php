<?php
/**
 * Message Details plugin - main frame
 *
 * Plugin to view the RFC822 raw message output and the bodystructure of 
 * a message
 *
 * @author Marc Groot Koerkamp
 * @copyright 2002 Marc Groot Koerkamp, The Netherlands
 * @copyright 2004-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: message_details_main.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage message_details
 */

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');

displayHtmlHeader( _("Message Details"), '', FALSE );

sqgetGlobalVar('mailbox', $mailbox, SQ_GET);
sqgetGlobalVar('passed_id', $passed_id, SQ_GET);
if (!sqgetGlobalVar('passed_ent_id', $passed_ent_id, SQ_GET))
    $passed_ent_id = 0;

echo "<frameset rows=\"60, *\" noresize border=\"0\">\n";
echo '<frame src="message_details_top.php?mailbox=' 
    . urlencode($mailbox) .'&amp;passed_id=' . $passed_id
    . '&amp;passed_ent_id=' . $passed_ent_id
    . '" name="top_frame" scrolling="off">';
echo '<frame src="message_details_bottom.php?mailbox=' 
    . urlencode($mailbox) .'&amp;passed_id=' . $passed_id 
    . '&amp;passed_ent_id=' . $passed_ent_id
    . '" name="bottom_frame">';
echo '</frameset>'."\n"."</html>\n";
?>
