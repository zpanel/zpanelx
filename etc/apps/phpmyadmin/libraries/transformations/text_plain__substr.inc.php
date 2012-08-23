<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @package PhpMyAdmin-Transformation
 */

function PMA_transformation_text_plain__substr_info()
{
    return array(
        'info' => __('Displays a part of a string. The first option is the number of characters to skip from the beginning of the string (Default 0). The second option is the number of characters to return (Default: until end of string). The third option is the string to append and/or prepend when truncation occurs (Default: "...").'),
        );
}

/**
 *
 */
function PMA_transformation_text_plain__substr($buffer, $options = array(), $meta = '')
{
    // possibly use a global transform and feed it with special options:
    // include './libraries/transformations/global.inc.php';

    // further operations on $buffer using the $options[] array.
    if (!isset($options[0]) ||  $options[0] == '') {
        $options[0] = 0;
    }

    if (!isset($options[1]) ||  $options[1] == '') {
        $options[1] = 'all';
    }

    if (!isset($options[2]) || $options[2] == '') {
        $options[2] = '...';
    }

    $newtext = '';
    if ($options[1] != 'all') {
        $newtext = PMA_substr($buffer, $options[0], $options[1]);
    } else {
        $newtext = PMA_substr($buffer, $options[0]);
    }

    $length = strlen($newtext);
    $baselength = strlen($buffer);
    if ($length != $baselength) {
        if ($options[0] != 0) {
            $newtext = $options[2] . $newtext;
        }

        if (($length + $options[0]) != $baselength) {
            $newtext .= $options[2];
        }
    }

    return $newtext;
}

?>
