<?php

/**
 * functions/addressbook.php - Functions and classes for the addressbook system
 *
 * Functions require SM_PATH and support of forms.php functions
 *
 * @copyright 1999-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: addressbook.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage addressbook
 */

/**
 * If SM_PATH isn't defined, define it.  Required to include files.
 * @ignore
 */
if (!defined('SM_PATH'))  {
    define('SM_PATH','../');
}

/* make sure that display_messages.php is loaded */
include_once(SM_PATH . 'functions/display_messages.php');

global $addrbook_dsn, $addrbook_global_dsn;

/**
   Create and initialize an addressbook object.
   Returns the created object
*/
function addressbook_init($showerr = true, $onlylocal = false) {
    global $data_dir, $username, $color, $ldap_server, $address_book_global_filename;
    global $addrbook_dsn, $addrbook_table;
    // Shared file based address book globals
    global $abook_global_file, $abook_global_file_writeable, $abook_global_file_listing;
    // Shared DB based address book globals
    global $addrbook_global_dsn, $addrbook_global_table, $addrbook_global_writeable, $addrbook_global_listing;
    // Record size restriction in file based address books
    global $abook_file_line_length;

    /* Create a new addressbook object */
    $abook = new AddressBook;

    /* Create empty error message */
    $abook_init_error='';

    /*
        Always add a local backend. We use *either* file-based *or* a
        database addressbook. If $addrbook_dsn is set, the database
        backend is used. If not, addressbooks are stores in files.
    */
    if (isset($addrbook_dsn) && !empty($addrbook_dsn)) {
        /* Database */
        if (!isset($addrbook_table) || empty($addrbook_table)) {
            $addrbook_table = 'address';
        }
        $r = $abook->add_backend('database', Array('dsn' => $addrbook_dsn,
                            'owner' => $username,
                            'table' => $addrbook_table));
        if (!$r && $showerr) {
            $abook_init_error.=_("Error initializing address book database.") .' '. $abook->error;
        }
    } else {
        /* File */
        $filename = getHashedFile($username, $data_dir, "$username.abook");
        $r = $abook->add_backend('local_file', Array('filename' => $filename,
                                                     'umask' => 0077,
                                                     'line_length' => $abook_file_line_length,
                                                     'create'   => true));
        if(!$r && $showerr) {
            $abook_init_error.=sprintf( _("Error opening file %s"), $filename );
        }
    }

    /* This would be for the global addressbook */
    if (isset($abook_global_file) && isset($abook_global_file_writeable)
        && trim($abook_global_file)!=''){
        // Detect place of address book
        if (! preg_match("/[\/\\\]/",$abook_global_file)) {
            /* no path chars, address book stored in data directory
             * make sure that there is a slash between data directory
             * and address book file name
             */
            $abook_global_filename=$data_dir
                . ((substr($data_dir, -1) != '/') ? '/' : '')
                . $abook_global_file;
        } elseif (preg_match("/^\/|\w:/",$abook_global_file)) {
            // full path is set in options (starts with slash or x:)
            $abook_global_filename=$abook_global_file;
        } else {
            $abook_global_filename=SM_PATH . $abook_global_file;
        }
        $r = $abook->add_backend('local_file',array('filename'=>$abook_global_filename,
                                                    'name' => _("Global address book"),
                                                    'detect_writeable' => false,
                                                    'line_length' => $abook_file_line_length,
                                                    'writeable'=> $abook_global_file_writeable,
                                                    'listing' => $abook_global_file_listing));
        if (!$r && $showerr) {
            if ($abook_init_error!='') $abook_init_error.="\n";
            $abook_init_error.=_("Error initializing global address book.") . "\n" . $abook->error;
        }
    }

    /* Load global addressbook from SQL if configured */
    if (isset($addrbook_global_dsn) && !empty($addrbook_global_dsn)) {
        /* Database configured */
        if (!isset($addrbook_global_table) || empty($addrbook_global_table)) {
            $addrbook_global_table = 'global_abook';
        }
        $r = $abook->add_backend('database',
                                 Array('dsn' => $addrbook_global_dsn,
                                       'owner' => 'global',
                                       'name' => _("Global address book"),
                                       'writeable' => $addrbook_global_writeable,
                                       'listing' => $addrbook_global_listing,
                                       'table' => $addrbook_global_table));
        if (!$r && $showerr) {
            if ($abook_init_error!='') $abook_init_error.="\n";
            $abook_init_error.=_("Error initializing global address book.") . "\n" . $abook->error;
    }
    }

    /*
     * hook allows to include different address book backends.
     * plugins should extract $abook and $r from arguments
     * and use same add_backend commands as above functions.
     * @since 1.5.1 and 1.4.5
     */
    $hookReturn = do_hook('abook_init', $abook, $r);
    $abook = $hookReturn[1];
    $r = $hookReturn[2];

    if (! $onlylocal) {
    /* Load configured LDAP servers (if PHP has LDAP support) */
    if (isset($ldap_server) && is_array($ldap_server) && function_exists('ldap_connect')) {
        reset($ldap_server);
        while (list($undef,$param) = each($ldap_server)) {
            if (is_array($param)) {
                $r = $abook->add_backend('ldap_server', $param);
                if (!$r && $showerr) {
                        if ($abook_init_error!='') $abook_init_error.="\n";
                        $abook_init_error.=sprintf(_("Error initializing LDAP server %s:") .
                            "\n", $param['host']);
                        $abook_init_error.= $abook->error;
                    }
                }
            }
        }
    } // end of remote abook backends init

    /**
     * display address book init errors.
     */
    if ($abook_init_error!='' && $showerr) {
        $abook_init_error = htmlspecialchars($abook_init_error);
        error_box($abook_init_error,$color);
    }

    /* Return the initialized object */
    return $abook;
}


/*
 *   Had to move this function outside of the Addressbook Class
 *   PHP 4.0.4 Seemed to be having problems with inline functions.
 */
function addressbook_cmp($a,$b) {

    if($a['backend'] > $b['backend']) {
        return 1;
    } else if($a['backend'] < $b['backend']) {
        return -1;
    }

    return (strtolower($a['name']) > strtolower($b['name'])) ? 1 : -1;

}

/**
 * Sort array by the key "name"
 */
function alistcmp($a,$b) {
    $abook_sort_order=get_abook_sort();

    switch ($abook_sort_order) {
    case 0:
    case 1:
      $abook_sort='nickname';
      break;
    case 4:
    case 5:
      $abook_sort='email';
      break;
    case 6:
    case 7:
      $abook_sort='label';
      break;
    case 2:
    case 3:
    case 8:
    default:
      $abook_sort='name';
    }

    if ($a['backend'] > $b['backend']) {
        return 1;
    } else {
        if ($a['backend'] < $b['backend']) {
            return -1;
        }
    }

    if( (($abook_sort_order+2) % 2) == 1) {
      return (strtolower($a[$abook_sort]) < strtolower($b[$abook_sort])) ? 1 : -1;
    } else {
      return (strtolower($a[$abook_sort]) > strtolower($b[$abook_sort])) ? 1 : -1;
    }
}

/**
 * Address book sorting options
 *
 * returns address book sorting order
 * @return integer book sorting options order
 */
function get_abook_sort() {
    global $data_dir, $username;

    /* get sorting order */
    if(sqgetGlobalVar('abook_sort_order', $temp, SQ_GET)) {
      $abook_sort_order = (int) $temp;

      if ($abook_sort_order < 0 or $abook_sort_order > 8)
        $abook_sort_order=8;

      setPref($data_dir, $username, 'abook_sort_order', $abook_sort_order);
    } else {
      /* get previous sorting options. default to unsorted */
      $abook_sort_order = getPref($data_dir, $username, 'abook_sort_order', 8);
    }

    return $abook_sort_order;
}

/**
 * This function shows the address book sort button.
 *
 * @param integer $abook_sort_order current sort value
 * @param string $alt_tag alt tag value (string visible to text only browsers)
 * @param integer $Down sort value when list is sorted ascending
 * @param integer $Up sort value when list is sorted descending
 * @return string html code with sorting images and urls
 * @since 1.5.1 and 1.4.6
 */
function show_abook_sort_button($abook_sort_order, $alt_tag, $Down, $Up ) {
    global $form_url;

     /* Figure out which image we want to use. */
    if ($abook_sort_order != $Up && $abook_sort_order != $Down) {
        $img = 'sort_none.png';
        $which = $Up;
    } elseif ($abook_sort_order == $Up) {
        $img = 'up_pointer.png';
        $which = $Down;
    } else {
        $img = 'down_pointer.png';
        $which = 8;
    }

      /* Now that we have everything figured out, show the actual button. */
    return ' <a href="' . $form_url .'?abook_sort_order=' . $which
         . '"><img src="../images/' . $img
         . '" border="0" width="12" height="10" alt="' . $alt_tag . '" title="'
         . _("Click here to change the sorting of the address list") .'" /></a>';
}


/**
 * This is the main address book class that connect all the
 * backends and provide services to the functions above.
 * @package squirrelmail
 */

class AddressBook {

    var $backends    = array();
    var $numbackends = 0;
    var $error       = '';
    var $localbackend = 0;
    var $localbackendname = '';
    var $add_extra_field = false;

      // Constructor function.
    function AddressBook() {
        $this->localbackendname = _("Personal address book");
    }

    /*
     * Return an array of backends of a given type,
     * or all backends if no type is specified.
     */
    function get_backend_list($type = '') {
        $ret = array();
        for ($i = 1 ; $i <= $this->numbackends ; $i++) {
            if (empty($type) || $type == $this->backends[$i]->btype) {
                $ret[] = &$this->backends[$i];
            }
        }
        return $ret;
    }


    /*
       ========================== Public ========================

        Add a new backend. $backend is the name of a backend
        (without the abook_ prefix), and $param is an optional
        mixed variable that is passed to the backend constructor.
        See each of the backend classes for valid parameters.
     */
    function add_backend($backend, $param = '') {
        $backend_name = 'abook_' . $backend;
        eval('$newback = new ' . $backend_name . '($param);');
        if(!empty($newback->error)) {
            $this->error = $newback->error;
            return false;
        }

        $this->numbackends++;

        $newback->bnum = $this->numbackends;
        $this->backends[$this->numbackends] = $newback;

        /* Store ID of first local backend added */
        if ($this->localbackend == 0 && $newback->btype == 'local') {
            $this->localbackend = $this->numbackends;
            $this->localbackendname = $newback->sname;
        }

        return $this->numbackends;
    }


    /*
     * This function takes a $row array as returned by the addressbook
     * search and returns an e-mail address with the full name or
     * nickname optionally prepended.
     */

    function full_address($row) {
        global $data_dir, $username;
        $addrsrch_fullname = getPref($data_dir, $username, 'addrsrch_fullname', 'fullname');

        // allow multiple addresses in one row (poor person's grouping - bah)
        // (separate with commas)
        //
        $return = '';
        $addresses = explode(',', $row['email']);
        foreach ($addresses as $address) {

            if (!empty($return)) $return .= ', ';

            if ($addrsrch_fullname == 'fullname')
                $return .= '"' . $row['name'] . '" <' . trim($address) . '>';
            else if ($addrsrch_fullname == 'nickname')
                $return .= '"' . $row['nickname'] . '" <' . trim($address) . '>';
            else // "noprefix"
                $return .= trim($address);

        }

        return $return;
    }

    /*
        Return a list of addresses matching expression in
        all backends of a given type.
    */
    function search($expression, $bnum = -1) {
        $ret = array();
        $this->error = '';

        /* Search all backends */
        if ($bnum == -1) {
            $sel = $this->get_backend_list('');
            $failed = 0;
            for ($i = 0 ; $i < sizeof($sel) ; $i++) {
                $backend = &$sel[$i];
                $backend->error = '';
                $res = $backend->search($expression);
                if (is_array($res)) {
                    $ret = array_merge($ret, $res);
                } else {
                    $this->error .= "\n" . $backend->error;
                    $failed++;
                }
            }

            /* Only fail if all backends failed */
            if( $failed >= sizeof( $sel ) ) {
                $ret = FALSE;
            }

        }  else {

            /* Search only one backend */

            $ret = $this->backends[$bnum]->search($expression);
            if (!is_array($ret)) {
                $this->error .= "\n" . $this->backends[$bnum]->error;
                $ret = FALSE;
            }
        }

        return( $ret );
    }


    /* Return a sorted search */
    function s_search($expression, $bnum = -1) {

        $ret = $this->search($expression, $bnum);
        if ( is_array( $ret ) ) {
            usort($ret, 'addressbook_cmp');
        }
        return $ret;
    }


    /*
     *  Lookup an address by the indicated field. Only
     *  possible in local backends.
     */
    function lookup($value, $bnum = -1, $field = SM_ABOOK_FIELD_NICKNAME) {

        $ret = array();

        if ($bnum > -1) {
            $res = $this->backends[$bnum]->lookup($value, $field);
            if (is_array($res)) {
               return $res;
            } else {
               $this->error = $this->backends[$bnum]->error;
               return false;
            }
        }

        $sel = $this->get_backend_list('local');
        for ($i = 0 ; $i < sizeof($sel) ; $i++) {
            $backend = &$sel[$i];
            $backend->error = '';
            $res = $backend->lookup($value, $field);

            // return an address if one is found
            // (empty array means lookup concluded
            // but no result found - in this case,
            // proceed to next backend)
            //
            if (is_array($res)) {
                if (!empty($res)) return $res;
            } else {
                $this->error = $backend->error;
                return false;
            }
        }

        return $ret;
    }


    /* Return all addresses */
    function list_addr($bnum = -1) {
        $ret = array();

        if ($bnum == -1) {
            $sel = $this->get_backend_list('');
        } else {
            $sel = array(0 => &$this->backends[$bnum]);
        }

        for ($i = 0 ; $i < sizeof($sel) ; $i++) {
            $backend = &$sel[$i];
            $backend->error = '';
            $res = $backend->list_addr();
            if (is_array($res)) {
               $ret = array_merge($ret, $res);
            } else {
               $this->error = $backend->error;
               return false;
            }
        }

        return $ret;
    }

    /*
     * Create a new address from $userdata, in backend $bnum.
     * Return the backend number that the/ address was added
     * to, or false if it failed.
     */
    function add($userdata, $bnum) {

        /* Validate data */
        if (!is_array($userdata)) {
            $this->error = _("Invalid input data");
            return false;
        }
        if (empty($userdata['firstname']) && empty($userdata['lastname'])) {
            $this->error = _("Name is missing");
            return false;
        }
        if (empty($userdata['email'])) {
            $this->error = _("E-mail address is missing");
            return false;
        }
        if (empty($userdata['nickname'])) {
            $userdata['nickname'] = $userdata['email'];
        }

        if (preg_match('/[ :|#"!]/', $userdata['nickname'])) {
            $this->error = _("Nickname contains illegal characters");
            return false;
        }

        /* Check that specified backend accept new entries */
        if (!$this->backends[$bnum]->writeable) {
            $this->error = _("Address book is read-only");
            return false;
        }

        /* Add address to backend */
        $res = $this->backends[$bnum]->add($userdata);
        if ($res) {
            return $bnum;
        } else {
            $this->error = $this->backends[$bnum]->error;
            return false;
        }

        return false;  // Not reached
    } /* end of add() */


    /*
     * Remove the user identified by $alias from backend $bnum
     * If $alias is an array, all users in the array are removed.
     */
    function remove($alias, $bnum) {

        /* Check input */
        if (empty($alias)) {
            return true;
        }

        /* Convert string to single element array */
        if (!is_array($alias)) {
            $alias = array(0 => $alias);
        }

        /* Check that specified backend is writable */
        if (!$this->backends[$bnum]->writeable) {
            $this->error = _("Address book is read-only");
            return false;
        }

        /* Remove user from backend */
        $res = $this->backends[$bnum]->remove($alias);
        if ($res) {
            return $bnum;
        } else {
            $this->error = $this->backends[$bnum]->error;
            return false;
        }

        return FALSE;  /* Not reached */
    } /* end of remove() */


    /*
     * Remove the user identified by $alias from backend $bnum
     * If $alias is an array, all users in the array are removed.
     */
    function modify($alias, $userdata, $bnum) {

        /* Check input */
        if (empty($alias) || !is_string($alias)) {
            return true;
        }

        /* Validate data */
        if(!is_array($userdata)) {
            $this->error = _("Invalid input data");
            return false;
        }
        if (empty($userdata['firstname']) && empty($userdata['lastname'])) {
            $this->error = _("Name is missing");
            return false;
        }
        if (empty($userdata['email'])) {
            $this->error = _("E-mail address is missing");
            return false;
        }

        if (preg_match('/[: |#"!]/', $userdata['nickname'])) {
            $this->error = _("Nickname contains illegal characters");
            return false;
        }

        if (empty($userdata['nickname'])) {
            $userdata['nickname'] = $userdata['email'];
        }

        /* Check that specified backend is writable */
        if (!$this->backends[$bnum]->writeable) {
            $this->error = _("Address book is read-only");;
            return false;
        }

        /* Modify user in backend */
        $res = $this->backends[$bnum]->modify($alias, $userdata);
        if ($res) {
            return $bnum;
        } else {
            $this->error = $this->backends[$bnum]->error;
            return false;
        }

        return FALSE;  /* Not reached */
    } /* end of modify() */


} /* End of class Addressbook */

/**
 * Generic backend that all other backends extend
 * @package squirrelmail
 */
class addressbook_backend {

    /* Variables that all backends must provide. */
    var $btype      = 'dummy';
    var $bname      = 'dummy';
    var $sname      = 'Dummy backend';

    /*
     * Variables common for all backends, but that
     * should not be changed by the backends.
     */
    var $bnum       = -1;
    var $error      = '';
    var $writeable  = false;

    function set_error($string) {
        $this->error = '[' . $this->sname . '] ' . $string;
        return false;
    }


    /* ========================== Public ======================== */

    function search($expression) {
        $this->set_error('search not implemented');
        return false;
    }

    function lookup($value, $field) {
        $this->set_error('lookup not implemented');
        return false;
    }

    function list_addr() {
        $this->set_error('list_addr not implemented');
        return false;
    }

    function add($userdata) {
        $this->set_error('add not implemented');
        return false;
    }

    function remove($alias) {
        $this->set_error('delete not implemented');
        return false;
    }

    function modify($alias, $newuserdata) {
        $this->set_error('modify not implemented');
        return false;
    }

}

/*
  PHP 5 requires that the class be made first, which seems rather
  logical, and should have been the way it was generated the first time.
*/

require_once(SM_PATH . 'functions/abook_local_file.php');
require_once(SM_PATH . 'functions/abook_ldap_server.php');

/* Only load database backend if database is configured */
if((isset($addrbook_dsn) && !empty($addrbook_dsn)) ||
 (isset($addrbook_global_dsn) && !empty($addrbook_global_dsn))) {
  include_once(SM_PATH . 'functions/abook_database.php');
}

/*
 * hook allows adding different address book classes.
 * class must follow address book class coding standards.
 *
 * see addressbook_backend class and functions/abook_*.php files.
 * @since 1.5.1 and 1.4.5
 */
do_hook('abook_add_class');
