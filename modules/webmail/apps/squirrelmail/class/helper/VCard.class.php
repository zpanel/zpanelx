<?php

/**
 * vCard.class
 *
 * This (will) contain functions needed to vCards.
 *
 * http://www.imc.org/pdi/vcard-21.txt
 *
 * @copyright 2003-2011 The SquirrelMail Project Team
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id: VCard.class.php 14084 2011-01-06 02:44:03Z pdontthink $
 * @package squirrelmail
 * @since 1.3.2
 */

/**
 * Unimplemented class that should handle vcards
 * Don't use it unless it is marked as implemented.
 * @package squirrelmail
 */
class VCard {
    /**
     * Create vcard from information stored in array
     * @todo implement vcard creation from array
     * @param array $value_array
     * @return string
     */
    function create_vcard ($value_array) {
        return $vcard;
    }

    /**
     * Read vcard and convert it to array
     * @todo implement vcard parsing
     * @param string $vcard
     * @return array
     */
    function parse_vcard ($vcard) {
        return $array;
    }
}

