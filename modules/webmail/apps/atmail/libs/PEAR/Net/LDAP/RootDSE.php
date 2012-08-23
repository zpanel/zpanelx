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
// | Authors: Jan Wagner                                                      |
// +--------------------------------------------------------------------------+
//
// $Id: RootDSE.php,v 1.8 2006/12/22 11:16:57 beni Exp $

/**
 * Getting the rootDSE entry of a LDAP server
 *
 * @package Net_LDAP
 * @author Jan Wagner <wagner@netsols.de>
 * @version $Revision: 1.8 $
 */
class Net_LDAP_RootDSE extends PEAR
{
    /**
     * @access private
     * @var object Net_LDAP_Entry
     **/
    var $_entry;

    /**
     * class constructor
     *
     * @param Net_LDAP_Entry $entry Net_LDAP_Entry object
     */
    function Net_LDAP_RootDSE(&$entry)
    {
        $this->_entry = $entry;
    }

    /**
     * Gets the requested attribute value
     *
     * Same usuage as {@link Net_LDAP_Entry::get_value()}
     *
     * @access public
     * @param string $attr     Attribute name
     * @param array  $options  Array of options
     * @return mixed Net_LDAP_Error object or attribute values
     * @see Net_LDAP_Entry::get_value()
     */
    function getValue($attr = '', $options = '')
    {
        return $this->_entry->get_value($attr, $options);
    }

    /**
     * alias function of getValue() for perl-ldap interface
     *
     * @see getValue()
     */
     function get_value()
     {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'getValue' ), $args);
     }

    /**
     * Determines if the extension is supported
     *
     * @access public
     * @param array $oids Array of oids to check
     * @return boolean
     */
    function supportedExtension($oids)
    {
        return $this->_checkAttr($oids, 'supportedExtension');
    }

    /**
     * alias function of supportedExtension() for perl-ldap interface
     *
     * @see supportedExtension()
     */
     function supported_extension()
     {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'supportedExtension'), $args);
     }

    /**
     * Determines if the version is supported
     *
     * @access public
     * @param array $versions Versions to check
     * @return boolean
     */
    function supportedVersion($versions)
    {
        return $this->_checkAttr($versions, 'supportedLDAPVersion');
    }

    /**
     * alias function of supportedVersion() for perl-ldap interface
     *
     * @see supportedVersion()
     */
     function supported_version()
     {
        $args = func_get_args();
        return call_user_func_array(array(&$this, 'supportedVersion'), $args);
     }    

     /**
     * Determines if the control is supported
     *
     * @access public
     * @param array $oids Control oids to check
     * @return boolean
     */
    function supportedControl($oids)
    {
        return $this->_checkAttr($oids, 'supportedControl');
    }

    /**
     * alias function of supportedControl() for perl-ldap interface
     *
     * @see supportedControl()
     */
     function supported_control()
     {
        $args = func_get_args();
        return call_user_func_array(array(&$this, 'supportedControl' ), $args);
     }

    /**
     * Determines if the sasl mechanism is supported
     *
     * @access public
     * @param array $mechlist SASL mechanisms to check
     * @return boolean
     */
    function supportedSASLMechanism($mechlist)
    {
        return $this->_checkAttr($mechlist, 'supportedSASLMechanisms');
    }

    /**
     * alias function of supportedSASLMechanism() for perl-ldap interface
     *
     * @see supportedSASLMechanism()
     */
     function supported_sasl_mechanism() 
     {
        $args = func_get_args();
        return call_user_func_array(array(&$this, 'supportedSASLMechanism'), $args);
     }

     /**
     * Checks for existance of value in attribute
     *
     * @access private
     * @param array $values values to check
     * @param attr $attr attribute name
     * @return boolean
     */
    function _checkAttr($values, $attr)
    {
        if (!is_array($values)) $values = array($values);

        foreach ($values as $value) {
            if (!@in_array($value, $this->get_value($attr, 'all'))) {
                return false;
            }
        }
        return true;
    }
}

?>