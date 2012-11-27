<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Javascript escaping functions.
 *
 * @package phpMyAdmin
 *
 */

/**
 * Format a string so it can be a string inside JavaScript code inside an
 * eventhandler (onclick, onchange, on..., ).
 * This function is used to displays a javascript confirmation box for
 * "DROP/DELETE/ALTER" queries.
 *
 * @uses    PMA_escapeJsString()
 * @uses    PMA_backquote()
 * @uses    is_string()
 * @uses    htmlspecialchars()
 * @uses    str_replace()
 * @param   string   $a_string          the string to format
 * @param   boolean  $add_backquotes    whether to add backquotes to the string or not
 *
 * @return  string   the formatted string
 *
 * @access  public
 */
function PMA_jsFormat($a_string = '', $add_backquotes = true) {
    if (is_string($a_string)) {
        $a_string = htmlspecialchars($a_string);
        $a_string = PMA_escapeJsString($a_string);
        /**
         * @todo what is this good for?
         */
        $a_string = str_replace('#', '\\#', $a_string);
    }

    return (($add_backquotes) ? PMA_backquote($a_string) : $a_string);
}

// end of the 'PMA_jsFormat()' function

/**
 * escapes a string to be inserted as string a JavaScript block
 * enclosed by <![CDATA[ ... ]]>
 * this requires only to escape ' with \' and end of script block
 *
 * We also remove NUL byte as some browsers (namely MSIE) ignore it and
 * inserting it anywhere inside </script would allow to bypass this check.
 *
 * @uses    strtr()
 * @uses    preg_replace()
 * @param   string  $string the string to be escaped
 * @return  string  the escaped string
 */
function PMA_escapeJsString($string) {
    return preg_replace('@</script@i', '</\' + \'script', strtr($string, array(
                        "\000" => '',
                        '\\' => '\\\\',
                        '\'' => '\\\'',
                        '"' => '\"',
                        "\n" => '\n',
                        "\r" => '\r')));
}

/**
 * Prints an javascript assignment with proper escaping of a value
 * and support for assigning array of strings.
 *
 * @param string $key Name of value to set
 * @param mixed $value Value to set, can be either string or array of strings
 */
function PMA_printJsValue($key, $value) {
    echo $key . ' = ';
    if (is_array($value)) {
        echo '[';
        foreach ($value as $id => $val) {
            echo "'" . PMA_escapeJsString($val) . "',";
        }
        echo "];\n";
    } else {
        echo "'" . PMA_escapeJsString($value) . "';\n";
    }
}

?>
