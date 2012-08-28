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


/*  WORK HOOKS FUNCTIONS
    For every work hook the plugin registers in config.php there must
    be a corresponding function in this file called <plugin name>_<hook name>
    See docs/work_hooks.txt for a list of work hooks and descriptions.
*/
function hello_world_init($tools) {

    /* store a name for the link in the menu hook to use */

    $tools->add_to_store('link_name', $tools->str[1]);

    $tools->register_ajax_callback('test', 0, 'clock_div');

    /* connect to the database. If this is true 
       tools now contains a set of methods available
       with $tools->db.
    */
    //if ($tools->get_db()) {

        /* example select statement. $res is either an empty array
           or an array of the matching rows:
            array (
                0 => array('field' => 'value', 'field2' => 'value2'),
                1 => array('field' => 'value', 'field2' => 'value2'),
                ....
        */

        //$res = $tools->db_query('select * from table');

        /* example insert statement . $res is set to the number of
           rows affected by the insert statement */

        //$res = $tools->db_insert('insert into table values(()');


        /* example update statement. $res is set to the number of rows
           affected by the update statement */

        //$res = $tools->db_update('update table set field=value');


        /* example delete statement. $res is set to the number of rows
           affecte by the delete statement */

        //$res = $tools->db_delete('delete from table');

        /* output debug for development */

        //$tools->db_puke();
       
    //}
}
?>
