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

$calendar_hooks = array(

    /* sets up work hooks for any back end processing the plugin needs */

    'work_hooks'        => array('init', 'update_settings', 'page_end'),

    /* sets up display hooks for inserting content into existing hm pages */

    'display_hooks'     => array('menu', 'folder_list_bottom', 'general_options_table', 'message_top'),

    /* If true sets up a place for the plugin can have its own pages */

    'page_hook'         => true,

);
$calendar_langs = array(
    'en_US' => array(
        1 => 'Calendar',
        2 => 'January',
        3 => 'February',
        4 => 'March',
        5 => 'April',
        6 => 'May',
        7 => 'June',
        8 => 'July',
        9 => 'August',
        10 => 'September',
        11 => 'October',
        12 => 'November',
        13 => 'December',
        14 => 'Add Event',
        15 => 'Page Not Found',
        16 => 'Year',
        17 => 'Month',
        18 => 'Day',
        19 => 'Title',  
        20 => 'Detail',
        21 => 'Repeat',
        22 => 'None',
        23 => 'Yearly',
        24 => 'Monthly',
        25 => 'Weekly',
        26 => 'Daily',
        27 => 'Add',
        28 => 'Show event summary below folder list',
    ),
    'pl_PL' => array(
        1 => 'Kalendarz',
        2 => 'Styczeń',
        3 => 'Luty',
        4 => 'Marec',
        5 => 'Kwiecień',
        6 => 'Maj',
        7 => 'Czerwiec',
        8 => 'Lipec',
        9 => 'Sierpień',
        10 => 'Wrzesień',
        11 => 'Październik',
        12 => 'Listopad',
        13 => 'Grudzień',
        14 => 'Dodaj zdarzenie',
        15 => 'Strona nie znaleziona',
        16 => 'Rok',
        17 => 'Miesiąc',
        18 => 'Dzień',
        19 => 'Tytuł',  
        20 => 'Detale',
        21 => 'Powtórz',
        22 => 'Brak',
        23 => 'Rocznie',
        24 => 'Miesięcznie',
        25 => 'Tygodniowo',
        26 => 'Dziennie',
        27 => 'Dodaj',
        28 => 'Pokazuj podsumowanie zdarzeń poniżej listy folderów',
    ),
);

?>
