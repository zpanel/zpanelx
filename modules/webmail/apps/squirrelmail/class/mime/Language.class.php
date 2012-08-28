<?php

/**
 * Language.class.php
 *
 * This file should contain class needed to handle Language properties in 
 * mime messages. I suspect that it is RFC2231
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: Language.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.2
 */

/**
 * Class that can be used to handle language properties in MIME headers.
 *
 * @package squirrelmail
 * @subpackage mime
 * @since 1.3.0
 */
class Language {
    /**
     * Class constructor
     * @param mixed $name
     */
    function Language($name) {
        /** @var mixed */
        $this->name = $name;
        /**
         * Language properties
         * @var array 
         */
        $this->properties = array();
    }
}

