<?php
   /** 
    **  spamcop.php -- SpamCop plugin           
    **
    **  Copyright (c) 1999-2011 The SquirrelMail Project Team
    **  Licensed under the GNU GPL. For full terms see the file COPYING.
    **  
    **  $Id: spamcop.php 14084 2011-01-06 02:44:03Z pdontthink $                                                         
    **/

define('SM_PATH','../../');

 /* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');

function getMessage_RFC822_Attachment($message, $composeMessage, $passed_id, 
                                      $passed_ent_id='', $imapConnection) {
    global $attachments, $attachment_dir, $username, $data_dir, $uid_support;
    
    $hashed_attachment_dir = getHashedDir($username, $attachment_dir);
    $localfilename = GenerateRandomString(32, 'FILE', 7);
        
    if (!$passed_ent_id) {
        $body_a = sqimap_run_command($imapConnection, 
                                    'FETCH '.$passed_id.' RFC822',
                                    TRUE, $response, $readmessage, 
                                    $uid_support);
    } else {
        $body_a = sqimap_run_command($imapConnection, 
                                     'FETCH '.$passed_id.' BODY['.$passed_ent_id.']',
                                     TRUE, $response, $readmessage, $uid_support);
        $message = $message->parent;
    }
    if ($response == 'OK') {
        $subject = encodeHeader($message->rfc822_header->subject);
        array_shift($body_a);
        $body = implode('', $body_a) . "\r\n";
        
        $full_localfilename = "$hashed_attachment_dir/$localfilename";
        $fp = fopen( $full_localfilename, 'w');
        fwrite ($fp, $body);
        fclose($fp);

        $composeMessage->initAttachment('message/rfc822','email.txt', $localfilename);
    }
    return $composeMessage;
}


/* GLOBALS */

sqgetGlobalVar('username', $username, SQ_SESSION);
sqgetGlobalVar('key',      $key,      SQ_COOKIE);
sqgetGlobalVar('onetimepad', $onetimepad, SQ_SESSION);

sqgetGlobalVar('mailbox', $mailbox, SQ_GET);
sqgetGlobalVar('passed_id', $passed_id, SQ_GET);

if (! sqgetGlobalVar('startMessage', $startMessage, SQ_GET) ) {
    $startMessage = 1;
}
if (! sqgetGlobalVar('passed_ent_id', $passed_ent_id, SQ_GET) ) {
    $passed_ent_id = 0;
}

sqgetGlobalVar('compose_messages', $compose_messages, SQ_SESSION);

if(! sqgetGlobalVar('composesession', $composesession, SQ_SESSION) ) {
    $composesession = 0;
    sqsession_register($composesession, 'composesession');
}
/* END GLOBALS */

    
    displayPageHeader($color, $mailbox);

    $imap_stream = sqimap_login($username, $key, $imapServerAddress, 
       $imapPort, 0);
    sqimap_mailbox_select($imap_stream, $mailbox);

    if ($spamcop_method == 'quick_email' || 
        $spamcop_method == 'thorough_email') {
       // Use email-based reporting -- save as an attachment
       $session = "$composesession"+1;
       $composesession = $session;
       sqsession_register($composesession,'composesession');
       if (!isset($compose_messages)) {
          $compose_messages = array();
       }
       if (!isset($compose_messages[$session]) || ($compose_messages[$session] == NULL)) {
          $composeMessage = new Message();
          $rfc822_header = new Rfc822Header();
          $composeMessage->rfc822_header = $rfc822_header;
          $composeMessage->reply_rfc822_header = '';
          $compose_messages[$session] = $composeMessage;
          sqsession_register($compose_messages,'compose_messages');  
       } else {
          $composeMessage=$compose_messages[$session];
       }


        $message = sqimap_get_message($imap_stream, $passed_id, $mailbox);
        $composeMessage = getMessage_RFC822_Attachment($message, $composeMessage, $passed_id, 
                                      $passed_ent_id, $imap_stream);
                                      
        $compose_messages[$session] = $composeMessage;
        sqsession_register($compose_messages, 'compose_messages');

        $fn = getPref($data_dir, $username, 'full_name');
        $em = getPref($data_dir, $username, 'email_address');

        $HowItLooks = $fn . ' ';
        if ($em != '')
            $HowItLooks .= '<' . $em . '>';
     }


echo "<p>";
echo _("Sending this spam report will give you back a reply with URLs that you can click on to properly report this spam message to the proper authorities. This is a free service. By pressing the \"Send Spam Report\" button, you agree to follow SpamCop's rules/terms of service/etc.");
echo "</p>";

?>

<table align="center" width="75%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="left" valign="top">
<?php if (isset($js_web) && $js_web) {
  echo "<form method=\"post\" action=\"javascript:return false\">\n";
  echo '<input type="button" value="' . _("Close Window") . "\" onClick=\"window.close(); return true;\" />\n";
} else {
   ?><form method="post" action="<?php echo sqm_baseuri(); ?>src/right_main.php">
  <input type="hidden" name="mailbox" value="<?php echo htmlspecialchars($mailbox) ?>" />
  <input type="hidden" name="startMessage" value="<?php echo htmlspecialchars($startMessage) ?>" />
   <?php
  echo '<input type="submit" value="' . _("Cancel / Done") . "\" />\n";
}
  ?></form>
</td>
<td align="right" valign="top">
<?php if ($spamcop_method == 'thorough_email' ||
          $spamcop_method == 'quick_email') {
   if ($spamcop_method == 'thorough_email')
      $report_email = 'submit.' . $spamcop_id . '@spam.spamcop.net';
   else
      $report_email = 'quick.' . $spamcop_id . '@spam.spamcop.net';
   $form_action = sqm_baseuri() . 'src/compose.php';
?>  <form method="post" action="<?php echo $form_action?>">
  <input type="hidden" name="smtoken" value="<?php echo sm_generate_security_token(); ?>" />
  <input type="hidden" name="mailbox" value="<?php echo htmlspecialchars($mailbox) ?>" />
  <input type="hidden" name="spamcop_is_composing" value="<?php echo htmlspecialchars($passed_id) ?>" />
  <input type="hidden" name="send_to" value="<?php echo htmlspecialchars($report_email)?>" />
  <input type="hidden" name="subject" value="reply anyway" />
  <input type="hidden" name="identity" value="0" />
  <input type="hidden" name="session" value="<?php echo $session?>" />
<?php  
  echo '<input type="submit" name="send" value="' . _("Send Spam Report") . "\" />\n";
 } else {
   $spam_message = mime_fetch_body ($imap_stream, $passed_id, $passed_ent_id, 50000);

   if (strlen($spam_message) == 50000) {
      $Warning = "\n[truncated by SpamCop]\n";
      $spam_message = substr($spam_message, 0, 50000 - strlen($Warning)) . $Warning;
   }
   if (isset($js_web) && $js_web) {
?>  <form method="post" action="http://members.spamcop.net/sc" name="submitspam"
    enctype="multipart/form-data"><?php
   } else {
?>  <form method="post" action="http://members.spamcop.net/sc" name="submitspam"
    enctype="multipart/form-data" target="_top"><?php
   } ?>
  <input type="hidden" name="action" value="submit" />
  <input type="hidden" name="oldverbose" value="1" />
  <input type="hidden" name="spam" value="<?php echo htmlspecialchars($spam_message); ?>" />
<?php
  echo '<input type="submit" name="x1" value="' . _("Send Spam Report") . "\" />";
 }
?>
</form>
</td></tr>
</table>
</body></html>
