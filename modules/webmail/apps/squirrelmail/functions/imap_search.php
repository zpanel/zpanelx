<?php

/**
 * imap_search.php
 *
 * IMAP search routines
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: imap_search.php 14104 2011-04-26 19:05:34Z pdontthink $
 * @package squirrelmail
 * @subpackage imap
 * @deprecated This search interface has been largely replaced by asearch
 */

/**
 * Load up a bunch of SM functions */
require_once(SM_PATH . 'functions/imap.php');
require_once(SM_PATH . 'functions/date.php');
require_once(SM_PATH . 'functions/mailbox_display.php');
require_once(SM_PATH . 'functions/mime.php');

function sqimap_search($imapConnection, $search_where, $search_what, $mailbox,
                       $color, $search_position = '', $search_all, $count_all) {

    global $message_highlight_list, $squirrelmail_language, $languages,
           $index_order, $pos, $allow_charset_search, $uid_support,
	   $imap_server_type;

    $pos = $search_position;

    $urlMailbox = urlencode($mailbox);

    /* construct the search query, taking multiple search terms into account */
    $multi_search = array();
    $search_what  = trim($search_what);
    $search_what  = preg_replace('/[ ]{2,}/', ' ', $search_what);
    $multi_search = explode(' ', $search_what);
    $search_string = '';

    if (strtoupper($languages[$squirrelmail_language]['CHARSET']) == 'ISO-2022-JP') {
        foreach($multi_search as $idx=>$search_part) {
            $multi_search[$idx] = mb_convert_encoding($search_part, 'JIS', 'auto');
        }
    }

    foreach ($multi_search as $string) {
       $search_string .= $search_where
                      . ' "'
                      . str_replace(array('\\', '"'), array('\\\\', '\\"'), $string)
                      . '" ';
    }

    $search_string = trim($search_string);

    /* now use $search_string in the imap search */
    if ($allow_charset_search && isset($languages[$squirrelmail_language]['CHARSET']) &&
        $languages[$squirrelmail_language]['CHARSET']) {
        $ss = "SEARCH CHARSET "
            . strtoupper($languages[$squirrelmail_language]['CHARSET'])
            . " ALL $search_string";
    } else {
        $ss = "SEARCH ALL $search_string";
    }

    /* read data back from IMAP */
    $readin = sqimap_run_command($imapConnection, $ss, false, $result, $message, $uid_support);

    /* try US-ASCII charset if search fails */
    if (isset($languages[$squirrelmail_language]['CHARSET'])
        && strtolower($result) == 'no') {
        $ss = "SEARCH CHARSET \"US-ASCII\" ALL $search_string";
        if (empty($search_lit)) {
            $readin = sqimap_run_command($imapConnection, $ss, false, $result, $message, $uid_support);
        } else {
            $search_lit['command'] = $ss;
            $readin = sqimap_run_literal_command($imapConnection, $search_lit, false, $result, $message, $uid_support);
        }
    }

    unset($messagelist);

    /* Keep going till we find the SEARCH response */
    foreach ($readin as $readin_part) {
        /* Check to see if a SEARCH response was received */
        if (substr($readin_part, 0, 9) == '* SEARCH ') {
            $messagelist = preg_split("/ /", substr($readin_part, 9));
        } else if (isset($errors)) {
            $errors = $errors.$readin_part;
        } else {
            $errors = $readin_part;
        }
    }

    /* If nothing is found * SEARCH should be the first error else echo errors */
    if (isset($errors)) {
        if (strstr($errors,'* SEARCH')) {
            return array();
        }
        echo '<!-- '.htmlspecialchars($errors) .' -->';
    }


    global $sent_folder;

    $cnt = count($messagelist);
    for ($q = 0; $q < $cnt; $q++) {
        $id[$q] = trim($messagelist[$q]);
    }
    $issent = ($mailbox == $sent_folder);

    $msgs = fillMessageArray($imapConnection,$id,$cnt);

    return $msgs;
}



