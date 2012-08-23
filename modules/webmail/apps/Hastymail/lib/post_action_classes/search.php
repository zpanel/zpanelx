<?php

/*  post_action_class.php: Process POST forms
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
class fw_post_action_search extends fw_user_action_with_post {
function set_post_page_vars() {
    global $user;
    $forms = array(
    'reset_search' => array(
        'current_mailbox'        => array('string', 1, 'Current mailbox'),
    ),
    'more_search' => array(
        'search_max'   => array('int', 1, 'Search Criteria count'),
        'search_folders' => array('array', 0, 'Folders to Search'),
        'keywords_1_0' => array('string', 0, 'Keywords'),
        'search_fld_1_0' => array('int', 0, 'Message field'),
        'search_flags_1_0' => array('int', 0, 'Message flags'),
        'search_size_1_0' => array('int', 0, 'Message size comparison'),
        'search_size_2_0' => array('float', 0, 'Message size'),
        'search_size_3_0' => array('int', 0, 'Size units'),
        'search_date_1_0' => array('int', 0, 'Date comparison'),
        'search_date_2_0' => array('int', 0, 'Month'),
        'search_date_3_0' => array('int', 0, 'Day'),
        'search_date_4_0' => array('int', 0, 'Year'),

        'keywords_1_1' => array('string', 0, 'Keywords'),
        'and_or_1' => array('int', 0, 'and/or'),
        'search_fld_1_1' => array('int', 0, 'Message field'),
        'search_flags_1_1' => array('int', 0, 'Message flags'),
        'search_size_1_1' => array('int', 0, 'Message size comparison'),
        'search_size_2_1' => array('float', 0, 'Message size'),
        'search_size_3_1' => array('int', 0, 'Size units'),
        'search_date_1_1' => array('int', 0, 'Date comparison'),
        'search_date_2_1' => array('int', 0, 'Month'),
        'search_date_3_1' => array('int', 0, 'Day'),
        'search_date_4_1' => array('int', 0, 'Year'),

        'and_or_2' => array('int', 0, 'and/or'),
        'keywords_1_2' => array('string', 0, 'Keywords'),
        'search_fld_1_2' => array('int', 0, 'Message field'),
        'search_flags_1_2' => array('int', 0, 'Message flags'),
        'search_size_1_2' => array('int', 0, 'Message size comparison'),
        'search_size_2_2' => array('float', 0, 'Message size'),
        'search_size_3_2' => array('int', 0, 'Size units'),
        'search_date_1_2' => array('int', 0, 'Date comparison'),
        'search_date_2_2' => array('int', 0, 'Month'),
        'search_date_3_2' => array('int', 0, 'Day'),
        'search_date_4_2' => array('int', 0, 'Year'),

        'and_or_3' => array('int', 0, 'and/or'),
        'keywords_1_3' => array('string', 0, 'Keywords'),
        'search_fld_1_3' => array('int', 0, 'Message field'),
        'search_flags_1_3' => array('int', 0, 'Message flags'),
        'search_size_1_3' => array('int', 0, 'Message size comparison'),
        'search_size_2_3' => array('float', 0, 'Message size'),
        'search_size_3_3' => array('int', 0, 'Size units'),
        'search_date_1_3' => array('int', 0, 'Date comparison'),
        'search_date_2_3' => array('int', 0, 'Month'),
        'search_date_3_3' => array('int', 0, 'Day'),
        'search_date_4_3' => array('int', 0, 'Year'),

        'and_or_4' => array('int', 0, 'and/or'),
        'keywords_1_4' => array('string', 0, 'Keywords'),
        'search_fld_1_4' => array('int', 0, 'Message field'),
        'search_flags_1_4' => array('int', 0, 'Message flags'),
        'search_size_1_4' => array('int', 0, 'Message size comparison'),
        'search_size_2_4' => array('float', 0, 'Message size'),
        'search_size_3_4' => array('int', 0, 'Size units'),
        'search_date_1_4' => array('int', 0, 'Date comparison'),
        'search_date_2_4' => array('int', 0, 'Month'),
        'search_date_3_4' => array('int', 0, 'Day'),
        'search_date_4_4' => array('int', 0, 'Year'),
    ),
    'less_search' => array(
        'search_max'   => array('int', 1, 'Search Criteria count'),
        'search_folders' => array('array', 0, 'Folders to Search'),
        'keywords_1_0' => array('string', 0, 'Keywords'),
        'search_fld_1_0' => array('int', 0, 'Message field'),
        'search_flags_1_0' => array('int', 0, 'Message flags'),
        'search_size_1_0' => array('int', 0, 'Message size comparison'),
        'search_size_2_0' => array('float', 0, 'Message size'),
        'search_size_3_0' => array('int', 0, 'Size units'),
        'search_date_1_0' => array('int', 0, 'Date comparison'),
        'search_date_2_0' => array('int', 0, 'Month'),
        'search_date_3_0' => array('int', 0, 'Day'),
        'search_date_4_0' => array('int', 0, 'Year'),

        'keywords_1_1' => array('string', 0, 'Keywords'),
        'and_or_1' => array('int', 0, 'and/or'),
        'search_fld_1_1' => array('int', 0, 'Message field'),
        'search_flags_1_1' => array('int', 0, 'Message flags'),
        'search_size_1_1' => array('int', 0, 'Message size comparison'),
        'search_size_2_1' => array('float', 0, 'Message size'),
        'search_size_3_1' => array('int', 0, 'Size units'),
        'search_date_1_1' => array('int', 0, 'Date comparison'),
        'search_date_2_1' => array('int', 0, 'Month'),
        'search_date_3_1' => array('int', 0, 'Day'),
        'search_date_4_1' => array('int', 0, 'Year'),

        'and_or_2' => array('int', 0, 'and/or'),
        'keywords_1_2' => array('string', 0, 'Keywords'),
        'search_fld_1_2' => array('int', 0, 'Message field'),
        'search_flags_1_2' => array('int', 0, 'Message flags'),
        'search_size_1_2' => array('int', 0, 'Message size comparison'),
        'search_size_2_2' => array('float', 0, 'Message size'),
        'search_size_3_2' => array('int', 0, 'Size units'),
        'search_date_1_2' => array('int', 0, 'Date comparison'),
        'search_date_2_2' => array('int', 0, 'Month'),
        'search_date_3_2' => array('int', 0, 'Day'),
        'search_date_4_2' => array('int', 0, 'Year'),

        'and_or_3' => array('int', 0, 'and/or'),
        'keywords_1_3' => array('string', 0, 'Keywords'),
        'search_fld_1_3' => array('int', 0, 'Message field'),
        'search_flags_1_3' => array('int', 0, 'Message flags'),
        'search_size_1_3' => array('int', 0, 'Message size comparison'),
        'search_size_2_3' => array('float', 0, 'Message size'),
        'search_size_3_3' => array('int', 0, 'Size units'),
        'search_date_1_3' => array('int', 0, 'Date comparison'),
        'search_date_2_3' => array('int', 0, 'Month'),
        'search_date_3_3' => array('int', 0, 'Day'),
        'search_date_4_3' => array('int', 0, 'Year'),

        'and_or_4' => array('int', 0, 'and/or'),
        'keywords_1_4' => array('string', 0, 'Keywords'),
        'search_fld_1_4' => array('int', 0, 'Message field'),
        'search_flags_1_4' => array('int', 0, 'Message flags'),
        'search_size_1_4' => array('int', 0, 'Message size comparison'),
        'search_size_2_4' => array('float', 0, 'Message size'),
        'search_size_3_4' => array('int', 0, 'Size units'),
        'search_date_1_4' => array('int', 0, 'Date comparison'),
        'search_date_2_4' => array('int', 0, 'Month'),
        'search_date_3_4' => array('int', 0, 'Day'),
        'search_date_4_4' => array('int', 0, 'Year'),
    ),
    'full_search' => array(
        'search_max' => array('int', 1, 'Search term amount'),
        'search_folders' => array('array', 0, 'Folders to Search'),
        'keywords_1_0' => array('string', 0, 'Keywords'),
        'search_fld_1_0' => array('int', 0, 'Message field'),
        'search_flags_1_0' => array('int', 0, 'Message flags'),
        'search_size_1_0' => array('int', 0, 'Message size comparison'),
        'search_size_2_0' => array('float', 0, 'Message size'),
        'search_size_3_0' => array('int', 0, 'Size units'),
        'search_date_1_0' => array('int', 0, 'Date comparison'),
        'search_date_2_0' => array('int', 0, 'Month'),
        'search_date_3_0' => array('int', 0, 'Day'),
        'search_date_4_0' => array('int', 0, 'Year'),

        'keywords_1_1' => array('string', 0, 'Keywords'),
        'and_or_1' => array('int', 0, 'and/or'),
        'search_fld_1_1' => array('int', 0, 'Message field'),
        'search_flags_1_1' => array('int', 0, 'Message flags'),
        'search_size_1_1' => array('int', 0, 'Message size comparison'),
        'search_size_2_1' => array('float', 0, 'Message size'),
        'search_size_3_1' => array('int', 0, 'Size units'),
        'search_date_1_1' => array('int', 0, 'Date comparison'),
        'search_date_2_1' => array('int', 0, 'Month'),
        'search_date_3_1' => array('int', 0, 'Day'),
        'search_date_4_1' => array('int', 0, 'Year'),

        'and_or_2' => array('int', 0, 'and/or'),
        'keywords_1_2' => array('string', 0, 'Keywords'),
        'search_fld_1_2' => array('int', 0, 'Message field'),
        'search_flags_1_2' => array('int', 0, 'Message flags'),
        'search_size_1_2' => array('int', 0, 'Message size comparison'),
        'search_size_2_2' => array('float', 0, 'Message size'),
        'search_size_3_2' => array('int', 0, 'Size units'),
        'search_date_1_2' => array('int', 0, 'Date comparison'),
        'search_date_2_2' => array('int', 0, 'Month'),
        'search_date_3_2' => array('int', 0, 'Day'),
        'search_date_4_2' => array('int', 0, 'Year'),

        'and_or_3' => array('int', 0, 'and/or'),
        'keywords_1_3' => array('string', 0, 'Keywords'),
        'search_fld_1_3' => array('int', 0, 'Message field'),
        'search_flags_1_3' => array('int', 0, 'Message flags'),
        'search_size_1_3' => array('int', 0, 'Message size comparison'),
        'search_size_2_3' => array('float', 0, 'Message size'),
        'search_size_3_3' => array('int', 0, 'Size units'),
        'search_date_1_3' => array('int', 0, 'Date comparison'),
        'search_date_2_3' => array('int', 0, 'Month'),
        'search_date_3_3' => array('int', 0, 'Day'),
        'search_date_4_3' => array('int', 0, 'Year'),

        'and_or_4' => array('int', 0, 'and/or'),
        'keywords_1_4' => array('string', 0, 'Keywords'),
        'search_fld_1_4' => array('int', 0, 'Message field'),
        'search_flags_1_4' => array('int', 0, 'Message flags'),
        'search_size_1_4' => array('int', 0, 'Message size comparison'),
        'search_size_2_4' => array('float', 0, 'Message size'),
        'search_size_3_4' => array('int', 0, 'Size units'),
        'search_date_1_4' => array('int', 0, 'Date comparison'),
        'search_date_2_4' => array('int', 0, 'Month'),
        'search_date_3_4' => array('int', 0, 'Day'),
        'search_date_4_4' => array('int', 0, 'Year'),
    ),
    ); return $forms;
}
function form_action_full_search($form, $post) {
    global $user;
    global $imap;
    if ($user->logged_in) {
    $max = $post['search_max']; 
    $boxes = array();
    if (isset($post['search_folders'])) {
        $boxes = $post['search_folders'];
    }
    if (!is_array($boxes) || empty($boxes)) {
        $this->errors[] = $user->str[368];
    }
    $search_final = '';
    for ($i=0;$i<$max;$i++) {
        $and_or = 0;
        $string = '';
        if ($i > 0) {
            if (isset($post['and_or_'.$i])) {
                if ($post['and_or_'.$i] == 1) {
                    $and_or = 1;
                }
                elseif ($post['and_or_'.$i] == 2) {
                    $search_final = ' OR '.$search_final;
                    $and_or = 2;
                }
                else {
                    $this->errors[] = $user->str[369];
                }
            }
            else {
                $this->errors[] = $user->str[369];
            }
        }
        $string .= '(';
        $keywords = false;
        $fld_id = 1;
        $flag_id = 1; 
        $size1 = 0;
        $size2 = 0;
        $size3 = 0;
        $date1 = 0;
        $date2 = 0;
        $date3 = 0;
        $date4 = 0;
        if (isset($post['keywords_1_'.$i])) {
            $keywords = trim(str_replace(array("\r\n", "\r", "\n", '"'), array(' ', ' ', ' ', '\"'), $post['keywords_1_'.$i]));
        }
        if (isset($post['search_fld_1_'.$i])) {
            $fld_id = $post['search_fld_1_'.$i];
        }
        if (isset($post['search_flags_1_'.$i])) {
            $flag_id = $post['search_flags_1_'.$i];
        }
        if (isset($post['search_size_1_'.$i])) {
            $size1 = $post['search_size_1_'.$i];
        }
        if (isset($post['search_size_2_'.$i])) {
            $size2 = $post['search_size_2_'.$i];
        }
        if (isset($post['search_size_3_'.$i])) {
            $size3 = $post['search_size_3_'.$i];
        }
        if (isset($post['search_date_1_'.$i])) {
            $date1 = $post['search_date_1_'.$i];
        }
        if (isset($post['search_date_2_'.$i])) {
            $date2 = $post['search_date_2_'.$i];
        }
        if (isset($post['search_date_3_'.$i])) {
            $date3 = $post['search_date_3_'.$i];
        }
        if (isset($post['search_date_4_'.$i])) {
            $date4 = $post['search_date_4_'.$i];
        }
        switch ($flag_id) {
                case 2:
                    $string .= 'UNSEEN';
                    break;
                case 3:
                    $string .= 'SEEN';
                    break;
                case 4:
                    $string .= 'FLAGGED';
                    break;
                case 5:
                    $string .= 'UNFLAGGED';
                    break;
                case 6:
                    $string .= 'ANSWERED';
                    break;
                case 7:
                    $string .= 'UNANSWERED';
                    break;
                default:
                    $string .= 'ALL';
                    break;
        }
        if ($keywords) {
            switch ($fld_id) {
                case 2:
                    $string .= ' FROM';
                    break;
                case 3:
                    $string .= ' SUBJECT';
                    break;
                case 4:
                    $string .= ' TO';
                    break;
                case 5:
                    $string .= ' CC';
                    break;
                case 6:
                    $string .= ' BODY';
                    break;
                default:
                    $string .= ' TEXT';
                    break;
            }
            $string .= ' "'.$keywords.'"';
        }
        $size_used = false;
        if ($size1) {
            if (!$size2 || !$size3) {
                $this->errors[] = $user->str[370];
            }
            else {
                $size_used = true;
                switch ($size1) {
                    case 2:
                        $string .= ' SMALLER';
                        break;
                    case 3:
                        $string .= ' LARGER';
                        break;
                }
                $size2 = (int) $size2;
                $string .= ' ';
                switch ($size3) {
                    case 2:
                        $string .= ($size2*1024).' ';
                        break;
                    case 3:
                        $string .= ($size2*1024*1024).' ';
                        break;
                    default:
                        $string .= $size2.' ';
                        break;
                }
            }
        }
        if ($date1) {
            $month_id = (int) $date2;
            $day = (int) $date3;
            $year = (int) $date4;
            if (!$month_id || !$day || !$year) {
                $this->errors[] = $user->str[371];
            }
            else {
                switch ($date1) {
                    case 1:
                        $string .= ' SENTON';
                        break;
                    case 2:
                        $string .= ' SENTBEFORE';
                        break;
                    case 3:
                        $string .= ' SENTSINCE';
                        break;
                }
                $string  .= ' '.$day.'-'.date("M", mktime(0, 0, 0, $month_id, 1, $year)).'-'.$year;
            }
        }
        $string .= ')';
        $_SESSION['search_terms'][$i]['date1'] = $date1;
        $_SESSION['search_terms'][$i]['and_or'] = $and_or;
        $_SESSION['search_terms'][$i]['date2'] = $date2;
        $_SESSION['search_terms'][$i]['date3'] = $date3;
        $_SESSION['search_terms'][$i]['date4'] = $date4;
        $_SESSION['search_terms'][$i]['size1'] = $size1;
        $_SESSION['search_terms'][$i]['size2'] = $size2;
        $_SESSION['search_terms'][$i]['size3'] = $size3;
        $_SESSION['search_terms'][$i]['words'] = $keywords;
        $_SESSION['search_terms'][$i]['location'] = $flag_id;
        $_SESSION['search_terms'][$i]['fld'] = $fld_id;
        $_SESSION['search_terms']['folders'] = $boxes;
        $search_final .= $string;
    }
    if (empty($this->errors)) {
        $res_cnt = 0;
        $res = array();
        foreach ($boxes as $box) {
            $imap->select_mailbox($box, false);
            $result = $imap->full_search($search_final);
            if (!empty($result)) {
                $res_cnt += count($result);
                $res[$box] = $result;
            }
        }
        if (!empty($res_cnt)) {
            $this->errors[] = $user->str[372].': '.$res_cnt;
        }
        else {
            $this->errors[] = $user->str[373];
        }
        $_SESSION['search_total'] = $res_cnt;
        $_SESSION['search_results'] = $res;
        $this->form_redirect = true;
    }
    }
}
function form_action_less_search($form, $post) {
    global $user;
    if ($user->logged_in) {
    $_SESSION['search_max'] = $post['search_max'] - 1;
    for ($i=0;$i<$post['search_max'];$i++) {
        $keywords = false;
        $fld_id = 1;
        $flag_id = 1; 
        $size1 = 0;
        $size2 = 0;
        $size3 = 0;
        $date1 = 0;
        $date2 = 0;
        $date3 = 0;
        $date4 = 0;
        $and_or = 0;
        if (isset($post['and_or_'.$i])) {
            $and_or = (int) $post['and_or_'.$i];
        }
        if (isset($post['keywords_1_'.$i])) {
            $keywords = trim(str_replace(array("\r\n", "\r", "\n", '"'), array(' ', ' ', ' ', '\"'), $post['keywords_1_'.$i]));
        }
        if (isset($post['search_fld_1_'.$i])) {
            $fld_id = $post['search_fld_1_'.$i];
        }
        if (isset($post['search_flags_1_'.$i])) {
            $flag_id = $post['search_flags_1_'.$i];
        }
        if (isset($post['search_size_1_'.$i])) {
            $size1 = $post['search_size_1_'.$i];
        }
        if (isset($post['search_size_2_'.$i])) {
            $size2 = $post['search_size_2_'.$i];
        }
        if (isset($post['search_size_3_'.$i])) {
            $size3 = $post['search_size_3_'.$i];
        }
        if (isset($post['search_date_1_'.$i])) {
            $date1 = $post['search_date_1_'.$i];
        }
        if (isset($post['search_date_2_'.$i])) {
            $date2 = $post['search_date_2_'.$i];
        }
        if (isset($post['search_date_3_'.$i])) {
            $date3 = $post['search_date_3_'.$i];
        }
        if (isset($post['search_date_4_'.$i])) {
            $date4 = $post['search_date_4_'.$i];
        }
        $_SESSION['search_terms'][$i]['date1'] = $date1;
        $_SESSION['search_terms'][$i]['and_or'] = $and_or;
        $_SESSION['search_terms'][$i]['date2'] = $date2;
        $_SESSION['search_terms'][$i]['date3'] = $date3;
        $_SESSION['search_terms'][$i]['date4'] = $date4;
        $_SESSION['search_terms'][$i]['size1'] = $size1;
        $_SESSION['search_terms'][$i]['size2'] = $size2;
        $_SESSION['search_terms'][$i]['size3'] = $size3;
        $_SESSION['search_terms'][$i]['words'] = $keywords;
        $_SESSION['search_terms'][$i]['location'] = $flag_id;
        $_SESSION['search_terms'][$i]['fld'] = $fld_id;
        $this->form_redirect = true;
    }
    }
}
function form_action_more_search($form, $post) {
    global $user;
    if ($user->logged_in) {
        $_SESSION['search_max'] = $post['search_max'] + 1;
    for ($i=0;$i<$post['search_max'];$i++) {
        $keywords = false;
        $fld_id = 1;
        $flag_id = 1; 
        $size1 = 0;
        $size2 = 0;
        $size3 = 0;
        $date1 = 0;
        $date2 = 0;
        $date3 = 0;
        $date4 = 0;
        $and_or = 0;
        if (isset($post['and_or_'.$i])) {
            $and_or = (int) $post['and_or_'.$i];
        }
        if (isset($post['keywords_1_'.$i])) {
            $keywords = trim(str_replace(array("\r\n", "\r", "\n", '"'), array(' ', ' ', ' ', '\"'), $post['keywords_1_'.$i]));
        }
        if (isset($post['search_fld_1_'.$i])) {
            $fld_id = $post['search_fld_1_'.$i];
        }
        if (isset($post['search_flags_1_'.$i])) {
            $flag_id = $post['search_flags_1_'.$i];
        }
        if (isset($post['search_size_1_'.$i])) {
            $size1 = $post['search_size_1_'.$i];
        }
        if (isset($post['search_size_2_'.$i])) {
            $size2 = $post['search_size_2_'.$i];
        }
        if (isset($post['search_size_3_'.$i])) {
            $size3 = $post['search_size_3_'.$i];
        }
        if (isset($post['search_date_1_'.$i])) {
            $date1 = $post['search_date_1_'.$i];
        }
        if (isset($post['search_date_2_'.$i])) {
            $date2 = $post['search_date_2_'.$i];
        }
        if (isset($post['search_date_3_'.$i])) {
            $date3 = $post['search_date_3_'.$i];
        }
        if (isset($post['search_date_4_'.$i])) {
            $date4 = $post['search_date_4_'.$i];
        }
        $_SESSION['search_terms'][$i]['date1'] = $date1;
        $_SESSION['search_terms'][$i]['and_or'] = $and_or;
        $_SESSION['search_terms'][$i]['date2'] = $date2;
        $_SESSION['search_terms'][$i]['date3'] = $date3;
        $_SESSION['search_terms'][$i]['date4'] = $date4;
        $_SESSION['search_terms'][$i]['size1'] = $size1;
        $_SESSION['search_terms'][$i]['size2'] = $size2;
        $_SESSION['search_terms'][$i]['size3'] = $size3;
        $_SESSION['search_terms'][$i]['words'] = $keywords;
        $_SESSION['search_terms'][$i]['location'] = $flag_id;
        $_SESSION['search_terms'][$i]['fld'] = $fld_id;
        $this->form_redirect = true;
    }
    }
}
}?>
