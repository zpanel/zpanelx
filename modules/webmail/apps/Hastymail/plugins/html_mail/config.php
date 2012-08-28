<?php

/*  config.php: Plugin file responsible for defining how the plugin interacts with Hastymail 
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

/*  PLUGIN DEFINITION

    The following array defines how the plugin interacts with Hastymail.
    No other code should be present in this file except the hooks array.
    The array should be named <plugin name>_hooks. It contains 3 sections:

        'work_hooks'        These hooks allow plugins to do backend processing at different
                            points in the page load process. See docs/plugin_work_hooks.txt
                            for more information. Plugin functions triggered from these hooks
                            must be located in a file called "work.php" in the plugin directory.

        'display_hooks'     These hooks allow plugins to output HTML into an existing HTML page.
                            See docs/plugin_display_hooks.txt for more information. Plugin functions
                            triggered by these hooks must be located in a file called "display.php"
                            in the plugin directory.

        'page_hook'         Set to true if the plugin needs to have it's own pages
                            within Hastymail. A minimum of 2 functions with specific names
                            must be created in a file called "page.php" in the plugin directory.
                            See docs/plugin_pages.txt.

    For more information about developing plugins for Hastymail see docs/plugin_basics.txt
  
*/

$html_mail_hooks = array(

    /* sets up work hooks for any back end processing the plugin needs
      for every hook in this list there must be an function defined in work.php
      with the name <plugin name>_<hook name>. So this plugin has a function called
      at this hook point called hello_world_init() */

    'work_hooks'        => array('init', 'update_settings', 'page_end', 'message_send', 'message_save'),

    /* sets up display hooks for inserting content into existing hm pages. Just
       like work hooks this requires a function to be created called
       <plugin name>_<hook name> except this time in display.php */

    'display_hooks'     => array('compose_options_table', 'compose_after_options'),

    /* If true sets up a place for the plugin can have its own pages. This
       requires the creation of a file called page.php with at least 2 mandatory
       functions called <plugin name>_url_action and print_<plugin name>. See the
       page.php file for more information. */

    'page_hook'         => true,

);

$html_mail_langs = array(
    'en_US' => array(
        1 => 'Compose with HTML by default',
        2 => 'Default HTML font size',
        3 => 'Default HTML font family',
        4 => 'Show HTML/Text mode toggle',
        5 => 'Text Mode',
        6 => 'HTML Mode',
    ),
    'gr_GR' => array(
        1 => 'Σύνθεση μηνυμάτων, εξ ορισμού, σε HTML', //Compose with HTML by default
        2 => 'Εξ ορισμού μέγεθος γραμματοσειράς HTML', //Default HTML font size
        3 => 'Εξ ορισμού γραμματοσειρά HTML', //Default HTML font family
        4 => 'Εμφάνιση κουμπιού εναλλαγής HTML/Text (απλό κείμενο)', //Show HTML/Text mode toggle
        5 => 'Λειτουργία απλού κειμένου (text)', //Text Mode
        6 => 'Λειτουργία HTML', //HTML Mode
    ),
    'pl_PL' => array(
        1 => 'Redaguj wiadomości używając formatu HTML',
        2 => 'Domyślna wielkość czcionki formatu HTML',
        3 => 'Domyślny styl czcionki formatu HTML',
        4 => 'Show HTML/Text mode toggle',
        5 => 'Text Mode',
        6 => 'HTML Mode',
    ),
);
?>
