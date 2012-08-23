<?php

/**
 * abook_local_file.php
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: abook_local_file.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage addressbook
 */

/**
 * Backend for address book as a pipe separated file
 *
 * Stores the address book in a local file
 *
 * An array with the following elements must be passed to
 * the class constructor (elements marked ? are optional):
 *<pre>
 *   filename  => path to addressbook file
 * ? create    => if true: file is created if it does not exist.
 * ? umask     => umask set before opening file.
 * ? name      => name of address book.
 * ? detect_writeable => detect address book access permissions by
 *                checking file permissions.
 * ? writeable => allow writing into address book. Used only when
 *                detect_writeable is set to false.
 * ? listing   => enable/disable listing
 * ? line_length => allowed address book record size
 *</pre>
 * NOTE. This class should not be used directly. Use the
 *       "AddressBook" class instead.
 * @package squirrelmail
 */
class abook_local_file extends addressbook_backend {
    /**
     * Backend type
     * @var string
     */
    var $btype = 'local';
    /**
     * Backend name
     * @var string
     */
    var $bname = 'local_file';

    /**
     * File used to store data
     * @var string
     */
    var $filename = '';
    /**
     * File handle
     * @var object
     */
    var $filehandle = 0;
    /**
     * Create file, if it not present
     * @var bool
     */
    var $create = false;
    /**
     * Detect, if address book is writeable by checking file permisions
     * @var bool
     */
    var $detect_writeable   = true;
    /**
     * Control write access to address book
     *
     * Option does not have any effect, if 'detect_writeable' is 'true'
     * @var bool
     */
    var $writeable = false;
    /**
     * controls listing of address book
     * @var bool
     * @since 1.5.1 and 1.4.9
     */
    var $listing = true;
    /**
     * Umask of the file
     * @var string
     */
    var $umask;
    /**
     * Sets max entry size (number of bytes used for all address book fields
     * (including escapes) + 4 delimiters + 1 linefeed)
     * @var integer
     * @since 1.5.2 and 1.4.9
     */
    var $line_length = 2048;

    /* ========================== Private ======================= */

    /**
     * Constructor
     * @param array $param backend options
     * @return bool
     */
    function abook_local_file($param) {
        $this->sname = _("Personal address book");
        $this->umask = Umask();

        if(is_array($param)) {
            if(empty($param['filename'])) {
                return $this->set_error('Invalid parameters');
            }
            if(!is_string($param['filename'])) {
                return $this->set_error($param['filename'] . ': '.
                     _("Not a file name"));
            }

            $this->filename = $param['filename'];

            if(isset($param['create'])) {
                $this->create = $param['create'];
            }
            if(isset($param['umask'])) {
                $this->umask = $param['umask'];
            }
            if(isset($param['name'])) {
                $this->sname = $param['name'];
            }
            if(isset($param['detect_writeable'])) {
                $this->detect_writeable = $param['detect_writeable'];
            }
            if(!empty($param['writeable'])) {
                $this->writeable = $param['writeable'];
            }
            if(isset($param['listing'])) {
                $this->listing = $param['listing'];
            }
            if(isset($param['line_length']) && ! empty($param['line_length'])) {
                $this->line_length = (int) $param['line_length'];
            }

            $this->open(true);
        } else {
            $this->set_error('Invalid argument to constructor');
        }
    }

    /**
     * Open the addressbook file and store the file pointer.
     * Use $file as the file to open, or the class' own
     * filename property. If $param is empty and file is
     * open, do nothing.
     * @param bool $new is file already opened
     * @return bool
     */
    function open($new = false) {
        $this->error = '';
        $file   = $this->filename;
        $create = $this->create;
        $fopenmode = (($this->writeable && is_writable($file)) ? 'a+' : 'r');

        /* Return true is file is open and $new is unset */
        if($this->filehandle && !$new) {
            return true;
        }

        /* Check that new file exitsts */
        if((!(file_exists($file) && is_readable($file))) && !$create) {
            return $this->set_error("$file: " . _("No such file or directory"));
        }

        /* Close old file, if any */
        if($this->filehandle) { $this->close(); }

        umask($this->umask);
        if (! $this->detect_writeable) {
            $fh = @fopen($file,$fopenmode);
            if ($fh) {
                $this->filehandle = &$fh;
                $this->filename = $file;
            } else {
                return $this->set_error("$file: " . _("Open failed"));
            }
        } else {
            /* Open file. First try to open for reading and writing,
             * but fall back to read only. */
            $fh = @fopen($file, 'a+');
            if($fh) {
                $this->filehandle = &$fh;
                $this->filename   = $file;
                $this->writeable  = true;
            } else {
                $fh = @fopen($file, 'r');
                if($fh) {
                    $this->filehandle = &$fh;
                    $this->filename   = $file;
                    $this->writeable  = false;
                } else {
                    return $this->set_error("$file: " . _("Open failed"));
                }
            }
        }
        return true;
    }

    /** Close the file and forget the filehandle */
    function close() {
        @fclose($this->filehandle);
        $this->filehandle = 0;
        $this->filename   = '';
        $this->writable   = false;
    }

    /** Lock the datafile - try 20 times in 5 seconds */
    function lock() {
        for($i = 0 ; $i < 20 ; $i++) {
            if(flock($this->filehandle, 2 + 4))
                return true;
            else
                usleep(250000);
        }
        return false;
    }

    /** Unlock the datafile */
    function unlock() {
        return flock($this->filehandle, 3);
    }

    /**
     * Overwrite the file with data from $rows
     * NOTE! Previous locks are broken by this function
     * @param array $rows new data
     * @return bool
     */
    function overwrite(&$rows) {
        $this->unlock();
        $newfh = @fopen($this->filename.'.tmp', 'w');

        if(!$newfh) {
            return $this->set_error($this->filename. '.tmp:' . _("Open failed"));
        }

        for($i = 0, $cnt=sizeof($rows) ; $i < $cnt ; $i++) {
            if(is_array($rows[$i])) {
                for($j = 0, $cnt_part=count($rows[$i]) ; $j < $cnt_part ; $j++) {
                    $rows[$i][$j] = $this->quotevalue($rows[$i][$j]);
                }
                $tmpwrite = sq_fwrite($newfh, join('|', $rows[$i]) . "\n");
                if ($tmpwrite === FALSE) {
                    return $this->set_error($this->filename . '.tmp:' . _("Write failed"));
                }
            }
        }

        fclose($newfh);
        if (!@copy($this->filename . '.tmp' , $this->filename)) {
          return $this->set_error($this->filename . ':' . _("Unable to update"));
        }
        @unlink($this->filename . '.tmp');
        @chmod($this->filename, 0600);
        $this->unlock();
        $this->open(true);
        return true;
    }

    /* ========================== Public ======================== */

    /**
     * Search the file
     * @param string $expr search expression
     * @return array search results
     */
    function search($expr) {

        /* To be replaced by advanded search expression parsing */
        if(is_array($expr)) { return; }

        // don't allow wide search when listing is disabled.
        if ($expr=='*' && ! $this->listing)
            return array();

        /* Make regexp from glob'ed expression
         * May want to quote other special characters like (, ), -, [, ], etc. */
        $expr = str_replace('?', '.', $expr);
        $expr = str_replace('*', '.*', $expr);

        $res = array();
        if(!$this->open()) {
            return false;
        }
        @rewind($this->filehandle);

        while ($row = @fgetcsv($this->filehandle, $this->line_length, '|')) {
            if (count($row)<5) {
                /** address book is corrupted. */
                global $color;
                error_box(_("Address book is corrupted. Required fields are missing."),$color);
                die('</body></html>');
            } else {
                $line = join(' ', $row);
                // errors on preg_match call are suppressed in order to prevent display of regexp compilation errors
                if(@preg_match('/' . $expr . '/i', $line)) {
                    array_push($res, array('nickname'  => $row[0],
                                           'name'      => $row[1] . ' ' . $row[2],
                                           'firstname' => $row[1],
                                           'lastname'  => $row[2],
                                           'email'     => $row[3],
                                           'label'     => $row[4],
                                           'backend'   => $this->bnum,
                                           'source'    => &$this->sname));
                }
            }
        }
        return $res;
    }

    /**
     * Lookup by the indicated field
     *
     * @param string  $value Value to look up
     * @param integer $field The field to look in, should be one
     *                       of the SM_ABOOK_FIELD_* constants
     *                       defined in functions/constants.php
     *                       (OPTIONAL; defaults to nickname field)
     *                       NOTE: uniqueness is only guaranteed
     *                       when the nickname field is used here;
     *                       otherwise, the first matching address
     *                       is returned.
     *
     * @return array search results
     *
     */
    function lookup($value, $field=SM_ABOOK_FIELD_NICKNAME) {
        if(empty($value)) {
            return array();
        }

        $value = strtolower($value);

        $this->open();
        @rewind($this->filehandle);

        while ($row = @fgetcsv($this->filehandle, $this->line_length, '|')) {
            if (count($row)<5) {
                /** address book is corrupted. */
                global $color;
                error_box(_("Address book is corrupted. Required fields are missing."),$color);
                die('</body></html>');
            } else {
                if(strtolower($row[$field]) == $value) {
                    return array('nickname'  => $row[0],
                                 'name'      => $row[1] . ' ' . $row[2],
                                 'firstname' => $row[1],
                                 'lastname'  => $row[2],
                                 'email'     => $row[3],
                                 'label'     => $row[4],
                                 'backend'   => $this->bnum,
                                 'source'    => &$this->sname);
                }
            }
        }

        return array();
    }

    /**
     * List all addresses
     * @return array list of all addresses
     */
    function list_addr() {
        // check if listing is not disabled
        if(isset($this->listing) && !$this->listing) {
            return array();
        }

        $res = array();
        $this->open();
        @rewind($this->filehandle);

        while ($row = @fgetcsv($this->filehandle, $this->line_length, '|')) {
            if (count($row)<5) {
                /** address book is corrupted. */
                global $color;
                error_box(_("Address book is corrupted. Required fields are missing."),$color);
                die('</body></html>');
            } else {
                array_push($res, array('nickname'  => $row[0],
                                       'name'      => $row[1] . ' ' . $row[2],
                                       'firstname' => $row[1],
                                       'lastname'  => $row[2],
                                       'email'     => $row[3],
                                       'label'     => $row[4],
                                       'backend'   => $this->bnum,
                                       'source'    => &$this->sname));
            }
        }
        return $res;
    }

    /**
     * Add address
     * @param array $userdata new data
     * @return bool
     */
    function add($userdata) {
        if(!$this->writeable) {
            return $this->set_error(_("Address book is read-only"));
        }
        /* See if user exists already */
        $ret = $this->lookup($userdata['nickname']);
        if(!empty($ret)) {
            // i18n: don't use html formating in translation
            return $this->set_error(sprintf(_("User \"%s\" already exists"), $ret['nickname']));
        }

        /* Here is the data to write */
        $data = $this->quotevalue($userdata['nickname']) . '|' .
                $this->quotevalue($userdata['firstname']) . '|' .
                $this->quotevalue((!empty($userdata['lastname'])?$userdata['lastname']:'')) . '|' .
                $this->quotevalue($userdata['email']) . '|' .
                $this->quotevalue((!empty($userdata['label'])?$userdata['label']:''));

        /* Strip linefeeds */
		$nl_str = array("\r","\n");
		$data = str_replace($nl_str, ' ', $data);

        /**
         * Make sure that entry fits into allocated record space.
         * One byte is reserved for linefeed
         */
        if (strlen($data) >= $this->line_length) {
            return $this->set_error(_("Address book entry is too big"));
        }

        /* Add linefeed at end */
        $data = $data . "\n";

        /* Reopen file, just to be sure */
        $this->open(true);
        if(!$this->writeable) {
            return $this->set_error(_("Address book is read-only"));
        }

        /* Lock the file */
        if(!$this->lock()) {
            return $this->set_error(_("Could not lock datafile"));
        }

        /* Write */
        $r = sq_fwrite($this->filehandle, $data);

        /* Unlock file */
        $this->unlock();

        /* Test write result */
        if($r === FALSE) {
            /* Fail */
            $this->set_error(_("Write to address book failed"));
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Delete address
     * @param string $alias alias that has to be deleted
     * @return bool
     */
    function remove($alias) {
        if(!$this->writeable) {
            return $this->set_error(_("Address book is read-only"));
        }

        /* Lock the file to make sure we're the only process working
         * on it. */
        if(!$this->lock()) {
            return $this->set_error(_("Could not lock datafile"));
        }

        /* Read file into memory, ignoring nicknames to delete */
        @rewind($this->filehandle);
        $i = 0;
        $rows = array();
        while($row = @fgetcsv($this->filehandle, $this->line_length, '|')) {
            if(!in_array($row[0], $alias)) {
                $rows[$i++] = $row;
            }
        }

        /* Write data back */
        if(!$this->overwrite($rows)) {
            $this->unlock();
            return false;
        }

        $this->unlock();
        return true;
    }

    /**
     * Modify address
     * @param string $alias modified alias
     * @param array $userdata new data
     * @return bool true, if operation successful
     */
    function modify($alias, $userdata) {
        if(!$this->writeable) {
            return $this->set_error(_("Address book is read-only"));
        }

        /* See if user exists */
        $ret = $this->lookup($alias);
        if(empty($ret)) {
            // i18n: don't use html formating in translation
            return $this->set_error(sprintf(_("User \"%s\" does not exist"), $alias));
        }

        /* If the alias changed, see if the new alias exists */
        if (strtolower($alias) != strtolower($userdata['nickname'])) {
            $ret = $this->lookup($userdata['nickname']);
            if (!empty($ret)) {
                return $this->set_error(sprintf(_("User \"%s\" already exists"), $userdata['nickname']));
            }
        }

        /* calculate userdata size */
        $data = $this->quotevalue($userdata['nickname']) . '|'
            . $this->quotevalue($userdata['firstname']) . '|'
            . $this->quotevalue((!empty($userdata['lastname'])?$userdata['lastname']:'')) . '|'
            . $this->quotevalue($userdata['email']) . '|'
            . $this->quotevalue((!empty($userdata['label'])?$userdata['label']:''));
        /* make sure that it fits into allocated space */
        if (strlen($data) >= $this->line_length) {
            return $this->set_error(_("Address book entry is too big"));
        }

        /* Lock the file to make sure we're the only process working
         * on it. */
        if(!$this->lock()) {
            return $this->set_error(_("Could not lock datafile"));
        }

        /* Read file into memory, modifying the data for the
         * user identified by $alias */
        $this->open(true);
        @rewind($this->filehandle);
        $i = 0;
        $rows = array();
        while($row = @fgetcsv($this->filehandle, $this->line_length, '|')) {
            if(strtolower($row[0]) != strtolower($alias)) {
                $rows[$i++] = $row;
            } else {
                $rows[$i++] = array(0 => $userdata['nickname'],
                                    1 => $userdata['firstname'],
                                    2 => (!empty($userdata['lastname'])?$userdata['lastname']:''),
                                    3 => $userdata['email'],
                                    4 => (!empty($userdata['label'])?$userdata['label']:''));
            }
        }

        /* Write data back */
        if(!$this->overwrite($rows)) {
            $this->unlock();
            return false;
        }

        $this->unlock();
        return true;
    }

    /**
     * Function for quoting values before saving
     * @param string $value string that has to be quoted
     * @param string quoted string
     */
    function quotevalue($value) {
        /* Quote the field if it contains | or ". Double quotes need to
         * be replaced with "" */
        if(stristr($value, '"') || stristr($value, '|')) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

} /* End of class abook_local_file */
