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


$news_hooks = array(

    /* sets up work hooks for any back end processing the plugin needs */

    'work_hooks'        => array('init'),

    /* sets up display hooks for inserting content into existing hm pages */

    'display_hooks'     => array('menu'),

    /* If true sets up a place for the plugin can have its own pages */

    'page_hook'         => true,

);
$news_langs = array(
    'en_US' => array(
    ),
);

?>
