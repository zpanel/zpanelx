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
// | Authors: Jan Wagner, Tarjej Huse                                         |
// +--------------------------------------------------------------------------+
//
// $Id: Entry.php,v 1.42 2007/06/28 07:59:44 beni Exp $

require_once("PEAR.php");
require_once('Util.php');

/**
 * Object representation of a directory entry
 *
 * This class represents a directory entry. You can add, delete, replace
 * attributes and their values, rename the entry, delete the entry.
 *
 * @package Net_LDAP
 * @author Jan Wagner <wagner@netsols.de>
 * @author Tarjej Huse
 * @version $Revision: 1.42 $
 */

class Net_LDAP_Entry extends PEAR
{
    /**
     * Entry ressource identifier
     *
     * @access private
     * @var ressourcee
     */
    var $_entry = null;

    /**
     * LDAP ressource identifier
     *
     * @access private
     * @var ressource
     */
    var $_link = null;

    /**
     * Net_LDAP object
     *
     * This object will be used for updating and schema checking
     *
     * @access private
     * @var object Net_LDAP
     */
    var $_ldap = null;

    /**
     * Distinguished name of the entry
     *
     * @access private
     * @var string
     */
    var $_dn = null;

    /**
     * Attributes
     *
     * @access private
     * @var array
     */
    var $_attributes = array();

    /**
     * Original attributes before any modification
     *
     * @access private
     * @var array
     */
    var $_original = array();


    /**
     * Map of attribute names
     *
     * @access private
     * @var array
     */
    var $_map = array();


    /**
     * Is this a new entry?
     *
     * @access private
     * @var boolean
     */
    var $_new = true;

    /**
     * New distinguished name
     *
     * @access private
     * @var string
     */
    var $_newdn = null;

    /**
     * Shall the entry be deleted?
     *
     * @access private
     * @var boolean
     */
    var $_delete = false;

    /**
     * Map with changes to the entry
     *
     * @access private
     * @var array
     */
    var $_changes = array("add"     => array(),
                          "delete"  => array(),
                          "replace" => array()
                          );
    /**
     * Internal Constructor
     *
     * Constructor of the entry. Sets up the distinguished name and the entries
     * attributes.
     * You should not call this method manually! Use {@link Net_LDAP_Entry::createFresh()} instead!
     *
     * @access protected
     * @param Net_LDAP|ressource|array $ldap   Net_LDAP object, ldap-link ressource or array of attributes
     * @param string|ressource         $entry  Either a DN or a LDAP-Entry ressource
     * @return none
     */
    function Net_LDAP_Entry(&$ldap, $entry = null)
    {
        $this->PEAR('Net_LDAP_Error');

        if (is_resource($entry)) {
            $this->_entry = &$entry;
        } else {
            $this->_dn = $entry;
        }

        if (is_a($ldap, 'Net_LDAP')) {
            $this->_ldap = &$ldap;
            $this->_link = $ldap->getLink();
        } elseif (is_resource($ldap)) {
            $this->_link = $ldap;
        } elseif (is_array($ldap)) {
            $this->_setAttributes($ldap);  // setup attrs manually
        }

        if (is_resource($this->_entry) && is_resource($this->_link)) {
            $this->_new = false;
            $this->_dn  = @ldap_get_dn($this->_link, $this->_entry);
            $this->_setAttributes();  // fetch attributes from server
        }
    }

    /**
    * Creates a fresh entry that may be added to the directory later on
    *
    * The method should be called statically: $entry = Net_LDAP_Entry::createFresh();
    *
    * Use this method, if you want to initialize a fresh entry.
    * The attributes parameter is as following:
    * <code>
    * $attrs = array( 'attribute1' => array('value1', 'value2'),
    *                 'attribute2' => 'single value'
    *          );
    * </code>
    *
    * @static
    * @param string $dn     DN of the Entry
    * @param array  $attrs  Attributes of the entry
    * @return Net_LDAP_Entry
    */
    function createFresh($dn, $attrs = array())
    {
        if (!is_array($attrs)) {
            return PEAR::raiseError("Unable to create fresh entry: Parameter \$attrs needs to be an array!");
        }

        $entry = new Net_LDAP_Entry($attrs, $dn);
        return $entry;
    }

    /**
     * Get or set the distinguished name of the entry
     *
     * If called without an argument the current (or the new DN if set) DN gets returned.
     * If you provide an DN, this entry is moved to the new location specified if a DN existed.
     * If the DN was not set, the DN gets initialized. Call {@link update()} to actually create
     * the new Entry in the directory.
     *
     * Please note that special characters (eg german umlauts) should be encoded using utf8_encode().
     *
     * @access public
     * @param string $dn New distinguished name
     * @return string|true Distinguished name (or true if a new DN was provided)
     */
    function dn($dn = null)
    {
        if (false == is_null($dn)) {
            if (is_null($this->_dn)) {
                $this->_dn = $dn;
            } else {
                $this->_newdn = $dn;
            }
            $return = true;
            return $return;
        }
        return (isset($this->_newdn) ? $this->_newdn : $this->_dn);
    }

    /**
     * Sets the internal attributes array
     *
     * This fetches the values for the attributes from the server.
     * The attribute Syntax will be checked so binary attributes will be returned
     * as binary values.
     *
     * Attributes may be passed directly via the $attributes parameter to setup this
     * entry manually. This overrides attribute fetching from the server.
     *
     * @access private
     * @param array $attributes
     */
    function _setAttributes($attributes = null)
    {
        /*
        * fetch attributes from the server
        */
        if (is_null($attributes) && is_resource($this->_entry) && is_resource($this->_link))
        {
            // fetch schema
            if (is_a($this->_ldap, 'Net_LDAP')) {
                $schema =& $this->_ldap->schema();
            }
            // fetch attributes
            $attributes = array();
            do {
                if (empty($attr)) {
                    $ber = null;
                    $attr = @ldap_first_attribute($this->_link, $this->_entry, $ber);
                } else {
                    $attr = @ldap_next_attribute($this->_link, $this->_entry, $ber);
                }
                if ($attr) {
                    $func = 'ldap_get_values'; // standard function to fetch value

                    // Try to get binary values as binary data
                    if (is_a($schema, 'Net_LDAP_Schema')) {
                        if ($schema->isBinary($attr)) {
                             $func = 'ldap_get_values_len';
                        }
                    }
                    // fetch attribute value (needs error checking?)
                    $attributes[$attr] = $func($this->_link, $this->_entry, $attr);
                }
            } while ($attr);
        }

        /*
        * set attribute data directly, if passed
        */
        if (is_array($attributes) && count($attributes) > 0) {
            if (isset($attributes["count"]) && is_numeric($attributes["count"])) {
                unset($attributes["count"]);
            }
            foreach ($attributes as $k => $v) {
                // attribute names should not be numeric
                if (is_numeric($k)) {
                    continue;
                }
                // map generic attribute name to real one
                $this->_map[strtolower($k)] = $k;
                // attribute values should be in an array
                if (false == is_array($v)) {
                    $v = array($v);
                }
                // remove the value count (comes from ldap server)
                if (isset($v["count"])) {
                    unset($v["count"]);
                }
                $this->_attributes[$k] = $v;
            }
        }

        // save a copy for later use
        $this->_original = $this->_attributes;
    }

    /**
     * Get the values of all attributes in a hash
     *
     * The returned hash has the form
     * array('attributename' => 'single value',
     *       'attributename' => array('value1', value2', value3'))
     *
     * @access public
     * @return array Hash of all attributes with their values
     */
    function getValues()
    {
        $attrs = array();
        foreach ($this->_attributes as $attr => $value) {
            $attrs[$attr] = $this->getValue($attr);
        }
        return $attrs;
    }

    /**
     * Get the value of a specific attribute
     *
     * The first parameter is the name of the attribute
     * The second parameter influences the way the value is returned:
     * 'single': only the first value is returned as string
     * 'all': all values including the value count are returned in an
     *               array
     * 'default': in all other cases an attribute value with a single value is
     *            returned as string, if it has multiple values it is returned
     *            as an array (without value count)
     *
     * @access public
     * @param string $attr Attribute name
     * @param string $option Option
     * @return string|array|PEAR_Error string, array or PEAR_Error
     */
    function getValue($attr, $option = null)
    {
        $attr = $this->_getAttrName($attr);

        if (false == array_key_exists($attr, $this->_attributes)) {
            return PEAR::raiseError("Unknown attribute ($attr) requested");
        }

        $value = $this->_attributes[$attr];

        if ($option == "single" || (count($value) == 1 && $option != 'all')) {
            $value = array_shift($value);
        }

        return $value;
    }

    /**
     * Alias function of getValue for perl-ldap interface
     *
     * @see getValue()
     */
    function get_value()
    {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'getValue' ), $args);
    }

    /**
     * Returns an array of attributes names
     *
     * @access public
     * @return array Array of attribute names
     */
    function attributes()
    {
        return array_keys($this->_attributes);
    }

    /**
     * Returns whether an attribute exists or not
     *
     * @access public
     * @param string $attr Attribute name
     * @return boolean
     */
    function exists($attr)
    {
        $attr = $this->_getAttrName($attr);
        return array_key_exists($attr, $this->_attributes);
    }

    /**
     * Adds a new attribute or a new value to an existing attribute
     *
     * The paramter has to be an array of the form:
     * array('attributename' => 'single value',
     *       'attributename' => array('value1', 'value2))
     * When the attribute already exists the values will be added, else the
     * attribute will be created. These changes are local to the entry and do
     * not affect the entry on the server until update() is called.
     *
     * Note, that you can add values of attributes that you haven't selected, but if
     * you do so, {@link getValue()} and {@link getValues()} will only return the
     * values you added, _NOT_ all values present on the server. To avoid this, just refetch
     * the entry after calling {@link update()} or select the attribute.
     *
     * @access public
     * @param array $attr
     * @return true|Net_LDAP_Error
     */
    function add($attr = array())
    {
        if (false == is_array($attr)) {
            return PEAR::raiseError("Parameter must be an array");
        }
        foreach ($attr as $k => $v) {
            $k = $this->_getAttrName($k);
            if (false == is_array($v)) {
                // Do not add empty values
                if ($v == null) {
                    continue;
                } else {
                    $v = array($v);
                }
            }
            // add new values to existing attribute
            if ($this->exists($k)) {
                $this->_attributes[$k] = array_merge($this->_attributes[$k], $v);
            } else {
                $this->_map[strtolower($k)] = $k;
                $this->_attributes[$k] = $v;
            }
            // save changes for update()
            if (empty($this->_changes["add"][$k])) {
                $this->_changes["add"][$k] = array();
            }
            $this->_changes["add"][$k] = array_merge($this->_changes["add"][$k], $v);
        }
        $return = true;
        return $return;
    }

    /**
     * Deletes an whole attribute or a value or the whole entry
     *
     * The parameter can be one of the following:
     *
     * "attributename" - The attribute as a whole will be deleted
     * array("attributename1", "attributename2) - All given attributes will be
     *                                            deleted
     * array("attributename" => "value") - The value will be deleted
     * array("attributename" => array("value1", "value2") - The given values
     *                                                      will be deleted
     * If $attr is null or omitted , then the whole Entry will be deleted!
     *
     * These changes are local to the entry and do
     * not affect the entry on the server until {@link update()} is called.
     *
     * Please note that you must select the attribute (at $ldap->search() for example)
     * to be able to delete values of it, Otherwise {@link update()} will silently fail
     * and remove nothing.
     *
     * @access public
     * @param string|array $attr
     * @return true
     */
    function delete($attr = null)
    {
        if (is_null($attr)) {
            $this->_delete = true;
            $return = true;
            return $return;
        }
        if (is_string($attr)) {
            $attr = array($attr);
        }
        // Make the assumption that attribute names cannot be numeric,
        // therefore this has to be a simple list of attribute names to delete
        if (is_numeric(key($attr))) {
            foreach ($attr as $name) {
                $name = $this->_getAttrName($name);
                if ($this->exists($name)) {
                    $this->_changes["delete"][$name] = null;
                    unset($this->_attributes[$name]);
                }
            }
        } else {
            // Here we have a hash with "attributename" => "value to delete"
            foreach ($attr as $name => $values) {
                // get the correct attribute name
                $name = $this->_getAttrName($name);
                if ($this->exists($name)) {
                    if (false == is_array($values)) {
                        $values = array($values);
                    }
                    // save values to be deleted
                    if (empty($this->_changes["delete"][$name])) {
                        $this->_changes["delete"][$name] = array();
                    }
                    $this->_changes["delete"][$name] =
                        array_merge($this->_changes["delete"][$name], $values);
                    foreach ($values as $value) {
                        // find the key for the value that should be deleted
                        $key = array_search($value, $this->_attributes[$name]);
                        if (false !== $key) {
                            // delete the value
                            unset($this->_attributes[$name][$key]);
                        }
                    }
                }
            }
        }
        $return = true;
        return $return;
    }

    /**
     * Replaces attributes or its values
     *
     * The parameter has to an array of the following form:
     * array("attributename" => "single value",
     *       "attribute2name" => array("value1", "value2"))
     * If the attribute does not yet exist it will be added instead.
     * If the attribue value is null, the attribute will de deleted
     *
     * These changes are local to the entry and do
     * not affect the entry on the server until {@link update()} is called.
     *
     * @access public
     * @param array $attr
     * @return true|Net_LDAP_Error
     */
    function replace($attr = array())
    {
        if (false == is_array($attr)) {
            return PEAR::raiseError("Parameter must be an array");
        }
        foreach ($attr as $k => $v) {
            $k = $this->_getAttrName($k);
            if (false == is_array($v)) {
                // delete attributes with empty values
                if ($v == null) {
                    $this->delete($k);
                    continue;
                } else {
                    $v = array($v);
                }
            }
            // existing attributes will get replaced
            if ($this->exists($k)) {
                $this->_changes["replace"][$k] = $v;
                $this->_attributes[$k] = $v;
            } else {
                // new ones just get added
                $this->add(array($k => $v));
            }
        }
        $return = true;
        return $return;
    }

    /**
     * Update the entry on the directory server
     *
     * @access public
     * @param Net_LDAP $ldap (optional) If you provide a Net_LDAP object, be sure to PASS IT VIA REFERENCE!
     * @return true|Net_LDAP_Error
     * @todo Entry rename with a DN containing special characters needs testing!
     */
    function update($ldap=false)
    {
        if (!$ldap) {  // If object is not provided, then use this entrys ldap object
            $ldap =& $this->_ldap;
        } else {
            if (!is_a($ldap, 'Net_LDAP')) {
                $ldap = false; // throw error
            } else {
                // store the provided ldap object internally, if we haven't got one already
                if (!$this->_ldap) $this->_ldap =& $ldap;
            }
        }

        // ensure we have a valid LDAP object
        if (!is_a($ldap, 'Net_LDAP')) {
           return PEAR::raiseError("Need a Net_LDAP object as parameter");
        }

        $link = $ldap->getLink();

        /*
        * Delete the entry
        */
        if (true === $this->_delete) {
            return $ldap->delete($this);
        }

        /*
        * New entry
        */
        if (true === $this->_new) {
            $msg = $ldap->add($this);
            if (Net_LDAP::isError($msg)) {
                return $msg;
            }
            $this->_new = false;
            $this->_changes['add'] = array();
            $this->_changes['delete'] = array();
            $this->_changes['replace'] = array();
            $this->_original = $this->_attributes;

            $return = true;
            return $return;
        }

        /*
        * Rename/move entry
        */
        if (false == is_null($this->_newdn)) {
            if ($ldap->getLDAPVersion() !== 3) {
                return PEAR::raiseError("Renaming/Moving an entry is only supported in LDAPv3");
            }
            // make dn relative to parent (needed for ldap rename)
            $parent = Net_LDAP_Util::ldap_explode_dn($this->_newdn, array('casefolding' => 'none', 'reverse' => false, 'onlyvalues' => false));
            if (Net_LDAP::isError($parent)) {
                return $parent;
            }
            $child = array_shift($parent);
            // maybe the dn consist of a multivalued RDN, we must build the dn in this case
            // because the $child-RDN is an array!
            if (is_array($child)) {
                $child = Net_LDAP_Util::canonical_dn($child);
            }
            $parent = Net_LDAP_Util::canonical_dn($parent);

            // rename
            if (false == @ldap_rename($link, $this->_dn, $child, $parent, true)) {
                return PEAR::raiseError("Entry not renamed: " .
                                        @ldap_error($link), @ldap_errno($link));
            }
            // reflect changes to local copy
            $this->_dn = $this->_newdn;
            $this->_newdn = null;
        }

        /*
        * Carry out modifications to the entry
        */
        // ADD
        foreach ($this->_changes["add"] as $attr => $value) {
            // if attribute exists, add new values
            if ($this->exists($attr)) {
                if (false === @ldap_mod_add($link, $this->dn(), array($attr => $value))) {
                    return PEAR::raiseError("Could not add new values to attribute $attr: " .
                                            @ldap_error($link), @ldap_errno($link));
                }
            } else {
                // new attribute
                if (false === @ldap_modify($link, $this->dn(), array($attr => $value))) {
                    return PEAR::raiseError("Could not add new attribute $attr: " .
                                            @ldap_error($link), @ldap_errno($link));
                }
            }
            // all went well here, I guess
            unset($this->_changes["add"][$attr]);
        }

        // DELETE
        foreach ($this->_changes["delete"] as $attr => $value) {
            // In LDAPv3 you need to specify the old values for deleting
            if (is_null($value) && $ldap->getLDAPVersion() === 3) {
                $value = $this->_original[$attr];
            }
            if (false === @ldap_mod_del($link, $this->dn(), array($attr => $value))) {
                return PEAR::raiseError("Could not delete attribute $attr: " .
                                        @ldap_error($link), @ldap_errno($link));
            }
            unset($this->_changes["delete"][$attr]);
        }

        // REPLACE
        foreach ($this->_changes["replace"] as $attr => $value) {
            if (false === @ldap_modify($link, $this->dn(), array($attr => $value))) {
                return PEAR::raiseError("Could not replace attribute $attr values: " .
                                        @ldap_error($link), @ldap_errno($link));
            }
            unset($this->_changes["replace"][$attr]);
        }

        // all went well, so _original (server) becomes _attributes (local copy)
        $this->_original = $this->_attributes;

        $return = true;
        return $return;
    }

    /**
     * Returns the right attribute name
     *
     * @access private
     * @param string $attr Name of attribute
     * @return string The right name of the attribute
     */
    function _getAttrName($attr)
    {
        $name = strtolower($attr);
        if (array_key_exists($name, $this->_map)) {
            $attr = $this->_map[$name];
        }
        return $attr;
    }

    /**
     * Returns a reference to the LDAP-Object of this entry
     *
     * @access public
     * @return Net_LDAP|Net_LDAP_Error   Reference to the Net_LDAP Object (the connection) or Net_LDAP_Error
     */
    function &getLDAP()
    {
        if (!is_a($this->_ldap, 'Net_LDAP')) {
            return PEAR::raiseError("LDAP is not a valid Net_LDAP object");
        } else {
            return $this->_ldap;
        }
    }

    /**
    * Applies a regular expression onto a single- or multivalued attribute (like preg_match())
    *
    * This method behaves like PHPs preg_match() but with some exceptions.
    * If you want to retrieve match information, then you MUST pass the
    * $matches parameter via reference! otherwise you will get no matches.
    * Since it is possible to have multi valued attributes the $matches
    * array will have a additionally numerical dimension (one for each value):
    * <code>
    * $matches = array(
    *         0 => array (usual preg_match() returnarray),
    *         1 => array (usual preg_match() returnarray)
    *     )
    * </code>
    * Please note, that $matches will be initialized to an empty array inside.
    *
    * Usage example:
    * <code>
    * $result = $entry->preg_match('/089(\d+)/', 'telephoneNumber', &$matches);
    * if ( $result === true ){
    *     echo "First match: ".$matches[0][1];   // Match of value 1, content of first bracket
    * } else {
    *     if ( Net_LDAP::isError($result) ) {
    *         echo "Error: ".$result->getMessage();
    *     } else {
    *         echo "No match found.";
    *     }
    * }
    * </code>
    *
    * Please note that it is important to test for an Net_LDAP_Error, because objects are
    * evaluating to true by default, thus if a error occured, and you only check using "==" then
    * you get misleading results. Use the "identical" (===) operator to test for matches to
    * avoid this as shown above.
    *
    * @param string $regex     The regular expression
    * @param string $attr_name The attribute to search in
    * @param array  $matches   (optional, PASS BY REFERENCE!) Array to store matches in
    * @return boolean|Net_LDAP_Error  TRUE, if we had a match in one of the values, otherwise false. Net_LDAP_Error in case something went wrong
    */
    function preg_match($regex, $attr_name, $matches = array()){
        $matches = array();

        // fetch attribute values
        $attr = $this->getValue($attr_name, 'all');
        if (Net_LDAP::isError($attr)) {
            return $attr;
        } else {
            unset($attr['count']);
        }

        // perform preg_match() on all values
        $match = false;
        foreach ($attr as $thisvalue) {
            $matches_int = array();
            if (preg_match($regex, $thisvalue, $matches_int)) {
                $match = true;
                array_push($matches, $matches_int); // store matches in reference
            }
        }
        return $match;
    }
}
?>