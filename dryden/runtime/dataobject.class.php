<?php

/**
 * Dataobject class a means to handle data arrays.
 * @package zpanelx
 * @subpackage dryden -> runtime
 * @version 1.0.0
 * @author Bobby Allen (ballen@zpanelcp.com)
 * @copyright ZPanel Project (http://www.zpanelcp.com/)
 * @link http://www.zpanelcp.com/
 * @license GPL (http://www.gnu.org/licenses/gpl.html)
 */
class runtime_dataobject {

    /**
     * @var array The associated array of which to store all dataobject entites in. 
     */
    var $object;

    function __construct() {
        $this->object = array();
    }

    /**
     * Used to 'push' an associated array on to the object's $object array.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param array $arr The associative array data to add to the object.
     * @return obj 
     */
    private function array_push_associative(&$arr) {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $key => $value) {
                    $arr[$key] = $value;
                    $this->object++;
                }
            } else {
                $arr[$arg] = "";
            }
        }
        return $this->object;
    }

    /**
     * Enables an easy to use key/value pushing to the data object.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $name The name of the key.
     * @param string $value The value of the key.
     */
    public function addItemValue($name, $value) {
        $this->array_push_associative($this->object, array("$name" => "$value"));
    }

    /**
     * Returns an associated array (data object)
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @return array The dataobject contents.
     */
    public function getDataObject() {
        return $this->object;
    }

    /**
     * Returns a named data object record value.
     * @author Bobby Allen (ballen@zpanelcp.com)
     * @param string $name The name of the data record (key) to return from the current object.
     * @return string The value.
     */
    public function getDataRecord($name) {
        return $this->object[$name];
    }

}

?>
