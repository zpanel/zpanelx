<?php

   /**
    **  options.php -- SpamCop options page
    **
    **  Copyright (c) 1999-2011 The SquirrelMail Project Team
    **  Licensed under the GNU GPL. For full terms see the file COPYING.
    **
    **  $Id: options.php 14084 2011-01-06 02:44:03Z pdontthink $
    **/

define('SM_PATH','../../');
require_once(SM_PATH . 'include/validate.php');

function spamcop_enable_disable($option,$disable_action,$enable_action) {
    if ($option) { 
	$ret= _("Enabled") . " (<a href=\"options.php?action=$disable_action\">" . _("Disable it") . "</a>)\n";
    } else {
	$ret = _("Disabled") . " (<a href=\"options.php?action=$enable_action\">" . _("Enable it") . "</a>)\n";
    }
    return $ret;
}

displayPageHeader($color, 'None');
   
/* globals */
sqgetGlobalVar('action', $action);
sqgetGlobalVar('meth', $meth);
sqgetGlobalVar('ID' , $ID);

sqgetGlobalVar('username', $username, SQ_SESSION);
/* end of globals */

$action = (!isset($action) ? '' : $action);

switch ($action) {
    case 'enable':
        setPref($data_dir, $username, 'spamcop_enabled', 1);
        break;
    case 'disable':
        setPref($data_dir, $username, 'spamcop_enabled', '');
        break;
    case 'save':
        setPref($data_dir, $username, 'spamcop_delete', '');
        break;
    case 'delete':
        setPref($data_dir, $username, 'spamcop_delete', 1);
        break;
    case 'meth':
        if (isset($meth)) {
            setPref($data_dir, $username, 'spamcop_method', $meth);
        }
        break;
    case 'save_id':
        if (isset($ID)) {
            $ID = trim($ID);
            $ID = preg_replace('/@.*/','',$ID);
            $ID = preg_replace('/.*\./','',$ID);
            setPref($data_dir, $username, 'spamcop_id', $ID);
        }
        break;
}

global $spamcop_enabled, $spamcop_delete, $spamcop_quick_report;
spamcop_load();

?>
      <br />
      <table width="95%" align="center" border="0" cellpadding="2" cellspacing="0">
      <tr><td bgcolor="<?php echo $color[0]; ?>">
         <center><b>
	 <?php echo _("Options") . " - " . _("Spam reporting"); ?>
	 </b></center>
      </td></tr></table>
      <br />
      
      <table align="center">
        <tr>
	  <?php
	  echo html_tag('td',_("SpamCop link is:"),'right');
	  echo html_tag('td', spamcop_enable_disable($spamcop_enabled,'disable','enable') );
	  ?>
	</tr>
        <tr>
	  <?php
	  echo html_tag('td',_("Delete spam when reported:") . "<br />\n" .
	  '<small>(' . _("Only works with email-based reporting") . ')</small>',
	  'right','','valign="top"');
	  echo html_tag('td', spamcop_enable_disable($spamcop_delete,'save','delete'),'','','valign="top"');
	  ?>
	</tr>
	<tr>
	  <?php
	  echo html_tag('td',_("Spam Reporting Method:"),'right','','valign="top"');
	  ?>
	  <td>
	  <form method="post" action="options.php">
	    <select name="meth">
		<?php
		    if ($spamcop_quick_report) {
			echo '<option value="quick_email"';
    	    		if ($spamcop_method == 'quick_email') echo ' selected';
			echo ">"._("Quick email-based reporting");
			echo '</option>';
		    }

		    $selected = '';
		    if ($spamcop_method == 'thorough_email') {
		        $selected = ' selected';
		    }
		    echo sprintf('	      <option value="thorough_email"%s>%s</option>',$selected, _("Through email-based reporting"));
		    
		    $selected = '';
		    if ($spamcop_method == 'web_form') {
		        $selected = ' selected';
		    }
		    echo sprintf('	      <option value="web_form"%s>%s</option>', $selected, _("Web-based form"));
		    
		?>
	    </select>
	    <input type="hidden" name="action" value="meth" />
	    <?php
		echo '<input type="submit" value="' . _("Save Method") . "\" />\n";
	    ?>
	  </form></td>
	</tr>
	<tr>
	  <?php
	    echo html_tag('td',_("Your SpamCop authorization code:") . "<br />" .
	    '<small>(' . _("see below") . ')</small>','right','','valign="top"');
	  ?>
	  <td valign="top"><form method="post" action="options.php">
	    <input type="text" size="30" name="ID" value="<?php echo htmlspecialchars($spamcop_id) ?>" />
	    <input type="hidden" name="action" value="save_id" />
	    <?php
		echo '<input type="submit" value="' . _("Save ID") . "\" />\n";
	    ?>
	  </form></td>
	</tr>
      </table>
<?php
echo '<p><b>' . _("About SpamCop") . '</b><br />';
echo _("SpamCop is a free service that greatly assists in finding the true source of the spam and helps in letting the proper people know about the abuse.");
echo "</p>\n";

echo '<p>';
printf(_("To use it, you must get a SpamCop authorization code. There is a free %ssign up page%s so you can use SpamCop."), '<a href="http://spamcop.net/anonsignup.shtml">', '</a>');
echo "</p>\n";

echo '<p><b>' . _("Before you sign up, be warned") . '</b><br />';
printf(_("Some users have reported that the email addresses used with SpamCop find their way onto spam lists. To be safe, you can just create an email forwarding account and have all SpamCop reports get sent to there. Also, if it gets flooded with spam, you can then just delete that account with no worries about losing your real email address. Just go create an email forwarder somewhere (%s has a %slist of places%s) so that messages from system administrators and what not can be sent to you."), '<a href="http://www.yahoo.com/">Yahoo!</a>', '<a href="http://dir.yahoo.com/Business_and_Economy/Business_to_Business/Communications_and_Networking/Internet_and_World_Wide_Web/Email_Providers/Forwarding_Services/Free_Forwarding/">', '</a>');
echo "</p>\n";

echo '<p>';
echo _("Once you have signed up with SpamCop and have received your SpamCop authorization code, you need to enable this plugin by clicking the link above. Once enabled, you go about your normal life. If you encounter a spam message in your mailbox, just view it. On the right-hand side, near the top of where the message is displayed, you will see a link to report this message as spam. Clicking on it brings you to a confirmation page. Confirming that you want the spam report sent will do different things with different reporting methods.");
echo "</p>\n";

echo '<p><b>' . _("Email-based reporting") . '</b><br />';
echo _("Pressing the button forwards the message to the SpamCop service and will optionally delete the message. From there, you just need to go to your INBOX and quite soon a message should appear from SpamCop. (It gets sent to the account you registered with, so make sure that your mail forwarder works!) Open it up, click on the appropriate link at the top, and a new browser window will open.");
echo "</p>\n";

if ($spamcop_quick_report) {
    echo '<p>';
    echo _("Currently, the quick reporting just forwards the request to the thorough reporting. Also, it appears that this is for members (non-free) only. Hopefully this will change soon.");
    echo "</p>\n";
}

echo '<p><b>' . _("Web-based reporting") . '</b><br />';
echo _("When you press the button on the confirmation page, this will pop open a new browser window and the SpamCop service should appear inside. The message will not be deleted (working on that part), but you won't need to wait for a response email to start the spam reporting.");
echo "</p>\n";

echo '<p>';
echo _("The SpamCop service will display information as it finds it, so scroll down until you see a form button. It might pause a little while it is looking up information, so be a little patient. Read what it says, and submit the spam. Close the browser window. Press Cancel or click on the appropriate mail folder to see messages and/or delete the spam.");
echo "</p>\n";

echo '<p><b>' . _("SpamCop service type") . '</b><br />';
echo _("Service type option allows selecting which SpamCop services you are using. Member services use different web reporting forms and does not display nags. You can purchase these services, if you want to support SpamCop.");
echo "</p>\n";

echo '<p><b>' . _("More information") . '</b><br />';
printf(_("For more information about SpamCop, it's services, spam in general, and many related topics, try reading through SpamCop's %sHelp and Feedback%s section."), '<a href="http://spamcop.net/help.shtml">', '</a>');
echo "</p>\n";
?>
</body></html>
