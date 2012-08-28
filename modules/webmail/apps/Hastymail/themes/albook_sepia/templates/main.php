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
global $javascript_dev;

/* override plugin output for the main menu*/
$menu_overrides = array();

/* plugin menu entries overrides */
$menu_overrides = array(
    'calendar_menu' => '<a href="?page=calendar"><img src="themes/'.$pd->pd['theme'].'/images/calendar.png" title="Calendar" alt="Calendar" /></a>&nbsp;',
);
if (isset($_SESSION['user_settings']['filter_menu']) && $_SESSION['user_settings']['filter_menu']) {
	$menu_overrides['filters_menu'] = '<a href="?page=filters"><img src="themes/'.$pd->pd['theme'].'/images/filter.png" title="Filters" alt="Filters" /></a>&nbsp;';
}


/* HTML head section */

global $app_pages;
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
        table { padding-bottom: 10px;}
        a { padding-right: 5px; }
        .mbx_unseen_subject a { font-weight: bold; }
        th { text-align: left; padding-top: 10px; }
        td, th { padding-right: 5px; }
        #compose_from, .address_fld { width: 400px;}
        </style></simple-'.$page_id.'>
    </head>' ?>


<!-- start the body tag, main container, and top div -->

<body id="body_tag">
    <div id="nonfooter">
        <input type="hidden" id="page_id" value="<?php echo $page_id ?>" />
        <?php echo do_display_hook('page_top') ?>
        <?php if (!$pd->new_window) { ?>
        <div id="top">


<!-- Icon, dropdown, and clock -->

        <?php if ($pd->user->logged_in) { ?>
        <div id="icon" title="Hastymail 2">
            <?php echo do_display_hook('icon').'
            '.$pd->print_icon() ?>
            <div id="dropdown_outer"><div id="dd_inner">
                <?php echo $pd->print_folder_dropdown($pd->pd['folders']).
                '</div><complex-'.$page_id.'><div id="clock_div">'.$pd->print_clock().'</div></complex-'.$page_id.'>
                 <span id="unread_total"><a href="?page=new&amp;mailbox='.urlencode($pd->pd['mailbox']).'">'.sprintf($pd->user->str[537], $_SESSION['total_unread']).'</a></span>
               '.do_display_hook('clock') ?>
            </div>
            <?php echo '<complex-'.$page_id.'>'.'<a href="'.$sticky_url.'" id="show_folders" '; if (isset($_SESSION['hide_folder_list']) && $_SESSION['hide_folder_list']) {
                  echo 'style="display: inline;" '; } echo 'onclick="hide_folder_list(); return false;">'.$pd->user->str[36].'</a></complex-'.$page_id.'>'; ?>
        </div>


<!-- main menu --> 

        <div id="menu"><?php echo '
            <complex-'.$page_id.'>
            <!-- <a href="?page=new&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['new_link_class'].'">'.$pd->user->str[10].'</a>&#160; -->
            <a href="?page=compose&amp;compose_session=new&amp;mailbox='.$pd->pd['url_mailbox'].'" onclick="'.$pd->pd['compose_onclick'].'" class="'.$pd->pd['compose_link_class'].'"><img src="themes/'.$pd->pd['theme'].'/images/compose.png" title="'.$pd->user->str[3].'" alt="'.$pd->user->str[3].'" /></a>&#160;
            <a href="?page=search&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['search_link_class'].'"><img src="themes/'.$pd->pd['theme'].'/images/search.png" title="'.$pd->user->str[9].'" alt="'.$pd->user->str[9].'" /></a>&#160;
            <a href="?page=contacts&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['contacts_link_class'].'"><img src="themes/'.$pd->pd['theme'].'/images/contacts.png" title="'.$pd->user->str[8].'" alt="'.$pd->user->str[8].'" /></a>&#160;
            <a href="?page=options&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['options_link_class'].'"><img src="themes/'.$pd->pd['theme'].'/images/options.png" title="'.$pd->user->str[4].'" alt="'.$pd->user->str[4].'" /></a>&#160;
            <a href="?page=profile&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['profile_link_class'].'"><img src="themes/'.$pd->pd['theme'].'/images/profile.png" title="'.$pd->user->str[236].'" alt="'.$pd->user->str[236].'" /></a>&#160;
            <a href="?page=folders&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['folder_link_class'].'"><img src="themes/'.$pd->pd['theme'].'/images/folders.png" title="'.$pd->user->str[7].'" alt="'.$pd->user->str[7].'" /></a>&#160;
            '.do_display_hook('menu', $menu_overrides).'<a href="?page=logout"><img src="themes/'.$pd->pd['theme'].'/images/quit.png" title="'.$pd->user->str[5].'" alt="'.$pd->user->str[5].'" /></a></complex-'.$page_id.'>
            <simple-'.$page_id.'><br />
            <a href="?page=new&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['new_link_class'].'">'.$pd->user->str[10].'</a>&#160;
            <a href="?page=options&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['options_link_class'].'">'.$pd->user->str[4].'</a>&#160;
            <a href="?page=compose&amp;compose_session=new&amp;mailbox='.$pd->pd['url_mailbox'].'" class="'.$pd->pd['compose_link_class'].'">'.$pd->user->str[3].'</a>&#160;
            <a href="?page=logout">'.$pd->user->str[5].'</a>
            </simple-'.$page_id.'>
            
            ' ?>
        </div>
        <?php } ?>


<!-- primary content area -->

    </div>
    <?php } else { echo '<a name="top"></a><br />'; } ?>
    <?php if (!$pd->user->logged_in) { echo '<table cellpadding="0" cellspacing="0" align="center" id="main_table"><tr><td>'; } ?>
        <?php if ($pd->user->logged_in  && $pd->pd['settings']['show_folder_list'] && !$pd->new_window) { echo '<complex-'.$page_id.'>' ?>


<!-- folder list if enabled -->

            <table cellpadding="0" cellspacing="0" style="clear: both;" width="100%">
                <tr>
                    <td valign="top" id="folder_cell">
                        <div id="folder_cell_inner" <?php if (isset($_SESSION['hide_folder_list']) && $_SESSION['hide_folder_list']) {
                    echo 'style="display: none;" '; }?>>
                        <b class="rtop"><b class="r1">&#160;</b><b class="r2">&#160;</b>
                        <b class="r3">&#160;</b><b class="r4">&#160;</b></b>
                        <div class="folder_border">
                        <b class="ftop"><b class="f1">&#160;</b><b class="f2">&#160;</b>
                        <b class="f3">&#160;</b><b class="f4">&#160;</b></b>
                        <div class="folder_inner">
                        <?php echo do_display_hook('folder_list_top') ?>
                            <div id="folder_outer"><div>
                                <?php echo $pd->print_folder_list($pd->pd['folders']) ?>
                            </div></div>
                            <?php echo do_display_hook('folder_list_bottom') ?>
                            <div id="hide_link"><a href="<?php echo $sticky_url ?>" onclick="hide_folder_list(); return false;"><?php echo $pd->user->str[61] ?></a></div>
                        </div>
                        <b class="fbottom"><b class="f4">&#160;</b><b class="f3">&#160;</b>
                        <b class="f2">&#160;</b><b class="f1">&#160;</b></b>
                        </div>
                        <b class="rbottom"><b class="r4">&#160;</b><b class="r3">&#160;</b>
                        <b class="r2">&#160;</b><b class="r1">&#160;</b></b>
                    </div>
                    </td>
                    <td valign="top" width="99%">
                    <?php echo '</complex-'.$page_id.'>'; } ?>


<!-- user notices/errors  -->

                    <div id="content">
                        <b class="rtop"><b class="r1">&#160;</b><b class="r2">&#160;</b>
                        <b class="r3">&#160;</b><b class="r4">&#160;</b></b>
                        <div id="content_inner">
                        <div id="notices">
                            <?php echo do_display_hook('notices_top') ?>
                            <?php echo $pd->print_notices($pd->dsp_page) ?>
                            <?php echo do_display_hook('notices_bottom') ?>
                        </div>


<!-- jump to the page specific template -->
    
                        <?php run_template() ?>
                        <?php echo do_display_hook('content_bottom') ?>
                        </div>
                        <b class="rbottom"><b class="r4">&#160;</b><b class="r3">&#160;</b>
                        <b class="r2">&#160;</b><b class="r1">&#160;</b></b>
                        </div>


<!-- close additional tags if we are showing the folder list -->

                        <?php if (isset($pd->pd['settings']['show_folder_list']) && !$pd->new_window && $pd->pd['settings']['show_folder_list']) { echo '<complex-'.$page_id.'>' ?>
                    </td>
                </tr>
            </table>
            <?php echo '</complex-'.$page_id.'>'; } ?>


<!-- bottom of the main content area -->

    <?php if (!$pd->user->logged_in) { echo '</td></tr></table>'; } ?>
    </div>


<!-- page footer -->

    <div id="footer">
        <?php echo '<div id="top_link">'.$pd->pd['top_link'].'</div>' ?>
        <?php echo do_display_hook("footer") ?>
        <complex-<?php echo $page_id ?>>&copy; Hastymail Development Group 2010</complex-<?php echo $page_id ?>>

    </div>
    <?php echo $pd->print_inline_js() ?>
    </body>
</html>
