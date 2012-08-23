<?php

/**
 * MessageHeader.class.php
 *
 * This file contains functions needed to handle headers in mime messages.
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: MessageHeader.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.2
 */

/**
 * Message header class
 * Class contains all variables available in a bodystructure
 * entity like described in rfc2060
 * It was called msg_header in 1.3.0 and 1.3.1.
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.2
 */
class MessageHeader {
    /**
     * Media type
     * @var string
     */
    var $type0 = '';
    /**
     * Media subtype
     * @var string
     */
    var $type1 = '';
    /**
     * Content type parameters
     * @var array
     */
    var $parameters = array();
    /**
     * @var mixed
     */
    var $id = 0;
    /**
     * @var string
     */
    var $description = '';
    /**
     * @var string
     */
    var $encoding='';
    /**
     * Message size
     * @var integer
     */
    var $size = 0;
    /**
     * @var string
     */
    var $md5='';
    /**
     * @var mixed
     */
    var $disposition = '';
    /**
     * @var mixed
     */
    var $language='';

    /**
     * Sets header variable
     * @param string $var
     * @param mixed $value
     */
    function setVar($var, $value) {
        $this->{$var} = $value;
    }

    /**
     * Gets parameter value from $parameters array
     * @param string $p
     * @return mixed
     */
    function getParameter($p) {
        $value = strtolower($p);
        return (isset($this->parameters[$p]) ? $this->parameters[$p] : '');
    }

    /**
     * Sets parameter value in $parameters array
     * @param string $parameter
     * @param mixed $value
     */
    function setParameter($parameter, $value) {
        $this->parameters[strtolower($parameter)] = $value;
    }
}

