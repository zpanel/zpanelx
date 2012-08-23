<?php
/*  about.php: About page template 
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
}?>


<!-- title and outer div -->

<div id="about_page"><?php echo do_display_hook('about_page_top') ?>
    <h2 id="mailbox_title2"><?php echo $pd->user->str[2] ?></h2>
    <table cellpadding="0" cellspacing="0" class="about_table">


<!-- hastymail settings -->

        <tr><td colspan="2"><h4>Hastymail</h4></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[262] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['version']) ?></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[263] ?></td>
            <td><?php echo $pd->pd['ajax_flag'] ?></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[264] ?></td>
            <td><?php echo $pd->pd['fcache_flag'] ?></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[265] ?></td>
            <td><?php echo $pd->pd['ucache_flag'] ?></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[266] ?></td>
            <td><?php echo $pd->pd['hcache_flag'] ?></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[533] ?></td>
            <td><?php echo $pd->pd['mod_util_flag'] ?></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[267] ?></td>
            <td><?php echo $pd->pd['plugins'] ?></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[449] ?></td>
            <td><a href="http://www.hastymail.org" target="_blank">www.hastymail.org</a></td>
        </tr><tr>


<!-- Web server info -->

            <td colspan="2"><br /><h4><?php echo $pd->user->str[268] ?></h4></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[269] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['server']) ?></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[270] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['admin']) ?></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[271] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['host']) ?></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[272] ?></td>
            <td><?php echo $pd->pd['server_time'] ?></td>
        </tr><tr>


<!-- IMAP server info -->

            <td colspan="2"><br /><h4><?php echo $pd->user->str[273] ?></h4></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[274] ?></td>
             <td><?php echo $pd->user->htmlsafe($pd->pd['banner']) ?></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[275] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['caps']) ?></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[271] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['imap_server']) ?></td>
        </tr><tr>


<!-- Browser info -->

            <td colspan="2"><br /><h4><?php echo $pd->user->str[276] ?></h4></td>
        </tr><tr>
            <td class="opt_leftcol" nowrap="nowrap"><?php echo $pd->user->str[277] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['browser']) ?></td>
        </tr><tr>
            <td class="opt_leftcol"><?php echo $pd->user->str[278] ?></td>
            <td><?php echo $pd->user->htmlsafe($pd->pd['ip']) ?></td></tr>
    </table>


<!-- Image links -->
    <?php echo do_display_hook('about_table_bottom') ?>
    <div id="about_images">
        <a href="http://www.php.net" target="_blank"><img src="images/php.png" alt="PHP" /></a>
        <a href="http://www.w3c.org" target="_blank"><img src="images/w3c_1.png" alt="W3c" /></a>
        <a href="http://www.w3c.org" target="_blank"><img src="images/w3c_2.png" alt="W3c" /></a>
        <a href="http://www.sf.net" target="_blank"><img src="images/sf.png" alt="SourceForge" /></a>
    </div>
    <?php echo do_display_hook('about_page_bottom') ?>
</div>
