<?php

/**
 * mailbox_display.php
 *
 * This contains functions that display mailbox information, such as the
 * table row that has sender, date, subject, etc...
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: mailbox_display.php 14114 2011-05-15 22:02:24Z pdontthink $
 * @package squirrelmail
 */

/** The standard includes.. */
require_once(SM_PATH . 'functions/strings.php');
require_once(SM_PATH . 'functions/html.php');
require_once(SM_PATH . 'class/html.class.php');
require_once(SM_PATH . 'functions/imap_mailbox.php');

/* Constants:
 *   PG_SEL_MAX:   default value for page_selector_max
 *   SUBJ_TRIM_AT: the length at which we trim off subjects
 */
define('PG_SEL_MAX', 10);
define('SUBJ_TRIM_AT', 55);

function elapsed($start)
{
   $end = microtime();
   list($start2, $start1) = explode(" ", $start);
   list($end2, $end1) = explode(" ", $end);
   $diff1 = $end1 - $start1;
   $diff2 = $end2 - $start2;
   if( $diff2 < 0 ){
       $diff1 -= 1;
       $diff2 += 1.0;
   }
   return $diff2 + $diff1;
}

function printMessageInfo($imapConnection, $t, $not_last=true, $key, $mailbox,
                          $start_msg, $where, $what) {
    global $checkall, $preselected,
           $color, $msgs, $msort, $td_str, $msg,
           $default_use_priority,
           $message_highlight_list,
           $index_order,
           $indent_array,   /* indent subject by */
           $pos,            /* Search postion (if any)  */
           $thread_sort_messages, /* thread sorting on/off */
           $server_sort_order, /* sort value when using server-sorting */
           $row_count,
           $allow_server_sort, /* enable/disable server-side sorting */
           $truncate_subject,
           $truncate_sender,
           $internal_date_sort;

    sqgetGlobalVar('sort', $sort, SQ_SESSION);

    $color_string = $color[4];

    if ($GLOBALS['alt_index_colors']) {
        if (!isset($row_count)) {
            $row_count = 0;
        }
        $row_count++;
        if ($row_count % 2) {
            if (!isset($color[12])) {
                $color[12] = '#EAEAEA';
            }
            $color_string = $color[12];
        }
    }
    $msg = $msgs[$key];

    if($mailbox == 'None') {
        $boxes   = sqimap_mailbox_list($imapConnection);
        $mailbox = $boxes[0]['unformatted'];
        unset($boxes);
    }
    $urlMailbox = urlencode($mailbox);

    if (handleAsSent($mailbox)) {
       $msg['FROM'] = $msg['TO'];
    }
    $msg['FROM'] = parseAddress($msg['FROM'],1);


       /*
        * This is done in case you're looking into Sent folders,
        * because you can have multiple receivers.
        */

    $senderNames = $msg['FROM'];
    $senderName  = '';
    $senderFrom = '';


    if (sizeof($senderNames)){
        foreach ($senderNames as $senderNames_part) {
            if ($senderName != '') {
                $senderName .= ', ';
            }

            if ($senderFrom != '') {
                $senderFrom .= ', ';
            }

            if ($senderNames_part[1]) {
                $senderName .= decodeHeader($senderNames_part[1]);
            } else {
                $senderName .= htmlspecialchars($senderNames_part[0]);
            }

            $senderFrom .= htmlspecialchars($senderNames_part[0]);
        }
    }
    $senderName = str_replace('&nbsp;',' ',$senderName);
    if (substr($senderName, 0, 6) == '&quot;'
     && substr($senderName, -6) == '&quot;')
        $senderName = substr(substr($senderName, 0, -6), 6);
    echo html_tag( 'tr','','','','valign="top"') . "\n";

    if (isset($msg['FLAG_FLAGGED']) && ($msg['FLAG_FLAGGED'] == true)) {
        $flag = "<font color=\"$color[2]\">";
        $flag_end = '</font>';
    } else {
        $flag = '';
        $flag_end = '';
    }
    if (!isset($msg['FLAG_SEEN']) || ($msg['FLAG_SEEN'] == false)) {
        $bold = '<b>';
        $bold_end = '</b>';
    } else {
        $bold = '';
        $bold_end = '';
    }
    if (handleAsSent($mailbox)) {
        $italic = '<i>';
        $italic_end = '</i>';
    } else {
        $italic = '';
        $italic_end = '';
    }
    if (isset($msg['FLAG_DELETED']) && $msg['FLAG_DELETED']) {
        $fontstr = "<font color=\"$color[9]\">";
        $fontstr_end = '</font>';
    } else {
        $fontstr = '';
        $fontstr_end = '';
    }

    if ($where && $what) {
        $searchstr = '&amp;where='.$where.'&amp;what='.$what;
    } else {
        $searchstr = '';
    }

    if (is_array($message_highlight_list) && count($message_highlight_list)) {
        $msg['TO'] = parseAddress($msg['TO']);
        $msg['CC'] = parseAddress($msg['CC']);
        $decoded_addresses = array();
        foreach ($message_highlight_list as $message_highlight_list_part) {
            if (trim($message_highlight_list_part['value']) != '') {
                $high_val   = strtolower($message_highlight_list_part['value']);
                $match_type = strtoupper($message_highlight_list_part['match_type']);
                if($match_type == 'TO_CC') {
                    $match = array('TO', 'CC');
                } else {
                    $match = array($match_type);
                }
                foreach($match as $match_type) {
                    switch($match_type) {
                        case('TO'):
                        case('CC'):
                        case('FROM'):
                            foreach ($msg[$match_type] as $i => $address) {
                                if (empty($decoded_addresses[$match_type][$i])) {
                                    $decoded_addresses[$match_type][$i][0] = decodeHeader($address[0], true, false);
                                    $decoded_addresses[$match_type][$i][1] = decodeHeader($address[1], true, false);
                                }
                                $address = $decoded_addresses[$match_type][$i];
                                if (strstr('^^' . strtolower($address[0]), $high_val) ||
                                    strstr('^^' . strtolower($address[1]), $high_val)) {
                                    $hlt_color = $message_highlight_list_part['color'];
                                    break 4;
                                }
                            }
                            break;
                        default:
                            $headertest = strtolower(decodeHeader($msg[$match_type], true, false));
                            if (strstr('^^' . $headertest, $high_val)) {
                                $hlt_color = $message_highlight_list_part['color'];
                                break 3;
                            }
                            break;
                    }
                }
            }
        }
    }

    if (!isset($hlt_color)) {
        $hlt_color = $color_string;
    }
    if ($checkall == 1 || in_array($msg['ID'], $preselected))
        $checked = ' checked="checked"';
    else
        $checked = '';
    $col = 0;
    $msg['SUBJECT'] = decodeHeader($msg['SUBJECT']);
//    $subject = processSubject($msg['SUBJECT'], $indent_array[$msg['ID']]);
    $subject = sm_truncate_string(str_replace('&nbsp;',' ',$msg['SUBJECT']), $truncate_subject, '...', TRUE);
    if (sizeof($index_order)) {
        foreach ($index_order as $index_order_part) {
            switch ($index_order_part) {
            case 1: /* checkbox */
                echo html_tag( 'td',
                               "<input type=\"checkbox\" name=\"msg[$t]\" id=\"msg".$msg['ID'].
                                   "\" value=\"".$msg['ID']."\"$checked>",
                               'center',
                               $hlt_color );
                break;
            case 2: /* from */
                $from_xtra = '';
                $from_xtra = 'title="' . $senderFrom . '"';
                echo html_tag( 'td',
                    html_tag('label',
                               $italic . $bold . $flag . $fontstr . sm_truncate_string($senderName, $truncate_sender, '...', TRUE) .
                               $fontstr_end . $flag_end . $bold_end . $italic_end,
                           '','','for="msg'.$msg['ID'].'"'),
                           'left',
                           $hlt_color, $from_xtra );
                break;
            case 3: /* date */
                // show internal date if using it to sort
                if ($internal_date_sort && ($sort == 0 || $sort == 1)) {
                    $date_string = $msg['RECEIVED_DATE_STRING'] . '';
                } else {
                    $date_string = $msg['DATE_STRING'] . '';
                }
                if ($date_string == '') {
                    $date_string = _("Unknown date");
                }
                echo html_tag( 'td',
                               $bold . $flag . $fontstr . $date_string .
                               $fontstr_end . $flag_end . $bold_end,
                               'center',
                               $hlt_color,
                               'nowrap' );
                break;
            case 4: /* subject */
                $td_str = $bold;
                if ($thread_sort_messages == 1) {
                    if (isset($indent_array[$msg['ID']])) {
                        $td_str .= str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$indent_array[$msg['ID']]);
                    }
                }
                $td_str .= '<a href="read_body.php?mailbox='.$urlMailbox
                        .  '&amp;passed_id='. $msg["ID"]
                        .  '&amp;startMessage='.$start_msg.$searchstr.'"';
                $td_str .= ' ' .concat_hook_function('subject_link', array($start_msg, $searchstr));
                if ($subject != $msg['SUBJECT']) {
                    $title = get_html_translation_table(HTML_SPECIALCHARS);
                    $title = array_flip($title);
                    $title = strtr($msg['SUBJECT'], $title);
                    $title = str_replace('"', "''", $title);
                    $td_str .= " title=\"$title\"";
                }
                $td_str .= ">$flag$subject$flag_end</a>$bold_end";
                echo html_tag( 'td', $td_str, 'left', $hlt_color );
                break;
            case 5: /* flags */
                $stuff = false;
                $td_str = "<b><small>";

                if (isset($msg['FLAG_ANSWERED']) && $msg['FLAG_ANSWERED'] == true) {
                    // i18n: "A" is short for "Answered". Make sure that two icon strings aren't translated to the same character (only in 1.5).
                    $td_str .= _("A");
                    $stuff = true;
                }
                if ($msg['TYPE0'] == 'multipart' && $msg['TYPE1'] == 'mixed') {
                    $td_str .= '+';
                    $stuff = true;
                }
                if ($default_use_priority) {
                    if ( ($msg['PRIORITY'] == 1) || ($msg['PRIORITY'] == 2) ) {
                        $td_str .= "<font color=\"$color[1]\">!</font>";
                        $stuff = true;
                    }
                    if ($msg['PRIORITY'] == 5) {
                        $td_str .= "<font color=\"$color[8]\">?</font>";
                        $stuff = true;
                    }
                }
                if (isset($msg['FLAG_DELETED']) && $msg['FLAG_DELETED'] == true) {
                    $td_str .= "<font color=\"$color[1]\">D</font>";
                    $stuff = true;
                }
                if (!$stuff) {
                    $td_str .= '&nbsp;';
                }
                do_hook("msg_envelope");
                $td_str .= '</small></b>';
                echo html_tag( 'td',
                               $td_str,
                               'center',
                               $hlt_color,
                               'nowrap' );
                break;
            case 6: /* size */
                echo html_tag( 'td',
                               $bold . $fontstr . show_readable_size($msg['SIZE']) .
                               $fontstr_end . $bold_end,
                               'right',
                               $hlt_color );
                break;
            }
            ++$col;
        }
    }
    if ($not_last) {
        echo '</tr>' . "\n" . '<tr><td colspan="' . $col . '" bgcolor="' .
             $color[0] . '" height="1"></td></tr>' . "\n";
    } else {
        echo '</tr>'."\n";
    }
}

function getServerMessages($imapConnection, $start_msg, $show_num, $num_msgs, $id) {
    if ($id != 'no') {
        $id = array_slice($id, ($start_msg-1), $show_num);
        $end = $start_msg + $show_num - 1;
        if ($num_msgs < $show_num) {
            $end_loop = $num_msgs;
        } else if ($end > $num_msgs) {
            $end_loop = $num_msgs - $start_msg + 1;
        } else {
            $end_loop = $show_num;
        }
        return fillMessageArray($imapConnection,$id,$end_loop,$show_num);
    } else {
        return false;
    }
}

function getThreadMessages($imapConnection, $start_msg, $show_num, $num_msgs) {
    $id = get_thread_sort($imapConnection);
    return getServerMessages($imapConnection, $start_msg, $show_num, $num_msgs, $id);
}

function getServerSortMessages($imapConnection, $start_msg, $show_num,
                               $num_msgs, $server_sort_order, $mbxresponse) {
    $id = sqimap_get_sort_order($imapConnection, $server_sort_order,$mbxresponse);
    return getServerMessages($imapConnection, $start_msg, $show_num, $num_msgs, $id);
}

function getSelfSortMessages($imapConnection, $start_msg, $show_num,
                              $num_msgs, $sort, $mbxresponse) {
    $msgs = array();
    if ($num_msgs >= 1) {
        $id = sqimap_get_php_sort_order ($imapConnection, $mbxresponse);
        if ($sort != 6 ) {
            $end = $num_msgs;
            $end_loop = $end;
            /* set shownum to 999999 to fool sqimap_get_small_header_list
               and rebuild the msgs_str to 1:* */
            $show_num = 999999;
        } else {
            /* if it's not sorted */
            if ($start_msg + ($show_num - 1) < $num_msgs) {
                $end_msg = $start_msg + ($show_num - 1);
            } else {
                $end_msg = $num_msgs;
            }
            if ($end_msg < $start_msg) {
                $start_msg = $start_msg - $show_num;
                if ($start_msg < 1) {
                    $start_msg = 1;
                }
            }
            $id = array_slice(array_reverse($id), ($start_msg-1), $show_num);
            $end = $start_msg + $show_num - 1;
            if ($num_msgs < $show_num) {
                $end_loop = $num_msgs;
            } else if ($end > $num_msgs) {
                $end_loop = $num_msgs - $start_msg + 1;
            } else {
                $end_loop = $show_num;
            }
        }
        $msgs = fillMessageArray($imapConnection,$id,$end_loop, $show_num);
    }
    return $msgs;
}



/*
 * This function loops through a group of messages in the mailbox
 * and shows them to the user.
 */
function showMessagesForMailbox($imapConnection, $mailbox, $num_msgs,
                                $start_msg, $sort, $color, $show_num,
                                $use_cache, $mode='') {
    global $msgs, $msort, $auto_expunge, $thread_sort_messages,
           $allow_server_sort, $server_sort_order;

    /*
     * For some reason, on PHP 4.3+, this being unset, and set in the session causes havoc
     * so setting it to an empty array beforehand seems to clean up the issue, and stopping the
     * "Your script possibly relies on a session side-effect which existed until PHP 4.2.3" error
     */

    if (!isset($msort)) {
        $msort = array();
    }

    if (!isset($msgs)) {
        $msgs = array();
    }

    //$start = microtime();
    /* If autoexpunge is turned on, then do it now. */
    $mbxresponse = sqimap_mailbox_select($imapConnection, $mailbox);
    $srt = $sort;
    /* If autoexpunge is turned on, then do it now. */
    if ($auto_expunge == true) {
        $exp_cnt = sqimap_mailbox_expunge($imapConnection, $mailbox, false, '');
        $mbxresponse['EXISTS'] = $mbxresponse['EXISTS'] - $exp_cnt;
        $num_msgs = $mbxresponse['EXISTS'];
    }

    if ($mbxresponse['EXISTS'] > 0) {
        /* if $start_msg is lower than $num_msgs, we probably deleted all messages
         * in the last page. We need to re-adjust the start_msg
         */

        if($start_msg > $num_msgs) {
            $start_msg -= $show_num;
            if($start_msg < 1) {
                $start_msg = 1;
            }
        }

        /* This code and the next if() block check for
         * server-side sorting methods. The $id array is
         * formatted and $sort is set to 6 to disable
         * SM internal sorting
         */

        if ($thread_sort_messages == 1) {
            $mode = 'thread';
        } elseif ($allow_server_sort == 1) {
            $mode = 'serversort';
        } else {
            $mode = '';
        }

        if ($use_cache) {
            sqgetGlobalVar('msgs', $msgs, SQ_SESSION);
            sqgetGlobalVar('msort', $msort, SQ_SESSION);
        } else {
            sqsession_unregister('msort');
            sqsession_unregister('msgs');
        }
        switch ($mode) {
            case 'thread':
                $id   = get_thread_sort($imapConnection);
                $msgs = getServerMessages($imapConnection, $start_msg, $show_num, $num_msgs, $id);
                if ($msgs === false) {
                    echo '<b><small><center><font color="red">' .
                        _("Thread sorting is not supported by your IMAP server.") . '<br />' .
                        _("Please contact your system administrator and report this error.") .
                        '</font></center></small></b>';
                    $thread_sort_messages = 0;
                    $msort = $msgs = array();
                } else {
                    $msort= $msgs;
                    $sort = 6;
                }
                break;
            case 'serversort':
                $id   = sqimap_get_sort_order($imapConnection, $sort, $mbxresponse);
                $msgs = getServerMessages($imapConnection, $start_msg, $show_num, $num_msgs, $id);
                if ($msgs === false) {
                    echo '<b><small><center><font color="red">' .
                        _( "Server-side sorting is not supported by your IMAP server.") . '<br />' .
                        _("Please contact your system administrator and report this error.") .
                        '</font></center></small></b>';
                    $sort = $server_sort_order;
                    $allow_server_sort = FALSE;
                    $msort = $msgs = array();
                    $id = array();
                } else {
                    $msort = $msgs;
                    $sort = 6;
                }
                break;
            default:
                if (!$use_cache) {
                    $msgs = getSelfSortMessages($imapConnection, $start_msg, $show_num,
                                                $num_msgs, $sort, $mbxresponse);
                    $msort = calc_msort($msgs, $sort, $mailbox);
                } /* !use cache */
                break;
        } // switch
        sqsession_register($msort, 'msort');
        sqsession_register($msgs,  'msgs');

    } /* if exists > 0 */

    $res = getEndMessage($start_msg, $show_num, $num_msgs);
    $start_msg = $res[0];
    $end_msg   = $res[1];

    if ($num_msgs > 0) {
        $paginator_str = get_paginator_str($mailbox, $start_msg, $end_msg,
                                           $num_msgs, $show_num, $sort);
    } else {
        $paginator_str = '';
    }

    $msg_cnt_str = get_msgcnt_str($start_msg, $end_msg, $num_msgs);

    do_hook('mailbox_index_before');

    $safe_name = preg_replace("/[^0-9A-Za-z_]/", '_', $mailbox);
    $form_name = "FormMsgs" . $safe_name;
    echo '<form name="' . $form_name . '" method="post" action="move_messages.php">' ."\n" .
        '<input type="hidden" name="smtoken" value="'.sm_generate_security_token().'">' . "\n" .
        '<input type="hidden" name="mailbox" value="'.htmlspecialchars($mailbox).'">' . "\n" .
        '<input type="hidden" name="startMessage" value="'.htmlspecialchars($start_msg).'">' . "\n";
    
    echo '<table border="0" width="100%" cellpadding="0" cellspacing="0">';
    echo '<tr><td>';

    mail_message_listing_beginning($imapConnection, $mailbox, $sort,
                                  $msg_cnt_str, $paginator_str, $start_msg);
    /* line between the button area and the list */
    echo '<tr><td height="5" bgcolor="'.$color[4].'"></td></tr>';

    echo '<tr><td>';
    echo '    <table width="100%" cellpadding="1" cellspacing="0" align="center"'.' border="0" bgcolor="'.$color[9].'">';
    echo '     <tr><td>';
    echo '       <table width="100%" cellpadding="1" cellspacing="0" align="center" border="0" bgcolor="'.$color[5].'">';
    printHeader($mailbox, $srt, $color, !$thread_sort_messages);

    displayMessageArray($imapConnection, $num_msgs, $start_msg,
                        $msort, $mailbox, $sort, $color, $show_num,0,0);
    echo '</table></td></tr></table>';

    mail_message_listing_end($num_msgs, $paginator_str, $msg_cnt_str, $color);
    echo '</table>';

    echo "\n</form>\n\n";
    
    //$t = elapsed($start);
    //echo("elapsed time = $t seconds\n");
}

function calc_msort($msgs, $sort, $mailbox = 'INBOX') {

    /*
     * 0 = Date (up)
     * 1 = Date (dn)
     * 2 = Name (up)
     * 3 = Name (dn)
     * 4 = Subject (up)
     * 5 = Subject (dn)
     * 6 = default no sort
     * 7 - UNUSED
     * 8 = Size (up)
     * 9 = Size (dn)
     */

    global $internal_date_sort;

    if (($sort == 0) || ($sort == 1)) {
        foreach ($msgs as $item) {
            if ($internal_date_sort)
                $msort[] = $item['RECEIVED_TIME_STAMP'];
            else
                $msort[] = $item['TIME_STAMP'];
        }
    } elseif (($sort == 2) || ($sort == 3)) {
        $fld_sort = (handleAsSent($mailbox)?'TO-SORT':'FROM-SORT');
        foreach ($msgs as $item) {
            $msort[] = $item[$fld_sort];
        }
    } elseif (($sort == 4) || ($sort == 5)) {
        foreach ($msgs as $item) {
            $msort[] = $item['SUBJECT-SORT'];
        }
    } elseif (($sort == 8) || ($sort == 9)) {
       foreach ($msgs as $item) {
           $msort[] = $item['SIZE'];
       }
    } else {
        $msort = $msgs;
    }
    if ($sort != 6) {
        if ($sort % 2) {
            asort($msort);
        } else {
            arsort($msort);
        }
    }
    return $msort;
}

function fillMessageArray($imapConnection, $id, $count, $show_num=false) {
    return sqimap_get_small_header_list($imapConnection, $id, $show_num);
}


/* Generic function to convert the msgs array into an HTML table. */
function displayMessageArray($imapConnection, $num_msgs, $start_msg,
                             $msort, $mailbox, $sort, $color,
                             $show_num, $where=0, $what=0) {
    global $imapServerAddress, $use_mailbox_cache, $index_order,
           $indent_array, $thread_sort_messages, $allow_server_sort,
           $server_sort_order, $PHP_SELF;

    $res = getEndMessage($start_msg, $show_num, $num_msgs);
    $start_msg = $res[0];
    $end_msg   = $res[1];

    $urlMailbox = urlencode($mailbox);

    /* get indent level for subject display */
    if ($thread_sort_messages == 1 && $num_msgs) {
        $indent_array = get_parent_level($imapConnection);
    }

    $real_startMessage = $start_msg;
    if ($sort == 6) {
        if ($end_msg - $start_msg < $show_num - 1) {
            $end_msg = $end_msg - $start_msg + 1;
            $start_msg = 1;
        } else if ($start_msg > $show_num) {
            $end_msg = $show_num;
            $start_msg = 1;
        }
    }
    $endVar = $end_msg + 1;

    /*
     * Loop through and display the info for each message.
     * ($t is used for the checkbox number)
     */
    $t = 0;

    /* messages display */

    if (!$num_msgs) {
    /* if there's no messages in this folder */
        echo html_tag( 'tr',
                html_tag( 'td',
                          "<br><b>" . _("THIS FOLDER IS EMPTY") . "</b><br>&nbsp;",
                          'center',
                          $color[4],
                          'colspan="' . count($index_order) . '"'
                )
        );
    } elseif ($start_msg == $end_msg) {
    /* if there's only one message in the box, handle it differently. */
        if ($sort != 6) {
            $i = $start_msg;
        } else {
            $i = 1;
        }
        reset($msort);
        $k = 0;
        do {
            $key = key($msort);
            next($msort);
            $k++;
        } while (isset ($key) && ($k < $i));
        printMessageInfo($imapConnection, $t, true, $key, $mailbox,
                         $real_startMessage, $where, $what);
    } else {
        $i = $start_msg;
        reset($msort);
        $k = 0;
        do {
            $key = key($msort);
            next($msort);
            $k++;
        } while (isset ($key) && ($k < $i));
        $not_last = true;
        do {
            if (!$i || $i == $endVar-1) $not_last = false;
                printMessageInfo($imapConnection, $t, $not_last, $key, $mailbox,
                                 $real_startMessage, $where, $what);
            $key = key($msort);
            $t++;
            $i++;
            next($msort);
        } while ($i && $i < $endVar);
    }
}

/*
 * Displays the standard message list header. To finish the table,
 * you need to do a "</table></table>";
 *
 * $moveURL is the URL to submit the delete/move form to
 * $mailbox is the current mailbox
 * $sort is the current sorting method (-1 for no sorting available [searches])
 * $Message is a message that is centered on top of the list
 * $More is a second line that is left aligned
 */

function mail_message_listing_beginning ($imapConnection,
                                         $mailbox = '', $sort = -1,
                                         $msg_cnt_str = '',
                                         $paginator = '&nbsp;',
                                         $start_msg = 1) {
    global $color, $auto_expunge, $base_uri, $thread_sort_messages,
           $allow_thread_sort, $allow_server_sort, $server_sort_order,
           $PHP_SELF;

    $php_self = $PHP_SELF;
    /* fix for incorrect $PHP_SELF */
    if (strpos($php_self, 'move_messages.php')) {
        $php_self = str_replace('move_messages.php', 'right_main.php', $php_self);
    }
    $urlMailbox = urlencode($mailbox);

    if (preg_match('/^(.+)\?.+$/',$php_self,$regs)) {
        $source_url = $regs[1];
    } else {
        $source_url = $php_self;
    }

    /*
     * This is the beginning of the message list table.
     * It wraps around all messages
     */

    if (!empty($paginator) && !empty($msg_cnt_str)) {

        echo html_tag( 'table' ,
            html_tag( 'tr',
                html_tag( 'td' ,
                    html_tag( 'table' ,
                        html_tag( 'tr',
                            html_tag( 'td', $paginator, 'left' ) .
                            html_tag( 'td', $msg_cnt_str, 'right' )
                        )
                    , '', $color[4], 'border="0" width="100%" cellpadding="1"  cellspacing="0"' )
                , 'left', '', '' )
            , '', $color[0] )
         , '', '', 'border="0" width="100%" cellpadding="1"  cellspacing="0"' );
    }
    /* line between header and button area */
        echo '</td></tr><tr><td height="5" bgcolor="'.$color[4].'"></td></tr>';

        echo html_tag( 'tr' ) . "\n"
        . html_tag( 'td' ,'' , 'left', '', '' )
         . html_tag( 'table' ,'' , '', $color[9], 'border="0" width="100%" cellpadding="1"  cellspacing="0"' )
          . '<tr><td>'
           . html_tag( 'table' ,'' , '', $color[0], 'border="0" width="100%" cellpadding="1"  cellspacing="0"' )
            . html_tag( 'tr',
             getSmallStringCell(_("Move Selected To"), 'left', 'nowrap') .
             getSmallStringCell(_("Transform Selected Messages"), 'right')
            )
            . html_tag( 'tr' ) ."\n"
            . html_tag( 'td', '', 'left', '', 'valign="middle" nowrap' );
            getMbxList($imapConnection);
            echo getButton('SUBMIT', 'moveButton',_("Move")) . "\n";
            echo getButton('SUBMIT', 'attache',_("Forward")) . "\n";
            do_hook('mailbox_display_buttons');

    echo "      </small></td>\n"
         . html_tag( 'td', '', 'right', '', 'nowrap' );

    if (!$auto_expunge) {
        echo getButton('SUBMIT', 'expungeButton',_("Expunge")) ."\n";
    }

    echo getButton('SUBMIT', 'markRead',_("Read")) . "\n";
    echo getButton('SUBMIT', 'markUnread',_("Unread")) . "\n";
    echo getButton('SUBMIT', 'delete',_("Delete")) ."&nbsp;\n";
    if (!strpos($php_self,'mailbox')) {
        $location = $php_self.'?mailbox=INBOX&amp;startMessage=1';
    } else {
        $location = $php_self;
    }

//    $location = urlencode($location);

    echo '<input type="hidden" name="location" value="'.$location.'">';
    echo "</td>\n"
         . "   </tr>\n";

    /* draws thread sorting links */
    if ($allow_thread_sort == TRUE) {
        if ($thread_sort_messages == 1 ) {
            $set_thread = 2;
            $thread_name = _("Unthread View");
        } elseif ($thread_sort_messages == 0) {
            $set_thread = 1;
            $thread_name = _("Thread View");
        }
        echo html_tag( 'tr' ,
                    html_tag( 'td' ,
                              '&nbsp;<small><a href="' . $source_url . '?sort='
                              . "$sort" . '&amp;start_messages=1&amp;set_thread=' . "$set_thread"
                              . '&amp;mailbox=' . urlencode($mailbox) . '">' . $thread_name
                              . '</a></small>&nbsp;'
                     , '', '', '' )
                 , '', '', '' );
    }

    echo "</table></td></tr></table></td></tr>\n";

    do_hook('mailbox_form_before');

    /* if using server sort we highjack the
     * the $sort var and use $server_sort_order
     * instead. but here we reset sort for a bit
     * since its easy
     */
    if ($allow_server_sort == TRUE) {
        $sort = $server_sort_order;
    }
}

function mail_message_listing_end($num_msgs, $paginator_str, $msg_cnt_str, $color) {
  if ($num_msgs) {
    /* space between list and footer */
    echo '<tr><td height="5" bgcolor="'.$color[4].'" colspan="1">';

    echo '</td></tr><tr><td>';
    echo html_tag( 'table',
            html_tag( 'tr',
                html_tag( 'td',
                    html_tag( 'table',
                        html_tag( 'tr',
                            html_tag( 'td', $paginator_str ) .
                            html_tag( 'td', $msg_cnt_str, 'right' )
                        )
                    , '', $color[4], 'width="100%" border="0" cellpadding="1" cellspacing="0"' )
                )
            )
        , '', $color[9], 'width="100%" border="0" cellpadding="1"  cellspacing="0"' );
    echo '</td></tr>';
  }
    /* End of message-list table */

    do_hook('mailbox_index_after');
}

function printHeader($mailbox, $sort, $color, $showsort=true) {

    global $index_order, $internal_date_sort, $imap_server_type;

    // gmail doesn't support custom sorting, so we always
    // hide the sort buttons when using gmail
    if ($imap_server_type == 'gmail') $showsort = false;

    echo html_tag( 'tr' ,'' , 'center', $color[5] );

    /* calculate the width of the subject column based on the
     * widths of the other columns */
    $widths = array(1=>1,2=>25,3=>5,4=>0,5=>1,6=>5);
    $subjectwidth = 100;
    foreach($index_order as $item) {
        $subjectwidth -= $widths[$item];
    }

    foreach ($index_order as $item) {
        switch ($item) {
        case 1: /* checkbox */
        case 5: /* flags */
            echo html_tag( 'td' ,'&nbsp;' , '', '', 'width="1%"' );
            break;
        case 2: /* from */
            if (handleAsSent($mailbox)) {
                echo html_tag( 'td' ,'' , 'left', '', 'width="25%"' )
                     . '<b>' . _("To") . '</b>';
            } else {
                echo html_tag( 'td' ,'' , 'left', '', 'width="25%"' )
                     . '<b>' . _("From") . '</b>';
            }
            if ($showsort) {
                ShowSortButton($sort, $mailbox, 2, 3);
            }
            echo "</td>\n";
            break;
        case 3: /* date */
            echo html_tag( 'td' ,'' , 'left', '', 'width="5%" nowrap' )
                 . '<b>'
                 . ($internal_date_sort && ($sort == 0 || $sort == 1) ? _("Received") : _("Date"))
                 . '</b>';
            if ($showsort) {
                ShowSortButton($sort, $mailbox, 0, 1);
            }
            echo "</td>\n";
            break;
        case 4: /* subject */
            echo html_tag( 'td' ,'' , 'left', '', 'width="'.$subjectwidth.'%"' )
                 . '<b>' . _("Subject") . '</b>';
            if ($showsort) {
                ShowSortButton($sort, $mailbox, 4, 5);
            }
            echo "</td>\n";
            break;
        case 6: /* size */
            echo html_tag( 'td' ,'' , 'left', '', 'width="5%" nowrap' )
                 . '<b>' . _("Size") . '</b>';
            if ($showsort) {
                ShowSortButton($sort, $mailbox, 8, 9);
            }
            echo "</td>\n";
            break;
        }
    }
    echo "</tr>\n";
}


/*
 * This function shows the sort button. Isn't this a good comment?
 */
function ShowSortButton($sort, $mailbox, $Up, $Down ) {
    global $PHP_SELF;
    /* Figure out which image we want to use. */
    if ($sort != $Up && $sort != $Down) {
        $img = 'sort_none.png';
        $which = $Up;
    } elseif ($sort == $Up) {
        $img = 'up_pointer.png';
        $which = $Down;
    } else {
        $img = 'down_pointer.png';
        $which = 6;
    }

    if (preg_match('/^(.+)\?.+$/',$PHP_SELF,$regs)) {
        $source_url = $regs[1];
    } else {
        $source_url = $PHP_SELF;
    }

    /* Now that we have everything figured out, show the actual button. */
    echo ' <a href="' . $source_url .'?newsort=' . $which
         . '&amp;startMessage=1&amp;mailbox=' . urlencode($mailbox)
         . '"><img src="../images/' . $img
         . '" border="0" width="12" height="10" alt="sort"></a>';
}

function get_selectall_link($start_msg, $sort) {
    global $checkall, $what, $where, $mailbox, $javascript_on;
    global $PHP_SELF, $PG_SHOWNUM;

    $result = '';
    if ($javascript_on) {
        $safe_name = preg_replace("/[^0-9A-Za-z_]/", '_', $mailbox);
        $func_name = "CheckAll" . $safe_name;
        $form_name = "FormMsgs" . $safe_name;
        $result = '<script language="JavaScript" type="text/javascript">'
                . "\n<!-- \n"
                . "function " . $func_name . "() {\n"
                . "  for (var i = 0; i < document." . $form_name . ".elements.length; i++) {\n"
                . "    if(document." . $form_name . ".elements[i].type == 'checkbox'){\n"
                . "      document." . $form_name . ".elements[i].checked = "
                . "        !(document." . $form_name . ".elements[i].checked);\n"
                . "    }\n"
                . "  }\n"
                . "}\n"
                . "//-->\n"
                . '</script><a href="javascript:void(0)" onClick="' . $func_name . '();">' . _("Toggle All")
/*                . '</script><a href="javascript:' . $func_name . '()">' . _("Toggle All")*/
                . "</a>\n";
    } else {
        if (strpos($PHP_SELF, "?")) {
            $result .= "<a href=\"$PHP_SELF&amp;mailbox=" . urlencode($mailbox)
                    .  "&amp;startMessage=$start_msg&amp;sort=$sort&amp;checkall=";
        } else {
            $result .= "<a href=\"$PHP_SELF?mailbox=" . urlencode($mailbox)
                    .  "&amp;startMessage=$start_msg&amp;sort=$sort&amp;checkall=";
        }
        if (isset($checkall) && $checkall == '1') {
            $result .= '0';
        } else {
            $result .= '1';
        }

        if (isset($where) && isset($what)) {
            $result .= '&amp;where=' . urlencode($where)
                    .  '&amp;what=' . urlencode($what);
        }
        $result .= "\">";

        if (isset($checkall) && ($checkall == '1')) {
            $result .= _("Unselect All");
        } else {
            $result .= _("Select All");
        }
        $result .= "</a>\n";
    }

    /* Return our final result. */
    return ($result);
}

/*
 * This function computes the "Viewing Messages..." string.
 */
function get_msgcnt_str($start_msg, $end_msg, $num_msgs) {
    /* Compute the $msg_cnt_str. */
    $result = '';
    if ($start_msg < $end_msg) {
        $result = sprintf(_("Viewing Messages: %s to %s (%s total)"),
                  '<b>'.$start_msg.'</b>', '<b>'.$end_msg.'</b>', $num_msgs);
    } else if ($start_msg == $end_msg) {
        $result = sprintf(_("Viewing Message: %s (%s total)"), '<b>'.$start_msg.'</b>', $num_msgs);
    } else {
        $result = '<br>';
    }
    /* Return our result string. */
    return ($result);
}

/*
 * Generate a paginator link.
 */
function get_paginator_link($box, $start_msg, $use, $text) {
    global $PHP_SELF;

    $result = "<a href=\"right_main.php?use_mailbox_cache=$use"
            . "&amp;startMessage=$start_msg&amp;mailbox=$box\" "
            . ">$text</a>";
    return ($result);
/*
    if (preg_match('/^(.+)\?.+$/',$PHP_SELF,$regs)) {
        $source_url = $regs[1];
    } else {
        $source_url = $PHP_SELF;
    }

    $result = '<A HREF="'. $source_url . "?use_mailbox_cache=$use"
            . "&amp;startMessage=$start_msg&amp;mailbox=$box\" "
            . ">$text</A>";
    return ($result);
*/
}

/*
 * This function computes the paginator string.
 */
function get_paginator_str($box, $start_msg, $end_msg, $num_msgs,
                           $show_num, $sort) {
    global $username, $data_dir, $use_mailbox_cache, $color, $PG_SHOWNUM;

    /* Initialize paginator string chunks. */
    $prv_str = '';
    $nxt_str = '';
    $pg_str  = '';
    $all_str = '';
    $tgl_str = '';

    $box = urlencode($box);

    /* Create simple strings that will be creating the paginator. */
    $spc = '&nbsp;';     /* This will be used as a space. */
    $sep = '|';          /* This will be used as a seperator. */

    /* Get some paginator preference values. */
    $pg_sel = getPref($data_dir, $username, 'page_selector', SMPREF_ON);
    $pg_max = getPref($data_dir, $username, 'page_selector_max', PG_SEL_MAX);

    /* Make sure that our start message number is not too big. */
    $start_msg = min($start_msg, $num_msgs);

    /* Decide whether or not we will use the mailbox cache. */
    /* Not sure why $use_mailbox_cache is even passed in.   */
    if ($sort == 6) {
        $use = 0;
    } else {
        $use = 1;
    }

    /* Compute the starting message of the previous and next page group. */
    $next_grp = $start_msg + $show_num;
    $prev_grp = $start_msg - $show_num;

    /* Compute the basic previous and next strings. */
    if (($next_grp <= $num_msgs) && ($prev_grp >= 0)) {
        $prv_str = get_paginator_link($box, $prev_grp, $use, _("Previous"));
        $nxt_str = get_paginator_link($box, $next_grp, $use, _("Next"));
    } else if (($next_grp > $num_msgs) && ($prev_grp >= 0)) {
        $prv_str = get_paginator_link($box, $prev_grp, $use, _("Previous"));
        $nxt_str = "<font color=\"$color[9]\">"._("Next")."</font>\n";
    } else if (($next_grp <= $num_msgs) && ($prev_grp < 0)) {
        $prv_str = "<font color=\"$color[9]\">"._("Previous") . '</font>';
        $nxt_str = get_paginator_link($box, $next_grp, $use, _("Next"));
    }

    /* Page selector block. Following code computes page links. */
    if ($pg_sel && ($num_msgs > $show_num)) {
        /* Most importantly, what is the current page!!! */
        $cur_pg = intval($start_msg / $show_num) + 1;

        /* Compute total # of pages and # of paginator page links. */
        $tot_pgs = ceil($num_msgs / $show_num);  /* Total number of Pages */
        $vis_pgs = min($pg_max, $tot_pgs - 1);   /* Visible Pages    */

        /* Compute the size of the four quarters of the page links. */

        /* If we can, just show all the pages. */
        if (($tot_pgs - 1) <= $pg_max) {
            $q1_pgs = $cur_pg - 1;
            $q2_pgs = $q3_pgs = 0;
            $q4_pgs = $tot_pgs - $cur_pg;

        /* Otherwise, compute some magic to choose the four quarters. */
        } else {
            /*
             * Compute the magic base values. Added together,
             * these values will always equal to the $pag_pgs.
             * NOTE: These are DEFAULT values and do not take
             * the current page into account. That is below.
             */
            $q1_pgs = floor($vis_pgs/4);
            $q2_pgs = round($vis_pgs/4, 0);
            $q3_pgs = ceil($vis_pgs/4);
            $q4_pgs = round(($vis_pgs - $q2_pgs)/3, 0);

            /* Adjust if the first quarter contains the current page. */
            if (($cur_pg - $q1_pgs) < 1) {
                $extra_pgs = ($q1_pgs - ($cur_pg - 1)) + $q2_pgs;
                $q1_pgs = $cur_pg - 1;
                $q2_pgs = 0;
                $q3_pgs += ceil($extra_pgs / 2);
                $q4_pgs += floor($extra_pgs / 2);

            /* Adjust if the first and second quarters intersect. */
            } else if (($cur_pg - $q2_pgs - ceil($q2_pgs/3)) <= $q1_pgs) {
                $extra_pgs = $q2_pgs;
                $extra_pgs -= ceil(($cur_pg - $q1_pgs - 1) * 3/4);
                $q2_pgs = ceil(($cur_pg - $q1_pgs - 1) * 3/4);
                $q3_pgs += ceil($extra_pgs / 2);
                $q4_pgs += floor($extra_pgs / 2);

            /* Adjust if the fourth quarter contains the current page. */
            } else if (($cur_pg + $q4_pgs) >= $tot_pgs) {
                $extra_pgs = ($q4_pgs - ($tot_pgs - $cur_pg)) + $q3_pgs;
                $q3_pgs = 0;
                $q4_pgs = $tot_pgs - $cur_pg;
                $q1_pgs += floor($extra_pgs / 2);
                $q2_pgs += ceil($extra_pgs / 2);

            /* Adjust if the third and fourth quarter intersect. */
            } else if (($cur_pg + $q3_pgs) >= ($tot_pgs - $q4_pgs)) {
                $extra_pgs = $q3_pgs;
                $extra_pgs -= ceil(($tot_pgs - $cur_pg - $q4_pgs) * 3/4);
                $q3_pgs = ceil(($tot_pgs - $cur_pg - $q4_pgs) * 3/4);
                $q1_pgs += floor($extra_pgs / 2);
                $q2_pgs += ceil($extra_pgs / 2);
            }
        }

        /*
         * I am leaving this debug code here, commented out, because
         * it is a really nice way to see what the above code is doing.
         */
         // echo "qts =  $q1_pgs/$q2_pgs/$q3_pgs/$q4_pgs = "
         //     . ($q1_pgs + $q2_pgs + $q3_pgs + $q4_pgs) . '<br>';


        /* Print out the page links from the compute page quarters. */

        /* Start with the first quarter. */
        if (($q1_pgs == 0) && ($cur_pg > 1)) {
            $pg_str .= "...$spc";
        } else {
            for ($pg = 1; $pg <= $q1_pgs; ++$pg) {
                $start = (($pg-1) * $show_num) + 1;
                $pg_str .= get_paginator_link($box, $start, $use, $pg) . $spc;
            }
            if ($cur_pg - $q2_pgs - $q1_pgs > 1) {
                $pg_str .= "...$spc";
            }
        }

        /* Continue with the second quarter. */
        for ($pg = $cur_pg - $q2_pgs; $pg < $cur_pg; ++$pg) {
            $start = (($pg-1) * $show_num) + 1;
            $pg_str .= get_paginator_link($box, $start, $use, $pg) . $spc;
        }

        /* Now print the current page. */
        $pg_str .= $cur_pg . $spc;

        /* Next comes the third quarter. */
        for ($pg = $cur_pg + 1; $pg <= $cur_pg + $q3_pgs; ++$pg) {
            $start = (($pg-1) * $show_num) + 1;
            $pg_str .= get_paginator_link($box, $start, $use, $pg) . $spc;
        }

        /* And last, print the forth quarter page links. */
        if (($q4_pgs == 0) && ($cur_pg < $tot_pgs)) {
            $pg_str .= "...$spc";
        } else {
            if (($tot_pgs - $q4_pgs) > ($cur_pg + $q3_pgs)) {
                $pg_str .= "...$spc";
            }
            for ($pg = $tot_pgs - $q4_pgs + 1; $pg <= $tot_pgs; ++$pg) {
                $start = (($pg-1) * $show_num) + 1;
                $pg_str .= get_paginator_link($box, $start, $use, $pg) . $spc;
            }
        }
    } else if ($PG_SHOWNUM == 999999) {
        $pg_str = "<a href=\"right_main.php?PG_SHOWALL=0"
                . "&amp;use_mailbox_cache=$use&amp;startMessage=1&amp;mailbox=$box\" "
                . ">" ._("Paginate") . '</a>' . $spc;
    }

    /* If necessary, compute the 'show all' string. */
    if (($prv_str != '') || ($nxt_str != '')) {
        $all_str = "<a href=\"right_main.php?PG_SHOWALL=1"
                 . "&amp;use_mailbox_cache=$use&amp;startMessage=1&amp;mailbox=$box\" "
                 . ">" . _("Show All") . '</a>';
    }

    /* Last but not least, get the value for the toggle all link. */
    $tgl_str = get_selectall_link($start_msg, $sort);

    /* Put all the pieces of the paginator string together. */
    /**
     * Hairy code... But let's leave it like it is since I am not certain
     * a different approach would be any easier to read. ;)
     */
    $result = '';
    $result .= ($prv_str != '' ? $prv_str . $spc . $sep . $spc : '');
    $result .= ($nxt_str != '' ? $nxt_str . $spc . $sep . $spc : '');
    $result .= ($pg_str  != '' ? $pg_str : '');
    $result .= ($all_str != '' ? $sep . $spc . $all_str . $spc : '');
    $result .= ($result  != '' ? $sep . $spc . $tgl_str: $tgl_str);

    /* If the resulting string is blank, return a non-breaking space. */
    if ($result == '') {
        $result = '&nbsp;';
    }

    /* Return our final magical paginator string. */
    return ($result);
}

function processSubject($subject, $threadlevel = 0) {
    global $languages, $squirrelmail_language;
    /* Shouldn't ever happen -- caught too many times in the IMAP functions */
    if ($subject == '') {
        return _("(no subject)");
    }

    $trim_at = SUBJ_TRIM_AT;

    /* if this is threaded, subtract two chars per indentlevel */
    if($threadlevel > 0 && $threadlevel <= 10) {
        $trim_at -= (2*$threadlevel);
    }

    if (strlen($subject) <= $trim_at) {
        return $subject;
    }

    $ent_strlen = $orig_len = strlen($subject);
    $trim_val = $trim_at - 5;
    $ent_offset = 0;
    /*
     * see if this is entities-encoded string
     * If so, Iterate through the whole string, find out
     * the real number of characters, and if more
     * than 55, substr with an updated trim value.
     */
    $step = $ent_loc = 0;
    while ( $ent_loc < $trim_val && (($ent_loc = strpos($subject, '&', $ent_offset)) !== false) &&
            (($ent_loc_end = strpos($subject, ';', $ent_loc+3)) !== false) ) {
        $trim_val += ($ent_loc_end-$ent_loc);
        $ent_offset  = $ent_loc_end+1;
        ++$step;
    }

    if (($trim_val > 50) && (strlen($subject) > ($trim_val))&& (strpos($subject,';',$trim_val) < ($trim_val +6))) {
        $i = strpos($subject,';',$trim_val);
        if ($i) {
            $trim_val = strpos($subject,';',$trim_val);
        }
    }
    if ($ent_strlen <= $trim_at){
        return $subject;
    }

    if (isset($languages[$squirrelmail_language]['XTRA_CODE']) &&
        function_exists($languages[$squirrelmail_language]['XTRA_CODE'])) {
        return $languages[$squirrelmail_language]['XTRA_CODE']('strimwidth', $subject, $trim_val);
    }

    // only print '...' when we're actually dropping part of the subject
    if(strlen($subject) <= $trim_val) {
        return $subject;
    } else {
        return substr($subject, 0, $trim_val) . '...';
    }
}

function getMbxList($imapConnection) {
    global $lastTargetMailbox;
    echo  '         <small>&nbsp;<tt><select name="targetMailbox">';
    echo sqimap_mailbox_option_list($imapConnection, array(strtolower($lastTargetMailbox)) );
    echo '         </select></tt>&nbsp;';
}

function getButton($type, $name, $value) {
    return '<input type="'.$type.'" name="'.$name.'" value="'.$value . '">';
}

function getSmallStringCell($string, $align) {
    return html_tag('td',
                    '<small>' . $string . ':&nbsp; </small>',
                    $align,
                    '',
                    'nowrap' );
}

function getEndMessage($start_msg, $show_num, $num_msgs) {
    if ($start_msg + ($show_num - 1) < $num_msgs){
        $end_msg = $start_msg + ($show_num - 1);
    } else {
        $end_msg = $num_msgs;
    }

    if ($end_msg < $start_msg) {
        $start_msg = $start_msg - $show_num;
        if ($start_msg < 1) {
            $start_msg = 1;
        }
    }
    return (array($start_msg,$end_msg));
}

function handleAsSent($mailbox) {
    global $handleAsSent_result;

    /* First check if this is the sent or draft folder. */
    $handleAsSent_result = isSentMailbox($mailbox) || isDraftMailbox($mailbox);

    /* Then check the result of the handleAsSent hook. */
    do_hook('check_handleAsSent_result', $mailbox);

    /* And return the result. */
    return $handleAsSent_result;
}

