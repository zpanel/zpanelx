<?php

/**
 * move_messages.php
 *
 * Enables message moving between folders on the IMAP server.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: move_messages.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the move_messages page */
define('PAGE_NAME', 'move_messages');

/* Path for SquirrelMail required files. */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/global.php');
require_once(SM_PATH . 'functions/display_messages.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/html.php');

global $compose_new_win;

if ( !sqgetGlobalVar('composesession', $composesession, SQ_SESSION) ) {
  $composesession = 0;
}

function attachSelectedMessages($msg, $imapConnection) {
    global $username, $attachment_dir, $startMessage,
           $data_dir, $composesession, $uid_support, $mailbox,
       $msgs, $thread_sort_messages, $allow_server_sort, $show_num,
       $compose_messages;

    if (!isset($compose_messages)) {
        $compose_messages = array();
            sqsession_register($compose_messages,'compose_messages');
    }

    if (!$composesession) {
        $composesession = 1;
            sqsession_register($composesession,'composesession');
    } else {
        $composesession++;
        sqsession_register($composesession,'composesession');
    }

    $hashed_attachment_dir = getHashedDir($username, $attachment_dir, $composesession);

    if ($thread_sort_messages || $allow_server_sort) {
       $start_index=0;
    } else {
       $start_index = ($startMessage-1) * $show_num;
    }

    $i = 0;
    $j = 0;
    $hashed_attachment_dir = getHashedDir($username, $attachment_dir);

    $composeMessage = new Message();
    $rfc822_header = new Rfc822Header();
    $composeMessage->rfc822_header = $rfc822_header;
    $composeMessage->reply_rfc822_header = '';

    while ($j < count($msg)) {
        if (isset($msg[$i])) {
            $id = $msg[$i];
            
            $body_a = sqimap_run_command($imapConnection, "FETCH $id RFC822",true, $response, $readmessage, $uid_support);
            
            if ($response == 'OK') {
                $message = sqimap_get_message($imapConnection, $id, $mailbox);

                // fetch the subject for the message from the object
                //
                $subject = $message->rfc822_header->subject;

                // use subject for file name
                //
                if ( empty($subject) )
                    $filename = "untitled-".$message->entity_id;
                else
                    $filename = $subject;
                $filename .= '.msg';
                $filename = decodeHeader($filename, false, false);

                // figure out a subject for new message
                //
                $subject = decodeHeader($subject, false, false, true);
                $subject = trim($subject);
                if (substr(strtolower($subject), 0, 4) != 'fwd:') {
                    $subject = 'Fwd: ' . $subject;
                }

                array_shift($body_a);
                array_pop($body_a);
                $body = implode('', $body_a);
                $body .= "\r\n";

                $localfilename = GenerateRandomString(32, 'FILE', 7);
                $full_localfilename = "$hashed_attachment_dir/$localfilename";
                while (file_exists($full_localfilename)) {
                    $localfilename = GenerateRandomString(32, 'FILE', 7);
                    $full_localfilename = "$hashed_attachment_dir/$localfilename";
                }

                $fp = fopen( $full_localfilename, 'wb');
                fwrite ($fp, $body);
                fclose($fp);
                $composeMessage->initAttachment('message/rfc822',$filename,
                     $localfilename);
                $composeMessage->rfc822_header->subject = $subject;
            }
            $j++;
        }
        $i++;
    }
    $compose_messages[$composesession] = $composeMessage;
    sqsession_register($compose_messages,'compose_messages');
    session_write_close();
    return $composesession;
}



/* get globals */
sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
sqgetGlobalVar('base_uri',  $base_uri,      SQ_SESSION);

sqgetGlobalVar('mailbox', $mailbox);
sqgetGlobalVar('startMessage', $startMessage);
sqgetGlobalVar('msg', $msg);

sqgetGlobalVar('msgs',              $msgs,              SQ_SESSION);
sqgetGlobalVar('composesession',    $composesession,    SQ_SESSION);
sqgetGlobalVar('lastTargetMailbox', $lastTargetMailbox, SQ_SESSION);

sqgetGlobalVar('moveButton',      $moveButton,      SQ_POST);
sqgetGlobalVar('expungeButton',   $expungeButton,   SQ_POST);
sqgetGlobalVar('targetMailbox',   $targetMailbox,   SQ_POST);
sqgetGlobalVar('expungeButton',   $expungeButton,   SQ_POST);
sqgetGlobalVar('undeleteButton',  $undeleteButton,  SQ_POST);
sqgetGlobalVar('markRead',        $markRead,        SQ_POST);
sqgetGlobalVar('markUnread',      $markUnread,      SQ_POST);
sqgetGlobalVar('attache',         $attache,         SQ_POST);
sqgetGlobalVar('location',        $location,        SQ_POST);

if (!sqgetGlobalVar('smtoken',$submitted_token, SQ_POST)) {
    $submitted_token = '';
}
/* end of get globals */

// security check
sm_validate_security_token($submitted_token, 3600, TRUE);

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$mbx_response=sqimap_mailbox_select($imapConnection, $mailbox);

$location = set_url_var($location,'composenew',0,false);
$location = set_url_var($location,'composesession',0,false);
$location = set_url_var($location,'session',0,false);

// make sure that cache is not used
$location = set_url_var($location,'use_mailbox_cache',0,false);

/* remember changes to mailbox setting */
if (!isset($lastTargetMailbox)) {
    $lastTargetMailbox = 'INBOX';
}
if ($targetMailbox != $lastTargetMailbox) {
    $lastTargetMailbox = $targetMailbox;
    sqsession_register($lastTargetMailbox, 'lastTargetMailbox');
}
$exception = false;

do_hook('move_before_move');


/*
    Move msg list sorting up here, as it is used several times,
    makes it more efficient to do it in one place for the code
*/
$id = array();
if (isset($msg) && is_array($msg)) {
    foreach( $msg as $key=>$uid ) {
        // using foreach removes the risk of infinite loops that was there //
        $id[] = $uid;
    }
}

// expunge-on-demand if user isn't using move_to_trash or auto_expunge
if(isset($expungeButton)) {
    $cnt = sqimap_mailbox_expunge($imapConnection, $mailbox, true);
    if (($startMessage+$cnt-1) >= $mbx_response['EXISTS']) {
        if ($startMessage > $show_num) {
            $location = set_url_var($location,'startMessage',$startMessage-$show_num,false);
        } else {
            $location = set_url_var($location,'startMessage',1,false);
        }
    }
} elseif(isset($undeleteButton)) {
    // undelete messages if user isn't using move_to_trash or auto_expunge
    // Removes \Deleted flag from selected messages
    if (count($id)) {
        sqimap_toggle_flag($imapConnection, $id, '\\Deleted',false,true);
    } else {
        $exception = true;
    }
} elseif (!isset($moveButton)) {
    if (count($id)) {
        $cnt = count($id);
        if (!isset($attache)) {
            if (isset($markRead)) {
                sqimap_toggle_flag($imapConnection, $id, '\\Seen',true,true);
            } else if (isset($markUnread)) {
                sqimap_toggle_flag($imapConnection, $id, '\\Seen',false,true);
            } else  {
                sqimap_msgs_list_delete($imapConnection, $mailbox, $id);
                if ($auto_expunge) {
                    $cnt = sqimap_mailbox_expunge($imapConnection, $mailbox, true);
                }
                if (($startMessage+$cnt-1) >= $mbx_response['EXISTS']) {
                    if ($startMessage > $show_num) {
                        $location = set_url_var($location,'startMessage',$startMessage-$show_num, false);
                    } else {
                        $location = set_url_var($location,'startMessage',1, false);
                    }
                }
            }
        } else {
            $composesession = attachSelectedMessages($id, $imapConnection);
            $location = set_url_var($location, 'session', $composesession, false);
            $location = set_url_var($location, 'forward_as_attachment_init', 1, false);
            if ($compose_new_win) {
                $location = set_url_var($location, 'composenew', 1, false);
            } else {
                $location = str_replace('search.php','compose.php',$location);
                $location = str_replace('right_main.php','compose.php',$location);
            }
        }
    } else {
        $exception = true;
    }
} else {    // Move messages
    if (count($id)) {
        // move messages only when target mailbox is not the same as source mailbox
        if ($mailbox!=$targetMailbox) {
            sqimap_msgs_list_move($imapConnection,$id,$targetMailbox);
            if ($auto_expunge) {
                $cnt = sqimap_mailbox_expunge($imapConnection, $mailbox, true);
            } else {
                $cnt = 0;
            }

            if (($startMessage+$cnt-1) >= $mbx_response['EXISTS']) {
                if ($startMessage > $show_num) {
                    $location = set_url_var($location,'startMessage',$startMessage-$show_num, false);
                } else {
                    $location = set_url_var($location,'startMessage',1, false);
                }
            }
        }
    } else {
        $exception = true;
    }
}
// Log out this session
sqimap_logout($imapConnection);
if ($exception) {
    displayPageHeader($color, $mailbox);
    error_message(_("No messages were selected."), $mailbox, $sort, $startMessage, $color);
} else {
    header("Location: $location");
    exit;
}
?>
</body></html>
