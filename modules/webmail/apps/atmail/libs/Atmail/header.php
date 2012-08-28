<?php
$path = '.' . PATH_SEPARATOR . 'libs/' . PATH_SEPARATOR . 'libs/PEAR'. PATH_SEPARATOR . '..'
      . PATH_SEPARATOR . '../../' . PATH_SEPARATOR . 'libs/Atmail' . PATH_SEPARATOR . 'webmail/libs/Atmail'
      . PATH_SEPARATOR . 'webmail/libs/PEAR'. PATH_SEPARATOR . '/usr/local/atmail/webmail/libs/Atmail'
      . PATH_SEPARATOR . '/usr/local/atmail/webmail/libs/';
set_include_path($path . PATH_SEPARATOR . get_include_path());
?>
