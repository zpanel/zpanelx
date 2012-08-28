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

$auto_address_hooks = array(
    'work_hooks'        => array('init', 'update_settings'),
    'display_hooks'     => array('compose_options_table', 'compose_page_to_row', 'compose_page_cc_row', 'compose_page_bcc_row'),
);
$auto_address_langs = array(
    'en_US' => array(
        1 => 'Auto-complete addresses on',
        2 => 'Limit address auto-complete to the local addressbook',
        3 => 'Start address auto-complete after this many characters',
        4 => 'Limit address auto-complete results to',
    ),
    'gr_GR' => array(
        1 => 'Αυτόματη συμπλήρωση με', //Auto-complete addresses on
        2 => 'Περιορισμός της αυτόματης συμπλήρωσης διευθύνσεων στο τοπικό βιβλίο διευθύνσεων', //Limit address auto-complete to the local addressbook
        3 => 'Έναρξη αυτόματης συμπλήρωσης διευθύνσεων μετά από τόσους χαρακτήρες', //Start address auto-complete after this many characters
        4 => 'Περιορισμός αποτελεσμάτων αυτόματης συμπλήρωσης διευθύνσεων σε', //Limit address auto-complete results to
    ),
    'pl_PL' => array(
        1 => 'Autouzupełnianie adresów włączone',
        2 => 'Używaj adresów z lokalnej książki adresowej przy autouzupełnianiu.',
        3 => 'Rozpocznij autouzupełnianie po wskazanej ilości znaków',
        4 => 'Ograniczaj wyniki autouzupełniania do',
    ),
);
?>
