<?php

$current_path = dirname(__FILE__);
$path = $current_path . PATH_SEPARATOR . $current_path . '/libs/PEAR'. PATH_SEPARATOR . dirname($current_path);
set_include_path($path . PATH_SEPARATOR . get_include_path());

?>