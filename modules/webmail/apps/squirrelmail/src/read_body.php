<?php

/**
 * read_body.php
 *
 * This file is used for reading the msgs array and displaying
 * the resulting emails in the right frame.
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: read_body.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 */

/** This is the read_body page */
define('PAGE_NAME', 'read_body');

/**
 * Path for SquirrelMail required files.
 * @ignore
 */
define('SM_PATH','../');

/* SquirrelMail required files. */
require_once(SM_PATH . 'include/validate.php');
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/mime.php');
require_once(SM_PATH . 'functions/date.php');
require_once(SM_PATH . 'functions/url_parser.php');
require_once(SM_PATH . 'functions/html.php');

/**
 * Given an IMAP message id number, this will look it up in the cached
 * and sorted msgs array and return the index. Used for finding the next
 * and previous messages.
 *
 * @return the index of the next valid message from the array
 */
function findNextMessage($passed_id) {
    global $msort, $msgs, $sort,
           $thread_sort_messages, $allow_server_sort,
           $server_sort_array;
    if (!is_array($server_sort_array)) {
        $thread_sort_messages = 0;
        $allow_server_sort = FALSE;
    }
    $result = -1;
    if ($thread_sort_messages || $allow_server_sort) {
        $count = count($server_sort_array) - 1;
        foreach($server_sort_array as $key=>$value) {
            if ($passed_id == $value) {
                if ($key == $count) {
                    break;
                }
                $result = $server_sort_array[$key + 1];
                break;
            }
        }
    } else {
        if (is_array($msort)) {
            for (reset($msort); ($key = key($msort)), (isset($key)); next($msort)) {
                if ($passed_id == $msgs[$key]['ID']) {
                    next($msort);
                    $key = key($msort);
                    if (isset($key)){
                        $result = $msgs[$key]['ID'];
                        break;
                    }
                }
            }
        }
    }
    return $result;
}

/** returns the index of the previous message from the array. */
function findPreviousMessage($numMessages, $passed_id) {
    global $msort, $sort, $msgs,
           $thread_sort_messages,
           $allow_server_sort, $server_sort_array;
    $result = -1;
    if (!is_array($server_sort_array)) {
        $thread_sort_messages = 0;
        $allow_server_sort = FALSE;
    }
    if ($thread_sort_messages || $allow_server_sort ) {
        foreach($server_sort_array as $key=>$value) {
            if ($passed_id == $value) {
                if ($key == 0) {
                    break;
                }
                $result = $server_sort_array[$key - 1];
                break;
            }
        }
    } else {
        if (is_array($msort)) {
            for (reset($msort); ($key = key($msort)), (isset($key)); next($msort)) {
                if ($passed_id == $msgs[$key]['ID']) {
                    prev($msort);
                    $key = key($msort);
                    if (isset($key)) {
                        $result = $msgs[$key]['ID'];
                        break;
                    }
                }
            }
        }
    }
    return $result;
}

/**
 * Displays a link to a page where the message is displayed more
 * "printer friendly".
 */
function printer_friendly_link($mailbox, $passed_id, $passed_ent_id, $color) {
    global $javascript_on;

    /* hackydiehack */

    // Pull "view_unsafe_images" from the URL to find out if the unsafe images
    // should be displayed. The default is not to display unsafe images.
    if( !sqgetGlobalVar('view_unsafe_images', $view_unsafe_images, SQ_GET) ) {
        // If "view_unsafe_images" isn't part of the URL, default to not
        // displaying unsafe images.
        $view_unsafe_images = false;
    } else {
        //  If "view_unsafe_images" is part of the URL, display unsafe images
        //  regardless of the value of the URL variable.
        // FIXME: Do we really want to display the unsafe images regardless of the value in URL variable?
        $view_unsafe_images = true;
    }

    $params = '?passed_ent_id=' . urlencode($passed_ent_id) .
              '&mailbox=' . urlencode($mailbox) .
              '&passed_id=' . urlencode($passed_id).
              '&view_unsafe_images='. (bool) $view_unsafe_images;

    $print_text = _("View Printable Version");

    $result = '';
    /* Output the link. */
    if ($javascript_on) {
        $result = '<script language="javascript" type="text/javascript">' . "\n" .
                  '<!--' . "\n" .
                  "  function printFormat() {\n" .
                  '    window.open("../src/printer_friendly_main.php' .
                  $params . '","Print","width=800,height=600");' . "\n".
                  "  }\n" .
                  "// -->\n" .
                  "</script>\n" .
                  "<a href=\"javascript:printFormat();\">$print_text</a>\n";
    } else {
        $result = '<a target="_blank" href="../src/printer_friendly_bottom.php' .
                  "$params\">$print_text</a>\n";
    }
    return $result;
}

function ServerMDNSupport($read) {
    /* escaping $ doesn't work -> \x36 */
    $ret = preg_match('/(\x36MDNSent|\\\\\*)/i', $read);
    return $ret;
}

function SendMDN ( $mailbox, $passed_id, $sender, $message, $imapConnection) {
    global $username, $attachment_dir, $color, $default_move_to_sent,
           $version, $attachments, $squirrelmail_language, $default_charset,
           $languages, $useSendmail, $domain, $sent_folder,
           $popuser, $data_dir;

    sqgetGlobalVar('SERVER_NAME', $SERVER_NAME, SQ_SERVER);

    $header = $message->rfc822_header;
    $hashed_attachment_dir = getHashedDir($username, $attachment_dir);

    $rfc822_header = new Rfc822Header();
    $content_type  = new ContentType('multipart/report');
    $content_type->properties['report-type']='disposition-notification';

    set_my_charset();
    if ($default_charset) {
        $content_type->properties['charset']=$default_charset;
    }
    $rfc822_header->content_type = $content_type;
    $rfc822_header->to[] = $header->dnt;
    $rfc822_header->subject = _("Read:") . ' ' . decodeHeader($header->subject, true, false);

    // FIXME: use identity.php from SM 1.5. Change this also in compose.php

    $reply_to = '';
    if (isset($identity) && $identity != 'default') {
        $from_mail = getPref($data_dir, $username,
                             'email_address' . $identity);
        $full_name = getPref($data_dir, $username,
                             'full_name' . $identity);
        $from_addr = '"'.$full_name.'" <'.$from_mail.'>';
        $reply_to  = getPref($data_dir, $username,
                             'reply_to' . $identity);
    } else {
        $from_mail = getPref($data_dir, $username, 'email_address');
        $full_name = getPref($data_dir, $username, 'full_name');
        $from_addr = '"'.$full_name.'" <'.$from_mail.'>';
        $reply_to  = getPref($data_dir, $username,'reply_to');
    }

    // Patch #793504 Return Receipt Failing with <@> from Tim Craig (burny_md)
    // This merely comes from compose.php and only happens when there is no
    // email_addr specified in user's identity (which is the startup config)
    if (preg_match('|^([^@%/]+)[@%/](.+)$|', $username, $usernamedata)) {
       $popuser = $usernamedata[1];
       $domain  = $usernamedata[2];
       unset($usernamedata);
    } else {
       $popuser = $username;
    }

    if (!$from_mail) {
       $from_mail = "$popuser@$domain";
       $from_addr = $from_mail;
    }

    $rfc822_header->from = $rfc822_header->parseAddress($from_addr,true);
    if ($reply_to) {
       $rfc822_header->reply_to = $rfc822_header->parseAddress($reply_to,true);
    }

    // part 1 (RFC2298)
    $senton = getLongDateString( $header->date, $header->date_unparsed );
    $to_array = $header->to;
    $to = '';
    foreach ($to_array as $line) {
        $to .= ' '.$line->getAddress();
    }
    $now = getLongDateString( time() );
    set_my_charset();
    $body = _("Your message") . "\r\n\r\n" .
            "\t" . _("To") . ': ' . decodeHeader($to,false,false,true) . "\r\n" .
            "\t" . _("Subject") . ': ' . decodeHeader($header->subject,false,false,true) . "\r\n" .
            "\t" . _("Sent") . ': ' . $senton . "\r\n" .
            "\r\n" .
            sprintf( _("Was displayed on %s"), $now );

    $special_encoding = '';
    if (isset($languages[$squirrelmail_language]['XTRA_CODE']) &&
        function_exists($languages[$squirrelmail_language]['XTRA_CODE'])) {
        $body = $languages[$squirrelmail_language]['XTRA_CODE']('encode', $body);
        if (strtolower($default_charset) == 'iso-2022-jp') {
            if (mb_detect_encoding($body) == 'ASCII') {
                $special_encoding = '8bit';
            } else {
                $body = mb_convert_encoding($body, 'JIS');
                $special_encoding = '7bit';
            }
        }
    } elseif (sq_is8bit($body)) {
        // detect 8bit symbols added by translations
        $special_encoding = '8bit';
    }
    $part1 = new Message();
    $part1->setBody($body);
    $mime_header = new MessageHeader;
    $mime_header->type0 = 'text';
    $mime_header->type1 = 'plain';
    if ($special_encoding) {
        $mime_header->encoding = $special_encoding;
    } else {
        $mime_header->encoding = '7bit';
    }
    if ($default_charset) {
        $mime_header->parameters['charset'] = $default_charset;
    }
    $part1->mime_header = $mime_header;

    // part2  (RFC2298)
    $original_recipient  = $to;
    $original_message_id = $header->message_id;

    $report = "Reporting-UA : $SERVER_NAME ; SquirrelMail (version $version) \r\n";
    if ($original_recipient != '') {
        $report .= "Original-Recipient : $original_recipient\r\n";
    }
    $final_recipient = $sender;
    $report .= "Final-Recipient: rfc822; $final_recipient\r\n" .
              "Original-Message-ID : $original_message_id\r\n" .
              "Disposition: manual-action/MDN-sent-manually; displayed\r\n";

    $part2 = new Message();
    $part2->setBody($report);
    $mime_header = new MessageHeader;
    $mime_header->type0 = 'message';
    $mime_header->type1 = 'disposition-notification';
    $mime_header->encoding = '7bit';
    $part2->mime_header = $mime_header;

    $composeMessage = new Message();
    $composeMessage->rfc822_header = $rfc822_header;
    $composeMessage->addEntity($part1);
    $composeMessage->addEntity($part2);


    if ($useSendmail) {
        require_once(SM_PATH . 'class/deliver/Deliver_SendMail.class.php');
        global $sendmail_path, $sendmail_args;
        // Check for outdated configuration
        if (!isset($sendmail_args)) {
            if ($sendmail_path=='/var/qmail/bin/qmail-inject') {
                $sendmail_args = '';
            } else {
                $sendmail_args = '-i -t';
            }
        }
        $deliver = new Deliver_SendMail(array('sendmail_args'=>$sendmail_args));
        $stream = $deliver->initStream($composeMessage,$sendmail_path);
    } else {
        require_once(SM_PATH . 'class/deliver/Deliver_SMTP.class.php');
        $deliver = new Deliver_SMTP();
        global $smtpServerAddress, $smtpPort, $pop_before_smtp, $pop_before_smtp_host;

        $authPop = (isset($pop_before_smtp) && $pop_before_smtp) ? true : false;

        $user = '';
        $pass = '';
        if (empty($pop_before_smtp_host))
            $pop_before_smtp_host = $smtpServerAddress;

        get_smtp_user($user, $pass);

        $stream = $deliver->initStream($composeMessage,$domain,0,
                $smtpServerAddress, $smtpPort, $user, $pass, $authPop, $pop_before_smtp_host);
    }
    $success = false;
    if ($stream) {
        $deliver->mail($composeMessage, $stream);
        $success = $deliver->finalizeStream($stream);
    }
    if (!$success) {
        $msg  = _("Message not sent.") .' '.  _("Server replied:") .
            "\n<blockquote>\n" . $deliver->dlv_msg . '<br />' .
            $deliver->dlv_ret_nr . ' ' .
            $deliver->dlv_server_msg . "</blockquote>\n\n";
        require_once(SM_PATH . 'functions/display_messages.php');
        plain_error_message($msg, $color);
    } else {
        unset ($deliver);

        // copy message to sent folder
        $move_to_sent = getPref($data_dir,$username,'move_to_sent');
        if (isset($default_move_to_sent) && ($default_move_to_sent != 0)) {
            $svr_allow_sent = true;
        } else {
            $svr_allow_sent = false;
        }

        if (isset($sent_folder) && (($sent_folder != '') || ($sent_folder != 'none'))
                && sqimap_mailbox_exists( $imapConnection, $sent_folder)) {
            $fld_sent = true;
        } else {
            $fld_sent = false;
        }

        if ((isset($move_to_sent) && ($move_to_sent != 0)) || (!isset($move_to_sent))) {
            $lcl_allow_sent = true;
        } else {
            $lcl_allow_sent = false;
        }

        if (($fld_sent && $svr_allow_sent && !$lcl_allow_sent) || ($fld_sent && $lcl_allow_sent)) {
            require_once(SM_PATH . 'class/deliver/Deliver_IMAP.class.php');
            $imap_deliver = new Deliver_IMAP();
            $imap_deliver->mail($composeMessage, $imapConnection, 0, 0, $imapConnection, $sent_folder);
            unset ($imap_deliver);
        }
    }
    return $success;
}

function ToggleMDNflag ($set ,$imapConnection, $mailbox, $passed_id, $uid_support) {
    $sg   =  $set?'+':'-';
    $cmd  = 'STORE ' . $passed_id . ' ' . $sg . 'FLAGS ($MDNSent)';
    $read = sqimap_run_command ($imapConnection, $cmd, true, $response,
                                $readmessage, $uid_support);
}

function formatRecipientString($recipients, $item ) {
    global $show_more_cc, $show_more, $show_more_bcc,
           $PHP_SELF;

    $string = '';
    if ((is_array($recipients)) && (isset($recipients[0]))) {
        $show = false;

        if ($item == 'to') {
            if ($show_more) {
                $show = true;
                $url = set_url_var($PHP_SELF, 'show_more',0);
            } else {
                $url = set_url_var($PHP_SELF, 'show_more',1);
            }
        } else if ($item == 'cc') {
            if ($show_more_cc) {
                $show = true;
                $url = set_url_var($PHP_SELF, 'show_more_cc',0);
            } else {
                $url = set_url_var($PHP_SELF, 'show_more_cc',1);
            }
        } else if ($item == 'bcc') {
            if ($show_more_bcc) {
                $show = true;
                $url = set_url_var($PHP_SELF, 'show_more_bcc',0);
            } else {
                $url = set_url_var($PHP_SELF, 'show_more_bcc',1);
            }
        }

        $cnt = count($recipients);
        foreach($recipients as $r) {
            $add = decodeHeader($r->getAddress(true));
            if ($string) {
                $string .= '<br />' . $add;
            } else {
                $string = $add;
                if ($cnt > 1) {
                    $string .= '&nbsp;(<a href="'.$url;
                    if ($show) {
                       $string .= '">'._("less").'</a>)';
                    } else {
                       $string .= '">'._("more").'</a>)';
                       break;
                    }
                }
            }
        }
    }
    return $string;
}

function formatEnvheader($mailbox, $passed_id, $passed_ent_id, $message,
                         $color, $FirstTimeSee) {
    global $default_use_mdn, $default_use_priority,
           $show_xmailer_default, $mdn_user_support, $PHP_SELF, $javascript_on,
           $squirrelmail_language;

    $header = $message->rfc822_header;
    $env = array();
    $env[_("Subject")] = decodeHeader($header->subject);
    $from_name = $header->getAddr_s('from');
    if (!$from_name) {
        $from_name = $header->getAddr_s('sender');
        if (!$from_name) {
            $from_name = _("Unknown sender");
        }
    }
    $env[_("From")] = decodeHeader($from_name);
    $env[_("Date")] = getLongDateString($header->date, $header->date_unparsed);
    $env[_("To")] = formatRecipientString($header->to, "to");
    $env[_("Cc")] = formatRecipientString($header->cc, "cc");
    $env[_("Bcc")] = formatRecipientString($header->bcc, "bcc");
    if ($default_use_priority) {
        $env[_("Priority")] = htmlspecialchars(getPriorityStr($header->priority));
    }
    if ($show_xmailer_default) {
        $env[_("Mailer")] = decodeHeader($header->xmailer);
    }
    if ($default_use_mdn) {
        if ($mdn_user_support) {
            if ($header->dnt) {
                if ($message->is_mdnsent) {
                    $env[_("Read receipt")] = _("sent");
                } else {
                    $env[_("Read receipt")] = _("requested");
                    if (!(handleAsSent($mailbox) ||
                          $message->is_deleted ||
                          $passed_ent_id)) {
                        $mdn_url = $PHP_SELF;
                        $mdn_url = set_url_var($mdn_url, 'mailbox', urlencode($mailbox));
                        $mdn_url = set_url_var($mdn_url, 'passed_id', $passed_id);
                        $mdn_url = set_url_var($mdn_url, 'passed_ent_id', $passed_ent_id);
                        $mdn_url = set_url_var($mdn_url, 'sendreceipt', 1);
                        if ($FirstTimeSee && $javascript_on) {
                            $script  = '<script language="JavaScript" type="text/javascript">' . "\n";
                            $script .= '<!--'. "\n";
                            $script .= 'if(window.confirm("' .
                                       _("The message sender has requested a response to indicate that you have read this message. Would you like to send a receipt?") .
                                       '")) {  '."\n" .
                                       '    sendMDN()'.
                                       '}' . "\n";
                            $script .= '// -->'. "\n";
                            $script .= '</script>'. "\n";
                            echo $script;
                        }
                        $env[_("Read receipt")] .= '&nbsp;<a href="' . $mdn_url . '">[' .
                                                   _("Send read receipt now") . ']</a>';
                    }
                }
            }
        }
    }

    $s  = '<table width="100%" cellpadding="0" cellspacing="2" border="0"';
    $s .= ' align="center" bgcolor="'.$color[0].'">';
    foreach ($env as $key => $val) {
        if ($val) {
            $s .= '<tr>';
            $s .= html_tag('td', '<b>' . $key . ':&nbsp;&nbsp;</b>', 'right', '', 'valign="top" width="20%"') . "\n";
            $s .= html_tag('td', $val, 'left', '', 'valign="top" width="80%"') . "\n";
            $s .= '</tr>';
        }
    }
    echo '<table bgcolor="'.$color[9].'" width="100%" cellpadding="1"'.
         ' cellspacing="0" border="0" align="center">'."\n";
    echo '<tr><td height="5" colspan="2" bgcolor="'.
          $color[4].'"></td></tr><tr><td align="center">'."\n";
    echo $s;
    do_hook('read_body_header');
    formatToolbar($mailbox, $passed_id, $passed_ent_id, $message, $color);
    echo '</table>';
    echo '</td></tr><tr><td height="5" colspan="2" bgcolor="'.$color[4].'"></td></tr>'."\n";
    echo '</table>';
}

function formatMenubar($mailbox, $passed_id, $passed_ent_id, $message, $mbx_response) {
    global $base_uri, $draft_folder, $where, $what, $color, $sort,
           $startMessage, $PHP_SELF, $save_as_draft,
           $enable_forward_as_attachment;

    $topbar_delimiter = '&nbsp;|&nbsp;';
    $urlMailbox = urlencode($mailbox);
    $s = '<table width="100%" cellpadding="3" cellspacing="0" align="center"'.
         ' border="0" bgcolor="'.$color[9].'"><tr>' .
         html_tag( 'td', '', 'left', '', 'width="33%"' ) . '<small>';

    $msgs_url = $base_uri . 'src/';
    if (isset($where) && isset($what)) {
        $msgs_url .= 'search.php?smtoken=' . sm_generate_security_token() . '&amp;where=' . urlencode($where) .
                     '&amp;what=' . urlencode($what) . '&amp;mailbox=' . $urlMailbox;
        $msgs_str  = _("Search Results");
    } else {
        $msgs_url .= 'right_main.php?sort=' . $sort . '&amp;startMessage=' .
                     $startMessage . '&amp;mailbox=' . $urlMailbox;
        $msgs_str  = _("Message List");
    }
    $s .= '<a href="' . $msgs_url . '">' . $msgs_str . '</a>';

    $delete_url = $base_uri . 'src/delete_message.php?mailbox=' . $urlMailbox .
                  '&amp;message=' . $passed_id . '&amp;smtoken=' . sm_generate_security_token() . '&amp;';
    $unread_url = $base_uri . 'src/';
    if (!(isset($passed_ent_id) && $passed_ent_id)) {
        if ($where && $what) {
            $unread_url .= 'search.php?unread_passed_id=' . $passed_id . '&amp;smtoken=' . sm_generate_security_token() . '&amp;where=' . urlencode($where) . '&amp;what=' . urlencode($what) . '&amp;mailbox=' . $urlMailbox;
        } else {
            $unread_url .= 'right_main.php?unread_passed_id=' . $passed_id . '&amp;sort=' . $sort . '&amp;startMessage=' . $startMessage . '&amp;mailbox=' . $urlMailbox;
        }
        $s .= $topbar_delimiter;
        $s .= '<a href="' . $unread_url . '">' . _("Unread") . '</a>';

        if ($where && $what) {
            $delete_url .= 'where=' . urlencode($where) . '&amp;what=' . urlencode($what);
        } else {
            $delete_url .= 'sort=' . $sort . '&amp;startMessage=' . $startMessage;
        }
        $s .= $topbar_delimiter;
        $s .= '<a href="' . $delete_url . '">' . _("Delete") . '</a>';
    }

    $comp_uri = 'src/compose.php' .
                '?passed_id=' . $passed_id .
                '&amp;mailbox=' . $urlMailbox .
                '&amp;startMessage=' . $startMessage .
                (isset($passed_ent_id)?'&amp;passed_ent_id='.urlencode($passed_ent_id):'');

    if (($mailbox == $draft_folder) && ($save_as_draft)) {
        $comp_alt_uri = $comp_uri . '&amp;smaction=draft';
        $comp_alt_string = _("Resume Draft");
    } else if (handleAsSent($mailbox)) {
        $comp_alt_uri = $comp_uri . '&amp;smaction=edit_as_new';
        $comp_alt_string = _("Edit Message as New");
    }
    if (isset($comp_alt_uri)) {
        $s .= $topbar_delimiter;
        $s .= makeComposeLink($comp_alt_uri, $comp_alt_string);
    }

    $s .= '</small></td><td align="center" width="33%"><small>';

    if (!(isset($where) && isset($what)) && !$passed_ent_id) {
        $prev = findPreviousMessage($mbx_response['EXISTS'], $passed_id);
        $next = findNextMessage($passed_id);
        if ($prev != -1) {
            $uri = $base_uri . 'src/read_body.php?passed_id='.$prev.
                   '&amp;mailbox='.$urlMailbox.'&amp;sort='.$sort.
                   '&amp;startMessage='.$startMessage.'&amp;show_more=0';
            $s .= '<a href="'.$uri.'">'._("Previous").'</a>';
        } else {
            $s .= _("Previous");
        }
        $s .= $topbar_delimiter;
        if ($next != -1) {
            $uri = $base_uri . 'src/read_body.php?passed_id='.$next.
                   '&amp;mailbox='.$urlMailbox.'&amp;sort='.$sort.
                   '&amp;startMessage='.$startMessage.'&amp;show_more=0';
            $s .= '<a href="'.$uri.'">'._("Next").'</a>';
        } else {
            $s .= _("Next");
        }
    } else if (isset($passed_ent_id) && $passed_ent_id) {
        /* code for navigating through attached message/rfc822 messages */
        $url = set_url_var($PHP_SELF, 'passed_ent_id',0);
        $s .= '<a href="'.$url.'">'._("View Message").'</a>';
        $entities     = array();
        $entity_count = array();
        $c = 0;

        foreach($message->parent->entities as $ent) {
            if ($ent->type0 == 'message' && $ent->type1 == 'rfc822') {
                $c++;
                $entity_count[$c] = $ent->entity_id;
                $entities[$ent->entity_id] = $c;
            }
        }

        $prev_link = _("Previous");
        if (!empty($entities[$passed_ent_id]) && ($entities[$passed_ent_id] > 1)) {
            $prev_ent_id = $entity_count[$entities[$passed_ent_id] - 1];
            $prev_link   = '<a href="'
                         . set_url_var($PHP_SELF, 'passed_ent_id', $prev_ent_id)
                         . '">' . $prev_link . '</a>';
        }

        $next_link = _("Next");
        if (!empty($entities[$passed_ent_id]) && ($entities[$passed_ent_id] < $c)) {
            $next_ent_id = $entity_count[$entities[$passed_ent_id] + 1];
            $next_link   = '<a href="'
                         . set_url_var($PHP_SELF, 'passed_ent_id', $next_ent_id)
                         . '">' . $next_link . '</a>';
        }
        $s .= $topbar_delimiter . $prev_link;
        $par_ent_id = $message->parent->entity_id;
        if ($par_ent_id) {
            $par_ent_id = substr($par_ent_id,0,-2);
            $s .= $topbar_delimiter;
            $url = set_url_var($PHP_SELF, 'passed_ent_id',$par_ent_id);
            $s .= '<a href="'.$url.'">'._("Up").'</a>';
        }
        $s .= $topbar_delimiter . $next_link;
    }

    $s .= '</small></td>' . "\n" .
          html_tag( 'td', '', 'right', '', 'width="33%" nowrap' ) . '<small>';
    $comp_action_uri = $comp_uri . '&amp;smaction=forward';
    $s .= makeComposeLink($comp_action_uri, _("Forward"));

    if ($enable_forward_as_attachment) {
        $comp_action_uri = $comp_uri . '&amp;smaction=forward_as_attachment';
        $s .= $topbar_delimiter;
        $s .= makeComposeLink($comp_action_uri, _("Forward as Attachment"));
    }

    $comp_action_uri = $comp_uri . '&amp;smaction=reply';
    $s .= $topbar_delimiter;
    $s .= makeComposeLink($comp_action_uri, _("Reply"));

    $comp_action_uri = $comp_uri . '&amp;smaction=reply_all';
    $s .= $topbar_delimiter;
    $s .= makeComposeLink($comp_action_uri, _("Reply All"));
    $s .= '</small></td></tr></table>';
    $ret = concat_hook_function('read_body_menu_top', $s);
    if($ret != '') {
        $s = $ret;
    }
    echo $s;
    do_hook('read_body_menu_bottom');
}

function formatToolbar($mailbox, $passed_id, $passed_ent_id, $message, $color) {
    global $base_uri, $where, $what, $download_and_unsafe_link;

    $urlMailbox = urlencode($mailbox);
    $urlPassed_id = urlencode($passed_id);
    $urlPassed_ent_id = urlencode($passed_ent_id);

    $query_string = 'mailbox=' . $urlMailbox . '&amp;passed_id=' . $urlPassed_id . '&amp;passed_ent_id=' . $urlPassed_ent_id;

    if (!empty($where)) {
        $query_string .= '&amp;where=' . urlencode($where);
    }

    if (!empty($what)) {
        $query_string .= '&amp;what=' . urlencode($what);
    }

    $url = $base_uri.'src/view_header.php?'.$query_string;

    $s  = "<tr>\n" .
          html_tag( 'td', '', 'right', '', 'valign="middle" width="20%"' ) . '<b>' . _("Options") . ":&nbsp;&nbsp;</b></td>\n" .
          html_tag( 'td', '', 'left', '', 'valign="middle" width="80%"' ) . '<small>' .
          '<a href="'.$url.'">'._("View Full Header").'</a>';

    /* Output the printer friendly link if we are in subtle mode. */
    $s .= '&nbsp;|&nbsp;' .
          printer_friendly_link($mailbox, $passed_id, $passed_ent_id, $color);
    echo $s;

    /* Output the download and/or unsafe images link/-s, if any. */
    if ($download_and_unsafe_link) {
        echo $download_and_unsafe_link;
    }

    do_hook("read_body_header_right");
    $s = "</small></td>\n" .
         "</tr>\n";
    echo $s;

}

/***************************/
/*   Main of read_body.php */
/***************************/

/* get the globals we may need */

sqgetGlobalVar('key',       $key,           SQ_COOKIE);
sqgetGlobalVar('username',  $username,      SQ_SESSION);
sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
sqgetGlobalVar('base_uri',  $base_uri,      SQ_SESSION);

sqgetGlobalVar('msgs',      $msgs,          SQ_SESSION);
sqgetGlobalVar('msort',     $msort,         SQ_SESSION);
sqgetGlobalVar('lastTargetMailbox', $lastTargetMailbox, SQ_SESSION);
sqgetGlobalVar('server_sort_array', $server_sort_array, SQ_SESSION);
if (!sqgetGlobalVar('messages', $messages, SQ_SESSION) ) {
    $messages = array();
}

/** GET VARS */
sqgetGlobalVar('sendreceipt',   $sendreceipt,   SQ_GET);
sqgetGlobalVar('where',         $where,         SQ_GET);
sqgetGlobalVar('what',          $what,          SQ_GET);
if ( sqgetGlobalVar('show_more', $temp,  SQ_GET) ) {
    $show_more = (int) $temp;
}
if ( sqgetGlobalVar('show_more_cc', $temp,  SQ_GET) ) {
    $show_more_cc = (int) $temp;
}
if ( sqgetGlobalVar('show_more_bcc', $temp,  SQ_GET) ) {
    $show_more_bcc = (int) $temp;
}
if ( sqgetGlobalVar('view_hdr', $temp,  SQ_GET) ) {
    $view_hdr = (int) $temp;
}

/** POST VARS */
sqgetGlobalVar('move_id',       $move_id,       SQ_POST);

/** GET/POST VARS */
sqgetGlobalVar('passed_ent_id', $passed_ent_id);
sqgetGlobalVar('mailbox',       $mailbox);

if ( sqgetGlobalVar('passed_id', $temp) ) {
    $passed_id = (int) $temp;
}
if ( sqgetGlobalVar('sort', $temp) ) {
    $sort = (int) $temp;
}
if ( sqgetGlobalVar('startMessage', $temp) ) {
    $startMessage = (int) $temp;
}

/* end of get globals */
global $uid_support, $sqimap_capabilities;

$imapConnection = sqimap_login($username, $key, $imapServerAddress, $imapPort, 0);
$mbx_response   = sqimap_mailbox_select($imapConnection, $mailbox, false, false, true);


/**
 * $message contains all information about the message
 * including header and body
 */

$uidvalidity = $mbx_response['UIDVALIDITY'];

if (!isset($messages[$uidvalidity])) {
   $messages[$uidvalidity] = array();
}
if (!isset($messages[$uidvalidity][$passed_id]) || !$uid_support) {
   $message = sqimap_get_message($imapConnection, $passed_id, $mailbox);
   $FirstTimeSee = !$message->is_seen;
   $message->is_seen = true;
   $messages[$uidvalidity][$passed_id] = $message;
} else {
//   $message = sqimap_get_message($imapConnection, $passed_id, $mailbox);
   $message = $messages[$uidvalidity][$passed_id];
   $FirstTimeSee = !$message->is_seen;
}

if (isset($passed_ent_id) && $passed_ent_id) {
    $message = $message->getEntity($passed_ent_id);
    if ($message->type0 != 'message'  && $message->type1 != 'rfc822') {
        $message = $message->parent;
    }
    $read = sqimap_run_command ($imapConnection, "FETCH $passed_id BODY[$passed_ent_id.HEADER]", true, $response, $msg, $uid_support);
    $rfc822_header = new Rfc822Header();
    $rfc822_header->parseHeader($read);
    $message->rfc822_header = $rfc822_header;
} else {
    $passed_ent_id = 0;
}
$header = $message->header;

// gmail does not mark messages as read when retrieving the message body
// even though RFC 3501, section 6.4.5 (FETCH Command) says:
// "The \Seen flag is implicitly set; if this causes the flags to change,
// they SHOULD be included as part of the FETCH responses."
//
if ($imap_server_type == 'gmail') {
    sqimap_toggle_flag($imapConnection, $passed_id, '\\Seen', true, true);
}

do_hook('html_top');

/****************************************/
/* Block for handling incoming url vars */
/****************************************/

if (isset($sendreceipt)) {
   if ( !$message->is_mdnsent ) {
      if (isset($identity) ) {
         $final_recipient = getPref($data_dir, $username, 'email_address0', '' );
      } else {
         $final_recipient = getPref($data_dir, $username, 'email_address', '' );
      }

      $final_recipient = trim($final_recipient);
      if ($final_recipient == '' ) {
         $final_recipient = getPref($data_dir, $username, 'email_address', '' );
      }
      $supportMDN = ServerMDNSupport($mbx_response["PERMANENTFLAGS"]);
      if ( SendMDN( $mailbox, $passed_id, $final_recipient, $message, $imapConnection ) > 0 && $supportMDN ) {
         ToggleMDNflag( true, $imapConnection, $mailbox, $passed_id, $uid_support);
         $message->is_mdnsent = true;
         $messages[$uidvalidity][$passed_id]=$message;
      }
   }
}
/***********************************************/
/* End of block for handling incoming url vars */
/***********************************************/

$msgs[$passed_id]['FLAG_SEEN'] = true;

$messagebody = '';
do_hook('read_body_top');
if ($show_html_default == 1) {
    $ent_ar = $message->findDisplayEntity(array());
} else {
    $ent_ar = $message->findDisplayEntity(array(), array('text/plain'));
}
$cnt = count($ent_ar);
for ($i = 0; $i < $cnt; $i++) {
   $messagebody .= formatBody($imapConnection, $message, $color, $wrap_at, $ent_ar[$i], $passed_id, $mailbox);
   if ($i != $cnt-1) {
       $messagebody .= '<hr noshade size=1>';
   }
}

displayPageHeader($color, $mailbox);
formatMenuBar($mailbox, $passed_id, $passed_ent_id, $message, $mbx_response);
formatEnvheader($mailbox, $passed_id, $passed_ent_id, $message, $color, $FirstTimeSee);
echo '<table width="100%" cellpadding="0" cellspacing="0" align="center" border="0">';
echo '  <tr><td>';
echo '    <table width="100%" cellpadding="1" cellspacing="0" align="center" border="0" bgcolor="'.$color[9].'">';
echo '      <tr><td>';
echo '        <table width="100%" cellpadding="3" cellspacing="0" align="center" border="0">';
echo '          <tr bgcolor="'.$color[4].'"><td>';
// echo '            <table cellpadding="1" cellspacing="5" align="left" border="0">';
echo html_tag( 'table' ,'' , 'left', '', 'cellpadding="1" cellspacing="5" border="0"' );
echo '              <tr>' . html_tag( 'td', '<br />'. $messagebody."\n", 'left')
                        . '</tr>';
echo '            </table>';
echo '          </td></tr>';
echo '        </table></td></tr>';
echo '    </table>';
echo '  </td></tr>';

echo '<tr><td height="5" colspan="2" bgcolor="'.
          $color[4].'"></td></tr>'."\n";

$attachmentsdisplay = formatAttachments($message,$ent_ar,$mailbox, $passed_id);
if ($attachmentsdisplay) {
   echo '  <tr><td>';
   echo '    <table width="100%" cellpadding="1" cellspacing="0" align="center"'.' border="0" bgcolor="'.$color[9].'">';
   echo '     <tr><td>';
   echo '       <table width="100%" cellpadding="0" cellspacing="0" align="center" border="0" bgcolor="'.$color[4].'">';
   echo '        <tr>' . html_tag( 'td', '', 'left', $color[9] );
   echo '           <b>' . _("Attachments") . ':</b>';
   echo '        </td></tr>';
   echo '        <tr><td>';
   echo '          <table width="100%" cellpadding="2" cellspacing="2" align="center"'.' border="0" bgcolor="'.$color[0].'"><tr><td>';
   echo              $attachmentsdisplay;
   echo '          </td></tr></table>';
   echo '       </td></tr></table>';
   echo '    </td></tr></table>';
   echo '  </td></tr>';
   echo '<tr><td height="5" colspan="2" bgcolor="'.
          $color[4].'"></td></tr>';
}
echo '</table>';

/* show attached images inline -- if pref'fed so */
if (($attachment_common_show_images) &&
    is_array($attachment_common_show_images_list)) {
    foreach ($attachment_common_show_images_list as $img) {
        $imgurl = SM_PATH . 'src/download.php' .
                '?' .
                'passed_id='     . urlencode($img['passed_id']) .
                '&amp;mailbox='       . urlencode($mailbox) .
                '&amp;ent_id=' . urlencode($img['ent_id']) .
                '&amp;absolute_dl=true';

        echo html_tag( 'table', "\n" .
                    html_tag( 'tr', "\n" .
                        html_tag( 'td', '<img src="' . $imgurl . '" />' ."\n", 'left'
                        )
                    ) ,
        'center', '', 'cellspacing="0" border="0" cellpadding="2"');
    }
}

//FIXME: one of these hooks should be removed if we can verify disuse (html_bottom?)
do_hook('read_body_bottom');
do_hook('html_bottom');
sqimap_logout($imapConnection);
/* sessions are written at the end of the script. it's better to register
   them at the end so we avoid double session_register calls */
sqsession_register($messages,'messages');

?>
</body></html>
