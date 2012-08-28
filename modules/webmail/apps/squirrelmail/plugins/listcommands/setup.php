<?php

/**
 * setup.php
 *
 * Copyright (c) 1999-2011 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Implementation of RFC 2369 for SquirrelMail.
 * When viewing a message from a mailinglist complying with this RFC,
 * this plugin displays a menu which gives the user a choice of mailinglist
 * commands such as (un)subscribe, help and list archives.
 *
 * $Id: setup.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package plugins
 * @subpackage listcommands
 */

function squirrelmail_plugin_init_listcommands () {
    global $squirrelmail_plugin_hooks;

    $squirrelmail_plugin_hooks['read_body_header']['listcommands'] = 'plugin_listcommands_menu';
}

function plugin_listcommands_menu() {
    global $passed_id, $passed_ent_id, $color, $mailbox,
           $message, $compose_new_win, $startMessage;

    /**
     * Array of commands we can deal with from the header. The Reply option
     * is added later because we generate it using the Post information.
     */
    $fieldsdescr = array('post'        => _("Post to List"),
                         'reply'       => _("Reply to List"),
                         'subscribe'   => _("Subscribe"),
                         'unsubscribe' => _("Unsubscribe"),
                         'archive'     => _("List Archives"),
                         'owner'       => _("Contact Listowner"),
                         'help'        => _("Help"));
    $output = array();

    foreach ($message->rfc822_header->mlist as $cmd => $actions) {

        /* I don't know this action... skip it */
        if ( !array_key_exists($cmd, $fieldsdescr) ) {
            continue;
        }

        /* proto = {mailto,href} */
        $aActionKeys = array_keys($actions);
        $proto = array_shift($aActionKeys);
        $act   = array_shift($actions);

        if ($proto == 'mailto') {

            if (($cmd == 'post') || ($cmd == 'owner')) {
                $url = 'src/compose.php?' .
                (isset($startMessage)?'startMessage='.$startMessage.'&amp;':'');
            } else {
                $url = "plugins/listcommands/mailout.php?action=$cmd&amp;";
            }
            $url .= 'send_to=' . str_replace('?','&amp;', $act);

            $output[] = makeComposeLink($url, $fieldsdescr[$cmd]);

            if ($cmd == 'post') {
                $url .= '&amp;passed_id='.$passed_id.
                    '&amp;mailbox='.urlencode($mailbox).
                    (isset($passed_ent_id)?'&amp;passed_ent_id='.$passed_ent_id:'');
                $url .= '&amp;smaction=reply';
                
                $output[] = makeComposeLink($url, $fieldsdescr['reply']);
            }
        } else if ($proto == 'href') {
            $output[] = '<a href="' . $act . '" target="_blank">'
                      . $fieldsdescr[$cmd] . '</a>';
        }
    }

    if (count($output) > 0) {
        echo '<tr>';
        echo html_tag('td', '<b>' . _("Mailing List") . ':&nbsp;&nbsp;</b>',
                      'right', '', 'valign="middle" width="20%"') . "\n";
        echo html_tag('td', '<small>' . implode('&nbsp;|&nbsp;', $output) . '</small>',
                      'left', $color[0], 'valign="middle" width="80%"') . "\n";
        echo '</tr>';
    }
}

