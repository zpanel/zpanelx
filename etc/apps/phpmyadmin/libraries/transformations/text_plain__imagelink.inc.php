<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @package PhpMyAdmin-Transformation
 */

function PMA_transformation_text_plain__imagelink_info()
{
    return array(
        'info' => __('Displays an image and a link; the column contains the filename. The first option is a URL prefix like "http://www.example.com/". The second and third options are the width and the height in pixels.'),
        );
}

/**
 *
 */
function PMA_transformation_text_plain__imagelink($buffer, $options = array(), $meta = '')
{
    include_once './libraries/transformations/global.inc.php';

    $transform_options = array ('string' => '<a href="' . (isset($options[0]) ? $options[0] : '') . $buffer . '" target="_blank"><img src="' . (isset($options[0]) ? $options[0] : '') . $buffer . '" border="0" width="' . (isset($options[1]) ? $options[1] : 100) . '" height="' . (isset($options[2]) ? $options[2] : 50) . '" />' . $buffer . '</a>');
    $buffer = PMA_transformation_global_html_replace($buffer, $transform_options);
    return $buffer;
}

?>
