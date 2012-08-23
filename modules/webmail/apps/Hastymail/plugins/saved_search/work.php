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


function str_terms() {
    $str = '';
    if (isset($_SESSION['search_terms']) && !empty($_SESSION['search_terms']) &&
        isset($_SESSION['search_terms']['folders']) && !empty($_SESSION['search_terms']['folders'])) {
        foreach($_SESSION['search_terms'] as $i => $v) {
            $str .= implode('', $v);
        }
    }
    return $str;
}
function saved_search_init($tools) {
    if ($tools->get_page() == 'search' && isset($_POST['full_search'])) {
        $_SESSION['search_post'] = true;
    }
}
function saved_search_page_end($tools) {
    $max_prev_search = 10;
    if ($tools->get_page() == 'search') {
        $str = str_terms();
        if ($str && isset($_SESSION['search_post'])) { 
            $prev_searches = $tools->get_setting('prev_searches');
            if (!$prev_searches) {
                $prev_searches = array();
            }
            if (!isset($prev_searches[$str])) {
                $prev_searches[$str] = $_SESSION['search_terms'];
            }
            if (count($prev_searches) >= $max_prev_search) {
                array_shift($prev_searches);
            }
            $tools->save_setting('prev_searches', $prev_searches);
            unset($_SESSION['search_post']);
        }
        elseif (isset($_GET['delete_search'])) {
            $saved_searches = $tools->get_setting('saved_searches');
            if (isset($saved_searches[$_GET['delete_search']])) {
                unset($saved_searches[$_GET['delete_search']]);
                $tools->save_setting('saved_searches', $saved_searches);
            }
        }
        elseif (isset($_GET['forget_search'])) {
            $prev_searches = $tools->get_setting('prev_searches');
            if (isset($prev_searches[$_GET['forget_search']])) {
                unset($prev_searches[$_GET['forget_search']]);
                $tools->save_setting('prev_searches', $prev_searches);
            }
        }
        elseif (isset($_GET['save_search'])) {
            $saved_searches = $tools->get_setting('saved_searches');
            $prev_searches = $tools->get_setting('prev_searches');
            if (!isset($saved_searches[$_GET['save_search']]) && isset($prev_searches[$_GET['save_search']])) {
                $saved_searches[$_GET['save_search']] = $prev_searches[$_GET['save_search']];
                $tools->save_setting('saved_searches', $saved_searches);
            }
        }
        elseif (isset($_GET['edit_search'])) {
            $saved_searches = $tools->get_setting('saved_searches');
            $prev_searches = $tools->get_setting('prev_searches');
            $found = false;
            if (is_array($prev_searches)) {
                foreach ($prev_searches as $index => $vals) {
                    if ($_GET['edit_search'] == $index) {
                        $_SESSION['search_terms'] = $vals;
                        $tools->set_search_params($vals);
                        $found = true;
                        break;
                    }
                }
            }
            if (is_array($saved_searches)) {
                foreach ($saved_searches as $index => $vals) {
                    if ($_GET['edit_search'] == $index) {
                        $_SESSION['search_terms'] = $vals;
                        $tools->set_search_params($vals);
                        $found = true;
                        break;
                    }
                }
            }
        }
        elseif (isset($_GET['run_search'])) {
            $saved_searches = $tools->get_setting('saved_searches');
            $prev_searches = $tools->get_setting('prev_searches');
            $found = false;
            if (is_array($prev_searches)) {
                foreach ($prev_searches as $index => $vals) {
                    if ($_GET['run_search'] == $index) {
                        $_SESSION['search_terms'] = $vals;
                        $tools->set_search_params($vals);
                        $found = true;
                        break;
                    }
                }
            }
            if (is_array($saved_searches)) {
                foreach ($saved_searches as $index => $vals) {
                    if ($_GET['run_search'] == $index) {
                        $_SESSION['search_terms'] = $vals;
                        $tools->set_search_params($vals);
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                $tools->send_notice($tools->str[15]);
            }
            else {
                $tools->send_notice($tools->str[16]);
                $tools->add_js_onload('setTimeout(function(){document.getElementById("full_search").click();}, 500);');
            }
        }
        $tools->add_style('<style type="text/css">
        .saved_searches, .prev_searches {padding-left: 20px; padding-bottom: 40px !important; width: 100%;}
        .empty_list{font-style: italic; color: #999; padding: 20px !important; padding-left: 30px !important; padding-bottom: 0px !important;}
        .saved_searches th, .prev_searches th{font-weight: normal !important; padding: 5px !important; text-align: left !important; border-bottom: solid 1px #ccc !important;}
        .saved_searches td, .prev_searches td{padding: 5px;}
        .saved_searches a, .prev_searches a{padding-right: 8px;}
        </style>');
        //$tools->save_setting('prev_searches', array());
    }
}
?>
