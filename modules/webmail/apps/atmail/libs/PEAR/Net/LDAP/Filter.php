<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +--------------------------------------------------------------------------+
// | Net_LDAP                                                                 |
// +--------------------------------------------------------------------------+
// | Copyright (c) 1997-2007 The PHP Group                                    |
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
// | Authors: Benedikt Hallinger                                              |
// +--------------------------------------------------------------------------+
//
// $Id: Filter.php,v 1.7 2007/06/13 13:12:08 beni Exp $

require_once("PEAR.php");
require_once('Util.php');

/**
* Object representation of a part of a LDAP filter.
*
* This Class is not completely compatible to the PERL interface!
*
* The purpose of this class is, that users can easily build LDAP filters
* without having to worry about right escaping etc.
* A Filter is built using several independent filter objects
* which are combined afterwards. This object works in two
* modes, depending how the object is created.
* If the object is created using the {@link create()} method, then this is a leaf-object.
* If the object is created using the {@link combine()} method, then this is a container object.
*
* LDAP filters are defined in RFC-2254 and can be found under
* {@link http://www.ietf.org/rfc/rfc2254.txt}
*
* Here a quick copy&paste example:
* <code>
* $filter0 = Net_LDAP_Filter::create('stars', 'equals', '***');
* $filter_not0 = Net_LDAP_Filter::combine('not', $filter0);
*
* $filter1 = Net_LDAP_Filter::create('gn', 'begins', 'bar');
* $filter2 = Net_LDAP_Filter::create('gn', 'ends', 'baz');
* $filter_comp = Net_LDAP_Filter::combine('or',array($filter_not0, $filter1, $filter2));
*
* echo $filter_comp->asString();
* // This will output: (|(!(stars=\0x5c0x2a\0x5c0x2a\0x5c0x2a))(gn=bar*)(gn=*baz))
* // The stars in $filter0 are treaten as real stars unless you disable escaping.
* </code>
*
* @package Net_LDAP
* @author Benedikt Hallinger <beni@php.net>
* @version $Revision: 1.7 $
*/

class Net_LDAP_Filter extends PEAR
{
    /**
    * Storage for combination of filters
    *
    * This variable holds a array of filter objects
    * that should be combined by this filter object.
    *
    * @access private
    * @var array
    */
    var $_subfilters = array();

    /**
    * Match of this filter
    *
    * If this is a leaf filter, then a matching rule is stored,
    * if it is a container, then it is a logical operator
    *
    * @access private
    * @var string
    */
    var $_match;

    /**
    * Single filter
    *
    * If we operate in leaf filter mode,
    * then the constructing method stores
    * the filter representation here
    *
    * @acces private
    * @var string
    */
    var $_filter;

    /**
    * Private, empty constructor
    *
    * Construction of Net_LDAP_Filter objects occours through either
    * {@link create()} or {@link combine()}
    *
    * @access private
    */
    function Net_LDAP_Filter()
    {
    }

    /**
    * Constructor of a new part of a LDAP filter.
    *
    * The following matching rules exists:
    *    - equals:         One of the attributes values is exactly $value
    *                      Please note that case sensitiviness is depends on the
    *                      attributes syntax configured in the server.
    *    - begins:         One of the attributes values must begin with $value
    *    - ends:           One of the attributes values must end with $value
    *    - contains:       One of the attributes values must contain $value
    *    - any:            The attribute can contain any value but must be existent
    *    - greater:        The attributes value is greater than $value
    *    - less:           The attributes value is less than $value
    *    - greaterOrEqual: The attributes value is greater or equal than $value
    *    - lessOrEqual:    The attributes value is less or equal than $value
    *    - approx:         One of the attributes values is similar to $value
    *
    * If $escape is set to true (default) then $value will be escaped
    * properly. If it is set to false then $value will be treaten as raw value.
    *
    * Examples:
    * $filter = new Net_LDAP_Filter('sn', 'ends', 'foobar');
    * -> This will find entries that contain a attribute "sn" that ends with "foobar".
    * $filter = new Net_LDAP_Filter('sn', 'any');
    * -> This will find entries that contain a attribute "sn" that has any value set.
    *
    * @param string  $attr_name       Name of the attribute the filter should apply to
    * @param string  $match           Matching rule (equals, begins, ends, contains, greater, less, greaterOrEqual, lessOrEqual, approx, any)
    * @param string  $value           (optional) if given, then this is used as a filter
    * @param boolean $escape          Should the whole $value be escaped? (default: yes, see {@link escape()} for detailed information)
    * @return Net_LDAP_Filter|Net_LDAP_Error
    * @see escape()
    * @todo implement greaterOrEqual, lessOrEqual, approx
    */
    function &create($attr_name, $match, $value = '', $escape = true)
    {
        $leaf_filter = new Net_LDAP_Filter();
        if ($escape) {
            $array = Net_LDAP_Util::escape_filter_value(array($value));
            $value = $array[0];
        }
        switch (strtolower($match)) {
            case 'equals':
                $leaf_filter->_filter = '(' . $attr_name . '=' . $value . ')';
            break;
            case 'begins':
            $leaf_filter->_filter = '(' . $attr_name . '=' . $value . '*)';
            break;
            case 'ends':
                $leaf_filter->_filter = '(' . $attr_name . '=*' . $value . ')';
            break;
            case 'contains':
                $leaf_filter->_filter = '(' . $attr_name . '=*' . $value . '*)';
            break;
            case 'greater':
                $leaf_filter->_filter = '(' . $attr_name . '>' . $value . ')';
            break;
            case 'less':
                $leaf_filter->_filter = '(' . $attr_name . '<' . $value . ')';
            break;
            case 'greaterorequal':
                $leaf_filter->_filter = '(' . $attr_name . '>=' . $value . ')';
            break;
            case 'lessorequal':
                $leaf_filter->_filter = '(' . $attr_name . '<=' . $value . ')';
            break;
            case 'approx':
                $leaf_filter->_filter = '(' . $attr_name . '=~' . $value . ')';
            break;
            case 'any':
                $leaf_filter->_filter = '(' . $attr_name . '=*)';
            break;
            default:
                return PEAR::raiseError('Net_LDAP_Filter create error: matching rule "' . $match . '" not known!');
        }
        return $leaf_filter;
    }

    /**
    * Combine two or more filter objects using a logical operator
    *
    * This static method combines two or more filter objects and returns one single
    * filter object that contains all the others.
    * Call this method statically: $filter =& Net_LDAP_Filter('or', array($filter1, $filter2))
    *
    * @param string $log_op         The locicall operator. May be "and", "or", "not" or the subsequent logical equivalents "&", "|", "!"
    * @param array|Net_LDAP_Filter  $filters     array with Net_LDAP_Filter objects
    * @return Net_LDAP_Filter|Net_LDAP_Error
    * @static
    */
    function &combine($log_op, $filters)
    {
        if (PEAR::isError($filters)) {
            return $filters;
        }

        // substitude named operators to logical operators
        if ($log_op == 'and') $log_op = '&';
        if ($log_op == 'or')  $log_op = '|';
        if ($log_op == 'not') $log_op = '!';

        // tests for sane operation
        if ($log_op == '!') {
            // Not-combination, here we also accept one filter object
            if (!is_array($filters) && is_a($filters, 'Net_LDAP_Filter')) {
                $filters = array($filters); // force array
            } else {
                $err = PEAR::raiseError('Net_LDAP_Filter combine error: operator is "not" but $filter is not a valid Net_LDAP_Filter nor an array!');
                return $err;
            }
        } elseif ($log_op == '&' || $log_op == '|') {
            if (!is_array($filters) || count($filters) < 2) {
                $err = PEAR::raiseError('Net_LDAP_Filter combine error: Parameter $filters is not a array or contains less than two Net_LDAP_Filter objects!');
                return $err;
            }
        } else {
            $err = PEAR::raiseError('Net_LDAP_Filter combine error: logical operator is not known!');
            return $err;
        }


        if ($log_op != '&' && $log_op != '|' && $log_op != '!') {
            return PEAR::raiseError('Net_LDAP_Filter combine error: Logical operator "' . $log_op . '" not known!');
        }

        $combined_filter = new Net_LDAP_Filter();
        foreach ($filters as $testfilter) {     // check for errors
            if (is_a($testfilter, 'Net_LDAP_Error')) {
                return $testfilter;
            }
        }

        $combined_filter->_subfilters = $filters;
        $combined_filter->_match = $log_op;
        return $combined_filter;
    }

    /**
    * Get the string representation of this filter
    *
    * This method runs through all filter objects and creates
    * the string representation of the filter. If this
    * filter object is a leaf filter, then it will return
    * the string representation of this filter.
    *
    * @return string
    */
    function asString()
    {
        if ($this->_isLeaf()) {
            $return = $this->_filter;
        } else {
            $return = '';
            foreach ($this->_subfilters as $filter) {
                $return = $return.$filter->asString();
            }
            $return = '(' . $this->_match . $return . ')';
        }
        return $return;
    }

    /**
    * Alias for perl interface as_string()
    *
    * @see asString()
    */
    function as_string()
    {
        return $this->asString();
    }

    /**
    * This can be used to escape a string to provide a valid LDAP-Filter.
    *
    * LDAP will only recognise certain characters as the
    * character istself if it is properly escaped. This is
    * what this method does.
    * The method can be called statically, so you can use it outside
    * for your own purposes (eg for escaping only parts of strings)
    *
    * In fact, this is just a shorthand to {@link Net_LDAP_Util::escape_filter_value()}.
    *
    * @static
    * @param string $string  Any string who should be escaped
    * @return string         The string $string, but escaped
    * @deprecated  Do not use this method anymore, instead use Net_LDAP_Util::escape_filter_value()
    */
    function escape($string)
    {
        return PEAR::raiseError("PLEASE DO NOT USE Net_LDAP_Filter anymore! use Net_LDAP_Util::escape_filter_value() instead!");
    }

    /**
    * Is this a container or a leaf filter object?
    *
    * @access private
    * @return boolean
    */
    function _isLeaf()
    {
        if (count($this->_subfilters) > 0) {
            return false; // Container!
        } else {
            return true; // Leaf!
        }
    }
}
?>