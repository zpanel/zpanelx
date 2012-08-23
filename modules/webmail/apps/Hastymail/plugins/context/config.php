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

$context_hooks = array(
    'work_hooks'        => array('init'),
    'display_hooks'     => array('message_headers_bottom'),
    'page_hook'         => true,
);
$context_langs = array(
    'en_US' => array(
        1 => 'Look up the selected text using Google',
        2 => 'Look up the selected text using Wikipedia',
        3 => 'Look up the selected text in a Dictionary',
        4 => 'Look up the selected text in a Thesaurus',
        5 => 'Select some text to activate the context search toolbar',
        6 => 'Look up the selected text using Google Maps',
    ),
    'gr_GR' => array(
        1 => 'Αναζήτηση επιλεγμένου κειμένου στο Google', //Look up the selected text using Google
        2 => 'Αναζήτηση του επιλεγμένου κειμένου στην (αγγλική) Wikipedia', //Look up the selected text using Wikipedia
        3 => 'Αναζήτηση του επιλεγμένου κειμένου σε ένα Λεξικό', //Look up the selected text in a Dictionary
        4 => 'Αναζήτηση του επιλεγμένου κειμένου σε ένα Θησαυρό', //Look up the selected text in a Thesaurus
        5 => 'Επιλέξτε κάποιο κείμενο για την ενεργοποίηση των στοιχείων ελέγχου αναζήτησης', //Select some text to activite the context search toolbar
        6 => 'Look up the selected text using Google Maps',
    ),
    'pl_PL' => array(
        1 => 'Sprawdź zaznaczony tekst używając Google',
        2 => 'Sprawdź zaznaczony tekst używając Wikipedia',
        3 => 'Sprawdź zaznaczony tekst w Słowniku',
        4 => 'Sprawdź zaznaczony tekst w Słowniku wyrazów bliskoznacznych',
        5 => 'Wybierz fragment tekstu, aby aktywować pasek narzędzi',
        6 => 'Look up the selected text using Google Maps',
    )
);
?>
