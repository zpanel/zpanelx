<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Set of functions used to build OpenDocument Text dumps of tables
 *
 * @package phpMyAdmin-Export-ODT
 */
if (!defined('PHPMYADMIN')) {
    exit;
}

/**
 *
 */
if (isset($plugin_list)) {
    $hide_structure = false;
    if ($plugin_param['export_type'] == 'table' && !$plugin_param['single_table']) {
        $hide_structure = true;
    }
    $plugin_list['odt'] = array(
        'text' => __('Open Document Text'),
        'extension' => 'odt',
        'mime_type' => 'application/vnd.oasis.opendocument.text',
        'force_file' => true,
        'options' => array(), /* Filled later */
        'options_text' => __('Options'),
    );

    /* what to dump (structure/data/both) */
    $plugin_list['odt']['options'][] =
            array('type' => 'begin_group', 'text' => __('Dump table'), 'name' => 'general_opts');
    $plugin_list['odt']['options'][] =
            array('type' => 'radio', 'name' => 'structure_or_data', 'values' => array('structure' => __('structure'), 'data' => __('data'), 'structure_and_data' => __('structure and data')));
    $plugin_list['odt']['options'][] = array('type' => 'end_group');

    /* Structure options */
    if (!$hide_structure) {
        $plugin_list['odt']['options'][] =
                array('type' => 'begin_group', 'name' => 'structure', 'text' => __('Object creation options'), 'force' => 'data');
        if (!empty($GLOBALS['cfgRelation']['relation'])) {
            $plugin_list['odt']['options'][] =
                    array('type' => 'bool', 'name' => 'relation', 'text' => __('Display foreign key relationships'));
        }
        $plugin_list['odt']['options'][] =
                array('type' => 'bool', 'name' => 'comments', 'text' => __('Display comments'));
        if (!empty($GLOBALS['cfgRelation']['mimework'])) {
            $plugin_list['odt']['options'][] =
                    array('type' => 'bool', 'name' => 'mime', 'text' => __('Display MIME types'));
        }
        $plugin_list['odt']['options'][] =
                array('type' => 'end_group');
    }
    /* Data */
    $plugin_list['odt']['options'][] =
            array('type' => 'begin_group', 'name' => 'data', 'text' => __('Data dump options'), 'force' => 'structure');
    $plugin_list['odt']['options'][] =
            array('type' => 'bool', 'name' => 'columns', 'text' => __('Put columns names in the first row'));
    $plugin_list['odt']['options'][] =
            array('type' => 'text', 'name' => 'null', 'text' => __('Replace NULL with:'));
    $plugin_list['odt']['options'][] =
            array('type' => 'end_group');
} else {

    $GLOBALS['odt_buffer'] = '';
    require_once './libraries/opendocument.lib.php';

    /**
     * Outputs comment
     *
     * @param   string      Text of comment
     *
     * @return  bool        Whether it suceeded
     */
    function PMA_exportComment($text) {
        return TRUE;
    }

    /**
     * Outputs export footer
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    function PMA_exportFooter() {
        $GLOBALS['odt_buffer'] .= '</office:text>'
                . '</office:body>'
                . '</office:document-content>';
        if (!PMA_exportOutputHandler(PMA_createOpenDocument('application/vnd.oasis.opendocument.text', $GLOBALS['odt_buffer']))) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Outputs export header
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    function PMA_exportHeader() {
        $GLOBALS['odt_buffer'] .= '<?xml version="1.0" encoding="' . $GLOBALS['charset'] . '"?' . '>'
                . '<office:document-content ' . $GLOBALS['OpenDocumentNS'] . 'office:version="1.0">'
                . '<office:body>'
                . '<office:text>';
        return TRUE;
    }

    /**
     * Outputs database header
     *
     * @param   string      Database name
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    function PMA_exportDBHeader($db) {
        $GLOBALS['odt_buffer'] .= '<text:h text:outline-level="1" text:style-name="Heading_1" text:is-list-header="true">' . htmlspecialchars(__('Database') . ' ' . $db) . '</text:h>';
        return TRUE;
    }

    /**
     * Outputs database footer
     *
     * @param   string      Database name
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    function PMA_exportDBFooter($db) {
        return TRUE;
    }

    /**
     * Outputs create database database
     *
     * @param   string      Database name
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    function PMA_exportDBCreate($db) {
        return TRUE;
    }

    /**
     * Outputs the content of a table in CSV format
     *
     * @param   string      the database name
     * @param   string      the table name
     * @param   string      the end of line sequence
     * @param   string      the url to go back in case of error
     * @param   string      SQL query for obtaining data
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    function PMA_exportData($db, $table, $crlf, $error_url, $sql_query) {
        global $what;

        // Gets the data from the database
        $result = PMA_DBI_query($sql_query, null, PMA_DBI_QUERY_UNBUFFERED);
        $fields_cnt = PMA_DBI_num_fields($result);
        $fields_meta = PMA_DBI_get_fields_meta($result);
        $field_flags = array();
        for ($j = 0; $j < $fields_cnt; $j++) {
            $field_flags[$j] = PMA_DBI_field_flags($result, $j);
        }

        $GLOBALS['odt_buffer'] .= '<text:h text:outline-level="2" text:style-name="Heading_2" text:is-list-header="true">' . htmlspecialchars(__('Dumping data for table') . ' ' . $table) . '</text:h>';
        $GLOBALS['odt_buffer'] .= '<table:table table:name="' . htmlspecialchars($table) . '_structure">';
        $GLOBALS['odt_buffer'] .= '<table:table-column table:number-columns-repeated="' . $fields_cnt . '"/>';

        // If required, get fields name at the first line
        if (isset($GLOBALS[$what . '_columns'])) {
            $GLOBALS['odt_buffer'] .= '<table:table-row>';
            for ($i = 0; $i < $fields_cnt; $i++) {
                $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                        . '<text:p>' . htmlspecialchars(stripslashes(PMA_DBI_field_name($result, $i))) . '</text:p>'
                        . '</table:table-cell>';
            } // end for
            $GLOBALS['odt_buffer'] .= '</table:table-row>';
        } // end if
        // Format the data
        while ($row = PMA_DBI_fetch_row($result)) {
            $GLOBALS['odt_buffer'] .= '<table:table-row>';
            for ($j = 0; $j < $fields_cnt; $j++) {
                if (!isset($row[$j]) || is_null($row[$j])) {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p>' . htmlspecialchars($GLOBALS[$what . '_null']) . '</text:p>'
                            . '</table:table-cell>';
                    // ignore BLOB
                } elseif (stristr($field_flags[$j], 'BINARY')
                        && $fields_meta[$j]->blob) {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p></text:p>'
                            . '</table:table-cell>';
                } elseif ($fields_meta[$j]->numeric && $fields_meta[$j]->type != 'timestamp' && !$fields_meta[$j]->blob) {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="float" office:value="' . $row[$j] . '" >'
                            . '<text:p>' . htmlspecialchars($row[$j]) . '</text:p>'
                            . '</table:table-cell>';
                } else {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p>' . htmlspecialchars($row[$j]) . '</text:p>'
                            . '</table:table-cell>';
                }
            } // end for
            $GLOBALS['odt_buffer'] .= '</table:table-row>';
        } // end while
        PMA_DBI_free_result($result);

        $GLOBALS['odt_buffer'] .= '</table:table>';

        return TRUE;
    }

    /**
     * Returns $table's structure as Open Document Text
     *
     * @param   string   the database name
     * @param   string   the table name
     * @param   string   the end of line sequence
     * @param   string   the url to go back in case of error
     * @param   boolean  whether to include relation comments
     * @param   boolean  whether to include column comments
     * @param   boolean  whether to include mime comments
     * @param   string   future feature: support view dependencies
     *
     * @return  bool     Whether it suceeded
     *
     * @access  public
     */
    // @@@ Table structure 
    function PMA_exportStructure($db, $table, $crlf, $error_url, $do_relation = false, $do_comments = false, $do_mime = false, $dates = false, $dummy) {
        global $cfgRelation;

        /* Heading */
        $GLOBALS['odt_buffer'] .= '<text:h text:outline-level="2" text:style-name="Heading_2" text:is-list-header="true">' . htmlspecialchars(__('Table structure for table') . ' ' . $table) . '</text:h>';

        /**
         * Get the unique keys in the table
         */
        $keys_query = 'SHOW KEYS FROM ' . PMA_backquote($table) . ' FROM ' . PMA_backquote($db);
        $keys_result = PMA_DBI_query($keys_query);
        $unique_keys = array();
        while ($key = PMA_DBI_fetch_assoc($keys_result)) {
            if ($key['Non_unique'] == 0) {
                $unique_keys[] = $key['Column_name'];
            }
        }
        PMA_DBI_free_result($keys_result);

        /**
         * Gets fields properties
         */
        PMA_DBI_select_db($db);
        $local_query = 'SHOW FIELDS FROM ' . PMA_backquote($db) . '.' . PMA_backquote($table);
        $result = PMA_DBI_query($local_query);
        $fields_cnt = PMA_DBI_num_rows($result);

        // Check if we can use Relations (Mike Beck)
        if ($do_relation && !empty($cfgRelation['relation'])) {
            // Find which tables are related with the current one and write it in
            // an array
            $res_rel = PMA_getForeigners($db, $table);

            if ($res_rel && count($res_rel) > 0) {
                $have_rel = TRUE;
            } else {
                $have_rel = FALSE;
            }
        } else {
            $have_rel = FALSE;
        } // end if

        /**
         * Displays the table structure
         */
        $GLOBALS['odt_buffer'] .= '<table:table table:name="' . htmlspecialchars($table) . '_data">';
        $columns_cnt = 4;
        if ($do_relation && $have_rel) {
            $columns_cnt++;
        }
        if ($do_comments) {
            $columns_cnt++;
        }
        if ($do_mime && $cfgRelation['mimework']) {
            $columns_cnt++;
        }
        $GLOBALS['odt_buffer'] .= '<table:table-column table:number-columns-repeated="' . $columns_cnt . '"/>';
        /* Header */
        $GLOBALS['odt_buffer'] .= '<table:table-row>';
        $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                . '<text:p>' . htmlspecialchars(__('Column')) . '</text:p>'
                . '</table:table-cell>';
        $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                . '<text:p>' . htmlspecialchars(__('Type')) . '</text:p>'
                . '</table:table-cell>';
        $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                . '<text:p>' . htmlspecialchars(__('Null')) . '</text:p>'
                . '</table:table-cell>';
        $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                . '<text:p>' . htmlspecialchars(__('Default')) . '</text:p>'
                . '</table:table-cell>';
        if ($do_relation && $have_rel) {
            $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars(__('Links to')) . '</text:p>'
                    . '</table:table-cell>';
        }
        if ($do_comments) {
            $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars(__('Comments')) . '</text:p>'
                    . '</table:table-cell>';
            $comments = PMA_getComments($db, $table);
        }
        if ($do_mime && $cfgRelation['mimework']) {
            $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars(__('MIME type')) . '</text:p>'
                    . '</table:table-cell>';
            $mime_map = PMA_getMIME($db, $table, true);
        }
        $GLOBALS['odt_buffer'] .= '</table:table-row>';

        while ($row = PMA_DBI_fetch_assoc($result)) {

            $GLOBALS['odt_buffer'] .= '<table:table-row>';
            $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars($row['Field']) . '</text:p>'
                    . '</table:table-cell>';
            // reformat mysql query output
            // set or enum types: slashes single quotes inside options
            $field_name = $row['Field'];
            $type = $row['Type'];
            if (preg_match('/^(set|enum)\((.+)\)$/i', $type, $tmp)) {
                $tmp[2] = substr(preg_replace('/([^,])\'\'/', '\\1\\\'', ',' . $tmp[2]), 1);
                $type = $tmp[1] . '(' . str_replace(',', ', ', $tmp[2]) . ')';
                $type_nowrap = '';

                $binary = 0;
                $unsigned = 0;
                $zerofill = 0;
            } else {
                $type_nowrap = ' nowrap="nowrap"';
                $type = preg_replace('/BINARY/i', '', $type);
                $type = preg_replace('/ZEROFILL/i', '', $type);
                $type = preg_replace('/UNSIGNED/i', '', $type);
                if (empty($type)) {
                    $type = '&nbsp;';
                }

                $binary = preg_match('/BINARY/i', $row['Type']);
                $unsigned = preg_match('/UNSIGNED/i', $row['Type']);
                $zerofill = preg_match('/ZEROFILL/i', $row['Type']);
            }
            $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars($type) . '</text:p>'
                    . '</table:table-cell>';
            if (!isset($row['Default'])) {
                if ($row['Null'] != 'NO') {
                    $row['Default'] = 'NULL';
                } else {
                    $row['Default'] = '';
                }
            } else {
                $row['Default'] = $row['Default'];
            }
            $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars(($row['Null'] == '' || $row['Null'] == 'NO') ? __('No') : __('Yes')) . '</text:p>'
                    . '</table:table-cell>';
            $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                    . '<text:p>' . htmlspecialchars($row['Default']) . '</text:p>'
                    . '</table:table-cell>';

            if ($do_relation && $have_rel) {
                if (isset($res_rel[$field_name])) {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p>' . htmlspecialchars($res_rel[$field_name]['foreign_table'] . ' (' . $res_rel[$field_name]['foreign_field'] . ')') . '</text:p>'
                            . '</table:table-cell>';
                }
            }
            if ($do_comments) {
                if (isset($comments[$field_name])) {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p>' . htmlspecialchars($comments[$field_name]) . '</text:p>'
                            . '</table:table-cell>';
                } else {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p></text:p>'
                            . '</table:table-cell>';
                }
            }
            if ($do_mime && $cfgRelation['mimework']) {
                if (isset($mime_map[$field_name])) {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p>' . htmlspecialchars(str_replace('_', '/', $mime_map[$field_name]['mimetype'])) . '</text:p>'
                            . '</table:table-cell>';
                } else {
                    $GLOBALS['odt_buffer'] .= '<table:table-cell office:value-type="string">'
                            . '<text:p></text:p>'
                            . '</table:table-cell>';
                }
            }
            $GLOBALS['odt_buffer'] .= '</table:table-row>';
        } // end while
        PMA_DBI_free_result($result);

        $GLOBALS['odt_buffer'] .= '</table:table>';
        return TRUE;
    }

// end of the 'PMA_exportStructure' function
} // end else
?>
