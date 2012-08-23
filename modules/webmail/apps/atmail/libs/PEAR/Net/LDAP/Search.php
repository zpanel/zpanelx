<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +--------------------------------------------------------------------------+
// | Net_LDAP                                                                 |
// +--------------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                    |
// +--------------------------------------------------------------------------+
// | This library is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU Lesser General Public               |
// | License as published by the Free Software Foundation; either             |
// | version 2.1 of the License, or (at your option) any later version.       |
// |                                                                          |
// | This library is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU        |
// | Lesser General Public License for more details.                          |
// |                                                                          |
// | You should have received a copy of the GNU Lesser General Public         |
// | License along with this library; if not, write to the Free Software      |
// | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA |
// +--------------------------------------------------------------------------+
// | Authors: Tarjej Huse, Benedikt Hallinger                                 |
// +--------------------------------------------------------------------------+
//
// $Id: Search.php,v 1.21 2007/06/13 11:49:21 beni Exp $

/**
 * Result set of an LDAP search
 *
 * @author  Tarjei Huse
 * @author  Benedikt Hallinger <beni@php.net>
 * @version $Revision: 1.21 $
 * @package Net_LDAP
 */
class Net_LDAP_Search extends PEAR
{
    /**
     * Search result identifier
     *
     * @access private
     * @var resource
     */
    var $_search;

    /**
     * LDAP resource link
     *
     * @access private
     * @var resource
     */
    var $_link;

    /**
     * Net_LDAP object
     *
     * A reference of the Net_LDAP object for passing to Net_LDAP_Entry
     *
     * @access private
     * @var object Net_LDAP
     */
    var $_ldap;

    /**
     * Result entry identifier
     *
     * @access private
     * @var resource
     */
    var $_entry = null;

    /**
     * The errorcode the search got
     *
     * Some errorcodes might be of interest, but might not be best handled as errors.
     * examples: 4 - LDAP_SIZELIMIT_EXCEEDED - indicates a huge search.
     *               Incomplete results are returned. If you just want to check if there's anything in the search.
     *               than this is a point to handle.
     *           32 - no such object - search here returns a count of 0.
     *
     * @access private
     * @var int
     */
    var $_errorCode = 0; // if not set - sucess!

    /**
    * What attributes we searched for
    *
    * The $attributes array contains the names of the searched attributes and gets
    * passed from $Net_LDAP->search() so the Net_LDAP_Search object can tell
    * what attributes was searched for ({@link _searchedAttrs())
    *
    * This variable gets set from the constructor and returned
    * from {@link _searchedAttrs()}
    *
    * @access private
    * @var array
    */
    var $_searchedAttrs = array();

    /**
    * Cache variable for storing entries fetched internally
    *
    * This currently is only used by {@link pop_entry()}
    *
    * @access private
    * @var array
    */
    var $_entry_cache = false;

   /**
    * Constructor
    *
    * @access protected
    * @param resource $search         Search result identifier
    * @param Net_LDAP|resource $ldap  Net_LDAP object or just a LDAP-Link resource
    * @param array $attributes        (optional) Array with searched attribute names. (see {@link $_searchedAttrs})
    */
    function Net_LDAP_Search(&$search, &$ldap, $attributes = array())
    {
        $this->PEAR('Net_LDAP_Error');

        $this->setSearch($search);

        if (is_a($ldap, 'Net_LDAP')) {
            $this->_ldap =& $ldap;
            $this->setLink($this->_ldap->getLink());
        } else {
            $this->setLink($ldap);
        }

        $this->_errorCode = @ldap_errno($this->_link);

        if (is_array($attributes) && !empty($attributes)) {
            $this->_searchedAttrs = $attributes;
        }
    }

    /**
     * Returns an assosiative array of entry objects
     *
     * @return array Array of entry objects.
     */
    function entries()
    {
        $entries = array();

        while ($entry = $this->shiftEntry()) {
            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * Get the next entry in the searchresult.
     *
     * This will return a valid Net_LDAP_Entry object or false, so
     * you can use this method to easily iterate over the entries inside
     * a while loop.
     *
     * @return Net_LDAP_Entry|false  Reference to Net_LDAP_Entry object or false
     */
    function &shiftEntry()
    {
        if ($this->count() == 0 ) {
            $false = false;
            return $false;
        }

        if (is_null($this->_entry)) {
            $this->_entry = @ldap_first_entry($this->_link, $this->_search);
            $entry = new Net_LDAP_Entry($this->_ldap, $this->_entry);
        } else {
            if (!$this->_entry = @ldap_next_entry($this->_link, $this->_entry)) {
                $false = false;
                return $false;
            }
            $entry = new Net_LDAP_Entry($this->_ldap, $this->_entry);
        }
        return $entry;
    }

    /**
     * Alias function of shiftEntry() for perl-ldap interface
     *
     * @see shiftEntry()
     */
    function shift_entry()
    {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'shiftEntry' ), $args);
    }

    /**
     * Retrieve the next entry in the searchresult, but starting from last entry
     *
     * This is the opposite to {@link shiftEntry()} and is also very useful
     * to be used inside a while loop.
     *
     * @return Net_LDAP_Entry|false
     */
    function popEntry()
    {
        if (false === $this->_entry_cache) {
            // fetch entries into cache if not done so far
            $this->_entry_cache = $this->entries();
        }

        $return = array_pop($this->_entry_cache);
        return (null === $return)? false : $return;
    }

    /**
     * Alias function of popEntry() for perl-ldap interface
     *
     * @see popEntry()
     */
    function pop_entry()
    {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'popEntry' ), $args);
    }

    /**
    * Return entries sorted as array
    *
    * This returns a array with sorted entries and the values.
    * Sorting is done with PHPs {@link array_multisort()}.
    * This method relies on {@link as_struct()} to fetch the raw data of the entries.
    *
    * Please note that attribute names are case sensitive!
    *
    * Usage example:
    * <code>
    *   // to sort entries first by location, then by surename, but descending:
    *   $entries = $search->sorted_as_struct(array('locality','sn'), SORT_DESC);
    * </code>
    *
    * @param array      $attrs Array of attribute names to sort; order from left to right.
    * @param int        $order Ordering direction, either constant SORT_ASC or SORT_DESC
    * @return array|Net_LDAP_Error   Array with sorted entries or error
    */
    function sorted_as_struct($attrs = array('cn'), $order = SORT_ASC)
    {
        /*
        * Old Code, suitable and fast for single valued sorting
        * This code should be used if we know that single valued sorting is desired,
        * but we need some method to get that knowledge...
        */
        /*
        $attrs = array_reverse($attrs);
        foreach ($attrs as $attribute) {
            if (!ldap_sort($this->_link, $this->_search, $attribute)){
                $this->raiseError("Sorting failed for Attribute " . $attribute);
            }
        }

        $results = ldap_get_entries($this->_link, $this->_search);

        unset($results['count']); //for tidier output
        if ($order) {
            return array_reverse($results);
        } else {
            return $results;
        }*/

        /*
        * New code: complete "client side" sorting
        */
        // first some parameterchecks
        if (!is_array($attrs)) {
            return PEAR::raiseError("Sorting failed: Parameterlist must be an array!");
        }
        if ($order != SORT_ASC && $order != SORT_DESC) {
            return PEAR::raiseError("Sorting failed: sorting direction not understood! (neither constant SORT_ASC nor SORT_DESC)");
        }

        // fetch the entries data
        $entries = $this->as_struct();

        // now sort each entries attribute values
        // this is neccessary because later we can only sort by one value,
        // so we need the highest or lowest attribute now, depending on the
        // selected ordering for that specific attribute
        foreach ($entries as $dn => $entry) {
            foreach ($entry as $attr_name => $attr_values) {
                sort($entries[$dn][$attr_name]);
                if($order == SORT_DESC) {
                    array_reverse($entries[$dn][$attr_name]);
                }
            }
        }

        // reformat entrys array for later use with array_multisort()
        $to_sort = array(); // <- will be a numeric array similar to ldap_get_entries
        foreach ($entries as $dn => $entry_attr) {
            $row = array();
            $row['dn'] = $dn;
            foreach ($entry_attr as $attr_name => $attr_values) {
                $row[$attr_name] = $attr_values;
            }
            $to_sort[] = $row;
        }

        // Build columns for array_multisort()
        // each requested attribute is one row
        $columns = array();
        foreach ($attrs as $attr_name) {
            foreach ($to_sort as $key => $row) {
                $columns[$attr_name][$key] =& $to_sort[$key][$attr_name][0];
            }
        }

        // sort the colums with array_multisort
        $sort_params = '';
        foreach ($attrs as $attr_name) {
            $sort_params .= '$columns[\''.$attr_name.'\'], '.$order.', ';
        }
        eval("array_multisort($sort_params \$to_sort);"); // perform sorting

        return $to_sort;
    }

    /**
    * Return entries sorted as objects
    *
    * This returns a array with sorted Net_LDAP_Entry objects.
    * The sorting is actually done with {@link sorted_as_struct()}.
    *
    * Please note that attribute names are case sensitive!
    *
    * Usage example:
    * <code>
    *   // to sort entries first by location, then by surename, but descending:
    *   $entries = $search->sorted(array('locality','sn'), SORT_DESC);
    * </code>
    *
    * @param array      $attrs Array of sort attributes to sort; order from left to right.
    * @param int        $order Ordering direction, either constant SORT_ASC or SORT_DESC
    * @return array|Net_LDAP_Error   Array with sorted Net_LDAP_Entries or error
    */
    function sorted($attrs = array('cn'), $order = SORT_ASC) {
        $return = array();
        $sorted = $this->sorted_as_struct($attrs, $order);
        if (PEAR::isError($sorted)) {
            return $sorted;
        }
        foreach ($sorted as $key => $row) {
            $entry = $this->_ldap->getEntry($row['dn'], $this->_searchedAttrs());
            if (!PEAR::isError($entry)) {
                array_push($return, $entry);
            } else {
                return $entry;
            }
        }
        return $return;
    }

   /**
    * Return entries as array
    *
    * This method returns the entries and the selected attributes values as
    * array.
    * The first array level contains all found entries where the keys are the
    * DNs of the entries. The second level arrays contian the entries attributes
    * such that the keys is the lowercased name of the attribute and the values
    * are stored in another indexed array. Note that the attribute values are stored
    * in an array even if there is no or just one value.
    *
    * The array has the following structure:
    * <code>
    * $return = array(
    *           'cn=foo,dc=example,dc=com' => array(
    *                                                'sn'       => array('foo'),
    *                                                'multival' => array('val1', 'val2', 'valN')
    *                                             )
    *           'cn=bar,dc=example,dc=com' => array(
    *                                                'sn'       => array('bar'),
    *                                                'multival' => array('val1', 'valN')
    *                                             )
    *           )
    * </code>
    *
    * @return array      associative result array as described above
    */
    function as_struct()
    {
        $return = array();
        $entries = $this->entries();
        foreach ($entries as $entry) {
        	$attrs = array();
        	$entry_attributes = $entry->attributes();
        	foreach ($entry_attributes as $attr_name) {
        		$attr_values = $entry->getValue($attr_name, 'all');
        		if (!is_array($attr_values)) {
        			$attr_values = array($attr_values);
        		}
        		$attrs[$attr_name] = $attr_values;
        	}
            $return[$entry->dn()] = $attrs;
        }
        return $return;
    }

   /**
    * Set the search objects resource link
    *
    * @access public
    * @param resource $search Search result identifier
    * @return void
    */
    function setSearch(&$search)
    {
        $this->_search = $search;
    }

   /**
    * Set the ldap ressource link
    *
    * @access public
    * @param resource $link Link identifier
    * @return void
    */
    function setLink(&$link)
    {
        $this->_link = $link;
    }

   /**
    * Returns the number of entries in the searchresult
    *
    * @return int Number of entries in search.
    */
    function count()
    {
        /* this catches the situation where OL returned errno 32 = no such object! */
        if (!$this->_search) {
            return 0;
        }
        return @ldap_count_entries($this->_link, $this->_search);
    }

    /**
     * Get the errorcode the object got in its search.
     *
     * @return int The ldap error number.
     */
    function getErrorCode()
    {
        return $this->_errorCode;
    }

   /**
    * Destructor
    *
    * @access protected
    */
    function _Net_LDAP_Search()
    {
        @ldap_free_result($this->_search);
    }

   /**
    * Closes search result
    */
    function done()
    {
        $this->_Net_LDAP_Search();
    }

    /**
    * Return the attribute names this search selected
    *
    * @return array
    * @see $_searchedAttrs
    * @access private
    */
    function _searchedAttrs()
    {
        return $this->_searchedAttrs;
    }
}

?>