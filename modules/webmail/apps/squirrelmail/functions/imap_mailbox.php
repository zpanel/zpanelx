<?php

/**
 * imap_mailbox.php
 *
 * This implements all functions that manipulate mailboxes
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: imap_mailbox.php 14110 2011-05-03 06:41:53Z pdontthink $
 * @package squirrelmail
 * @subpackage imap
 */

/** UTF7 support */
require_once(SM_PATH . 'functions/imap_utf7_local.php');

global $boxesnew;

function sortSpecialMbx($a, $b) {
    if ($a->is_inbox) {
        $acmp = '0'. $a->mailboxname_full;
    } else if ($a->is_special) {
        $acmp = '1'. $a->mailboxname_full;
    } else {
        $acmp = '2' . $a->mailboxname_full;
    }
    if ($b->is_inbox) {
        $bcmp = '0'. $b->mailboxname_full;
    }else if ($b->is_special) {
        $bcmp = '1' . $b->mailboxname_full;
    } else {
        $bcmp = '2' . $b->mailboxname_full;
    }
    if ($acmp == $bcmp) return 0;
    return ($acmp > $bcmp) ? 1: -1;
}

function find_mailbox_name ($mailbox) {
    if (preg_match('/\*.+\"([^\r\n\"]*)\"[\s\r\n]*$/', $mailbox, $regs))
        return $regs[1];
    if (preg_match('/ *"([^\r\n"]*)"[ \r\n]*$/', $mailbox, $regs))
        return $regs[1];
    preg_match('/ *([^ \r\n"]*)[ \r\n]*$/',$mailbox,$regs);
    return $regs[1];
}

/**
 * @return bool whether this is a Noselect mailbox.
 */
function check_is_noselect ($lsub_line) {
    return preg_match("/^\* (LSUB|LIST) \([^\)]*\\\\Noselect[^\)]*\)/i", $lsub_line);
}

/**
 * If $haystack is a full mailbox name, and $needle is the mailbox
 * separator character, returns the second last part of the full
 * mailbox name (i.e. the mailbox's parent mailbox)
 */
function readMailboxParent($haystack, $needle) {
    if ($needle == '') {
        $ret = '';
    } else {
        $parts = explode($needle, $haystack);
        $elem = array_pop($parts);
        while ($elem == '' && count($parts)) {
            $elem = array_pop($parts);
        }
        $ret = join($needle, $parts);
    }
    return( $ret );
}

/**
 * Check if $subbox is below the specified $parentbox
 */
function isBoxBelow( $subbox, $parentbox ) {
    global $delimiter;
    /*
     * Eliminate the obvious mismatch, where the
     * subfolder path is shorter than that of the potential parent
     */
    if ( strlen($subbox) < strlen($parentbox) ) {
      return false;
    }
    /* check for delimiter */
        if (substr($parentbox,-1) != $delimiter) {
            $parentbox.=$delimiter;
        }
        if (substr($subbox,0,strlen($parentbox)) == $parentbox) {
            return true;
        } else {
            return false;
        }
}

/**
 * Defines special mailboxes: given a mailbox name, it checks if this is a
 * "special" one: INBOX, Trash, Sent or Draft.
 *
 * Since 1.2.5 function includes special_mailbox hook.
 *
 * Since 1.4.3 hook supports more than one plugin.
 *
//FIXME: make $subfolders_of_inbox_are_special a configuration setting in conf.pl and config.php
 * Since 1.4.22/1.5.2, the administrator can add
 * $subfolders_of_inbox_are_special = TRUE;
 * to config/config_local.php and all subfolders
 * of the INBOX will be treated as special.
 *
 * @param string $box mailbox name
 * @param boolean $include_subs (since 1.5.2 and 1.4.9) if true, subfolders of 
 *  system folders are special. if false, subfolders are not special mailboxes 
 *  unless they are tagged as special in 'special_mailbox' hook.
 * @return boolean
 * @since 1.2.3
 */
function isSpecialMailbox($box,$include_subs=true) {
    global $subfolders_of_inbox_are_special;
    $ret = ( ($subfolders_of_inbox_are_special && isInboxMailbox($box,$include_subs)) ||
             (!$subfolders_of_inbox_are_special && strtolower($box) == 'inbox') ||
             isTrashMailbox($box,$include_subs) || 
             isSentMailbox($box,$include_subs) || 
             isDraftMailbox($box,$include_subs) );

    if ( !$ret ) {
        $ret = boolean_hook_function('special_mailbox',$box,1);
    }
    return $ret;
}

/**
 * Detects if mailbox is the Inbox folder or subfolder of the Inbox
 *
 * @param string $box The mailbox name to test
 * @param boolean $include_subs If true, subfolders of system folders
 *                              are special.  If false, subfolders are
 *                              not special mailboxes.
 *
 * @return boolean Whether this is the Inbox or a child thereof.
 *
 * @since 1.4.22
 */
function isInboxMailbox($box, $include_subs=TRUE) {
   return ((strtolower($box) == 'inbox')
        || ($include_subs && isBoxBelow(strtolower($box), 'inbox')));
}


/**
 * Detects if mailbox is a Trash folder or subfolder of Trash
 * @param string $box mailbox name
 * @param boolean $include_subs (since 1.5.2 and 1.4.9) if true, subfolders of 
 *  system folders are special. if false, subfolders are not special mailboxes.
 * @return bool whether this is a Trash folder
 * @since 1.4.0
 */
function isTrashMailbox ($box,$include_subs=true) {
    global $trash_folder, $move_to_trash;
    return $move_to_trash && $trash_folder &&
           ( $box == $trash_folder || 
             ($include_subs && isBoxBelow($box, $trash_folder)) );
}

/**
 * Detects if mailbox is a Sent folder or subfolder of Sent
 * @param string $box mailbox name
 * @param boolean $include_subs (since 1.5.2 and 1.4.9) if true, subfolders of 
 *  system folders are special. if false, subfolders are not special mailboxes.
 * @return bool whether this is a Sent folder
 * @since 1.4.0
 */
function isSentMailbox($box,$include_subs=true) {
   global $sent_folder, $move_to_sent;
   return $move_to_sent && $sent_folder &&
          ( $box == $sent_folder || 
            ($include_subs && isBoxBelow($box, $sent_folder)) );
}

/**
 * Detects if mailbox is a Drafts folder or subfolder of Drafts
 * @param string $box mailbox name
 * @param boolean $include_subs (since 1.5.2 and 1.4.9) if true, subfolders of 
 *  system folders are special. if false, subfolders are not special mailboxes.
 * @return bool whether this is a Draft folder
 * @since 1.4.0
 */
function isDraftMailbox($box,$include_subs=true) {
   global $draft_folder, $save_as_draft;
   return $save_as_draft &&
          ( $box == $draft_folder || 
            ($include_subs && isBoxBelow($box, $draft_folder)) );
}

/**
 * Expunges a mailbox, ie. delete all contents.
 */
function sqimap_mailbox_expunge ($imap_stream, $mailbox, $handle_errors = true, $id='') {
    global $uid_support;
    if ($id) {
        if (is_array($id)) {
            $id = sqimap_message_list_squisher($id);
        }
        $id = ' '.$id;
        $uid = $uid_support;
    } else {
        $uid = false;
    }
    $read = sqimap_run_command($imap_stream, 'EXPUNGE'.$id, $handle_errors,
                               $response, $message, $uid);
    $cnt = 0;

    if (is_array($read)) {
        foreach ($read as $r) {
            if (preg_match('/^\*\s[0-9]+\sEXPUNGE/AUi',$r,$regs)) {
                $cnt++;
            }
        }
    }
    return $cnt;
}

/**
 * Checks whether or not the specified mailbox exists
 */
function sqimap_mailbox_exists ($imap_stream, $mailbox) {
    if (!isset($mailbox) || empty($mailbox)) {
        return false;
    }
    $mbx = sqimap_run_command($imap_stream, "LIST \"\" \"$mailbox\"",
                              true, $response, $message);
    return isset($mbx[0]);
}

/**
 * Selects a mailbox
 */
function sqimap_mailbox_select ($imap_stream, $mailbox) {
    global $auto_expunge;

    if (empty($mailbox)) {
        return;
    }

    /**
     * Default UW IMAP server configuration allows to access other files
     * on server. $imap_server_type is not checked because interface can
     * be used with 'other' or any other server type setting. $mailbox
     * variable can be modified in any script that uses variable from GET 
     * or POST. This code blocks all standard SquirrelMail IMAP API requests 
     * that use mailbox with full path (/etc/passwd) or with ../ characters 
     * in path (../../etc/passwd)
     */
    if (strstr($mailbox, '../') || substr($mailbox, 0, 1) == '/') {
        global $color;
        include_once(SM_PATH . 'functions/display_messages.php');
        error_box(sprintf(_("Invalid mailbox name: %s"),htmlspecialchars($mailbox)),$color);
        sqimap_logout($imap_stream);
        die('</body></html>');
    }

    // cleanup $mailbox in order to prevent IMAP injection attacks
    $mailbox = str_replace(array("\r","\n"), array("",""),$mailbox);

    $read = sqimap_run_command($imap_stream, "SELECT \"$mailbox\"",
                               true, $response, $message);
    $result = array();
    for ($i = 0, $cnt = count($read); $i < $cnt; $i++) {
        if (preg_match('/^\*\s+OK\s\[(\w+)\s(\w+)\]/',$read[$i], $regs)) {
            $result[strtoupper($regs[1])] = $regs[2];
        } else if (preg_match('/^\*\s([0-9]+)\s(\w+)/',$read[$i], $regs)) {
            $result[strtoupper($regs[2])] = $regs[1];
        } else {
            if (preg_match("/PERMANENTFLAGS(.*)/i",$read[$i], $regs)) {
                $regs[1]=trim(preg_replace (  array ("/\(/","/\)/","/\]/") ,'', $regs[1])) ;
                $result['PERMANENTFLAGS'] = $regs[1];
            } else if (preg_match("/FLAGS(.*)/i",$read[$i], $regs)) {
                $regs[1]=trim(preg_replace (  array ("/\(/","/\)/") ,'', $regs[1])) ;
                $result['FLAGS'] = $regs[1];
            }
        }
    }
    if (preg_match('/^\[(.+)\]/',$message, $regs)) {
        $result['RIGHTS']=$regs[1];
    }

    if ($auto_expunge) {
        $tmp = sqimap_run_command($imap_stream, 'EXPUNGE', false, $a, $b);
    }
    return $result;
}

/**
 * Creates a folder.
 */
function sqimap_mailbox_create ($imap_stream, $mailbox, $type) {
    global $delimiter;
    if (strtolower($type) == 'noselect') {
        $create_mailbox = $mailbox . $delimiter;
    } else {
        $create_mailbox = $mailbox;
    }

    $read_ary = sqimap_run_command($imap_stream, "CREATE \"$create_mailbox\"",
                                   true, $response, $message);
    sqimap_subscribe ($imap_stream, $mailbox);
}

/**
 * Subscribes to an existing folder.
 */
function sqimap_subscribe ($imap_stream, $mailbox) {
    $read_ary = sqimap_run_command($imap_stream, "SUBSCRIBE \"$mailbox\"",
                                   true, $response, $message);
}

/**
 * Unsubscribes from an existing folder
 */
function sqimap_unsubscribe ($imap_stream, $mailbox) {
    $read_ary = sqimap_run_command($imap_stream, "UNSUBSCRIBE \"$mailbox\"",
                                   false, $response, $message);
}

/**
 * Deletes the given folder
 */
function sqimap_mailbox_delete ($imap_stream, $mailbox) {
    global $data_dir, $username;
    sqimap_unsubscribe ($imap_stream, $mailbox);
    if (sqimap_mailbox_exists($imap_stream, $mailbox)) {
        $read_ary = sqimap_run_command($imap_stream, "DELETE \"$mailbox\"",
                                       true, $response, $message);
        if ($response !== 'OK') {
            // subscribe again
            sqimap_subscribe ($imap_stream, $mailbox);
        } else {
            do_hook_function('rename_or_delete_folder', $args = array($mailbox, 'delete', ''));
            removePref($data_dir, $username, "thread_$mailbox");
            removePref($data_dir, $username, "collapse_folder_$mailbox");
        }
    }
}

/**
 * Determines if the user is subscribed to the folder or not
 */
function sqimap_mailbox_is_subscribed($imap_stream, $folder) {
    $boxesall = sqimap_mailbox_list ($imap_stream);
    foreach ($boxesall as $ref) {
        if ($ref['unformatted'] == $folder) {
            return true;
        }
    }
    return false;
}

/**
 * Renames a mailbox.
 */
function sqimap_mailbox_rename( $imap_stream, $old_name, $new_name ) {
    if ( $old_name != $new_name ) {
        global $delimiter, $imap_server_type, $data_dir, $username;
        if ( substr( $old_name, -1 ) == $delimiter  ) {
            $old_name = substr( $old_name, 0, strlen( $old_name ) - 1 );
            $new_name = substr( $new_name, 0, strlen( $new_name ) - 1 );
            $postfix = $delimiter;
        } else {
            $postfix = '';
        }

        $boxesall = sqimap_mailbox_list_all($imap_stream);
        $cmd = 'RENAME "' . $old_name . '" "' . $new_name . '"';
        $data = sqimap_run_command($imap_stream, $cmd, true, $response, $message);
        sqimap_unsubscribe($imap_stream, $old_name.$postfix);
        $oldpref_thread = getPref($data_dir, $username, 'thread_'.$old_name.$postfix);
        $oldpref_collapse = getPref($data_dir, $username, 'collapse_folder_'.$old_name.$postfix);
        removePref($data_dir, $username, 'thread_'.$old_name.$postfix);
        removePref($data_dir, $username, 'collapse_folder_'.$old_name.$postfix);
        sqimap_subscribe($imap_stream, $new_name.$postfix);
        setPref($data_dir, $username, 'thread_'.$new_name.$postfix, $oldpref_thread);
        setPref($data_dir, $username, 'collapse_folder_'.$new_name.$postfix, $oldpref_collapse);
        do_hook_function('rename_or_delete_folder',$args = array($old_name, 'rename', $new_name));
        $l = strlen( $old_name ) + 1;
        $p = 'unformatted';

        foreach ($boxesall as $box) {
            if (substr($box[$p], 0, $l) == $old_name . $delimiter) {
                $new_sub = $new_name . $delimiter . substr($box[$p], $l);
                /* With Cyrus IMAPd >= 2.0 rename is recursive, so don't check for errors here */
                if ($imap_server_type == 'cyrus') {
                    $cmd = 'RENAME "' . $box[$p] . '" "' . $new_sub . '"';
                    $data = sqimap_run_command($imap_stream, $cmd, false,
                                               $response, $message);
                }
                $was_subscribed = sqimap_mailbox_is_subscribed($imap_stream, $box[$p]);
                if ( $was_subscribed ) {
                    sqimap_unsubscribe($imap_stream, $box[$p]);
                }
                $oldpref_thread = getPref($data_dir, $username, 'thread_'.$box[$p]);
                $oldpref_collapse = getPref($data_dir, $username, 'collapse_folder_'.$box[$p]);
                removePref($data_dir, $username, 'thread_'.$box[$p]);
                removePref($data_dir, $username, 'collapse_folder_'.$box[$p]);
                if ( $was_subscribed ) {
                    sqimap_subscribe($imap_stream, $new_sub);
                }
                setPref($data_dir, $username, 'thread_'.$new_sub, $oldpref_thread);
                setPref($data_dir, $username, 'collapse_folder_'.$new_sub, $oldpref_collapse);
                do_hook_function('rename_or_delete_folder',
                                 $args = array($box[$p], 'rename', $new_sub));
            }
        }
    }
}

/**
 * Formats a mailbox into parts for the $boxesall array
 *
 * The parts are:
 *
 *     raw            - Raw LIST/LSUB response from the IMAP server
 *     formatted      - nicely formatted folder name
 *     unformatted    - unformatted, but with delimiter at end removed
 *     unformatted-dm - folder name as it appears in raw response
 *     unformatted-disp - unformatted without $folder_prefix
 */
function sqimap_mailbox_parse ($line, $line_lsub) {
    global $folder_prefix, $delimiter;

    /* Process each folder line */
    for ($g = 0, $cnt = count($line); $g < $cnt; ++$g) {
        /* Store the raw IMAP reply */
        if (isset($line[$g])) {
            $boxesall[$g]['raw'] = $line[$g];
        } else {
            $boxesall[$g]['raw'] = '';
        }

        /* Count number of delimiters ($delimiter) in folder name */
        $mailbox  = $line_lsub[$g];
        $dm_count = substr_count($mailbox, $delimiter);
        if (substr($mailbox, -1) == $delimiter) {
            /* If name ends in delimiter, decrement count by one */
            $dm_count--;
        }

        /* Format folder name, but only if it's a INBOX.* or has a parent. */
        $boxesallbyname[$mailbox] = $g;
        $parentfolder = readMailboxParent($mailbox, $delimiter);
        if ( (strtolower(substr($mailbox, 0, 5)) == "inbox") ||
             (substr($mailbox, 0, strlen($folder_prefix)) == $folder_prefix) ||
             (isset($boxesallbyname[$parentfolder]) &&
              (strlen($parentfolder) > 0) ) ) {
            $indent = $dm_count - (substr_count($folder_prefix, $delimiter));
            if ($indent > 0) {
                $boxesall[$g]['formatted'] = str_repeat('&nbsp;&nbsp;', $indent);
            } else {
                $boxesall[$g]['formatted'] = '';
            }
            $boxesall[$g]['formatted'] .= imap_utf7_decode_local(readShortMailboxName($mailbox, $delimiter));
        } else {
            $boxesall[$g]['formatted']  = imap_utf7_decode_local($mailbox);
        }

        $boxesall[$g]['unformatted-dm'] = $mailbox;
        if (substr($mailbox, -1) == $delimiter) {
            $mailbox = substr($mailbox, 0, strlen($mailbox) - 1);
        }
        $boxesall[$g]['unformatted'] = $mailbox;
        if (substr($mailbox,0,strlen($folder_prefix))==$folder_prefix) {
            $mailbox = substr($mailbox, strlen($folder_prefix));
        }
        $boxesall[$g]['unformatted-disp'] = $mailbox;
        $boxesall[$g]['id'] = $g;

        $boxesall[$g]['flags'] = array();
        if (isset($line[$g])) {
            if ( preg_match('/\(([^)]*)\)/',$line[$g],$regs) ) {
                $flags = trim(strtolower(str_replace('\\', '',$regs[1])));
                if ($flags) {
                    $boxesall[$g]['flags'] = explode(' ', $flags);
                }
            }
        }
    }
    return $boxesall;
}

/**
 * Sorting function used to sort mailbox names.
 *     + Original patch from dave_michmerhuizen@yahoo.com
 *     + Allows case insensitivity when sorting folders
 *     + Takes care of the delimiter being sorted to the end, causing
 *       subfolders to be listed in below folders that are prefixed
 *       with their parent folders name.
 *
 *       For example: INBOX.foo, INBOX.foobar, and INBOX.foo.bar
 *       Without special sort function: foobar between foo and foo.bar
 *       With special sort function: foobar AFTER foo and foo.bar :)
 */
function user_strcasecmp($a, $b) {
    return  strnatcasecmp($a, $b);
}

/**
 * Returns list of options (to be echoed into select statement
 * based on available mailboxes and separators
 * Caller should surround options with <select ...> </select> and
 * any formatting.
 *   $imap_stream - $imapConnection to query for mailboxes
 *   $show_selected - array containing list of mailboxes to pre-select (0 if none)
 *   $folder_skip - array of folders to keep out of option list (compared in lower)
 *   $boxes - list of already fetched boxes (for places like folder panel, where
 *            you know these options will be shown 3 times in a row.. (most often unset).
 *   $flag - flag to check for in mailbox flags, used to filter out mailboxes.
 *           'noselect' by default to remove unselectable mailboxes.
 *           'noinferiors' used to filter out folders that can not contain subfolders.
 *           NULL to avoid flag check entirely.
 *   $use_long_format - override folder display preference and always show full folder name.
 */
function sqimap_mailbox_option_list($imap_stream, $show_selected = 0, $folder_skip = 0, $boxes = 0,
                                    $flag = 'noselect', $use_long_format = false ) {
    global $username, $data_dir;
    $mbox_options = '';
    if ( $use_long_format ) {
        $shorten_box_names = 0;
    } else {
        $shorten_box_names = getPref($data_dir, $username, 'mailbox_select_style', 1);
    }

    if ($boxes == 0) {
        $boxes = sqimap_mailbox_list($imap_stream);
    }

    foreach ($boxes as $boxes_part) {
        if ($flag == NULL || !in_array($flag, $boxes_part['flags'])) {
            $box = $boxes_part['unformatted'];

            if ($folder_skip != 0 && in_array($box, $folder_skip) ) {
                continue;
            }
            $lowerbox = strtolower($box);
            // mailboxes are casesensitive => inbox.sent != inbox.Sent
            // nevermind, to many dependencies this should be fixed!

            if (strtolower($box) == 'inbox') { // inbox is special and not casesensitive
                $box2 = _("INBOX");
            } else {
                switch ($shorten_box_names)
                {
                  case 2:   /* delimited, style = 2 */
                    $box2 = str_replace('&nbsp;&nbsp;', '.&nbsp;', $boxes_part['formatted']);
                    break;
                  case 1:   /* indent, style = 1 */
                    $box2 = $boxes_part['formatted'];
                    break;
                  default:  /* default, long names, style = 0 */
                    $box2 = str_replace(' ', '&nbsp;', htmlspecialchars(imap_utf7_decode_local($boxes_part['unformatted-disp'])));
                    break;
                }
            }
            $box2 = str_replace(array('<','>'), array('&lt;','&gt;') , $box2);

            if ($show_selected != 0 && in_array($lowerbox, $show_selected) ) {
                $mbox_options .= '<option value="' . htmlspecialchars($box) .'" selected="selected">'.$box2.'</option>' . "\n";
            } else {
                $mbox_options .= '<option value="' . htmlspecialchars($box) .'">'.$box2.'</option>' . "\n";
            }
        }
    }
    return $mbox_options;
}

/**
 * Mailboxes with some chars (like -) can mess up the order, this fixes it
 */
function mailtree_sort(&$lsub) {
    if(!is_array($lsub)) return;
    
    global $delimiter;
    
    foreach($lsub as $index => $mailbox)
        $lsub[$index] = str_replace($delimiter,' -#- ',$lsub[$index]);

    usort($lsub, 'user_strcasecmp');

    foreach($lsub as $index => $mailbox)
        $lsub[$index] = str_replace(' -#- ',$delimiter,$lsub[$index]);
}

/**
 * Returns sorted mailbox lists in several different ways.
 * See comment on sqimap_mailbox_parse() for info about the returned array.
 */


function sqimap_mailbox_list($imap_stream, $force=false) {
    global $default_folder_prefix, $default_sub_of_inbox;

    if (!sqgetGlobalVar('boxesnew',$boxesnew,SQ_SESSION) || $force) {
        global $data_dir, $username, $list_special_folders_first,
               $folder_prefix, $trash_folder, $sent_folder, $draft_folder,
               $move_to_trash, $move_to_sent, $save_as_draft,
               $delimiter, $noselect_fix_enable;
        $inbox_in_list = false;
        $inbox_subscribed = false;

        require_once(SM_PATH . 'include/load_prefs.php');

        if ($noselect_fix_enable) {
            $lsub_args = "LSUB \"$folder_prefix\" \"*%\"";
        } else {
            $lsub_args = "LSUB \"$folder_prefix\" \"*\"";
        }
        /* LSUB array */
        $lsub_ary = sqimap_run_command ($imap_stream, $lsub_args,
                                        true, $response, $message);

        $sorted_lsub_ary = array();
        for ($i = 0, $cnt = count($lsub_ary);$i < $cnt; $i++) {
            /*
             * Workaround for mailboxes returned as literal
             * Doesn't work if the mailbox name is multiple lines
             * (larger then fgets buffer)
             */
            if (isset($lsub_ary[$i + 1]) && substr($lsub_ary[$i],-3) == "}\r\n") {
                if (preg_match('/^(\* [A-Z]+.*)\{[0-9]+\}([ \n\r\t]*)$/',
                     $lsub_ary[$i], $regs)) {
                        $i++;
                        $lsub_ary[$i] = $regs[1] . '"' . addslashes(trim($lsub_ary[$i])) . '"' . $regs[2];
                }
            }
            $temp_mailbox_name = find_mailbox_name($lsub_ary[$i]);
            $sorted_lsub_ary[] = $temp_mailbox_name;
            if (!$inbox_subscribed && strtoupper($temp_mailbox_name) == 'INBOX') {
                $inbox_subscribed = true;
            }
        }
        /* remove duplicates */
        $sorted_lsub_ary = array_unique($sorted_lsub_ary);
       
        /* natural sort mailboxes */
        if (isset($sorted_lsub_ary)) {
            mailtree_sort($sorted_lsub_ary);
        }
        /*
         * The LSUB response doesn't provide us information about \Noselect
         * mail boxes. The LIST response does, that's why we need to do a LIST
         * call to retrieve the flags for the mailbox
           * Note: according RFC2060 an imap server may provide \NoSelect flags in the LSUB response.
           * in other words, we cannot rely on it.
         */
        $sorted_list_ary = array();
        for ($i=0; $i < count($sorted_lsub_ary); $i++) {
            if (substr($sorted_lsub_ary[$i], -1) == $delimiter) {
                $mbx = substr($sorted_lsub_ary[$i], 0, strlen($sorted_lsub_ary[$i])-1);
            }
            else {
                $mbx = $sorted_lsub_ary[$i];
            }

            $read = sqimap_run_command ($imap_stream, "LIST \"\" \"$mbx\"",
                                        true, $response, $message);

            /* Another workaround for literals */

            if (isset($read[1]) && substr($read[1],-3) == "}\r\n") {
                if (preg_match('/^(\* [A-Z]+.*)\{[0-9]+\}([ \n\r\t]*)$/',
                     $read[0], $regs)) {
                    $read[0] = $regs[1] . '"' . addslashes(trim($read[1])) . '"' . $regs[2];
                }
            }

            if (isset($read[0])) {
                $sorted_list_ary[$i] = $read[0];
            } else {
                $sorted_list_ary[$i] = '';
            }
        }

        /*
         * Just in case they're not subscribed to their inbox,
         * we'll get it for them anyway
         */
        if (!$inbox_subscribed) {
            $inbox_ary = sqimap_run_command ($imap_stream, "LIST \"\" \"INBOX\"",
                                             true, $response, $message);
            /* Another workaround for literals */
            if (isset($inbox_ary[1]) && substr($inbox_ary[0],-3) == "}\r\n") {
                if (preg_match('/^(\* [A-Z]+.*)\{[0-9]+\}([ \n\r\t]*)$/',
                     $inbox_ary[0], $regs)) {
                    $inbox_ary[0] = $regs[1] . '"' . addslashes(trim($inbox_ary[1])) .
                                '"' . $regs[2];
                }
            }
            $sorted_list_ary[] = $inbox_ary[0];
            $sorted_lsub_ary[] = find_mailbox_name($inbox_ary[0]);
        }

        $boxesall = sqimap_mailbox_parse ($sorted_list_ary, $sorted_lsub_ary);

        /* Now, lets sort for special folders */
        $boxesnew = $used = array();

        /* Find INBOX */
        $cnt = count($boxesall);
        $used = array_pad($used,$cnt,false);
        for($k = 0; $k < $cnt; ++$k) {
            if (strtolower($boxesall[$k]['unformatted']) == 'inbox') {
                $boxesnew[] = $boxesall[$k];
                $used[$k] = true;
                break;
            }
        }

        /* 
         * For systems where folders might be either under the INBOX or
         * at the top-level (Dovecot, hMailServer), INBOX subfolders have
         * to be added before special folders
         */
        if (!$default_sub_of_inbox) {
            for($k = 0; $k < $cnt; ++$k) {
                if (!$used[$k] && isBoxBelow(strtolower($boxesall[$k]['unformatted']), 'inbox') &&
                    strtolower($boxesall[$k]['unformatted']) != 'inbox') {
                    $boxesnew[] = $boxesall[$k];
                    $used[$k] = true;
                }
            }
        }


        /* List special folders and their subfolders, if requested. */
        if ($list_special_folders_first) {
            for($k = 0; $k < $cnt; ++$k) {
                if (!$used[$k] && isSpecialMailbox($boxesall[$k]['unformatted'])) {
                    $boxesnew[] = $boxesall[$k];
                    $used[$k]   = true;
                }
            }
        }


        /* Find INBOX's children for systems where folders are ONLY under INBOX */
        if ($default_sub_of_inbox) {
            for($k = 0; $k < $cnt; ++$k) {
                if (!$used[$k] && isBoxBelow(strtolower($boxesall[$k]['unformatted']), 'inbox') &&
                    strtolower($boxesall[$k]['unformatted']) != 'inbox') {
                    $boxesnew[] = $boxesall[$k];
                    $used[$k] = true;
                }
            }
        }


        /* Rest of the folders */
        for($k = 0; $k < $cnt; $k++) {
            if (!$used[$k]) {
                $boxesnew[] = $boxesall[$k];
            }
        }
        sqsession_register($boxesnew,'boxesnew');
    }
    return $boxesnew;
}

/**
 *  Returns a list of all folders, subscribed or not
 */
function sqimap_mailbox_list_all($imap_stream) {
    global $list_special_folders_first, $folder_prefix, $delimiter;

    $ssid = sqimap_session_id();
    $lsid = strlen( $ssid );
    fputs ($imap_stream, $ssid . " LIST \"$folder_prefix\" *\r\n");
    $read_ary = sqimap_read_data ($imap_stream, $ssid, true, $response, $message);
    $g = 0;
    $phase = 'inbox';
    $fld_pre_length = strlen($folder_prefix);

    for ($i = 0, $cnt = count($read_ary); $i < $cnt; $i++) {
        /* Another workaround for EIMS */
        if (isset($read_ary[$i + 1]) &&
            preg_match('/^(\* [A-Z]+.*)\{[0-9]+\}([ \n\r\t]*)$/',
                 $read_ary[$i], $regs)) {
            $i ++;
            $read_ary[$i] = $regs[1] . '"' . addslashes(trim($read_ary[$i])) . '"' . $regs[2];
        }
        if (substr($read_ary[$i], 0, $lsid) != $ssid ) {
            /* Store the raw IMAP reply */
            $boxes[$g]['raw'] = $read_ary[$i];

            /* Count number of delimiters ($delimiter) in folder name */
            $mailbox = find_mailbox_name($read_ary[$i]);
            $dm_count =  substr_count($mailbox, $delimiter);
            if (substr($mailbox, -1) == $delimiter) {
                /* If name ends in delimiter - decrement count by one */
                $dm_count--;
            }

            /* Format folder name, but only if it's a INBOX.* or has a parent. */
            $boxesallbyname[$mailbox] = $g;
            $parentfolder = readMailboxParent($mailbox, $delimiter);
			/* @FIXME shouldn't use preg_match for simple string matching */
            if((preg_match('|^inbox'.quotemeta($delimiter).'|i', $mailbox)) ||
               (preg_match('|^'.$folder_prefix.'|', $mailbox)) ||
               ( isset($boxesallbyname[$parentfolder]) && (strlen($parentfolder) > 0) ) ) {
                if ($dm_count) {
                    $boxes[$g]['formatted']  = str_repeat('&nbsp;&nbsp;', $dm_count);
                } else {
                    $boxes[$g]['formatted'] = '';
                }
                $boxes[$g]['formatted'] .= imap_utf7_decode_local(readShortMailboxName($mailbox, $delimiter));
            } else {
                $boxes[$g]['formatted']  = imap_utf7_decode_local($mailbox);
            }

            $boxes[$g]['unformatted-dm'] = $mailbox;
            if (substr($mailbox, -1) == $delimiter) {
                $mailbox = substr($mailbox, 0, strlen($mailbox) - 1);
            }
            $boxes[$g]['unformatted'] = $mailbox;
            $boxes[$g]['unformatted-disp'] = substr($mailbox,$fld_pre_length);

            $boxes[$g]['id'] = $g;

            /* Now lets get the flags for this mailbox */
            $read_mlbx = $read_ary[$i];

//            $read_mlbx = sqimap_run_command ($imap_stream, "LIST \"\" \"$mailbox\"",
//                                             true, $response, $message);

            /* Another workaround for EIMS */
//            if (isset($read_mlbx[1]) &&
//                preg_match('/^(\* [A-Z]+.*)\{[0-9]+\}([ \n\r\t]*)$/', $read_mlbx[0], $regs)) {
//                $read_mlbx[0] = $regs[1] . '"' . addslashes(trim($read_mlbx[1])) . '"' . $regs[2];
//            }
//            echo  $read_mlbx[0] .' raw 2 <br>';

            $flags = substr($read_mlbx, strpos($read_mlbx, '(')+1);
            $flags = substr($flags, 0, strpos($flags, ')'));
            $flags = str_replace('\\', '', $flags);
            $flags = trim(strtolower($flags));
            if ($flags) {
                $boxes[$g]['flags'] = explode(' ', $flags);
            } else {
                $boxes[$g]['flags'] = array();
            }
        }
        $g++;
    }
    if(is_array($boxes)) {
        sort ($boxes);
    }

    return $boxes;
}

