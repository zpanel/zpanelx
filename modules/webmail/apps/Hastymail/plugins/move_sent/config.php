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

/*  PLUGIN DEFINITION */

$move_sent_hooks = array(

    'work_hooks'        => array('init', 'update_settings', 'compose_after_send', 'message_send'),
    'display_hooks'     => array('compose_after_options', 'compose_options_table'),
    'page_hook'         => false,

);

/* translation strings for the plugin. See docs/plugin_languages.txt */
$move_sent_langs = array(
    'en_US' => array(
        'compose_save_sent_to' => 'Save sent message in',
        'compose_move_replied_message_too' => 'Move replied to message',
        'config_global_enable' => 'Show Sent folder selection',
    ),
    'de_DE' => array(
        'compose_save_sent_to' => 'Gesendete Nachricht speichern in',
        'compose_move_replied_message_too' => 'Beantwortete Nachricht verschieben',
        'config_global_enable' => 'Ordnerauswahl anzeigen',
    ),
    'gr_GR' => array(
//        'compose_save_sent_to' => 'Save sent message in',
        'compose_save_sent_to' => 'Αποθήκευση απεσταλμένου σε',
//        'compose_move_replied_message_too' => 'Move replied to message',
        'compose_move_replied_message_too' => 'Μετακίνηση απαντημένου μηνύματος',
//        'config_global_enable' => 'Show Sent folder selection',
        'config_global_enable' => 'Εμφάνιση επιλογής φακέλου αποθήκευσης απεσταλμένου',
    ),
    'pl_PL' => array(
        'compose_save_sent_to' => 'Zapisz wysłaną wiadomość w',
        'compose_move_replied_message_too' => 'Przenieś wiadomości na które odpowiedziałam/em do',
        'config_global_enable' => 'Pokaż wybór katakogu wysłanych',
    ),
);

?>
