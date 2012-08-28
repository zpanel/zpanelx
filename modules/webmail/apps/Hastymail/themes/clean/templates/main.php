<?php
/*  main.php: Primary template file
    Copyright (C) 2002-2010  Hastymail Development group

    This file is part of Hastymail.

    Hastymail is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Hastymail is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hastymail; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

* $Id:$
*/

/* don't let this file be loaded by the browser directly */
if (!isset($pd) || !is_object($pd)) {
    exit;
}
global $sticky_url;
global $page_id;

$menu_overrides = array(
    'calendar_menu' => '<td><a href="?page=calendar" title="Calendar" class="calendar_link" >&#160;</a></td>',
    'filters_menu' => '<td><a href="?page=filters" title="Filters" class="filters_link" >&#160;</a></td>',
    'news_menu' => '<td><a href="?page=news" title="News" class="news_link" >&#160;</a></td>',
    'message_tags_menu' => '<td><a href="?page=message_tags" title="Tags" class="tags_link" >&#160;</a></td>',
    'pop_fetch_menu' => '<td><a href="?page=pop_fetch" title="Fetch" class="fetch_link" >&#160;</a></td>',
);

/* label for the "toolbar" */
if ($pd->user->logged_in) {
    $page_label = '<complex-'.$page_id.'><div id="mailbox_title"></complex-'.$page_id.'>';
    switch ($pd->dsp_page) {
        case 'mailbox':
            $page_label .= $pd->pd['mailbox_dsp'].' <span id="mailbox_meta">'.$pd->pd['frozen_dsp'].' '.$pd->user->str[41].': '.
                $pd->pd['mailbox_total'].', '.$pd->user->str[40].': '.$pd->pd['mailbox_page'].' ('.
                $pd->pd['mailbox_range'].')<span class="folder_unread"> '.$pd->user->str[34].' '.$pd->pd['folder_unread'].
                '</span></span>'.do_display_hook('mailbox_meta');
            break;
        case 'new':
            $page_label .= $pd->user->str[245].' <div>Found '.$pd->pd['grand_total'].' messages in '.$pd->pd['unread_folder_count'].' folders</div>';
            break;
        case 'options':
            $page_label .= $pd->user->str[4].' <div>Configure the Hastymail2 interface</div>';
            break;
        case 'compose':
            $page_label .= $pd->user->str[3].' <div>Send an outbound message</div>';
            break;
        case 'message':
            $page_label .= '<span class="folder_title">'.$pd->pd['mailbox_dsp'].'</span><div>'.
                $pd->pd['frozen_dsp'].$pd->user->str[322].'&#160; '.($pd->pd['uid_index'] + 1).' / '.$pd->pd['mailbox_total'].'
                &#160;&#160; '.$pd->user->str[321].' '.$pd->pd['mailbox_page'].''.do_display_hook('message_meta').'</div>';
            break;
        case 'thread_view':
            $page_label .= 'Thread View<div>'.$pd->pd['thread_count'].' messages in thread: <a href="?page=message&amp;uid='.
                $pd->pd['thread_uid'].'&amp;mailbox='.urlencode($pd->pd['mailbox']).'&amp;sort_by='.$pd->pd['sort_by'].'">'.$pd->pd['thread_subject'].'</a></div>';
            break;
        case 'about':
            $page_label .= $pd->user->str[2].'<div>Information about your webmail setup</div>';
            break;
        case 'profile':
            $page_label .= $pd->user->str[236].'<div>Manage your identities</div>';
            break;
        case 'folders':
            $page_label .= $pd->user->str[7].'<div>Manage your account folders</div>';
            break;
        case 'search':
            $page_label .= $pd->user->str[9].'<div>Find messages in your account</div>';
            break;
        case 'contact_groups':
        case 'contacts':
            $page_label .= $pd->user->str[8].'<div>Manage contacts and groups</div>';
            break;

        case 'news':
            $page_label .= 'News<div>RSS/Atom feed aggregation</div>';
            break;
        case 'message_tags':
            $page_label .= 'Tags<div>Use tags to organize messages</div>';
            break;
        case 'filters':
            $page_label .= 'Filters<div>Manage messages using rules</div>';
            break;
        case 'calendar':
            $page_label .= 'Calendar<div>Manage your schedule</div>';
            break;
        case 'pop_fetch':
            $page_label .= 'Fetch<div>Download E-mail from another account</div>';
            break;

        default:
            $page_label .= '';
            break;
    }
    $page_label .= '<complex-'.$page_id.'></div></complex-'.$page_id.'>';
}
else {
    $page_label = '';
}


/* HTML head section */

global $app_pages;
global $javascript_dev;
echo '
    <head>
        <base href="'.$pd->pd['base_href'].'" />
        <title id="title">';
        if ($pd->user->logged_in) { echo $_SESSION['total_unread'].' '.$pd->user->str[10]; } echo '
        '.$pd->user->page_title.' '.$pd->pd['site_title'].'</title><complex-'.$page_id.'>
        <link rel="shortcut icon" href="themes/'.$pd->pd['theme'].'/images/hastymail.ico" type="image/vnd.microsoft.icon" />
        <link rel="icon" href="themes/'.$pd->pd['theme'].'/images/hastymail.ico" type="image/vnd.microsoft.icon" /> 
        <link rel="stylesheet" type="text/css" href="?page='.$pd->dsp_page.'&amp;theme='.$pd->pd['theme'].'&amp;css=1" />';
        if ($pd->user->ajax_enabled) {
            if ($javascript_dev) {
                echo '<script type="text/javascript" src="js/sajax_wrappers.js"></script>';
                echo '<script type="text/javascript" src="js/site.js"></script>';
            }
            else {
                echo '<script type="text/javascript" src="js/site-min2.js"></script>';
            }
        }
        else {
            if ($javascript_dev) {
                echo '<script type="text/javascript" src="js/site.js"></script>';
            }
            else {
                echo '<script type="text/javascript" src="js/site-min.js"></script>';
            }
        }
        echo $pd->print_hm_html_head().'
        </complex-'.$page_id.'><simple-'.$page_id.'><style type="text/css">
        table { padding-bottom: 10px;} a { padding-right: 5px; } .mbx_unseen_subject a { font-weight: bold; }
        th { text-align: left; padding-top: 10px; } td, th { padding-right: 5px; } #compose_from, .address_fld { width: 400px;}
        </style></simple-'.$page_id.'>
    </head>' ?>


<!-- start the body tag, main container, and top div -->

<body id="body_tag">
    <div id="nonfooter">
        <input type="hidden" id="page_id" value="<?php echo $page_id ?>" />
        <?php echo do_display_hook('page_top') ?><?php if (!$pd->new_window) { ?><div id="top">

        <?php if ($pd->user->logged_in) { ?>

<!-- main menu --> 

<div id="menu"><?php echo '<complex-'.$page_id.'>'.
            '<table cellpadding="0" cellspacing="0"><tr><td>'.
            '<a href="?page=about" class="logo"></a></td><td class="page_label">'.$page_label.'</td><td>'.
            '<a href="?page=new&amp;mailbox='.urlencode($pd->pd['mailbox']).'" class="unread_link" title="'.sprintf($pd->user->str[537], $_SESSION['total_unread']).'">&#160;</a></td><td>'.
            '<a href="?page=compose&amp;compose_session=new&amp;mailbox='.$pd->pd['url_mailbox'].'" onclick="'.$pd->pd['compose_onclick'].'" class="compose_link" title="'.$pd->user->str[3].'">&#160;</a></td><td>'.
            '<a href="?page=search&amp;mailbox='.$pd->pd['url_mailbox'].'" class="search_link" title="'.$pd->user->str[9].'">&#160;</a></td><td>'.
            '<a href="?page=contacts&amp;mailbox='.$pd->pd['url_mailbox'].'" class="contacts_link" title="'.$pd->user->str[8].'">&#160;</a></td><td>'.
            '<a href="?page=options&amp;mailbox='.$pd->pd['url_mailbox'].'" class="options_link" title="'.$pd->user->str[4].'">&#160;</a></td><td>'.
            '<a href="?page=profile&amp;mailbox='.$pd->pd['url_mailbox'].'" class="profile_link" title="'.$pd->user->str[236].'">&#160;</a></td><td>'.
            '<a href="?page=folders&amp;mailbox='.$pd->pd['url_mailbox'].'" class="folders_link" title="'.$pd->user->str[7].'">&#160;</a></td>'.
            do_display_hook('menu', $menu_overrides).'</tr></table></div><div class="logout_div">
            <div id="clock_div">'.$pd->print_clock().'</div>
            <span class="logout_span"><a href="?page=logout" class="logout_link" title="'.$pd->user->str[5].'">&#160;</a></span>
            </complex-'.$page_id.'><simple-'.$page_id.'><br />'.
            '<a href="?page=new&amp;mailbox='.urlencode($pd->pd['mailbox']).'">'.sprintf($pd->user->str[537], $_SESSION['total_unread']).'</a>&#160; '.
            '<a href="?page=new&amp;mailbox='.urlencode($pd->pd['mailbox']).'">'.sprintf($pd->user->str[537], $_SESSION['total_unread']).'</a>&#160; '.
            '<a href="?page=options&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['options_link_class'].'">'.$pd->user->str[4].'</a>&#160; '.
            '<a href="?page=compose&amp;compose_session=new&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['compose_link_class'].'">'.$pd->user->str[3].'</a>&#160; '.
            '<a href="?page=logout">'.$pd->user->str[5].'</a>'.
            '<br /><br /></simple-'.$page_id.'>' ?>
        </div>
        <?php } else { echo '<div id="menu"><table cellpadding="0" align="center" cellspacing="0"><tr><td><a href="?page=login" class="logo"></a></td><td class="clean_td"><span>Hastymail2</span></td></tr></table></div>'; } ?>


<!-- primary content area -->
    </div><?php if ($pd->user->logged_in) { if (isset($pd->user->use_cookies) && !$pd->user->use_cookies) { echo '<input type="hidden" id="sid" value="'.session_id().'" />'; }
    echo '<input type="hidden" id="enable_delete_warning" value="'.$pd->pd['settings']['enable_delete_warning'].'" />'; } ?>
    <?php } else { echo '<a name="top"></a><br />'; } ?>
    <?php if (!$pd->user->logged_in) { echo '<table cellpadding="0" cellspacing="0" align="center" id="main_table"><tr><td>'; } ?>
        <?php if ($pd->user->logged_in  && !$pd->new_window) { echo '<complex-'.$page_id.'>' ?>


<!-- folder list if enabled -->

            <table cellpadding="0" cellspacing="0" style="clear: both;" id="main_table"><tr><td valign="top" id="folder_cell">
                <?php echo do_display_hook('folder_list_top') ?>
                <div id="folder_cell_inner">
                <div class="folder_border">
                <div class="folder_inner">
                <div id="folder_outer"><div><?php echo $pd->print_folder_list($pd->pd['folders']) ?></div></div>
                <?php echo do_display_hook('folder_list_bottom') ?><div id="hide_link">
                </div></div></div>
                </div></td><td valign="top" width="99%" id="content_cell"><?php echo '</complex-'.$page_id.'>'; } ?>


<!-- user notices/errors  -->

                    <div id="content">
                        <?php echo '<complex-'.$page_id.'>'; ?>
                        <?php echo '</complex-'.$page_id.'>'; ?><div id="content_outer"><?php echo '<complex-'.$page_id.'>'; ?>
                        <?php echo '</complex-'.$page_id.'>'; ?> <div id="content_inner"><div id="notices"><?php echo do_display_hook('notices_top') ?>
                        <?php echo $pd->print_notices($pd->dsp_page) ?><?php echo do_display_hook('notices_bottom') ?></div>


<!-- jump to the page specific template -->
    
                        <?php run_template() ?><?php echo do_display_hook('content_bottom') ?></div><?php echo '<complex-'.$page_id.'>'; ?>
                        <?php echo '</complex-'.$page_id.'>'; ?></div><?php echo '<complex-'.$page_id.'>'; ?>
                        <?php echo '</complex-'.$page_id.'>'; ?></div>

<!-- close additional tags if we are showing the folder list -->

            <?php if (isset($pd->pd['settings']['show_folder_list']) && !$pd->new_window && $pd->pd['settings']['show_folder_list']) { echo '<complex-'.$page_id.'>' ?></td></tr>
            <tr><td></td><td colspan="1">
    <?php if (!$pd->pd['new_window']) {?>
    <div id="footer"><?php if ($pd->user->logged_in) { echo '<div id="top_link">'.$pd->pd['top_link'].'</div>'; } ?>
    <?php if (!$pd->pd['new_window'] && ($pd->dsp_page == 'mailbox' || $pd->dsp_page == 'message')) { ?>
    <div id="page_links">
        <?php echo $pd->pd['page_links'] ?>
    </div>
    <?php } ?>
    <?php echo do_display_hook("footer") ?>
    <span class="copy">&copy; 2011 Hastymail2 Development Group</span>
    </div><?php } ?>
            </td></tr></table>
            <?php echo '</complex-'.$page_id.'>'; } ?>


<!-- bottom of the main content area -->

    <?php if (!$pd->user->logged_in) { echo '</td></tr></table>'; } ?></div>


<!-- page footer -->
    <?php echo $pd->print_inline_js() ?>
    </body>
</html>
