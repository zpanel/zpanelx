<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @package PhpMyAdmin-Engines
 */

/**
 *
 * @package PhpMyAdmin-Engines
 */
class PMA_StorageEngine_binlog extends PMA_StorageEngine
{
    /**
     * returns string with filename for the MySQL helppage
     * about this storage engne
     *
     * @return  string  mysql helppage filename
     */
    function getMysqlHelpPage()
    {
        return 'binary-log';
    }
}

?>
