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


$saved_search_hooks = array(
    'display_hooks' => array('search_page_bottom'),
    'work_hooks' => array('init', 'page_end')
);
$saved_search_langs = array(
    'en_US' => array(
        1 => 'Previous Searches',
        2 => 'Saved Searches',
        3 => 'No previous searches found',
        4 => 'No saved searches found',
        5 => 'Search terms',
        6 => 'Target fields',
        7 => 'Target folders',
        8 => 'Save',
        9 => 'Run',
        10 => 'Forget',
        11 => 'Edit',
        12 => 'Delete',
        13 => 'AND',
        14 => 'OR',
        15 => 'An error occured running the selected search',
        16 => 'Running search ...',
    ),
);

?>
