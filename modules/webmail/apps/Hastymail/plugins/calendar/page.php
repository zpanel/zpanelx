<?php

/*  page.php: Plugin file responsible for handling plugin specific pages 
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

/* main work function that parses user input (POST/GET) */
function url_action_calendar($tools, $get, $post) {
    require_once($tools->include_path.'cal_include.php');
    if (!$tools->logged_in()) {
        $tools->page_not_found();
    }
    /* get the current mailbox if any */
    $mailbox = $tools->get_mailbox();
    
    /* set the current mailbox */
    if ($mailbox) {
        $tools->set_mailbox($mailbox);
    }

    /* default values */
    $page_data = array();
    $week = false;
    $month = false;
    $today = date('m-d-Y');
    $year = false;
    $title = '';
    $detail = '';
    $repeat = 0;
    $duration = 0;
    $event_time = 0;
    $month_label = false;
    $day = false;
    $last_day = false;
    $events = array();
    $duration = '';
    $duration2 = '';
    $event_time = '';
    $event_time2 = '';
    $first_week_day = false;
    $all_events = array();
    $edit_id = 0;
    $dsp_page = 'calendar_month';
    $final_week = false;

    if (isset($post['calendar_add'])) {
        $req_flds = array('title', 'year', 'month', 'day');
        $opt_flds = array('repeat', 'detail', 'event_time', 'event_time2', 'duration', 'duration2');
        $cal_atts = normalize_input($req_flds, $opt_flds, $post);
        $cnt = count($req_flds) + count($opt_flds);
        if (count($cal_atts) == $cnt) {
            $edit_id = add_cal_event($cal_atts, $tools);
            if ($edit_id) {
                $tools->send_notice('Event Added');
                $dsp_page = 'edit';
            }
            else {
                $tools->send_notice('An error occured adding this event');
                $dsp_page = 'add';
            }
        }
        else {
            $dsp_page = 'add';        
            foreach ($req_flds as $v) {
                if (isset($cal_atts[$v])) {
                    $$v = $cal_atts[$v];
                }
            }
            foreach ($opt_flds as $v) {
                if (isset($cal_atts[$v])) {
                    $$v = $cal_atts[$v];
                }
            }
        }
    }
    elseif (isset($post['calendar_update'])) {
        if (isset($post['event_id']) && $event_id = $post['event_id']) {
            $edit_id =  $post['event_id'];
            $dsp_page = 'edit';
            $req_flds = array('title', 'year', 'month', 'day', 'event_id');
            $opt_flds = array('repeat', 'detail', 'event_time', 'event_time2', 'duration', 'duration2');
            $cal_atts = normalize_input($req_flds, $opt_flds, $post);
            $cnt = count($req_flds) + count($opt_flds);
            if (count($cal_atts) == $cnt) {
                $res = update_event($tools, $cal_atts);
                if ($res) {
                    $tools->send_notice('Event Updated');
                }
            }
        }
    }
    elseif (isset($post['calendar_delete'])) {
        if (isset($post['event_id']) && $del_id = intval($post['event_id'])) {
            if (delete_event($tools, $del_id)) {
                calendar_init($tools);
                $tools->send_notice('Event Deleted'); 
                $dsp_page = 'calendar_month';
                $month = date('m');
                $year = date('Y');
                $month_label = strtolower(date('F'));
                $last_day = date('d', mktime(0, 0, 0, ($month + 1), 0, $year));
                $first_week_day = date('w', mktime(0, 0, 0, ($month), 1, $year));
                if ($first_week_day + $last_day > 36) {
                    $final_week = 6;
                }
                elseif ($first_week_day == 0 && $last_day == 28) {
                    $final_week = 4;
                }
                else {
                    $final_week = 5;
                }
            }
            else {
                $edit_id = $del_id;
                send_notice('Could not delete event');
            }
        }
    }
    elseif (isset($get['list_events'])) {
        $dsp_page = 'list_events';
        $all_events = get_calendar_events(false, false, false, 'all', $tools);
    }
    elseif (isset($get['add_event'])) {
        $dsp_page = 'add';        
        foreach (array('year', 'month', 'day') as $v) {
            if (isset($get[$v])) {
                $$v = $get[$v];
            }
        }
    }
    elseif (isset($get['edit_event'])) {
        $dsp_page = 'edit';
        $edit_id = intval($get['edit_event']);
        foreach (array('year', 'month', 'day') as $v) {
            if (isset($get[$v])) {
                $$v = $get[$v];
            }
        }
    }
    elseif (isset($get['year']) && $get['year']) {
        if (preg_match("/^\d{4}$/", $get['year'])) {
            $year = $get['year'];
            $dsp_page = 'calendar_year';

            /* check for month in URL args */

            if (isset($get['month']) && $get['month']) {
                $month = strtotime($get['month']);
                if ($month && $month != -1) {
                    $month_label = $get['month'];
                    $month = date('m', $month);
                    $dsp_page = 'calendar_month';
                    $last_day = date('d', mktime(0, 0, 0, ($month + 1), 0, $year));
                    $first_week_day = date('w', mktime(0, 0, 0, ($month), 1, $year));
                    if ($first_week_day + $last_day > 36) {
                        $final_week = 6;
                    }
                    elseif ($first_week_day == 0 && $last_day == 28) {
                        $final_week = 4;
                    }
                    else {
                        $final_week = 5;
                    }

                    /* check for week in the URL args */

                    if (isset($get['week']) && $get['week']) {
                        if (preg_match("/^week([1-$final_week])$/", $get['week'], $matches)) {
                            $dsp_page = 'calendar_week';
                            $week = $matches[1]; 
                            if ($week == 1) {
                                $last_month = strtolower(date('F', mktime(0, 0, 0, ($month - 1), 1, $year)));
                                $last_int_month = strtolower(date('m', mktime(0, 0, 0, ($month - 1), 1, $year)));
                                $last_year = date('Y', mktime(0, 0, 0, ($month - 1), 1, $year));
                                $last_last_day = date('d', mktime(0, 0, 0, ($last_int_month + 1), 0, $last_year));
                                $last_month_first_week_day = date('w', mktime(0, 0, 0, ($last_int_month), 1, $last_year));
                                if ($last_month_first_week_day + $last_last_day > 36) {
                                    $last_final_week = 6;
                                }
                                elseif ($first_week_day == 0 && $last_day == 28) {
                                    $last_final_week = 4;
                                }
                                else {
                                    $last_final_week = 5;
                                }
                                $last_url = '?page=calendar&amp;year='.$last_year.'&amp;month='.$last_month.'&amp;week=week'.$last_final_week;
                                $next_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;week=week2';
                            }
                            elseif ($week == $final_week) {
                                $next_month = strtolower(date('F', mktime(0, 0, 0, ($month + 1), 1, $year)));
                                $next_year = date('Y', mktime(0, 0, 0, ($month + 1), 1, $year));
                                $next_url = '?page=calendar&amp;year='.$next_year.'&amp;month='.$next_month.'&amp;week=week1';
                                $last_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;week=week'.($week - 1);
                            }
                            else {
                                $next_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;week=week'.($week + 1);
                                $last_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;week=week'.($week - 1);
                            }
                            $page_data['last_url'] = $last_url;
                            $page_data['next_url'] = $next_url;
                        }
                    }

                    /* check for day in the URL args */

                    elseif (isset($get['day']) && $get['day'] > 0 && $get['day'] <= $last_day) {
                        $dsp_page = 'calendar_day';
                        $day = $get['day'];
                        if ($day == $last_day) {
                            $last_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;day='.($day - 1);
                            if ($month == 12) {
                                $next_url = '?page=calendar&amp;year='.($year + 1).'&amp;month=january&amp;day=1';
                            }
                            else {
                                $next_month = strtolower(date("F", mktime(0, 0, 0, ($month + 1), 1, $year)));
                                $next_url = '?page=calendar&amp;year='.$year.'&amp;month='.$next_month.'&amp;day=1';
                            }
                        }
                        elseif ($day == 1) {
                            if ($month == 1) {
                                $last_url = '?page=calendar&amp;year='.($year - 1).'&amp;month=december&amp;day=31';
                            }
                            else {
                                $last_month = strtolower(date("F", mktime(0, 0, 0, ($month - 1), 1, $year)));
                                $last_day = date("t", mktime(0, 0, 0, ($month - 1), 1, $year));
                                $last_url = '?page=calendar&amp;year='.$year.'&amp;month='.$last_month.'&amp;day='.$last_day;
                            }
                            $next_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;day=2';
                        }
                        else {
                            $last_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;day='.($day - 1);
                            $next_url = '?page=calendar&amp;year='.$year.'&amp;month='.$month_label.'&amp;day='.($day + 1);
                        }
                        $page_data['last_url'] = $last_url;
                        $page_data['next_url'] = $next_url;
                    }
                }
            }
        }
    }
    /* Default to month view of the current month if no URL args are present */
    else {
        $dsp_page = 'calendar_month';
        $month = date('m');
        $year = date('Y');
        $month_label = strtolower(date('F'));
        $last_day = date('d', mktime(0, 0, 0, ($month + 1), 0, $year));
        $first_week_day = date('w', mktime(0, 0, 0, ($month), 1, $year));
        if ($first_week_day + $last_day > 36) {
            $final_week = 6;
        }
        elseif ($first_week_day == 0 && $last_day == 28) {
            $final_week = 4;
        }
        else {
            $final_week = 5;
        }
    }
    if ($dsp_page != 'add' && $dsp_page != 'edit') {
        $events = get_calendar_events($month, $year, $day, $dsp_page, $tools);
    }
    if ($edit_id != 0 && $dsp_page == 'edit') {
        list($year, $month, $day, $event_time, $event_time2, $duration, $duration2, $title, $detail, $repeat) = get_cal_event($tools, $edit_id);
    }

    /* Build some more values for the display and stick everything into the $page_data array */

    $prev_month = strtolower(date('F', mktime(0, 0, 0, ($month - 1), 1, $year)));
    $prev_month_url = '?page=calendar&amp;year='.(date('Y', mktime(0, 0, 0, ($month - 1), 1, $year))).'&amp;month='.  $prev_month;
    $next_month = strtolower(date('F', mktime(0, 0, 0, ($month + 1), 1, $year)));
    $next_month_url = '?page=calendar&amp;year='.(date('Y', mktime(0, 0, 0, ($month + 1), 1, $year))).'&amp;month='.  $next_month;
    $page_data['cal_data'] = array(
        'events'            => $events,
        'all_events'        => $all_events,
        'month'             => $month,
        'year'              => $year,
        'day'               => $day,
        'week'              => $week,
        'last_day'          => $last_day, 
        'first_week_day'    => $first_week_day,
        'final_week'        => $final_week,
        'prev_month'        => $prev_month,
        'next_month'        => $next_month,
        'today'             => $today,
        'next_month_url'    => $next_month_url,
        'prev_month_url'    => $prev_month_url,
        'month_label'       => $month_label);

    $page_data['dsp_page']   = $dsp_page;
    $page_data['title']      = $title;
    $page_data['detail']     = $detail;
    $page_data['event_time'] = $event_time;
    $page_data['duration']   = $duration;
    $page_data['repeat']     = $repeat;
    $page_data['duration']   = $duration;
    $page_data['duration2']  = $duration2;
    $page_data['event_time'] = $event_time;
    $page_data['event_time2']= $event_time2;
    $page_data['edit_id']    = $edit_id;
    $tools->set_title($tools->str[1]);
    return $page_data;
}

/* main print function to output calendar pages */
function print_calendar($page_data, $tools) {
    require_once($tools->include_path.'cal_include.php');
    /* $page_data['dsp_page'] should be set to one of 4 values: calendar_month,
       calendar_year, calendar_week, or calendar_day.  Each has a corresponding
       function to build the XHTML for that view that is called from here */

    $cal = $page_data['cal_data'];
    $page = '';
    if (isset($page_data['dsp_page'])) {
        $links = '<div class="cal_links2"><a href="?page=calendar&amp;year='.date("Y").'&amp;month='.strtolower(date("F")).'&amp;day='.date("j").'">Today</a>';
        $links .= ' <a href="?page=calendar&amp;year='.date("Y").'&amp;month='.strtolower(date("F")).'&amp;week=week'.ceil((date("j") + $cal['first_week_day'])/7).'">This Week</a>';
        $links .= ' <a href="?page=calendar&amp;year='.date("Y").'&amp;month='.strtolower(date("F")).'">This Month</a>';
        $links .= ' <a href="?page=calendar&amp;year='.date("Y").'">This Year</a>
                  &nbsp;|&nbsp; <a href="?page=calendar&amp;add_event=1';
        if ($cal['year']) {
            $links .= '&amp;year='.$cal['year'];
        }
        if ($cal['month']) {
            $links .= '&amp;month='.$cal['month'];
        }
        if ($cal['day']) {
            $links .= '&amp;day='.$cal['day'];
        }
        $links .= '">'.$tools->str[14].'</a>';
        $links .= ' <a href="?page=calendar&amp;list_events=1">List All</a></div>';
        $page .= '<div id="calendar">'.$links;
        if ($page_data['dsp_page'] == 'add' || $page_data['dsp_page'] == 'edit') {
            $page .= print_event_form($tools, $cal['month'], $cal['year'], $cal['day'], $page_data['title'],
                $page_data['detail'], $page_data['repeat'], $page_data['event_time'], $page_data['event_time2'],
                $page_data['duration'], $page_data['duration2'], $page_data['dsp_page'], $page_data['edit_id']).'</div>';
        }
        elseif ($page_data['dsp_page'] == 'list_events') {
            $page .= list_all_events($cal['all_events'], $tools);
        }
        elseif (function_exists('print_'.$page_data['dsp_page'])) {
            $function_name = 'print_'.$page_data['dsp_page'];
            $page .= $function_name($page_data, $tools).'</div>';
        }
        else {
            $page .= print_calendar_page_not_found($tools);
        }
    }
    else {
        $page .= print_calendar_page_not_found($tools);
    }
    return $page;
}
?>
