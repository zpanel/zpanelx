<?php

/*  cal_include.php: Helper functions for the calendar plugin
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

/* class to handle vcal/ical format message parts */
class vcal {
    var $events;

    function vcal() {
        $this->events = array();
    }
    function import($message) {
        if (trim($message)) {
            $message = str_replace("\r\n", "\n", $message);
            $lines = split("\n", $message);
            $event_body = false;
            $vcal_body = false;
            $vcal_version = false;
            $vcal_prodid = false;
            $att_name = false;
            $att_properties = array();
            $lines = $this->unwrap_long_lines($lines);
            foreach ($lines as $line) {
                if (trim($line) == 'BEGIN:VCALENDAR') {
                    $vcal_body = true;
                    $vcal = array();
                    continue;
                }
                if (trim($line) == 'END:VCALENDAR') {
                    $vcal['version'] = $vcal_version;
                    $vcal['proid'] = $vcal_prodid;
                    $this->events[] = $vcal;
                    $vcal_body = false;
                    $vcal_prodid = false;
                    $vcal_version = false;
                    continue;
                }
                if (trim($line) == 'BEGIN:VEVENT') {
                    $event_body = true;
                    $event = array();
                    continue;
                }
                if (trim($line) == 'END:VEVENT') {
                    $event_body = false;
                    $vcal['events'][] = $event;
                    continue;
                }
                if ($vcal_body || $event_body) {
                    if (strpos($line, ':') !== false) {
                        list($att_name, $att_properties) = $this->get_att_name($line);
                        if ($event_body) {
                            $val = substr($line, (strpos($line, ':') + 1));
                            switch($att_name) {
                                case 'SUMMARY':
                                case 'STATUS':
                                case 'UID':
                                case 'ORGANIZER':
                                case 'PRIORITY':
                                case 'DTSTAMP':
                                case 'LOCATION':
                                case 'DESCRIPTION':
                                case 'CLASS':
                                case 'CATEGORIES':
                                    $event[strtolower($att_name)] = $val;
                                    break;
                                case 'DTEND':
                                case 'DTSTART':
                                    $event[strtolower($att_name)] = $this->dt_format($val);
                                    break;
                                case 'ATTENDEE':
                                    $event[strtolower($att_name)][] = $val;
                                    break;
                            }
                        }
                        elseif ($vcal_body) {
                            switch ($att_name) {
                                case 'VERSION':
                                    $vcal_version = substr($line, (strpos($line, ':') + 1));
                                    break;
                                case 'PRODID':
                                    $vcal_prodid = substr($line, (strpos($line, ':') + 1));
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }
    function unwrap_long_lines($lines) {
        $new_lines = array();
        $index = 0;
        foreach ($lines as $line) {
            if ($line{0} == ' ' && isset($new_lines[($index - 1)])) {
                $new_lines[$index - 1] .= $line;
            }
            else {
                $new_lines[$index] = $line;
                $index++;
            }
        }
        return $new_lines;
    }
    function get_att_name($line) {
        $name = false;
        $props = array();
        $pos1 = strpos($line, ';');
        $pos2 = strpos($line, ':');
        if ($pos1 && $pos2 && $pos1 < $pos2) {
            $name = substr($line, 0, strpos($line, ';'));
            $prop_str = substr($line, strlen($name), strpos($line, ':'));
            return array($name, array($prop_str));
        }
        elseif ($pos2) {
            $name = substr($line, 0, strpos($line, ':'));
            return array($name, $props);
        }
    }
    function dt_format($val) {
        $ts = false;
        if (preg_match("/(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})/", $val, $matches)) {
            $ts = strtotime($matches[1].'-'.$matches[2].'-'.$matches[3].' '.$matches[4].':'.$matches[5].':'.$matches[6]);
        }
        return $ts;
    }
    function display() {
        return print_r($this->events, true);
    }
    function save() {
    }
}

/* ---------------- WORK FUNCTIONS ---------------------------------*/

/* normalize input from the add/edit form */
function normalize_input($req_flds, $opt_flds, $post) {
    $cal_atts = array();
    foreach ($req_flds as $v) {
        if (isset($post[$v]) && trim($post[$v])) {
            $cal_atts[$v] = trim($post[$v]);
        }
        else {
            $tools->send_notice('Required field missing: '.ucfirst($v));
        }
    }
    foreach ($opt_flds as $v) {
        if (isset($post[$v]) && trim($post[$v])) {
            $cal_atts[$v] = trim($post[$v]);
        }
        else {
            $cal_atts[$v] = '';
        }
    }
    return $cal_atts;
}

/* get events for the current page from the db */
function get_calendar_events($month, $year, $day, $dsp_page, $tools) {
    $events = array();
    if ($tools->get_db()) {
        $date_sql = '';
        $repeat_sql = '';
        switch ($dsp_page) {
            case 'all':
                $date_sql = "(datetime > '0001-01-01 00:00:00')";
                break;
            case 'calendar_year':
                $date_sql = "(datetime > '".intval($year)."-01-01 00:00:00' and datetime < '".(intval($year) + 1)."-01-01 00:00:00')";
                break;
            case 'calendar_week':
            case 'calendar_month':
                if ($month == 12) {
                    $date_sql = "(datetime > '".intval($year)."-".intval($month)."-01 00:00:00' and datetime < '".(intval($year) + 1)."-01-01 00:00:00')";
                }
                else {
                    $date_sql = "(datetime > '".intval($year)."-".intval($month)."-01 00:00:00' and datetime < '".intval($year)."-".(intval($month) + 1)."-01 00:00:00')";
                }
                break;
            case 'calendar_day':
                $date_sql = "(datetime > '".intval($year)."-".intval($month)."-".intval($day)." 00:00:00' and datetime < '".intval($year)."-".intval($month)."-".intval($day)." 24:00:00')";
                break;
        }
        if ($date_sql) {
            $sql = 'select * from calendar where username='.$tools->db_quote($tools->username).
                   ' and ('.$date_sql.' or repeat_val > 0 ) order by datetime ASC';
            $res = $tools->db_query($sql);
            foreach ($res as $vals) {
                if (strpos($vals['datetime'], ' ')) {
                    $date = substr($vals['datetime'], 0, strpos($vals['datetime'], ' '));   
                    $time = substr($vals['datetime'], (strpos($vals['datetime'], ' ') + 1));   
                    $hour_time = substr($time, 0, 2);
                    $min_time = substr($time, 3, 2);
                    $vals['repeat'] = $vals['repeat_val'];
                    $events[$date][$hour_time][$min_time][] = $vals;
                }
            }
        }
    }
    $events = get_repeat_events($tools, $dsp_page, $events, $year, $month, $day);
    return $events;
}

/* setup repeating events */
function get_repeat_events($tools, $dsp_page, $events, $year, $month, $day) {
    $repeating_events = array();
    if ($dsp_page == 'all') {
        return $events;
    }
    foreach ($events as $date => $vals) {
        foreach ($vals as $hour => $atts_array) {
            foreach ($atts_array as $min => $atts_list) {
                foreach($atts_list as $i => $atts) {
                    if ($atts['repeat'] > 0) {
                        $new_events = $atts;
                        $new_events['date'] = $date;
                        $new_events['minute'] = $min;
                        $new_events['hour'] = $hour;
                        $repeating_events[] = $new_events;
                        unset($events[$date][$hour][$min][$i]);
                        if (empty($events[$date][$hour][$min])) {
                            unset($events[$date][$hour][$min]);
                            if (empty($events[$date][$hour])) {
                                unset($events[$date][$hour]);
                                if (empty($events[$date])) {
                                    unset($events[$date]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $new_events = array();
    foreach ($repeating_events as $vals) {
        $rmonth = substr($vals['date'], 5, 2);
        $ryear = substr($vals['date'], 0, 4);
        $rday = substr($vals['date'], 8, 2);
        $rweek = date('w', mktime(0, 0, 0, $rmonth, $rday, $ryear));
        switch ($vals['repeat']) {
            case 1: // yearly
                $vals['date'] = $year.substr($vals['date'], 4);
                $new_events[] = $vals;
                break;
            case 2: // monthly
                if ($dsp_page == 'calendar_year') {
                    for ($i=1;$i<13;$i++) {
                        $vals['date'] = $year.'-'.sprintf('%02u', $i).'-'.$rday;
                        $new_events[] = $vals;
                    }
                }
                else {
                    $vals['date'] = $year.'-'.$month.'-'.$rday;
                    $new_events[] = $vals;
                }
                break;
            case 3: // weekly
                if ($dsp_page == 'calendar_year') {
                    for ($i=1;$i<13;$i++) {
                        $end_day = date('j', mktime(0,0,0,($i+1),0,$year));
                        for ($n=1;$n<=$end_day;$n++) {
                            if ($rweek == date('w', mktime(0,0,0,$i,$n,$year))) {
                                $vals['date'] = $year.'-'.sprintf('%02u', $i).'-'.sprintf('%02u', $n);
                                $new_events[] = $vals;
                            }
                        }
                    }
                }
                else {
                    $end_day = date('j', mktime(0,0,0,($month+1),0,$year));
                    for ($n=1;$n<=$end_day;$n++) {
                        if ($rweek == date('w', mktime(0,0,0,$month,$n,$year))) {
                            $vals['date'] = $year.'-'.sprintf('%02u', $month).'-'.sprintf('%02u', $n);
                            $new_events[] = $vals;
                        }
                    }
                }
                break;
            case 4: // daily
                if ($dsp_page == 'calendar_year') {
                    for ($i=1;$i<13;$i++) {
                        $end_day = date('j', mktime(0,0,0,($i+1),0,$year));
                        for ($n=1;$n<=$end_day;$n++) {
                            $vals['date'] = $year.'-'.sprintf('%02u', $i).'-'.sprintf('%02u', $n);
                            $new_events[] = $vals;
                        }
                    }
                }
                else {
                    $end_day = date('j', mktime(0,0,0,($month+1),0,$year));
                    for ($n=1;$n<=$end_day;$n++) {
                        $vals['date'] = $year.'-'.sprintf('%02u', $month).'-'.sprintf('%02u', $n);
                        $new_events[] = $vals;
                    }
                }
                break;
        }
    }
    foreach ($new_events as $vals) {
        $events[$vals['date']][$vals['hour']][$vals['minute']][] = $vals;
    }
    return $events;
}

/* add an event to the database */
function add_cal_event($atts, $tools) {
    $id = 0;
    if ($tools->get_db()) {
        $date_str = intval($atts['year']).'-'.intval($atts['month']).'-'.intval($atts['day']).' '.intval($atts['event_time']).':'.intval($atts['event_time2']).':00';
        if ($atts['duration']) {
            if ($atts['duration2']) {
                $duration = intval($atts['duration']) + ($atts['duration2']/60);
            }
            else {
                $duration = $atts['duration'];
            }
        }
        else {
            $duration = 0;
        }
        $sql = 'insert into calendar values(default, '.
            $tools->db_quote($tools->username).', '.
            $tools->db_quote($date_str).', '.
            floatval($duration).', '.
            $tools->db_quote($atts['title']).', '.
            $tools->db_quote($atts['detail']).', '.
            intval($atts['repeat']).')';
        $res = $tools->db_insert($sql);
        if ($res) {
            $sql = 'select id from calendar order by id DESC limit 1';
            $id = $tools->db_query_one($sql);
        }
    }
    return $id;
}

/* get a single calendar event details */
function get_cal_event($tools, $id) {
    $vals = array();
    if ($tools->get_db()) {
        $event_time = false;
        $event_time2 = false;
        $duration = false;
        $duration2 = false;
        $year = false;
        $month = false;
        $day = false;
        $repeat = false;
        $detail = false;
        $title = false;
        $sql = 'select * from calendar where id='.intval($id);
        $res = $tools->db_query($sql);
        if (isset($res[0]) && is_array($res[0])) {
            $date = $res[0]['datetime'];
            if (strpos($date, ' ')) {
                list ($date, $event_time) = split(' ', $date);
            }
            else {
                $event_time = false;
            }
            if (preg_match("/(\d{4}-\d{2}-\d{2})/", $date, $matches)) {
                list($year, $month, $day) = split('-', $matches[1]);
            }
            if ($event_time) {
                if (strpos($event_time, ':')) {
                    $time_parts = split(':', $event_time);
                    if (isset($time_parts[0])) {
                        $event_time = $time_parts[0];
                    }
                    if (isset($time_parts[1])) {
                        $event_time2 = $time_parts[1];
                    }
                }
            }
            $title = $res[0]['title'];
            $repeat = $res[0]['repeat_val'];
            $detail = $res[0]['description'];
            $duration = $res[0]['duration'];
            if ($duration != intval($duration) && strpos($duration, '.')) {
                $duration_parts = explode('.', $duration);
                $duration2 = ('.'.$duration_parts[1])*60;
                $duration = $duration_parts[0];
            }
        }
    }
    return array(intval($year), $month, $day, $event_time, $event_time2, $duration, $duration2, $title, $detail, $repeat);
}

/* update an event in the db */
function update_event($tools, $atts) {
    $res = false;
    if ($tools->get_db()) {
        $date_str = intval($atts['year']).'-'.intval($atts['month']).'-'.intval($atts['day']).' '.intval($atts['event_time']).':'.intval($atts['event_time2']).':00';
        if ($atts['duration']) {
            if ($atts['duration2']) {
                $duration = intval($atts['duration']) + ($atts['duration2']/60);
            }
            else {
                $duration = $atts['duration'];
            }
        }
        else {
            $duration = 0;
        }
        $sql = 'update calendar set '.
            'datetime='.$tools->db_quote($date_str).', '.
            'duration='.floatval($duration).', '.
            'title='.$tools->db_quote($atts['title']).', '.
            'description='.$tools->db_quote($atts['detail']).', '.
            'repeat_val='.intval($atts['repeat']).' where id='.intval($atts['event_id']).' and username='.
            $tools->db_quote($tools->username);
        $res = $tools->db_update($sql);
    }
    return $res;
}

/* build data for add/edit form dropdowns */
function return_form_data($tools) {
    $repeat = array(
        22 => 0, 23 => 1, 24 => 2, 25 => 3, 26 => 4
    );
    $months = array(
        2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 5,
        7 => 6, 8 => 7, 9 => 8, 10 => 9, 11 => 10,
        12 => 11, 13 => 12,
    );
    $time_ints = array(
        '00', '15', '30', '45',
    );
    return array($repeat, $months, $time_ints);
}

/* delete an event from the db */
function delete_event($tools, $id) {
    $res = false;
    if ($tools->get_db()) {
        $sql = 'delete from calendar where username='.$tools->db_quote($tools->username).' and id='.$id;
        $res = $tools->db_delete($sql);
    }
    return $res;
}

/* ---------------- PRINT FUNCTIONS ---------------------------------*/

/* print out a yearly view of the calendar. Called by print_calendar */
function print_calendar_year($page_data, $tools) {
    $theme = 'default';
    global $conf;
    if (isset($_SESSION['user_settings']['theme'])) {
        $user_theme = $_SESSION['user_settings']['theme'];
        if (isset($conf['site_themes'][$user_theme])) {
            if ($conf['site_themes'][$user_theme]['css']) {
                $theme = $user_theme;
            }    
        }
    }
    $img_path = 'themes/'.$theme.'/images';
    $year = $page_data['cal_data']['year'];
    $data = '<div>';
    $data .= '<h2 id="mailbox_title2">Calendar</h2>';
    $data .= '<div class="cal_links"><a href="?page=calendar&amp;year='.($year - 1).
             '" title="'.($year - 1).'"><img border="0" src="'.$img_path.'/prev.png" alt="Previous" /></a> ';
    $data .= '&#160;'.$year.'&#160; ';
    $data .= '<a href="?page=calendar&amp;year='.($year + 1).'" title="'.($year + 1).
             '"><img border="0" src="'.$img_path.'/next.png" alt="Next" /></a></div>';
    $data .= '<table class="cal_year" cellpadding="0" cellspacing="0"><tr>';
    for ($i=1;$i<13;$i++) {
        $last_day = date('d', mktime(0, 0, 0, ($i + 1), 0, $year));
        $month_label = date('F', mktime(0, 0, 0, $i, 1, $year));
        $first_week_day = date('w', mktime(0, 0, 0, ($i), 1, $year));
        if ($first_week_day + $last_day > 36) {
            $final_week = 6;
        }
        elseif ($first_week_day == 0 && $last_day == 28) {
            $final_week = 4;
        }
        else {
            $final_week = 5;
        }
        $vals['year'] = $year;
        $vals['final_week'] = $final_week;
        $vals['first_week_day'] = $first_week_day;
        $vals['last_day'] = $last_day;
        $vals['year'] = $year;
        $vals['month'] = $i;
        $vals['today'] = $page_data['cal_data']['today'];
        $vals['month_label'] = $month_label;
        
        $data .= '<td>'.print_mini_month($vals, $page_data['cal_data']['events'], $tools).'</td>';
        if ($i != 12 && $i % 2 == 0) {
            $data .= '</tr><tr>';
        }
    }
    $data .= '</tr></table></div>';
    return $data;
}

/* used by print_calendar_year to print out each month of the year */
function print_mini_month($cal_data, $events, $tools) {
    $data = '<div class="mini_month">';
    $data .= '<br /><table class="mini_month" cellpadding="0" cellspacing="0">';
    $days = false;
    $day_num = 1;
    $data .= '<tr><th colspan="8"><a href="?page=calendar&amp;year='.$cal_data['year'].
             '&amp;month='.strtolower($cal_data['month_label']).'">'.
             $cal_data['month_label'].'</a><br /></th></tr>';
    for ($row = 1; $row < 7; $row++) {
        $data .= '<tr><td valign="top"><a href="?page=calendar&amp;year='.$cal_data['year'].
                 '&amp;month='.strtolower($cal_data['month_label']).'&amp;week=week'.$row.'">'.
                 'week '.$row.' </a></td>';
        for ($col=1; $col < 8; $col++) {
            if (!$days) {
                if ($col > $cal_data['first_week_day']) {
                    $days = true;
                }
            }
            if ($days) {
                if ($day_num <= $cal_data['last_day']) {
                    $event_str = '';
                    if (isset($events[$cal_data['year'].'-'.sprintf('%02u', $cal_data['month']).'-'.sprintf('%02u', $day_num)])) {
                        $event = $events[$cal_data['year'].'-'.sprintf('%02u', $cal_data['month']).'-'.sprintf('%02u', $day_num)];
                        if (!empty($events)) {
                            $event_str .= '<a class="year_event" href="?page=calendar&amp;year='.$cal_data['year'].'&amp;month='.$cal_data['month_label'].'&amp;day='.$day_num.'">&nbsp;</a>';
                        }
                    }
                    $data .= '<td>';
                    if ($event_str) {
                        $data .= '<div class="cal_mini_month_day2">';
                    }
                    else {
                        $data .= '<div class="cal_mini_month_day">';
                    }
                    $data .= '<a href="?page=calendar&amp;year='.$cal_data['year'].'&amp;month='.strtolower($cal_data['month_label']).'&amp;day='.$day_num.'" ';
                    if ($cal_data['month'].'-'.sprintf('%02u', $day_num).'-'.$cal_data['year'] == $cal_data['today']) {
                        $data .= ' style="font-weight: bold; font-size: 115%;"';
                    }
                    $data .= '>'.$day_num.'</a>';
                    $data .= $event_str.'</div>';
                }
                else {
                    $data .= '<td>';
                }
                $day_num++;
            }
            else {
                $data .= '<td>';
            }
            $data .= '</td>';
        }
        $data .= '</tr>';
        if ($row == $cal_data['final_week']) {
            break;
        }
    }
    $data .= '</table></div>';
    return $data;
}

/* print out a calendar month view. Called by print_calendar */
function print_calendar_month($page_data, $tools) {
    $theme = 'default';
    global $conf;
    if (isset($_SESSION['user_settings']['theme'])) {
        $user_theme = $_SESSION['user_settings']['theme'];
        if (isset($conf['site_themes'][$user_theme])) {
            if ($conf['site_themes'][$user_theme]['css']) {
                $theme = $user_theme;
            }    
        }
    }
    $img_path = 'themes/'.$theme.'/images';
    $cal_data = $page_data['cal_data'];
    $data = '<div>';
    $data .= '<h2 id="mailbox_title2">Calendar</h2>';
    $data .= '<div class="cal_links"><table cellpadding="0" width="100%" cellspacing="0"><tr><td>
              <a href="'.$cal_data['prev_month_url'].'" title="'.ucfirst($cal_data['prev_month']).'"><img src="'.$img_path.'/prev.png" alt="Previous" border="0" /></a> ';
    $data .= '&#160;'.ucfirst($cal_data['month_label']).'&#160; ';
    $data .= '<a href="'.$cal_data['next_month_url'].'" title="'.ucfirst($cal_data['next_month']).'"><img src="'.$img_path.'/next.png" alt="Next" border="0" /></a>';
    $data .= '</td><td align="right"><a href="?page=calendar&amp;year='.$cal_data['year'].'">'.$cal_data['year'].
             '</a></td></tr></table></div>';
    $data .= '<table class="cal_month" width="100%" cellpadding="0" cellspacing="0"><tr><th></th>';
    for ($i=1;$i<8;$i++) {
        $data .= '<th><div class="cal_month_heading">'.date('l', mktime(0, 0, 0, 4, $i, 2007)).'</div></th>';
    }
    $data .= '</tr>'; 
    $days = false;
    $day_num = 1;
    for ($row = 1; $row < 7; $row++) {
        $data .= '<tr><td valign="top"><div class="month_week_link"><a href="?page=calendar&amp;year='.$cal_data['year'].'&amp;month='.
                 strtolower($cal_data['month_label']).'&amp;week=week'.$row.'">'.
                 'week '.$row.' </a></div></td>';
        for ($col=1; $col < 8; $col++) {
            if (!$days) {
                if ($col > $cal_data['first_week_day']) {
                    $days = true;
                }
            }
            if ($days) {
                if ($day_num <= $cal_data['last_day']) {
                    $data .= '<td>';
                    $data .= '<a href="?page=calendar&amp;year='.$cal_data['year'].'&amp;month='.
                             strtolower($cal_data['month_label']).'&amp;day='.$day_num.'"';
                    if ($cal_data['month'].'-'.sprintf('%02u', $day_num).'-'.$cal_data['year'] == $cal_data['today']) {
                        $data .= ' style="font-weight: bold; font-size: 115%;"';
                    }
                    $data .= '>'.$day_num.'</a>';
                    if (isset($cal_data['events'][$cal_data['year'].'-'.$cal_data['month'].'-'.sprintf('%02u', $day_num)])) {
                        $event = $cal_data['events'][$cal_data['year'].'-'.$cal_data['month'].'-'.sprintf('%02u', $day_num)];
                        $data .= '<div class="month_event">';
                        foreach ($event as $index => $atts_array) {
                            foreach ($atts_array as $time => $atts_list) {
                                foreach ($atts_list as $atts) {
                                    $data .= '<span class="time">'.$tools->display_safe($index.':'.$time).'</span> ';
                                    $data .= '<a href="?page=calendar&amp;edit_event='.
                                             intval($atts['id']).'">'.$tools->display_safe($atts['title']).'</a><br />';
                                }
                            }
                        }
                        $data .= '</div>';
                    }
                }
                else {
                    $data .= '<td>';
                }
                $day_num++;
            }
            else {
                $data .= '<td>';
            }
            $data .= '</td>';
        }
        $data .= '</tr>';
        if ($row == $cal_data['final_week']) {
            break;
        }
    }
    $data .= '</table></div>';
    return $data;
}

/* print out a calendar week view. Called by print_calendar */
function print_calendar_week($page_data, $tools) {
    $theme = 'default';
    global $conf;
    if (isset($_SESSION['user_settings']['theme'])) {
        $user_theme = $_SESSION['user_settings']['theme'];
        if (isset($conf['site_themes'][$user_theme])) {
            if ($conf['site_themes'][$user_theme]['css']) {
                $theme = $user_theme;
            }    
        }
    }
    $img_path = 'themes/'.$theme.'/images';
    $vals = $page_data['cal_data'];
    $start_day = 1;
    if ($vals['week'] > 1) {
        $start_day += (7*($vals['week'] - 1)) - $vals['first_week_day'];
    }
    $data = '<div>';
    $data .= '<h2 id="mailbox_title2">Calendar</h2>';
    $data .= '<div class="cal_links"><table cellpadding="0" width="100%" cellspacing="0"><tr><td>';
    $data .= '<a href="'.$page_data['last_url'].'"><img src="'.$img_path.'/prev.png" alt="Previous" border="0" title="Previous week" /></a>';
    $data .= '&#160; Week '.$vals['week'].' <span style="font-size: 10pt; font-weight: normal;">of </span>';
    $data .= '<a href="?page=calendar&amp;year='.$vals['year'].'&amp;month='.strtolower($vals['month_label']).'">'.
             ucfirst($vals['month_label']).'</a>&#160; ';
    $data .= '<a href="'.$page_data['next_url'].'"><img src="'.$img_path.'/next.png" alt="Next" border="0" title="Next week" /></a>';
    $data .= '</td><td align="right"><a href="?page=calendar&amp;year='.$vals['year'].'">'.$vals['year'].
             '</a></td></tr></table></div>';
    $data .= '<table cellpadding="0" cellspacing="0" class="cal_month" width="100%"><tr>';
    for ($i=1;$i<8;$i++) {
        $data .= '<th><div class="cal_week_heading">'.date('l', mktime(0, 0, 0, 4, $i, 2007)).'</div></th>';
    }
    $data .= '</tr><tr>';
    for ($i=1;$i<8;$i++) {
        if ($start_day > $vals['last_day']) {
            $data .= '<td><div class="cal_month_week_day"></div></td>';
            continue;
        }
        if (($vals['week'] == 1 && $i > $vals['first_week_day']) || $vals['week'] != 1) {
            $data .= '<td><div class="cal_month_week_day"><a ';
            if ($vals['month'].'-'.sprintf('%02u', $start_day).'-'.$vals['year'] == $vals['today']) {
                $data .= ' style="font-weight: bold; font-size: 115%;" ';
            }
            $data .= 'href="?page=calendar&amp;year='.$vals['year'].'&amp;month='.strtolower($vals['month_label']).
                     '&amp;day='.$start_day.'">'.$start_day.'</a>';
            if (isset($vals['events'][$vals['year'].'-'.$vals['month'].'-'.sprintf('%02u', $start_day)])) {
                $event = $vals['events'][$vals['year'].'-'.$vals['month'].'-'.sprintf('%02u', $start_day)];
                foreach ($event as $index => $atts_array) {
                    foreach ($atts_array as $time => $atts_list) {
                        foreach ($atts_list as $atts) {
                            $data .= '<div class="week_event">';
                            $data .= '<span class="time">'.$tools->display_safe($index.':'.$time).'</span> ';
                            $data .= '<a href="?page=calendar&amp;edit_event='.intval($atts['id']).'">'.$tools->display_safe($atts['title']).'</a></div>';
                        }
                    }
                }
            }
            $data .= '</div></td>';
            $start_day++;
        }
        else {
            $data .= '<td><div class="cal_month_week_day"></div></td>';
        }
    }
    $data .= '</tr></table>';
    $data .= '</div>';
    return $data;
}

/* print out a calender day view. Called by print_calendar */
function print_calendar_day($page_data, $tools) {
    $theme = 'default';
    global $conf;
    if (isset($_SESSION['user_settings']['theme'])) {
        $user_theme = $_SESSION['user_settings']['theme'];
        if (isset($conf['site_themes'][$user_theme])) {
            if ($conf['site_themes'][$user_theme]['css']) {
                $theme = $user_theme;
            }    
        }
    }
    $img_path = 'themes/'.$theme.'/images';
    $vals = $page_data['cal_data'];
    $events = $vals['events'];
    $week_day = date("l", mktime(0, 0, 0, $vals['month'], $vals['day'], $vals['year']));
    $data = '<div>';
    $data .= '<h2 id="mailbox_title2">Calendar</h2>';
    $data .= '<div class="cal_links">';
    $data .= '<a href="'.$page_data['last_url'].'"><img src="'.$img_path.'/prev.png" alt="Previous" border="0" title="Previous day" /></a>';
    $data .= '&#160;&#160;'.$week_day.' '.'<a href="?page=calendar&amp;year='.$vals['year'].
             '&amp;month='.strtolower($vals['month_label']).'">'.
              ucfirst($vals['month_label']).'</a> '.$vals['day'].
             ', <a href="?page=calendar&amp;year='.$vals['year'].'">'.$vals['year'].'</a>&#160;&#160;';
    $data .= '<a href="'.$page_data['next_url'].'"><img src="'.$img_path.'/next.png" alt="Next" border="0" title="Next day" /></a>';
    $data .= '</div>';
    $data .= '<table cellpadding="0" width="100%" cellspacing="0">';
    for ($i=1;$i<25;$i++) {
        $data .= '<tr><td class="cal_time">'.$i.':00</td><td><div class="cal_day">';
        if (isset($vals['events'][$vals['year'].'-'.$vals['month'].'-'.sprintf('%02u', $vals['day'])])) {
            $event = $vals['events'][$vals['year'].'-'.$vals['month'].'-'.sprintf('%02u', $vals['day'])];
            if (isset($event[sprintf('%02u', $i)])) {
                $atts_array = $event[sprintf('%02u', $i)];
                foreach ($atts_array as $time => $atts_list) {
                    foreach ($atts_list as $atts) {
                        $data .= '<div class="week_event">';
                        $data .= '<a class="day_event_title" href="?page=calendar&amp;edit_event='.intval($atts['id']).'">';
                        $data .= '<span class="time">'.$tools->display_safe($i.':'.$time).'</span> ';
                        $data .= $tools->display_safe($atts['title']).'</a>'.$tools->display_safe($atts['description']).'</div>';
                    }
                }
            }
        }
        $data .= '</div></td></tr>';
    }
    $data .= '</table>';
    $data .= '</div>';
    return $data;
}

/* print a "page not found" page */
function print_calendar_page_not_found($tools) {
    $data = '<div id="cal_not_found">'.$tools->str[15].'</div>';
    return $data;
}

/* print the add/event form */
function print_event_form($tools, $month=false, $year=false, $day=false, $title=false, $detail=false, $repeat=false,
                          $time=false, $time2=false, $duration=false, $duration2=false, $dsp_page='add', $event_id=0) {
    if (!$year) {
        $year = date("Y");
    }
    list($repeats, $months, $time_ints) = return_form_data($tools);
    $data = '<div class="event_form"><h2 id="mailbox_title2">';
    if ($dsp_page == 'add') {
        $data .= 'Add Event';
    }
    else {
        $data .= 'Edit Event';
    }
    $data .= '</h2>';
    $data .= '<form method="post" action="">';
    $data .= '<table id="event_table">';
    $data .= '<tr><td>Date</td><td>';
    $data .= '<select name="month">';
    foreach ($months as $i => $v) {
        $data .= '<option ';
        if ($v == $month) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$v.'">'.$tools->str[$i].'</option>';
    }
    $data .= '</select> ';
    $data .= '<select name="day">';
    for ($i=1;$i< 32;$i++) {
        $data .= '<option ';
        if ($i == $day) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$i.'</option>';
    }
    $data .= '</select> ';
    $data .= '<select name="year">';
    for ($i=($year - 10);$i<($year + 10);$i++) {
        $data .= '<option ';
        if ($i == $year) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$i.'</option>';
    }
    $data .= '</select> ';
    $data .= '</td></tr><tr><td>Time</td><td><select name="event_time">';
    for ($i = 1; $i < 24; $i++) {
        $data .= '<option ';
        if ($i == $time) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$i.'</option>';
    }
    $data .= '</select>';
    $data .= ' <select name="event_time2">';
    foreach ($time_ints as $v) {
        $data .= '<option ';
        if ($v == $time2) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$v.'">'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td>Duration</td><td><select name="duration">';
    for ($i=0;$i<25;$i++) {
        $data .= '<option ';
        if ($i == $duration) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$i.'">'.$i.'</option>';
    }
    $data .= '</select> <select name="duration2">';
    foreach ($time_ints as $v) {
        $data .= '<option ';
        if ($v == $duration2) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$v.'">'.$v.'</option>';
    }
    $data .= '</select></td></tr>';
    $data .= '<tr><td>'.$tools->str[19].'</td><td><input type="text" size="60" name="title" value="'.$tools->display_safe($title).'"/></td></tr>';
    $data .= '<tr><td>'.$tools->str[20].'</td><td><textarea cols="60" rows="10" name="detail">'.$tools->display_safe($detail).'</textarea></td></tr>';
    $data .= '<tr><td>'.$tools->str[21].'</td><td><select name="repeat">';
    foreach ($repeats as $i => $v) {
        $data .= '<option ';
        if ($v == $repeat) { $data .= 'selected="selected" '; }
        $data .= 'value="'.$v.'">'.$tools->str[$i].'</option>';
    }
    $data .= '</select></td></tr>';
    if ($dsp_page == 'add') {
        $data .= '<tr><td align="right" colspan="2"><br /><input type="submit" name="calendar_add" value="'.$tools->str[27].'" /></td></tr>';
        $data .= '<tr><td colspan="2"><a href="?page=calendar">Back to calendar</a></td></tr>';
    }
    else {
        $data .= '<tr><td align="right" colspan="2"><input type="hidden" name="event_id" value="'.$event_id.'" /><br /><input type="submit" name="calendar_update" value="Update" />
                  <input type="submit" name="calendar_delete" value="Delete" /></td></tr>';
        $data .= '<tr><td colspan="2"><a href="?page=calendar">Back to calendar</a></td></tr>';
    }
    $data .= '</table></form></div>';
    return $data;
}

function list_all_events($events, $tools) {
    $data = '<div><h2 id="mailbox_title2">All Events</h2><div style="clear: both;">';
    list($repeats, $months, $time_ints) = return_form_data($tools);
    if (!empty($events)) {
        foreach ($events as $date => $vals) {
            foreach ($vals as $time => $atts_array) {
                foreach ($atts_array as $atts_list) {
                    foreach ($atts_list as $atts) {
                        $data .= '<div class="list_title"><a href="?page=calendar&amp;edit_event='.$atts['id'].'">'.$tools->display_safe($atts['title']).'</a></div>';
                        $data .= '<div class="list_meta"><span class="list_date">'.$tools->display_safe($date).'</span>';
                        $data .= ' '.$time.', &nbsp;Duration: '.$atts['duration'].', &nbsp;Repeat: ';
                        $repeat = false;
                        foreach ($repeats as $i => $v) {
                            if ($v == $atts['repeat']) {
                                $data .= $tools->str[$i];
                                $repeat = true;
                                break;  
                            }
                        }
                        if (!$repeat) {
                            $data .= 'None';
                        }
                        $data .= '</div>';
                        $data .= '<div class="list_detail">'.$tools->display_safe($atts['description']).'</div>';
                    }
                }
            }
        }
    }
    else {
        $data .= '<div class="empty_list">No Events Found<br /><a href="?page=calendar&amp;add_event=1">Add Event</a></div>';
    }
    $data .= '</div></div></div>';
    return $data;
}
?>
