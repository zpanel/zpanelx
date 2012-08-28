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

$notices_hooks = array(
    'work_hooks'        => array('init', 'update_settings', 'options_page_start'),
    'display_hooks'     => array('general_options_table'),
    'page_hook'         => false,
);
$notices_langs = array(
    'en_US' => array(
        1 => 'Notices',
        2 => 'New Message Received',
        3 => 'New Message',
        4 => 'Play a sound when a new message arrives',
        5 => 'Open a Popup window when a new message arrives',
        6 => 'Sound file',
    ),
    'gr_GR' => array(
        1 => 'Ειδοποιήσεις', //Notices
        2 => 'Παραλαβή Νέου Μηνύματος', //New Message Received
        3 => 'Νέο μήνυμα', //New Message
        4 => 'Αναπαραγωγή ήχου με την άφιξη νέου μηνύματος', //Play a sound when a new message arrives
        5 => 'Άνοιγμα αναδυομένου παραθύρου με την άφιξη νέου μηνύματος', //Open a Popup window when a new message arrives
        6 => 'Αρχείο ήχου', //Sound file
    ),
    'pl_PL' => array(
        1 => 'Powiadomienia',
        2 => 'Otrzymano nową wiadomość',
        3 => 'Nowa wiadomość',
        4 => 'Odtwórz dźwięk, podczas odebrania nowej wiadomości',
        5 => 'Otwórz wyskakujące okienko podczas odebrania nowej wiadomości',
        6 => 'Plik dźwiękowy',
    ),
);
?>
