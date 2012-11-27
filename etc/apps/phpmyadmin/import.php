<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Core script for import, this is just the glue around all other stuff
 *
 * @package PhpMyAdmin
 */

/**
 * Get the variables sent or posted to this script and a core script
 */
require_once './libraries/common.inc.php';
//require_once './libraries/display_import_functions.lib.php';

// reset import messages for ajax request
$_SESSION['Import_message']['message'] = null;
$_SESSION['Import_message']['go_back_url'] = null;
// default values
$GLOBALS['reload'] = false;

// Are we just executing plain query or sql file? (eg. non import, but query box/window run)
if (!empty($sql_query)) {
    // run SQL query
    $import_text = $sql_query;
    $import_type = 'query';
    $format = 'sql';

    // refresh left frame on changes in table or db structure
    if (preg_match('/^(CREATE|ALTER|DROP)\s+(VIEW|TABLE|DATABASE|SCHEMA)\s+/i', $sql_query)) {
        $GLOBALS['reload'] = true;
    }

    $sql_query = '';
} elseif (!empty($sql_localfile)) {
    // run SQL file on server
    $local_import_file = $sql_localfile;
    $import_type = 'queryfile';
    $format = 'sql';
    unset($sql_localfile);
} elseif (!empty($sql_file)) {
    // run uploaded SQL file
    $import_file = $sql_file;
    $import_type = 'queryfile';
    $format = 'sql';
    unset($sql_file);
} elseif (!empty($id_bookmark)) {
    // run bookmark
    $import_type = 'query';
    $format = 'sql';
}

// If we didn't get any parameters, either user called this directly, or
// upload limit has been reached, let's assume the second possibility.
;
if ($_POST == array() && $_GET == array()) {
    include_once './libraries/header.inc.php';
    $message = PMA_Message::error(__('You probably tried to upload too large file. Please refer to %sdocumentation%s for ways to workaround this limit.'));
    $message->addParam('[a@./Documentation.html#faq1_16@_blank]');
    $message->addParam('[/a]');

    // so we can obtain the message
    $_SESSION['Import_message']['message'] = $message->getDisplay();
    $_SESSION['Import_message']['go_back_url'] = $goto;

    $message->display();
    include './libraries/footer.inc.php';
}

// Check needed parameters
PMA_checkParameters(array('import_type', 'format'));

// We don't want anything special in format
$format = PMA_securePath($format);

// Import functions
require_once './libraries/import.lib.php';

// Create error and goto url
if ($import_type == 'table') {
    $err_url = 'tbl_import.php?' . PMA_generate_common_url($db, $table);
    $_SESSION['Import_message']['go_back_url'] = $err_url;
    $goto = 'tbl_import.php';
} elseif ($import_type == 'database') {
    $err_url = 'db_import.php?' . PMA_generate_common_url($db);
    $_SESSION['Import_message']['go_back_url'] = $err_url;
    $goto = 'db_import.php';
} elseif ($import_type == 'server') {
    $err_url = 'server_import.php?' . PMA_generate_common_url();
    $_SESSION['Import_message']['go_back_url'] = $err_url;
    $goto = 'server_import.php';
} else {
    if (empty($goto) || !preg_match('@^(server|db|tbl)(_[a-z]*)*\.php$@i', $goto)) {
        if (strlen($table) && strlen($db)) {
            $goto = 'tbl_structure.php';
        } elseif (strlen($db)) {
            $goto = 'db_structure.php';
        } else {
            $goto = 'server_sql.php';
        }
    }
    if (strlen($table) && strlen($db)) {
        $common = PMA_generate_common_url($db, $table);
    } elseif (strlen($db)) {
        $common = PMA_generate_common_url($db);
    } else {
        $common = PMA_generate_common_url();
    }
    $err_url  = $goto
              . '?' . $common
              . (preg_match('@^tbl_[a-z]*\.php$@', $goto) ? '&amp;table=' . htmlspecialchars($table) : '');
    $_SESSION['Import_message']['go_back_url'] = $err_url;
}


if (strlen($db)) {
    PMA_DBI_select_db($db);
}

@set_time_limit($cfg['ExecTimeLimit']);
if (!empty($cfg['MemoryLimit'])) {
    @ini_set('memory_limit', $cfg['MemoryLimit']);
}

$timestamp = time();
if (isset($allow_interrupt)) {
    $maximum_time = ini_get('max_execution_time');
} else {
    $maximum_time = 0;
}

// set default values
$timeout_passed = false;
$error = false;
$read_multiply = 1;
$finished = false;
$offset = 0;
$max_sql_len = 0;
$file_to_unlink = '';
$sql_query = '';
$sql_query_disabled = false;
$go_sql = false;
$executed_queries = 0;
$run_query = true;
$charset_conversion = false;
$reset_charset = false;
$bookmark_created = false;

// Bookmark Support: get a query back from bookmark if required
if (!empty($id_bookmark)) {
    $id_bookmark = (int)$id_bookmark;
    include_once './libraries/bookmark.lib.php';
    switch ($action_bookmark) {
        case 0: // bookmarked query that have to be run
            $import_text = PMA_Bookmark_get($db, $id_bookmark, 'id', isset($action_bookmark_all));
            if (isset($bookmark_variable) && !empty($bookmark_variable)) {
                $import_text = preg_replace('|/\*(.*)\[VARIABLE\](.*)\*/|imsU', '${1}' . PMA_sqlAddSlashes($bookmark_variable) . '${2}', $import_text);
            }

            // refresh left frame on changes in table or db structure
            if (preg_match('/^(CREATE|ALTER|DROP)\s+(VIEW|TABLE|DATABASE|SCHEMA)\s+/i', $import_text)) {
                $GLOBALS['reload'] = true;
            }

            break;
        case 1: // bookmarked query that have to be displayed
            $import_text = PMA_Bookmark_get($db, $id_bookmark);
            if ($GLOBALS['is_ajax_request'] == true) {
                $extra_data['sql_query'] = $import_text;
                $extra_data['action_bookmark'] = $action_bookmark;
                $message = PMA_Message::success(__('Showing bookmark'));
                PMA_ajaxResponse($message, $message->isSuccess(), $extra_data);
            } else {
                $run_query = false;
            }
            break;
        case 2: // bookmarked query that have to be deleted
            $import_text = PMA_Bookmark_get($db, $id_bookmark);
            PMA_Bookmark_delete($db, $id_bookmark);
            if ($GLOBALS['is_ajax_request'] == true) {
                $message = PMA_Message::success(__('The bookmark has been deleted.'));
                $extra_data['action_bookmark'] = $action_bookmark;
                $extra_data['id_bookmark'] = $id_bookmark;
                PMA_ajaxResponse($message, $message->isSuccess(), $extra_data);
            } else {
                $run_query = false;
                $error = true; // this is kind of hack to skip processing the query
            }
            break;
    }
} // end bookmarks reading

// Do no run query if we show PHP code
if (isset($GLOBALS['show_as_php'])) {
    $run_query = false;
    $go_sql = true;
}

// Store the query as a bookmark before executing it if bookmarklabel was given
if (!empty($bkm_label) && !empty($import_text)) {
    include_once './libraries/bookmark.lib.php';
    $bfields = array(
                 'dbase' => $db,
                 'user'  => $cfg['Bookmark']['user'],
                 'query' => urlencode($import_text),
                 'label' => $bkm_label
    );

    // Should we replace bookmark?
    if (isset($bkm_replace)) {
        $bookmarks = PMA_Bookmark_getList($db);
        foreach ($bookmarks as $key => $val) {
            if ($val == $bkm_label) {
                PMA_Bookmark_delete($db, $key);
            }
        }
    }

    PMA_Bookmark_save($bfields, isset($bkm_all_users));

    $bookmark_created = true;
} // end store bookmarks

// We can not read all at once, otherwise we can run out of memory
$memory_limit = trim(@ini_get('memory_limit'));
// 2 MB as default
if (empty($memory_limit)) {
    $memory_limit = 2 * 1024 * 1024;
}
// In case no memory limit we work on 10MB chunks
if ($memory_limit == -1) {
    $memory_limit = 10 * 1024 * 1024;
}

// Calculate value of the limit
if (strtolower(substr($memory_limit, -1)) == 'm') {
    $memory_limit = (int)substr($memory_limit, 0, -1) * 1024 * 1024;
} elseif (strtolower(substr($memory_limit, -1)) == 'k') {
    $memory_limit = (int)substr($memory_limit, 0, -1) * 1024;
} elseif (strtolower(substr($memory_limit, -1)) == 'g') {
    $memory_limit = (int)substr($memory_limit, 0, -1) * 1024 * 1024 * 1024;
} else {
    $memory_limit = (int)$memory_limit;
}

$read_limit = $memory_limit / 8; // Just to be sure, there might be lot of memory needed for uncompression

// handle filenames
if (!empty($local_import_file) && !empty($cfg['UploadDir'])) {

    // sanitize $local_import_file as it comes from a POST
    $local_import_file = PMA_securePath($local_import_file);

    $import_file  = PMA_userDir($cfg['UploadDir']) . $local_import_file;
} elseif (empty($import_file) || !is_uploaded_file($import_file)) {
    $import_file  = 'none';
}

// Do we have file to import?

if ($import_file != 'none' && !$error) {
    // work around open_basedir and other limitations
    $open_basedir = @ini_get('open_basedir');

    // If we are on a server with open_basedir, we must move the file
    // before opening it. The doc explains how to create the "./tmp"
    // directory

    if (!empty($open_basedir)) {

        $tmp_subdir = (PMA_IS_WINDOWS ? '.\\tmp\\' : './tmp/');

        if (is_writable($tmp_subdir)) {


            $import_file_new = $tmp_subdir . basename($import_file) . uniqid();
            if (move_uploaded_file($import_file, $import_file_new)) {
                $import_file = $import_file_new;
                $file_to_unlink = $import_file_new;
            }

            $size = filesize($import_file);
        }
    }

    /**
     *  Handle file compression
     *  @todo duplicate code exists in File.class.php
     */
    $compression = PMA_detectCompression($import_file);
    if ($compression === false) {
        $message = PMA_Message::error(__('File could not be read'));
        $error = true;
    } else {
        switch ($compression) {
            case 'application/bzip2':
                if ($cfg['BZipDump'] && @function_exists('bzopen')) {
                    $import_handle = @bzopen($import_file, 'r');
                } else {
                    $message = PMA_Message::error(__('You attempted to load file with unsupported compression (%s). Either support for it is not implemented or disabled by your configuration.'));
                    $message->addParam($compression);
                    $error = true;
                }
                break;
            case 'application/gzip':
                if ($cfg['GZipDump'] && @function_exists('gzopen')) {
                    $import_handle = @gzopen($import_file, 'r');
                } else {
                    $message = PMA_Message::error(__('You attempted to load file with unsupported compression (%s). Either support for it is not implemented or disabled by your configuration.'));
                    $message->addParam($compression);
                    $error = true;
                }
                break;
            case 'application/zip':
                if ($cfg['ZipDump'] && @function_exists('zip_open')) {
                    /**
                     * Load interface for zip extension.
                     */
                    include_once './libraries/zip_extension.lib.php';
                    $result = PMA_getZipContents($import_file);
                    if (! empty($result['error'])) {
                        $message = PMA_Message::rawError($result['error']);
                        $error = true;
                    } else {
                        $import_text = $result['data'];
                    }
                } else {
                    $message = PMA_Message::error(__('You attempted to load file with unsupported compression (%s). Either support for it is not implemented or disabled by your configuration.'));
                    $message->addParam($compression);
                    $error = true;
                }
                break;
            case 'none':
                $import_handle = @fopen($import_file, 'r');
                break;
            default:
                $message = PMA_Message::error(__('You attempted to load file with unsupported compression (%s). Either support for it is not implemented or disabled by your configuration.'));
                $message->addParam($compression);
                $error = true;
                break;
        }
    }
    // use isset() because zip compression type does not use a handle
    if (!$error && isset($import_handle) && $import_handle === false) {
        $message = PMA_Message::error(__('File could not be read'));
        $error = true;
    }
} elseif (!$error) {
    if (! isset($import_text) || empty($import_text)) {
        $message = PMA_Message::error(__('No data was received to import. Either no file name was submitted, or the file size exceeded the maximum size permitted by your PHP configuration. See [a@./Documentation.html#faq1_16@Documentation]FAQ 1.16[/a].'));
        $error = true;
    }
}

// so we can obtain the message
//$_SESSION['Import_message'] = $message->getDisplay();

// Convert the file's charset if necessary
if ($GLOBALS['PMA_recoding_engine'] != PMA_CHARSET_NONE && isset($charset_of_file)) {
    if ($charset_of_file != 'utf-8') {
        $charset_conversion = true;
    }
} elseif (isset($charset_of_file) && $charset_of_file != 'utf8') {
    if (PMA_DRIZZLE) {
        // Drizzle doesn't support other character sets, so we can't fallback to SET NAMES - throw an error
        $error = true;
        $message = PMA_Message::error(__('Cannot convert file\'s character set without character set conversion library'));
    } else {
        PMA_DBI_query('SET NAMES \'' . $charset_of_file . '\'');
        // We can not show query in this case, it is in different charset
        $sql_query_disabled = true;
        $reset_charset = true;
    }
}

// Something to skip?
if (!$error && isset($skip)) {
    $original_skip = $skip;
    while ($skip > 0) {
        PMA_importGetNextChunk($skip < $read_limit ? $skip : $read_limit);
        $read_multiply = 1; // Disable read progresivity, otherwise we eat all memory!
        $skip -= $read_limit;
    }
    unset($skip);
}

if (!$error) {
    // Check for file existance
    if (!file_exists('./libraries/import/' . $format . '.php')) {
        $error = true;
        $message = PMA_Message::error(__('Could not load import plugins, please check your installation!'));
    } else {
        // Do the real import
        $plugin_param = $import_type;
        include './libraries/import/' . $format . '.php';
    }
}

if (! $error && false !== $import_handle && null !== $import_handle) {
    fclose($import_handle);
}

// Cleanup temporary file
if ($file_to_unlink != '') {
    unlink($file_to_unlink);
}

// Reset charset back, if we did some changes
if ($reset_charset) {
    PMA_DBI_query('SET CHARACTER SET utf8');
    PMA_DBI_query('SET SESSION collation_connection =\'' . $collation_connection . '\'');
}

// Show correct message
if (!empty($id_bookmark) && $action_bookmark == 2) {
    $message = PMA_Message::success(__('The bookmark has been deleted.'));
    $display_query = $import_text;
    $error = false; // unset error marker, it was used just to skip processing
} elseif (!empty($id_bookmark) && $action_bookmark == 1) {
    $message = PMA_Message::notice(__('Showing bookmark'));
} elseif ($bookmark_created) {
    $special_message = '[br]' . sprintf(__('Bookmark %s created'), htmlspecialchars($bkm_label));
} elseif ($finished && !$error) {
    if ($import_type == 'query') {
        $message = PMA_Message::success();
    } else {
        if ($import_notice) {
            $message = PMA_Message::success('<em>'.__('Import has been successfully finished, %d queries executed.').'</em>');
            $message->addParam($executed_queries);

            $message->addString($import_notice);
            $message->addString('(' . $_FILES['import_file']['name'] . ')');
        } else {
            $message = PMA_Message::success(__('Import has been successfully finished, %d queries executed.'));
            $message->addParam($executed_queries);
            $message->addString('(' . $_FILES['import_file']['name'] . ')');
        }
    }
}

// Did we hit timeout? Tell it user.
if ($timeout_passed) {
    $message = PMA_Message::error(__('Script timeout passed, if you want to finish import, please resubmit same file and import will resume.'));
    if ($offset == 0 || (isset($original_skip) && $original_skip == $offset)) {
        $message->addString(__('However on last run no data has been parsed, this usually means phpMyAdmin won\'t be able to finish this import unless you increase php time limits.'));
    }
}

// if there is any message, copy it into $_SESSION as well, so we can obtain it by AJAX call
if (isset($message)) {
    $_SESSION['Import_message']['message'] = $message->getDisplay();
//  $_SESSION['Import_message']['go_back_url'] = $goto.'?'.  PMA_generate_common_url();
}
// Parse and analyze the query, for correct db and table name
// in case of a query typed in the query window
// (but if the query is too large, in case of an imported file, the parser
//  can choke on it so avoid parsing)
if (strlen($sql_query) <= $GLOBALS['cfg']['MaxCharactersInDisplayedSQL']) {
    include_once './libraries/parse_analyze.lib.php';
}

// There was an error?
if (isset($my_die)) {
    foreach ($my_die AS $key => $die) {
        PMA_mysqlDie($die['error'], $die['sql'], '', $err_url, $error);
    }
}

// we want to see the results of the last query that returned at least a row
if (! empty($last_query_with_results)) {
    // but we want to show intermediate results too
    $disp_query = $sql_query;
    $disp_message = __('Your SQL query has been executed successfully');
    $sql_query = $last_query_with_results;
    $go_sql = true;
}

if ($go_sql) {
    include './sql.php';
} else {
    $active_page = $goto;
    include './' . $goto;
}
exit();
?>
