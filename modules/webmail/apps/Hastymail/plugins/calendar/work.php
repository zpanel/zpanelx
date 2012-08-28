<?php

/*  work.php: Plugin file responsible for the backend processing
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
function calendar_update_settings($tools) {
    if (isset($_POST['calendar_event_summary']) && $_POST['calendar_event_summary']) {
        $tools->save_options_page_setting('calendar_event_summary', 1);
    }
    else {
        $tools->save_options_page_setting('calendar_event_summary', 0);
    }
}
function calendar_init($tools) {
    $cnt = 0;
    $opts = array($tools->str[28]);
    $tools->save_to_global_store('help_strings', $opts);
    if ($tools->get_setting('calendar_event_summary')) {
        require_once($tools->include_path.'page.php');
        if ($tools->get_db()) {
            $day = date('d');
            $year = date('Y');
            $month = date('m');
            $count = array();
            $events = get_calendar_events($month, $year, $day, 'calendar_day', $tools);
            foreach ($events as $date => $vals) {
                if ($date == $year.'-'.$month.'-'.$day) {
                    foreach ($vals as $time => $atts_list) {
                        foreach ($atts_list as $index => $event_array) {
                            foreach ($event_array as $atts) {
                                $count[$date.$time.$atts['id']] = 1;
                            }
                        }
                    }
                }
            }
            $cnt = count($count);
        }
    }
    $tools->add_to_store('event_cnt', $cnt);
}
function calendar_page_end($tools) {
    if ($tools->get_page() == 'message' && strstr($tools->get_url(), 'vcal=1')) {
    }
}
?>
