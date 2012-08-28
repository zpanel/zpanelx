<?php
/** 
 *  setup.php -- SpamCop plugin           
 *
 *  Copyright (c) 1999-2011 The SquirrelMail Project Team
 *  Licensed under the GNU GPL. For full terms see the file COPYING.
 *  
 *  $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $                                                         
 */

/** Disable Quick Reporting by default */
global $spamcop_quick_report;
$spamcop_quick_report = false;

/* Initialize the plugin */
function squirrelmail_plugin_init_spamcop() {
   global $squirrelmail_plugin_hooks;

   $squirrelmail_plugin_hooks['optpage_register_block']['spamcop'] =
       'spamcop_options';
   $squirrelmail_plugin_hooks['loading_prefs']['spamcop'] =
       'spamcop_load';
   $squirrelmail_plugin_hooks['read_body_header_right']['spamcop'] =
       'spamcop_show_link';
   $squirrelmail_plugin_hooks['compose_send']['spamcop'] =
       'spamcop_while_sending';
}


// Load the settings
// Validate some of it (make '' into 'default', etc.)
function spamcop_load() {
   global $username, $data_dir, $spamcop_enabled, $spamcop_delete,
      $spamcop_method, $spamcop_id, $spamcop_quick_report;

   $spamcop_enabled = getPref($data_dir, $username, 'spamcop_enabled');
   $spamcop_delete = getPref($data_dir, $username, 'spamcop_delete');
   $spamcop_method = getPref($data_dir, $username, 'spamcop_method');
   $spamcop_id = getPref($data_dir, $username, 'spamcop_id');
    if ($spamcop_method == '') {
// This variable is not used
//      if (getPref($data_dir, $username, 'spamcop_form'))
//         $spamcop_method = 'web_form';
//      else

// Default to web_form. It is faster.
        $spamcop_method = 'web_form';
        setPref($data_dir, $username, 'spamcop_method', $spamcop_method);
    }
   if (! $spamcop_quick_report && $spamcop_method=='quick_email') {
       $spamcop_method = 'web_form';
       setPref($data_dir, $username, 'spamcop_method', $spamcop_method);
   }
   if ($spamcop_id == '')
      $spamcop_enabled = 0;
}


// Show the link on the read-a-message screen
function spamcop_show_link() {
   global $spamcop_enabled, $spamcop_method, $spamcop_quick_report;

   if (! $spamcop_enabled)
      return;

   /* GLOBALS */
   sqgetGlobalVar('passed_id',    $passed_id,    SQ_FORM);
   sqgetGlobalVar('passed_ent_id',$passed_ent_id,SQ_FORM);
   sqgetGlobalVar('mailbox',      $mailbox,      SQ_FORM);
   if ( sqgetGlobalVar('startMessage', $startMessage, SQ_FORM) ) {
       $startMessage = (int)$startMessage;
   }
   /* END GLOBALS */

   // catch unset passed_ent_id
   if (! sqgetGlobalVar('passed_ent_id', $passed_ent_id, SQ_FORM) ) {
    $passed_ent_id = 0;
   }

   echo "<br>\n";

    /* 
       Catch situation when user use quick_email and does not update 
       preferences. User gets web_form link. If prefs are set to 
       quick_email format - they will be updated after clicking the link
     */
    if (! $spamcop_quick_report && $spamcop_method=='quick_email') {
        $spamcop_method = 'web_form';
    }
   
   if ($spamcop_method == 'web_form') {
?><script language="javascript" type="text/javascript">
document.write('<a href="../plugins/spamcop/spamcop.php?passed_id=<?php echo urlencode($passed_id); ?>&amp;js_web=1&amp;mailbox=<?php echo urlencode($mailbox); ?>&amp;passed_ent_id=<?php echo urlencode($passed_ent_id); ?>" target="_blank">');
document.write("<?php echo _("Report as Spam"); ?>");
document.write("</a>");
</script><noscript>
<a href="../plugins/spamcop/spamcop.php?passed_id=<?php echo urlencode($passed_id); ?>&amp;mailbox=<?php echo urlencode($mailbox); ?>&amp;startMessage=<?php echo urlencode($startMessage); ?>&amp;passed_ent_id=<?php echo urlencode($passed_ent_id); ?>">
<?php echo _("Report as Spam"); ?></a>
</noscript><?php
   } else {
?><a href="../plugins/spamcop/spamcop.php?passed_id=<?php echo urlencode($passed_id); ?>&amp;mailbox=<?php echo urlencode($mailbox); ?>&amp;startMessage=<?php echo urlencode($startMessage); ?>&amp;passed_ent_id=<?php echo urlencode($passed_ent_id); ?>">
<?php echo _("Report as Spam"); ?></a>
<?php
   }
}


// Show the link to our own custom options page
function spamcop_options() {
   global $optpage_blocks;
   
   $optpage_blocks[] = array(
      'name' => _("SpamCop - Spam Reporting"),
      'url' => '../plugins/spamcop/options.php',
      'desc' => _("Help fight the battle against unsolicited email. SpamCop reads the spam email and determines the correct addresses to send complaints to. Quite fast, really smart, and easy to use."),
      'js' => false
   );
}


// When we send the email, we optionally trash it then too
function spamcop_while_sending() {
   global $mailbox, $spamcop_delete, $auto_expunge, 
      $username, $key, $imapServerAddress, $imapPort;

   // load sqgetGlobalVar()
   include_once(SM_PATH . 'functions/global.php');

   // check if compose.php is called by spamcop plugin
   if (sqgetGlobalVar('spamcop_is_composing' , $spamcop_is_composing)) {
       if ($spamcop_delete) {
           $imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
           sqimap_mailbox_select($imapConnection, $mailbox);
           sqimap_msgs_list_delete($imapConnection, $mailbox, $spamcop_is_composing);
           if ($auto_expunge)
               sqimap_mailbox_expunge($imapConnection, $mailbox, true);
       }
       // change default email composition setting. Plugin always operates in right frame.
       // make sure that compose.php redirects to right page. Temporally override.
       global $compose_new_win;
       $compose_new_win = false;
   }
}
